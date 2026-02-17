<?php

namespace app\controllers;

use flight\Engine;
use app\models\DonModel;
use app\models\SinistreModel;
use app\models\VilleModel;

class SimulationController
{
    protected Engine $app;
    protected DonModel $donModel;
    protected SinistreModel $sinistreModel;
    protected VilleModel $villeModel;

    public function __construct($app)
    {
        $this->app = $app;
        $this->donModel = new DonModel($this->app->db());
        $this->sinistreModel = new SinistreModel($this->app->db());
        $this->villeModel = new VilleModel($this->app->db());
    }

    /**
     * Affiche la page de simulation
     */
    public function showSimulation(): void
    {
        $dons = $this->donModel->getForDispatch();
        $sinistres = $this->sinistreModel->get();
        $villes = $this->villeModel->get();

        // Filtrer les sinistres dont le besoin n'est pas 'Argent' et quantite > 0
        $sinistresFiltered = array_filter($sinistres, function ($s) {
            $besoin = isset($s['besoin']) ? trim(mb_strtolower($s['besoin'])) : '';
            $qty = (int)($s['quantite'] ?? 0);
            return $besoin !== 'argent' && $qty > 0;
        });

        // Filtrer les dons dont le besoin n'est pas 'Argent' et quantite > 0
        $donsFiltered = array_filter($dons, function ($d) {
            $besoin = isset($d['besoin']) ? trim(mb_strtolower($d['besoin'])) : '';
            $qty = (int)($d['quantite'] ?? 0);
            return $besoin !== 'argent' && $qty > 0;
        });

        $this->app->render('simulation.php', [
            'dons' => array_values($donsFiltered),
            'sinistres' => array_values($sinistresFiltered),
            'villes' => $villes,
            'simulationResults' => null,
            'simulationStatus' => null,
        ]);
    }

    /**
     * Simule le dispatch sans sauvegarder (prévisualisation)
     */
    public function simulate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->app->redirect(BASE_URL . '/simulation');
            return;
        }

        $results = $this->computeDispatch(false);
        
        // Afficher directement sans redirect
        $this->renderWithResults($results['data'], $results['status']);
    }

    /**
     * Valide et sauvegarde le dispatch réel
     */
    public function validate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->app->redirect(BASE_URL . '/simulation');
            return;
        }

        $results = $this->computeDispatch(true);

        // Afficher directement sans redirect
        $this->renderWithResults($results['data'], $results['status']);
    }

    /**
     * Affiche la page avec les résultats (évite les problèmes de session/redirect)
     */
    protected function renderWithResults(array $data, string $status): void
    {
        $dons = $this->donModel->getForDispatch();
        $sinistres = $this->sinistreModel->get();
        $villes = $this->villeModel->get();

        // Filtrer : exclure 'Argent' et quantite <= 0
        $sinistresFiltered = array_filter($sinistres, function ($s) {
            $besoin = isset($s['besoin']) ? trim(mb_strtolower($s['besoin'])) : '';
            $qty = (int)($s['quantite'] ?? 0);
            return $besoin !== 'argent' && $qty > 0;
        });

        $donsFiltered = array_filter($dons, function ($d) {
            $besoin = isset($d['besoin']) ? trim(mb_strtolower($d['besoin'])) : '';
            $qty = (int)($d['quantite'] ?? 0);
            return $besoin !== 'argent' && $qty > 0;
        });

        $this->app->render('simulation.php', [
            'dons' => array_values($donsFiltered),
            'sinistres' => array_values($sinistresFiltered),
            'villes' => $villes,
            'simulationResults' => $data,
            'simulationStatus' => $status,
        ]);
    }

    /**
     * Calcule le dispatch des dons vers les besoins
     * @param bool $save Si true, sauvegarde les modifications en BDD
     * @return array ['status' => string, 'data' => array]
     */
    protected function computeDispatch(bool $save): array
    {
        $results = [];
        $status = null;
        $db = $this->app->db();

        try {
            // Récupérer les dons et sinistres
            $dons = $this->donModel->getForDispatch();
            $sinistres = $this->sinistreModel->get();

            // Filtrer : exclure 'Argent' et quantite <= 0
            $dons = array_filter($dons, function ($d) {
                $besoin = isset($d['besoin']) ? trim(mb_strtolower($d['besoin'])) : '';
                return $besoin !== 'argent' && (int)($d['quantite'] ?? 0) > 0;
            });

            $sinistres = array_filter($sinistres, function ($s) {
                $besoin = isset($s['besoin']) ? trim(mb_strtolower($s['besoin'])) : '';
                return $besoin !== 'argent' && (int)($s['quantite'] ?? 0) > 0;
            });

            // Récupérer le critère de priorité (quantite ou date)
            $priority = isset($_POST['priority']) ? $_POST['priority'] : 'quantite';

            // Trier les sinistres selon le critère de priorité
            usort($sinistres, function ($a, $b) use ($priority) {
                if ($priority === 'quantite') {
                    // Priorité aux besoins avec la plus petite quantité
                    $qtyA = (int)($a['quantite'] ?? 0);
                    $qtyB = (int)($b['quantite'] ?? 0);
                    if ($qtyA === $qtyB) {
                        return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
                    }
                    return $qtyA <=> $qtyB;
                } else {
                    // Priorité aux besoins les plus anciens (par date)
                    $dateA = isset($a['date']) ? strtotime($a['date']) : 0;
                    $dateB = isset($b['date']) ? strtotime($b['date']) : 0;
                    if ($dateA === $dateB) {
                        return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
                    }
                    return $dateA <=> $dateB;
                }
            });

            if (empty($dons) || empty($sinistres)) {
                return ['status' => 'no-data', 'data' => []];
            }

            if ($save) {
                $db->beginTransaction();
            }

            $etatSatisfaitId = $this->getEtatIdByName('satisfait') ?? 2;

            // Copier pour manipulation sans affecter les originaux
            $donsLocal = [];
            foreach ($dons as $d) {
                $donsLocal[$d['id']] = $d;
                $donsLocal[$d['id']]['quantite'] = (int)$d['quantite'];
            }

            $sinistresLocal = [];
            foreach ($sinistres as $s) {
                $sinistresLocal[$s['id']] = $s;
                $sinistresLocal[$s['id']]['quantite'] = (int)$s['quantite'];
            }

            // Algorithme de dispatch
            foreach ($sinistresLocal as $sinId => &$sinistre) {
                $sinQty = (int)$sinistre['quantite'];
                if ($sinQty <= 0) {
                    continue;
                }

                foreach ($donsLocal as $donId => &$don) {
                    $donQty = (int)$don['quantite'];
                    if ($donQty <= 0) {
                        continue;
                    }

                    // Règle: même libellé
                    if (!$this->labelsMatch($sinistre['libellee'] ?? '', $don['libellee'] ?? '')) {
                        continue;
                    }

                    // Calculer quantité à transférer
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

                    // Sauvegarder si demandé
                    if ($save) {
                        $this->donModel->updateQuantite((int)$don['id'], max($donQty, 0));
                        $newEtatId = $sinQty <= 0 ? $etatSatisfaitId : (int)($sinistre['id_etat'] ?? 1);
                        $this->sinistreModel->updateQuantiteEtat((int)$sinistre['id'], max($sinQty, 0), $newEtatId);
                    }

                    $results[] = [
                        'don' => [
                            'id' => $don['id'],
                            'ville' => $don['ville'] ?? '',
                            'besoin' => $don['besoin'] ?? '',
                            'libellee' => $don['libellee'] ?? '',
                            'quantite_avant' => $preDonQty,
                            'quantite_apres' => $donQty,
                            'unite' => $don['unite'] ?? '',
                        ],
                        'sinistre' => [
                            'id' => $sinistre['id'],
                            'ville' => $sinistre['ville'] ?? '',
                            'besoin' => $sinistre['besoin'] ?? '',
                            'libellee' => $sinistre['libellee'] ?? '',
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

            if ($save) {
                $db->commit();
            }

            $status = !empty($results) ? ($save ? 'validated' : 'simulated') : 'no-match';

        } catch (\Throwable $e) {
            if ($save && $db->inTransaction()) {
                $db->rollBack();
            }
            return ['status' => 'error', 'data' => ['error' => $e->getMessage()]];
        }

        return ['status' => $status, 'data' => $results];
    }

    /**
     * Compare deux libellés (insensible à la casse et aux espaces)
     */
    protected function labelsMatch(string $a, string $b): bool
    {
        return mb_strtolower(trim($a)) === mb_strtolower(trim($b));
    }

    /**
     * Récupère l'ID d'un état par son nom
     */
    protected function getEtatIdByName(string $name): ?int
    {
        $stmt = $this->app->db()->prepare('SELECT id FROM BNGRC_etat WHERE LOWER(nom) = LOWER(?) LIMIT 1');
        $stmt->execute([$name]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row !== false ? (int)$row['id'] : null;
    }
}
