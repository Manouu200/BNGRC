<?php

namespace app\controllers;

use flight\Engine;
use app\models\BesoinModel;
use app\models\VilleModel;
use app\models\UniteModel;
use app\models\SinistreModel;

class HomeController
{
    protected Engine $app;
    protected BesoinModel $besoinModel;
    protected VilleModel $villeModel;
    protected UniteModel $uniteModel;
    protected SinistreModel $sinistreModel;

    public function __construct($app)
    {
        $this->app = $app;
        $this->besoinModel = new BesoinModel($this->app->db());
        $this->villeModel = new VilleModel($this->app->db());
        $this->uniteModel = new UniteModel($this->app->db());
        $this->sinistreModel = new SinistreModel($this->app->db());
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

        $this->app->render('home.php', ['username' => $username, 'userObjects' => $userObjects, 'besoins' => $besoins, 'villes' => $villes, 'unites' => $unites]);
    }

    public function createSinistre()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->app->redirect(BASE_URL . '/');
            return;
        }

        $id_besoin = isset($_POST['type_besoin']) ? (int)$_POST['type_besoin'] : 0;
        $libellee = isset($_POST['libellee']) ? trim((string)$_POST['libellee']) : '';
        $id_ville = isset($_POST['ville']) ? (int)$_POST['ville'] : 0;
        $quantite = isset($_POST['quantite']) ? (int)$_POST['quantite'] : 0;
        $id_unite = isset($_POST['unite']) ? (int)$_POST['unite'] : 0;
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
        if ($id_besoin <= 0 || $id_ville <= 0 || $id_unite <= 0 || $libellee === '') {
            $this->app->redirect(BASE_URL . '/?created=0');
            return;
        }

        try {
            $this->sinistreModel->insert($id_besoin, $libellee, $id_ville, $quantite, $id_unite, $date);
            $this->app->redirect(BASE_URL . '/?created=1');
        } catch (\Throwable $e) {
            $this->app->redirect(BASE_URL . '/?created=0');
        }
    }
}
