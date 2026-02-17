<?php
// Vue : Simulation - dispatch des dons vers les besoins
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulation - BNGRC</title>
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/BNGRC.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/theme.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/layout.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dashboard.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/buttons.css">
    <style>
        .card-body .table.table-sm {
            border-collapse: collapse;
            width: 100%;
        }
        .card-body .table.table-sm th,
        .card-body .table.table-sm td {
            border: 1px solid #d5dce5;
            padding: 0.45rem;
            vertical-align: middle;
        }
        .table-responsive {
            overflow: auto;
        }
        .simulation-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin: 1.5rem 0;
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .result-table th { background: #f8f9fa; }
        .qty-dispatched { color: #28a745; font-weight: bold; }
        .qty-remaining { color: #dc3545; }
        .etat-satisfait { color: #28a745; font-weight: bold; }
    </style>
</head>

<body>
    <div class="container-main">
        <?php include __DIR__ . '/templates/sidebar.php'; ?>
        <div class="main-content">
            <div class="page-wrapper">
                <div class="simulation-page">
                    <div class="page-header">
                        <h1 class="page-title">🔄 Simulation de Dispatch</h1>
                        <p class="page-subtitle">Prévisualiser et valider la distribution des dons vers les besoins</p>
                        <div style="width:100%; height:1px; background:linear-gradient(90deg, #0d6efd, #6cb2eb); margin-top:0.5rem;"></div>
                    </div>

                    <!-- Messages de statut -->
                    <?php if (isset($simulationStatus)): ?>
                        <?php if ($simulationStatus === 'simulated'): ?>
                            <div class="alert alert-info">
                                <strong>🔍 Résultat de la simulation (non sauvegardé)</strong>
                                <p style="margin:0.5rem 0 0;">Les modifications ci-dessous ne sont pas encore enregistrées. Cliquez sur <strong>Valider</strong> pour appliquer.</p>
                            </div>
                        <?php elseif ($simulationStatus === 'validated'): ?>
                            <div class="alert alert-success">
                                <strong>✅ Dispatch validé et sauvegardé</strong>
                                <p style="margin:0.5rem 0 0;">Les modifications ont été appliquées en base de données.</p>
                            </div>
                        <?php elseif ($simulationStatus === 'no-match'): ?>
                            <div class="alert alert-warning">
                                <strong>⚠️ Aucune correspondance</strong>
                                <p style="margin:0.5rem 0 0;">Aucun don ne correspond aux besoins (libellés différents).</p>
                            </div>
                        <?php elseif ($simulationStatus === 'no-data'): ?>
                            <div class="alert alert-warning">
                                <strong>⚠️ Données insuffisantes</strong>
                                <p style="margin:0.5rem 0 0;">Il n'y a pas de dons ou de besoins disponibles pour le dispatch.</p>
                            </div>
                        <?php elseif ($simulationStatus === 'error'): ?>
                            <div class="alert alert-danger">
                                <strong>❌ Erreur</strong>
                                <p style="margin:0.5rem 0 0;">Une erreur est survenue lors du traitement.</p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Boutons d'action -->
                    <div class="simulation-actions" style="flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <label style="font-weight: 500;">Priorité :</label>
                            <select id="prioritySelect" style="padding: 0.5rem 1rem; border-radius: 4px; border: 1px solid #ccc; font-size: 1rem;">
                                <option value="quantite">🔢 Plus petite quantité d'abord</option>
                                <option value="date">📅 Plus ancienne date d'abord</option>
                            </select>
                        </div>
                        <div style="display: flex; gap: 1rem;">
                        <form method="POST" action="<?php echo BASE_URL; ?>/simulation/simulate" style="display:inline;" id="simulateForm">
                            <input type="hidden" name="priority" id="simulatePriority" value="quantite">
                            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1.1rem;">
                                🔍 Simuler
                            </button>
                        </form>
                        <form method="POST" action="<?php echo BASE_URL; ?>/simulation/validate" style="display:inline;" id="validateForm">
                            <input type="hidden" name="priority" id="validatePriority" value="quantite">
                            <button type="submit" class="btn btn-success" style="padding: 0.75rem 2rem; font-size: 1.1rem; background: #28a745; border-color: #28a745;">
                                ✅ Valider
                            </button>
                        </form>
                        </div>
                    </div>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var prioritySelect = document.getElementById('prioritySelect');
                        var simulatePriority = document.getElementById('simulatePriority');
                        var validatePriority = document.getElementById('validatePriority');
                        
                        prioritySelect.addEventListener('change', function() {
                            simulatePriority.value = this.value;
                            validatePriority.value = this.value;
                        });
                    });
                    </script>

                    <!-- Résultats de simulation -->
                    <?php if (!empty($simulationResults) && is_array($simulationResults)): ?>
                        <div class="card" style="max-width:1200px; margin:1.5rem auto;">
                            <div class="card-body">
                                <h3 style="margin-top:0;">📊 Résultats du dispatch</h3>
                                <div class="table-responsive" style="margin-top:0.75rem;">
                                    <table class="table table-sm result-table">
                                        <thead>
                                            <tr>
                                                <th colspan="4" style="background:#e3f2fd; text-align:center;">Don</th>
                                                <th colspan="4" style="background:#fff3e0; text-align:center;">Besoin</th>
                                                <th style="background:#e8f5e9;">Transféré</th>
                                            </tr>
                                            <tr>
                                                <th>Ville</th>
                                                <th>Libellé</th>
                                                <th>Avant</th>
                                                <th>Après</th>
                                                <th>Ville</th>
                                                <th>Libellé</th>
                                                <th>Avant</th>
                                                <th>Après</th>
                                                <th>Quantité</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($simulationResults as $item): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($item['don']['ville'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($item['don']['libellee'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($item['don']['quantite_avant'] ?? '0'); ?></td>
                                                    <td class="qty-remaining"><?php echo htmlspecialchars($item['don']['quantite_apres'] ?? '0'); ?></td>
                                                    <td><?php echo htmlspecialchars($item['sinistre']['ville'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($item['sinistre']['libellee'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($item['sinistre']['quantite_avant'] ?? '0'); ?></td>
                                                    <td class="<?php echo ($item['sinistre']['quantite_apres'] ?? 0) <= 0 ? 'etat-satisfait' : 'qty-remaining'; ?>">
                                                        <?php echo htmlspecialchars($item['sinistre']['quantite_apres'] ?? '0'); ?>
                                                        <?php if (($item['sinistre']['quantite_apres'] ?? 0) <= 0): ?>
                                                            <small>(satisfait)</small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="qty-dispatched">+<?php echo htmlspecialchars($item['dispatched'] ?? '0'); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Aperçu des données actuelles -->
                    <div class="card" style="max-width:1400px; width:95%; margin:1.5rem auto;">
                        <div class="card-body" style="padding:1.75rem;">
                            <table style="width:100%; border:1px solid #d5dce5; border-collapse:separate; border-spacing:1.5rem;">
                                <tr>
                                    <td style="vertical-align:top; width:50%;">
                                        <h3 style="margin-top:0;">🤲 Dons disponibles</h3>
                                        <div class="table-responsive" style="margin-top:0.75rem;">
                                            <?php if (!empty($dons) && is_array($dons)): ?>
                                                <table class="table table-sm" style="border:1px solid #d5dce5; min-width:100%;">
                                                    <thead>
                                                        <tr>
                                                            <th>Ville</th>
                                                            <th>Libellé</th>
                                                            <th>Besoin</th>
                                                            <th>Quantité</th>
                                                            <th>Unité</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($dons as $don): ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($don['ville'] ?? ''); ?></td>
                                                                <td><?php echo htmlspecialchars($don['libellee'] ?? ''); ?></td>
                                                                <td><?php echo htmlspecialchars($don['besoin'] ?? ''); ?></td>
                                                                <td><strong><?php echo htmlspecialchars($don['quantite'] ?? '0'); ?></strong></td>
                                                                <td><?php echo htmlspecialchars($don['unite'] ?? ''); ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            <?php else: ?>
                                                <p style="margin:0; font-style:italic; color:var(--muted);">Aucun don disponible.</p>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td style="vertical-align:top; width:50%;">
                                        <h3 style="margin-top:0;">📋 Besoins en attente</h3>
                                        <div class="table-responsive" style="margin-top:0.75rem;">
                                            <?php if (!empty($sinistres) && is_array($sinistres)): ?>
                                                <table class="table table-sm" style="border:1px solid #d5dce5; min-width:100%;">
                                                    <thead>
                                                        <tr>
                                                            <th>Ville</th>
                                                            <th>Libellé</th>
                                                            <th>Besoin</th>
                                                            <th>Quantité</th>
                                                            <th>Unité</th>
                                                            <th>État</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($sinistres as $s): ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($s['ville'] ?? ''); ?></td>
                                                                <td><?php echo htmlspecialchars($s['libellee'] ?? ''); ?></td>
                                                                <td><?php echo htmlspecialchars($s['besoin'] ?? ''); ?></td>
                                                                <td><strong><?php echo htmlspecialchars($s['quantite'] ?? '0'); ?></strong></td>
                                                                <td><?php echo htmlspecialchars($s['unite'] ?? ''); ?></td>
                                                                <td><?php echo htmlspecialchars($s['etat'] ?? ''); ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            <?php else: ?>
                                                <p style="margin:0; font-style:italic; color:var(--muted);">Aucun besoin en attente.</p>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Règles de gestion -->
                    <div class="card" style="max-width:800px; margin:1.5rem auto;">
                        <div class="card-body">
                            <h3 style="margin-top:0;">📖 Règles de gestion</h3>
                            <ul style="margin:0.5rem 0; padding-left:1.5rem; line-height:1.8;">
                                <li><strong>Correspondance par libellé :</strong> Un don est dispatché vers un besoin ayant le même libellé d'objet.</li>
                                <li><strong>Priorité par quantité :</strong> Les besoins avec la plus petite quantité sont servis en premier (par défaut).</li>
                                <li><strong>Priorité par date :</strong> Optionnellement, les besoins les plus anciens peuvent être servis en premier.</li>
                                <li><strong>Mise à jour des états :</strong> Un besoin dont la quantité atteint 0 passe à l'état "satisfait".</li>
                                <li><strong>Exclusion Argent :</strong> Les dons/besoins de type "Argent" sont traités via la page Achat.</li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>

            <?php include __DIR__ . '/templates/footer.php'; ?>
        </div>
    </div>
</body>

</html>
