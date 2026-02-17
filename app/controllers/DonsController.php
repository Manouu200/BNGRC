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
            $newId = $this->donModel->insertByObjet($id_ville, $id_objet, $quantite, $date);
            
            // Stocker l'ID en session pour permettre la réinitialisation
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            if (!isset($_SESSION['created_dons'])) {
                $_SESSION['created_dons'] = [];
            }
            $_SESSION['created_dons'][] = $newId;
            
            $this->app->redirect(BASE_URL . '/dons?created=1');
        } catch (\Throwable $e) {
            $this->app->redirect(BASE_URL . '/dons?created=0');
        }
    }

    /**
     * Réinitialise (supprime) les dons créés pendant cette session
     */
    public function resetSession()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $deletedCount = 0;
        if (isset($_SESSION['created_dons']) && is_array($_SESSION['created_dons'])) {
            foreach ($_SESSION['created_dons'] as $id) {
                if ($this->donModel->delete((int)$id)) {
                    $deletedCount++;
                }
            }
            $_SESSION['created_dons'] = [];
        }

        $this->app->redirect(BASE_URL . '/dons?reset=' . $deletedCount);
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

            // Filtrer : exclure 'Argent' et quantite <= 0 (comme simulation)
            $dons = array_filter($dons, function ($d) {
                $besoin = isset($d['besoin']) ? trim(mb_strtolower($d['besoin'])) : '';
                return $besoin !== 'argent' && (int)($d['quantite'] ?? 0) > 0;
            });
            $dons = array_values($dons);

            $sinistres = array_filter($sinistres, function ($s) {
                $besoin = isset($s['besoin']) ? trim(mb_strtolower($s['besoin'])) : '';
                return $besoin !== 'argent' && (int)($s['quantite'] ?? 0) > 0;
            });
            $sinistres = array_values($sinistres);

            // Récupérer le mode de dispatch et l'ordre
            $dispatchMode = isset($_POST['dispatch_mode']) ? $_POST['dispatch_mode'] : 'date';
            $dispatchOrder = isset($_POST['dispatch_order']) ? $_POST['dispatch_order'] : 'asc';

            // Trier les sinistres selon le mode et l'ordre choisis
            if (!empty($sinistres)) {
                usort($sinistres, function ($a, $b) use ($dispatchMode, $dispatchOrder) {
                    if ($dispatchMode === 'quantite') {
                        $qtyA = (int)($a['quantite'] ?? 0);
                        $qtyB = (int)($b['quantite'] ?? 0);
                        if ($qtyA === $qtyB) {
                            return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
                        }
                        // asc = plus petite quantité d'abord, desc = plus grande d'abord
                        return $dispatchOrder === 'asc' ? ($qtyA <=> $qtyB) : ($qtyB <=> $qtyA);
                    } else {
                        // Mode date (par défaut) ou proportionnel (utilise aussi le tri par date)
                        $dateA = isset($a['date']) ? strtotime($a['date']) : 0;
                        $dateB = isset($b['date']) ? strtotime($b['date']) : 0;
                        if ($dateA === $dateB) {
                            return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
                        }
                        // asc = plus ancien d'abord, desc = plus récent d'abord
                        return $dispatchOrder === 'asc' ? ($dateA <=> $dateB) : ($dateB <=> $dateA);
                    }
                });
            }

            if (empty($dons) || empty($sinistres)) {
                $status = 'no-data';
            } else {
                $db->beginTransaction();
                $etatSatisfaitId = $this->getEtatIdByName('satisfait') ?? 2;

                // Créer des copies locales indexées par id pour faciliter les modifications
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

                // Mode proportionnel : répartir les dons proportionnellement entre tous les besoins correspondants
                if ($dispatchMode === 'proportionnel') {
                    // Grouper les dons par libellé
                    $donsByLibelle = [];
                    foreach ($donsLocal as $donId => $don) {
                        $libelleKey = trim($this->toLower($don['libellee'] ?? ''));
                        if ($libelleKey === '') continue;
                        if (!isset($donsByLibelle[$libelleKey])) {
                            $donsByLibelle[$libelleKey] = [];
                        }
                        $donsByLibelle[$libelleKey][$donId] = $don;
                    }

                    // Grouper les besoins par libellé
                    $besoinsByLibelle = [];
                    foreach ($sinistresLocal as $sinId => $sinistre) {
                        $libelleKey = trim($this->toLower($sinistre['libellee'] ?? ''));
                        if ($libelleKey === '') continue;
                        if (!isset($besoinsByLibelle[$libelleKey])) {
                            $besoinsByLibelle[$libelleKey] = [];
                        }
                        $besoinsByLibelle[$libelleKey][$sinId] = $sinistre;
                    }

                    // Pour chaque libellé commun, répartir proportionnellement
                    foreach ($donsByLibelle as $libelleKey => $donsGroupe) {
                        if (!isset($besoinsByLibelle[$libelleKey])) continue;
                        
                        $besoinsGroupe = $besoinsByLibelle[$libelleKey];
                        $totalBesoins = count($besoinsGroupe);
                        $totalDonsGroupe = count($donsGroupe);
                        
                        if ($totalBesoins === 0 || $totalDonsGroupe === 0) continue;

                        // Calculer la quantité totale disponible dans les dons pour ce libellé
                        $totalQtyDons = 0;
                        foreach ($donsGroupe as $don) {
                            $totalQtyDons += (int)$don['quantite'];
                        }

                        // Calculer la quantité totale demandée par les besoins pour ce libellé
                        $totalQtyBesoins = 0;
                        foreach ($besoinsGroupe as $besoin) {
                            $totalQtyBesoins += (int)$besoin['quantite'];
                        }

                        if ($totalQtyDons === 0 || $totalQtyBesoins === 0) continue;

                        // Formule: (nombre de besoins / nombre total de besoins) * nombre total de dons disponibles
                        // Chaque besoin reçoit une part proportionnelle de la quantité totale des dons
                        $besoinsSelectionnes = isset($_POST['besoins_selectionnes']) ? (int)$_POST['besoins_selectionnes'] : $totalBesoins;
                        if ($besoinsSelectionnes <= 0 || $besoinsSelectionnes > $totalBesoins) {
                            $besoinsSelectionnes = $totalBesoins;
                        }

                        // Calculer le nombre de dons à utiliser pour ce groupe
                        // Formule: (besoins sélectionnés / total besoins) * total dons disponibles
                        $donsAUtiliser = (int) floor(($besoinsSelectionnes / $totalBesoins) * $totalDonsGroupe);
                        if ($donsAUtiliser <= 0) $donsAUtiliser = 1;
                        if ($donsAUtiliser > $totalDonsGroupe) $donsAUtiliser = $totalDonsGroupe;

                        // Prendre les premiers dons à utiliser
                        $donsGroupeArray = array_values($donsGroupe);
                        $donsGroupeArray = array_slice($donsGroupeArray, 0, $donsAUtiliser);

                        // Recalculer la quantité totale des dons à utiliser
                        $qtyDonsADistribuer = 0;
                        foreach ($donsGroupeArray as $don) {
                            $qtyDonsADistribuer += (int)$don['quantite'];
                        }

                        // Limiter les besoins aux premiers sélectionnés
                        $besoinsGroupeArray = array_values($besoinsGroupe);
                        $besoinsGroupeArray = array_slice($besoinsGroupeArray, 0, $besoinsSelectionnes);

                        // Distribuer proportionnellement la quantité aux besoins sélectionnés
                        $qtyRestante = $qtyDonsADistribuer;
                        $totalQtyBesoinsSelectionnes = 0;
                        foreach ($besoinsGroupeArray as $besoin) {
                            $totalQtyBesoinsSelectionnes += (int)$besoin['quantite'];
                        }

                        foreach ($besoinsGroupeArray as $besoin) {
                            if ($qtyRestante <= 0) break;
                            
                            $besoinQty = (int)$besoin['quantite'];
                            $besoinId = $besoin['id'];
                            
                            // Part proportionnelle pour ce besoin
                            $partProportionnelle = ($totalQtyBesoinsSelectionnes > 0) 
                                ? (int) floor(($besoinQty / $totalQtyBesoinsSelectionnes) * $qtyDonsADistribuer)
                                : 0;
                            
                            // Ne pas dépasser la quantité demandée par le besoin ni la quantité restante
                            $qtyADonner = min($partProportionnelle, $besoinQty, $qtyRestante);
                            
                            if ($qtyADonner <= 0) continue;

                            // Identifier quel don utiliser (premier don disponible avec quantité)
                            foreach ($donsGroupeArray as &$donSource) {
                                $donId = $donSource['id'];
                                $donQtyDisponible = (int)($donsLocal[$donId]['quantite'] ?? 0);
                                if ($donQtyDisponible <= 0) continue;

                                $qtyTransfert = min($qtyADonner, $donQtyDisponible);
                                if ($qtyTransfert <= 0) continue;

                                $preDonQty = $donQtyDisponible;
                                $preSinQty = (int)($sinistresLocal[$besoinId]['quantite'] ?? $besoinQty);

                                // Mettre à jour les quantités locales
                                $donsLocal[$donId]['quantite'] -= $qtyTransfert;
                                $sinistresLocal[$besoinId]['quantite'] -= $qtyTransfert;

                                $newDonQty = $donsLocal[$donId]['quantite'];
                                $newSinQty = $sinistresLocal[$besoinId]['quantite'];

                                // Mettre à jour en base
                                $this->donModel->updateQuantite($donId, max($newDonQty, 0));
                                $newEtatId = $newSinQty <= 0 ? $etatSatisfaitId : (int)($sinistresLocal[$besoinId]['id_etat'] ?? 1);
                                $this->sinistreModel->updateQuantiteEtat($besoinId, max($newSinQty, 0), $newEtatId);

                                $results[] = [
                                    'don' => [
                                        'id' => $donId,
                                        'ville' => $donsLocal[$donId]['ville'] ?? '',
                                        'besoin' => $donsLocal[$donId]['besoin'] ?? '',
                                        'libellee' => $donsLocal[$donId]['libellee'] ?? '',
                                        'quantite_avant' => $preDonQty,
                                        'quantite_apres' => $newDonQty,
                                        'unite' => $donsLocal[$donId]['unite'] ?? '',
                                    ],
                                    'sinistre' => [
                                        'id' => $besoinId,
                                        'ville' => $sinistresLocal[$besoinId]['ville'] ?? '',
                                        'besoin' => $sinistresLocal[$besoinId]['besoin'] ?? '',
                                        'libellee' => $sinistresLocal[$besoinId]['libellee'] ?? '',
                                        'quantite_avant' => $preSinQty,
                                        'quantite_apres' => $newSinQty,
                                        'etat' => $newSinQty <= 0 ? 'satisfait' : ($sinistresLocal[$besoinId]['etat'] ?? 'insatisfait'),
                                    ],
                                    'dispatched' => $qtyTransfert,
                                ];

                                $qtyADonner -= $qtyTransfert;
                                $qtyRestante -= $qtyTransfert;

                                if ($qtyADonner <= 0) break;
                            }
                        }
                    }
                } else {
                    // Mode standard (date ou quantité) : dispatch séquentiel
                    foreach ($sinistresLocal as $sinId => &$sinistre) {
                        $sinQty = (int)$sinistre['quantite'];
                        if ($sinQty <= 0) {
                            continue;
                        }

                        foreach ($donsLocal as $donId => &$don) {
                            $donQty = (int)($don['quantite'] ?? 0);
                            if ($donQty <= 0) {
                                continue;
                            }

                            // Règle: même libellé uniquement (comme simulation)
                            if (!$this->labelsMatch($sinistre['libellee'] ?? '', $don['libellee'] ?? '')) {
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
            'unitesByBesoin' => $this->getUnitesByBesoin(),
            'dispatchResults' => [],
            'dispatchStatus' => null,
            'dispatchError' => null,
        ];

        $this->app->render('dons.php', array_merge($data, $extra));
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
