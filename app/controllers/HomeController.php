<?php

namespace app\controllers;

use flight\Engine;
use app\models\BesoinModel;
use app\models\VilleModel;
use app\models\UniteModel;
use app\models\SinistreModel;
use app\models\ObjetModel;

class HomeController
{
    protected Engine $app;
    protected BesoinModel $besoinModel;
    protected VilleModel $villeModel;
    protected UniteModel $uniteModel;
    protected SinistreModel $sinistreModel;
    protected ObjetModel $objetModel;

    public function __construct($app)
    {
        $this->app = $app;
        $this->besoinModel = new BesoinModel($this->app->db());
        $this->villeModel = new VilleModel($this->app->db());
        $this->uniteModel = new UniteModel($this->app->db());
        $this->sinistreModel = new SinistreModel($this->app->db());
        $this->objetModel = new ObjetModel($this->app->db());
    }

    public function showHome()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $username = 'Visiteur';
        $userObjects = [];

        // Récupère la liste des besoins pour la vue
        $besoins = $this->besoinModel->get();
        $villes = $this->villeModel->get();
        $unites = $this->uniteModel->get();
        $objets = $this->objetModel->getAll();

        // Récupère les unités associées à chaque besoin
        $unitesByBesoin = $this->getUnitesByBesoin();

        $this->app->render('home.php', [
            'username' => $username, 
            'userObjects' => $userObjects, 
            'besoins' => $besoins, 
            'villes' => $villes, 
            'unites' => $unites, 
            'objets' => $objets,
            'unitesByBesoin' => $unitesByBesoin
        ]);
    }

    private function getUnitesByBesoin()
    {
        $query = '
            SELECT DISTINCT b.id AS besoin_id, u.id AS unite_id
            FROM BNGRC_besoins b
            LEFT JOIN BNGRC_objet o ON b.id = o.id_besoins
            LEFT JOIN BNGRC_unite u ON o.id_unite = u.id
            WHERE u.id IS NOT NULL
            ORDER BY b.id, u.id
        ';
        
        $stmt = $this->app->db()->query($query);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $unitesByBesoin = [];
        foreach ($results as $row) {
            $besoinId = $row['besoin_id'];
            $uniteId = $row['unite_id'];
            if (!isset($unitesByBesoin[$besoinId])) {
                $unitesByBesoin[$besoinId] = [];
            }
            $unitesByBesoin[$besoinId][] = $uniteId;
        }
        
        return $unitesByBesoin;
    }

    public function createSinistre()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->app->redirect('/');
            return;
        }

        $id_objet = isset($_POST['objet']) ? (int)$_POST['objet'] : 0;
        $id_ville = isset($_POST['ville']) ? (int)$_POST['ville'] : 0;
        $quantite = isset($_POST['quantite']) ? (int)$_POST['quantite'] : 0;
        $dateRaw = isset($_POST['date']) ? trim((string)$_POST['date']) : '';

        // Normalize datetime-local (YYYY-MM-DDTHH:MM) to MySQL DATETIME (YYYY-MM-DD HH:MM:SS)
        $date = null;
        if ($dateRaw !== '') {
            $date = str_replace('T', ' ', $dateRaw);
            // if seconds missing, append :00
            if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $date)) {
                $date .= ':00';
            }
            try {
                $dt = new \DateTime($date);
                $date = $dt->format('Y-m-d H:i:s');
            } catch (\Throwable $e) {
                $date = null;
            }
        }

        // Simple validation
        if ($id_objet <= 0 || $id_ville <= 0) {
            $this->app->redirect('/?created=0');
            return;
        }

        try {
            $newId = $this->sinistreModel->insertByObjet($id_objet, $id_ville, $quantite, $date);
            
            // Stocker l'ID en session pour permettre la réinitialisation
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            if (!isset($_SESSION['created_besoins'])) {
                $_SESSION['created_besoins'] = [];
            }
            $_SESSION['created_besoins'][] = $newId;
            
            $this->app->redirect('/?created=1');
        } catch (\Throwable $e) {
            $this->app->redirect('/?created=0');
        }
    }

    /**
     * Réinitialise (supprime) les besoins créés pendant cette session
     */
    public function resetSession()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $deletedCount = 0;
        if (isset($_SESSION['created_besoins']) && is_array($_SESSION['created_besoins'])) {
            foreach ($_SESSION['created_besoins'] as $id) {
                if ($this->sinistreModel->delete((int)$id)) {
                    $deletedCount++;
                }
            }
            $_SESSION['created_besoins'] = [];
        }

        $this->app->redirect('/?reset=' . $deletedCount);
    }
}
