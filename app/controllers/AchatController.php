<?php

namespace app\controllers;

use flight\Engine;
use app\models\DonModel;
use app\models\SinistreModel;
use app\models\AchatModel;
use app\models\VilleModel;

class AchatController
{
    protected Engine $app;
    protected DonModel $donModel;
    protected SinistreModel $sinistreModel;
    protected AchatModel $achatModel;
    protected VilleModel $villeModel;
    public function __construct($app)
    {
        $this->app = $app;
        $this->donModel = new DonModel($this->app->db());
        $this->sinistreModel = new SinistreModel($this->app->db());
        $this->achatModel = new AchatModel($this->app->db());
        $this->villeModel = new VilleModel($this->app->db());
    }

    public function showAchat(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $total = $this->donModel->getTotalArgent();

        // Récupère la liste des villes pour le filtre
        $villes = [];
        try {
            $villes = $this->villeModel->get();
        } catch (\Throwable $e) {
            // ignore
        }

        // Récupère le filtre ville depuis GET
        $filtreVille = isset($_GET['ville']) ? (int)$_GET['ville'] : 0;

        // Récupère les sinistres et filtre ceux dont le besoin est différent de 'Argent'
        $sinistres = [];
        try {
            $all = $this->sinistreModel->get();
            foreach ($all as $s) {
                $besoin = isset($s['besoin']) ? trim(mb_strtolower($s['besoin'])) : '';
                if ($besoin !== 'argent') {
                    $sinistres[] = $s;
                }
            }
        } catch (\Throwable $e) {
            // ignore, on renverra tableau vide
        }

        // Récupère les achats existants
        $purchasedObjetIds = [];
        $achatList = [];
        try {
            $achatList = $this->achatModel->getAll();
            foreach ($achatList as $a) {
                if (isset($a['id_objet'])) {
                    $purchasedObjetIds[] = (int)$a['id_objet'];
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // Filtrer les sinistres pour n'afficher que ceux qui ne sont pas encore achetés
        if (!empty($purchasedObjetIds)) {
            $sinistres = array_values(array_filter($sinistres, function ($s) use ($purchasedObjetIds) {
                if (!isset($s['id_objet'])) {
                    return true;
                }
                return !in_array((int)$s['id_objet'], $purchasedObjetIds, true);
            }));
        }


        // Pourcentage de frais appliqué aux achats (ex: 10 pour 10%)
        $fraisPercent = 10;

        // Filtrer par ville si un filtre est sélectionné
        if ($filtreVille > 0) {
            $sinistres = array_values(array_filter($sinistres, function ($s) use ($filtreVille) {
                return isset($s['id_ville']) && (int)$s['id_ville'] === $filtreVille;
            }));
            $achatList = array_values(array_filter($achatList, function ($a) use ($filtreVille) {
                return isset($a['id_ville']) && (int)$a['id_ville'] === $filtreVille;
            }));
        }


        $this->app->render('achat.php', [
            'totalArgent' => $total,
            'sinistresNonArgent' => $sinistres,
            'purchasedObjetIds' => $purchasedObjetIds,
            'achatList' => $achatList,

            'fraisPercent' => $fraisPercent,

            'villes' => $villes,
            'filtreVille' => $filtreVille,
        ]);
    }

    /**
     * Point d'entrée pour effectuer un achat à partir d'un sinistre non-argent.
     * Expose : POST { sinistre_id }
     * Retourne JSON { success: bool, message: string, total?: float }
     */
    public function buy(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $sinistreId = isset($_POST['sinistre_id']) ? (int)$_POST['sinistre_id'] : 0;
        if ($sinistreId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'sinistre_id manquant']);
            return;
        }

        try {
            $db = $this->app->db();
            $sin = $this->sinistreModel->getById($sinistreId);
            if (empty($sin)) {
                throw new \RuntimeException('Sinistre introuvable');
            }

            $id_objet = isset($sin['id_objet']) ? (int)$sin['id_objet'] : 0;
            $qtyNeeded = isset($sin['quantite']) ? (int)$sin['quantite'] : 0;
            $prix = isset($sin['prix_unitaire']) ? (float)$sin['prix_unitaire'] : 0.0;

            if ($qtyNeeded <= 0) {
                throw new \RuntimeException('Quantité indisponible');
            }

            if ($prix <= 0.0) {
                throw new \RuntimeException('Prix unitaire manquant ou nul pour cet objet');
            }

            if ($this->achatModel->existsByObjet($id_objet)) {
                echo json_encode(['success' => false, 'message' => 'Achat déjà effectué pour cet objet']);
                return;
            }

            $totalArgent = (int)round($this->donModel->getTotalArgent());
            // Inclure les frais en pourcentage
            $fraisPercent = 10; // garder cohérent avec showAchat; remplacer par config si besoin
            $amountToSpend = (int)round($qtyNeeded * $prix * (1 + ($fraisPercent / 100)));

            if ($totalArgent <= 0) {
                throw new \RuntimeException('Pas de fonds disponibles');
            }

            if ($amountToSpend <= 0) {
                throw new \RuntimeException('Montant à dépenser invalide');
            }

            if ($totalArgent < $amountToSpend) {
                throw new \RuntimeException('Fonds insuffisants pour cet achat');
            }

            $argentDons = $this->donModel->getArgentDons();
            if (empty($argentDons)) {
                throw new \RuntimeException('Aucun don en argent disponible');
            }

            $remaining = $amountToSpend;
            $deductions = [];
            foreach ($argentDons as $don) {
                if ($remaining <= 0) {
                    break;
                }
                $donQty = (int)$don['quantite'];
                if ($donQty <= 0) {
                    continue;
                }

                $deduct = min($remaining, $donQty);
                $deductions[] = [
                    'id' => (int)$don['id'],
                    'newQty' => $donQty - $deduct,
                ];
                $remaining -= $deduct;
            }

            if ($remaining > 0) {
                throw new \RuntimeException('Fonds insuffisants pour couvrir le montant requis.');
            }

            $db->beginTransaction();

            foreach ($deductions as $item) {
                $this->donModel->updateQuantite($item['id'], max(0, $item['newQty']));
            }

            $achatDate = date('Y-m-d H:i:s');
            $this->achatModel->insert($id_objet, $achatDate);

            $etatSatisfaitId = $this->getEtatIdByName('satisfait') ?? 2;
            // Mettre à jour toutes les sinistres liées à cet objet (quantité = 0, état = satisfait)
            $this->sinistreModel->markAllByObjetAsSatisfied($id_objet, $etatSatisfaitId);

            $db->commit();

            $newTotal = $this->donModel->getTotalArgent();

            echo json_encode([
                'success' => true,
                'message' => 'Achat réalisé',
                'total' => $newTotal,
                'spent' => $amountToSpend,
                'achat_date' => $achatDate,
                'frais_percent' => $fraisPercent,
            ]);
            return;
        } catch (\Throwable $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            return;
        }
    }

    protected function getEtatIdByName(string $name): ?int
    {
        $stmt = $this->app->db()->prepare('SELECT id FROM BNGRC_etat WHERE LOWER(nom) = LOWER(?) LIMIT 1');
        $stmt->execute([$name]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row !== false ? (int)$row['id'] : null;
    }
}
