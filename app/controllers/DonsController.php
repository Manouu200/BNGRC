<?php

namespace app\controllers;

use flight\Engine;
use app\models\BesoinModel;
use app\models\VilleModel;
use app\models\UniteModel;
use app\models\DonModel;
use app\models\SinistreModel;
use app\models\ObjetModel;

class DonsController
{
    protected Engine $app;
    protected BesoinModel $besoinModel;
    protected VilleModel $villeModel;
    protected UniteModel $uniteModel;
    protected DonModel $donModel;
    protected SinistreModel $sinistreModel;
    protected ObjetModel $objetModel;

    public function __construct($app)
    {
        $this->app = $app;
        $this->besoinModel = new BesoinModel($this->app->db());
        $this->villeModel = new VilleModel($this->app->db());
        $this->uniteModel = new UniteModel($this->app->db());
        $this->donModel = new DonModel($this->app->db());
        $this->sinistreModel = new SinistreModel($this->app->db());
        $this->objetModel = new ObjetModel($this->app->db());
    }

    public function showForm()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $this->renderForm();
    }

    public function createDon()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->app->redirect(BASE_URL . '/dons');
            return;
        }

        $id_objet = isset($_POST['objet']) ? (int)$_POST['objet'] : 0;
        $id_ville = isset($_POST['ville']) ? (int)$_POST['ville'] : 0;
        $quantite = isset($_POST['quantite']) ? (int)$_POST['quantite'] : 0;
        $date = isset($_POST['date']) && $_POST['date'] !== '' ? $_POST['date'] : null;

        if ($id_objet <= 0 || $id_ville <= 0) {
            $this->app->redirect(BASE_URL . '/dons?created=0');
            return;
        }
        try {
            $this->donModel->insertByObjet($id_ville, $id_objet, $quantite, $date);
            $this->app->redirect(BASE_URL . '/dons?created=1');
        } catch (\Throwable $e) {
            $this->app->redirect(BASE_URL . '/dons?created=0');
        }
    }

    public function dispatchDons()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->app->redirect(BASE_URL . '/dons');
            return;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $results = [];
        $status = null;
        $errorMessage = null;
        $db = $this->app->db();

        try {
            $dons = $this->donModel->getForDispatch();
            $sinistres = $this->sinistreModel->get();

            if (!empty($sinistres)) {
                usort($sinistres, function ($a, $b) {
                    $dateA = isset($a['date']) ? strtotime($a['date']) : 0;
                    $dateB = isset($b['date']) ? strtotime($b['date']) : 0;
                    if ($dateA === $dateB) {
                        return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
                    }
                    return $dateA <=> $dateB;
                });
            }

            if (empty($dons) || empty($sinistres)) {
                $status = 'no-data';
            } else {
                $db->beginTransaction();
                $etatSatisfaitId = $this->getEtatIdByName('satisfait') ?? 2;

                foreach ($sinistres as &$sinistre) {
                    $sinQty = (int)($sinistre['quantite'] ?? 0);
                    if ($sinQty <= 0) {
                        continue;
                    }

                    foreach ($dons as &$don) {
                        $donQty = (int)($don['quantite'] ?? 0);
                        if ($donQty <= 0) {
                            continue;
                        }

                        if (
                            !$this->labelsMatch($sinistre['libellee'] ?? '', $don['libellee'] ?? '') ||
                            !$this->citiesMatch($sinistre['ville'] ?? '', $don['ville'] ?? '')
                        ) {
                            continue;
                        }

                        $dispatchQty = min($donQty, $sinQty);
                        if ($dispatchQty <= 0) {
                            continue;
                        }

                        $preDonQty = $donQty;
                        $preSinQty = $sinQty;

                        $donQty -= $dispatchQty;
                        $sinQty -= $dispatchQty;

                        $don['quantite'] = $donQty;
                        $sinistre['quantite'] = $sinQty;

                        $this->donModel->updateQuantite((int)$don['id'], max($donQty, 0));
                        $newEtatId = $sinQty <= 0 ? $etatSatisfaitId : (int)($sinistre['id_etat'] ?? 1);
                        $this->sinistreModel->updateQuantiteEtat((int)$sinistre['id'], max($sinQty, 0), $newEtatId);

                        $results[] = [
                            'don' => [
                                'id' => $don['id'],
                                'ville' => $don['ville'],
                                'besoin' => $don['besoin'],
                                'libellee' => $don['libellee'],
                                'quantite_avant' => $preDonQty,
                                'quantite_apres' => $donQty,
                                'unite' => $don['unite'],
                            ],
                            'sinistre' => [
                                'id' => $sinistre['id'],
                                'ville' => $sinistre['ville'],
                                'besoin' => $sinistre['besoin'],
                                'libellee' => $sinistre['libellee'],
                                'quantite_avant' => $preSinQty,
                                'quantite_apres' => $sinQty,
                                'etat' => $sinQty <= 0 ? 'satisfait' : ($sinistre['etat'] ?? 'insatisfait'),
                            ],
                            'dispatched' => $dispatchQty,
                        ];

                        if ($sinQty <= 0) {
                            break;
                        }
                    }
                }

                $db->commit();
                $status = !empty($results) ? 'success' : 'no-match';
            }
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $status = 'error';
            $errorMessage = $e->getMessage();
        }

        $this->renderDashboard([
            'dispatchResults' => $results,
            'dispatchStatus' => $status,
            'dispatchError' => $errorMessage,
        ]);
    }

    protected function renderDashboard(array $extra = []): void
    {
        $sinistres = $this->sinistreModel->get();
        $dons = [];
        try {
            $stmt = $this->app->db()->query('SELECT * FROM BNGRC_vue_dons');
            $dons = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            // Vue might not exist yet
        }

        $data = [
            'sinistres' => $sinistres,
            'dons' => $dons,
            'dispatchResults' => [],
            'dispatchStatus' => null,
            'dispatchError' => null,
        ];

        $this->app->render('dashboard.php', array_merge($data, $extra));
    }

    protected function renderForm(array $extra = []): void
    {
        $data = [
            'besoins' => $this->besoinModel->get(),
            'villes' => $this->villeModel->get(),
            'unites' => $this->uniteModel->get(),
            'objets' => $this->objetModel->getAll(),
            'dispatchResults' => [],
            'dispatchStatus' => null,
            'dispatchError' => null,
        ];

        $this->app->render('dons.php', array_merge($data, $extra));
    }

    protected function getEtatIdByName(string $name): ?int
    {
        $stmt = $this->app->db()->prepare('SELECT id FROM BNGRC_etat WHERE LOWER(nom) = LOWER(?) LIMIT 1');
        $stmt->execute([$name]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row !== false ? (int)$row['id'] : null;
    }

    protected function labelsMatch(string $a, string $b): bool
    {
        $na = trim($this->toLower($a));
        $nb = trim($this->toLower($b));
        if ($na === '' || $nb === '') {
            return false;
        }
        return $na === $nb;
    }

    protected function citiesMatch(string $a, string $b): bool
    {
        $na = trim($this->toLower($a));
        $nb = trim($this->toLower($b));
        if ($na === '' || $nb === '') {
            return false;
        }
        return $na === $nb;
    }

    protected function toLower(string $value): string
    {
        if (function_exists('mb_strtolower')) {
            return mb_strtolower($value, 'UTF-8');
        }
        return strtolower($value);
    }
}
