<?php

namespace app\controllers;

use flight\Engine;
use app\models\DonModel;
use app\models\SinistreModel;
use app\models\AchatModel;

class AchatController
{
    protected Engine $app;
    protected DonModel $donModel;
    protected SinistreModel $sinistreModel;
    protected AchatModel $achatModel;
    public function __construct($app)
    {
        $this->app = $app;
        $this->donModel = new DonModel($this->app->db());
        $this->sinistreModel = new SinistreModel($this->app->db());
        $this->achatModel = new AchatModel($this->app->db());
    }

    public function showAchat(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $total = $this->donModel->getTotalArgent();

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

        $this->app->render('achat.php', [
            'totalArgent' => $total,
            'sinistresNonArgent' => $sinistres,
            'purchasedObjetIds' => $purchasedObjetIds,
            'achatList' => $achatList,
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
            $amountToSpend = (int)round($qtyNeeded * $prix);

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
            $this->sinistreModel->updateQuantiteEtat($sinistreId, 0, $etatSatisfaitId);

            $db->commit();

            $newTotal = $this->donModel->getTotalArgent();

            echo json_encode([
                'success' => true,
                'message' => 'Achat réalisé',
                'total' => $newTotal,
                'spent' => $amountToSpend,
                'achat_date' => $achatDate,
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
