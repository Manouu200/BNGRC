<?php
// Vue : dashboard
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - BNGRC</title>
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/BNGRC.png">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/theme.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/layout.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/forms.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/buttons.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/tables.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dashboard.css">
</head>

<body>
    <div class="container-main">
        <?php include __DIR__ . '/templates/sidebar.php'; ?>

        <div class="main-content">
            <div class="page-wrapper">
                <div class="dashboard-page">
                    <!-- Page Header -->
                    <div class="dashboard-header">
                        <div>
                            <h1 class="dashboard-title">📊 Tableau de Bord</h1>
                            <p class="page-subtitle">Consultez tous les besoins enregistrés</p>
                        </div>
                        <div class="dashboard-actions">
                            <a href="<?php echo BASE_URL; ?>/" class="btn btn-primary">
                                <span>➕</span> Ajouter un Besoin
                            </a>
                            <form method="post" action="<?php echo BASE_URL; ?>/dons/dispatch" style="display: inline;">
                                <button type="submit" class="btn btn-outline-primary" title="Distribuer les dons vers les besoins correspondants">
                                    ⤴ Dispatcher les dons
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Dispatch Results Section -->
                    <?php if (!empty($dispatchStatus)): ?>
                        <?php if ($dispatchStatus === 'success' && !empty($dispatchResults)): ?>
                            <div class="alert alert-success" style="margin: 1.5rem 0; border-left: 4px solid #28a745;">
                                <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                                    <span style="font-size: 1.5rem; margin-right: 0.5rem;">✓</span>
                                    <strong style="font-size: 1.1rem;">Résultats du dispatch</strong>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm" style="margin-bottom: 0;">
                                        <thead style="background-color: #f0f8f5;">
                                            <tr>
                                                <th>Don (libellé)</th>
                                                <th>Besoin (libellé)</th>
                                                <th style="text-align: center;">Quantité transférée</th>
                                                <th style="text-align: center;">Reste don</th>
                                                <th style="text-align: center;">Reste besoin</th>
                                                <th style="text-align: center;">État besoin</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($dispatchResults as $item): ?>
                                                <tr style="border-bottom: 1px solid #e0e0e0;">
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($item['don']['libellee'] ?? ''); ?></strong>
                                                        <br><small style="color: #666;">📍 <?php echo htmlspecialchars($item['don']['ville'] ?? ''); ?></small>
                                                    </td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($item['sinistre']['libellee'] ?? ''); ?></strong>
                                                        <br><small style="color: #666;">📍 <?php echo htmlspecialchars($item['sinistre']['ville'] ?? ''); ?></small>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <strong style="color: #28a745; font-size: 1.1rem;">
                                                            <?php echo htmlspecialchars((string)($item['dispatched'] ?? '0')); ?> <?php echo htmlspecialchars($item['don']['unite'] ?? ''); ?>
                                                        </strong>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <span><?php echo htmlspecialchars((string)($item['don']['quantite_apres'] ?? '0')); ?> <?php echo htmlspecialchars($item['don']['unite'] ?? ''); ?></span>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <span><?php echo htmlspecialchars((string)($item['sinistre']['quantite_apres'] ?? '0')); ?> <?php echo htmlspecialchars($item['don']['unite'] ?? ''); ?></span>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <span class="badge" style="background-color: <?php echo ($item['sinistre']['etat'] === 'satisfait') ? '#28a745' : '#ffc107'; ?>; color: <?php echo ($item['sinistre']['etat'] === 'satisfait') ? 'white' : 'black'; ?>;">
                                                            <?php echo htmlspecialchars($item['sinistre']['etat'] ?? ''); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php elseif ($dispatchStatus === 'no-data'): ?>
                            <div class="alert alert-warning" style="margin: 1.5rem 0; border-left: 4px solid #ffc107;">
                                <strong>⚠️ Impossible de dispatcher :</strong> la liste des dons ou des besoins est vide.
                            </div>
                        <?php elseif ($dispatchStatus === 'no-match'): ?>
                            <div class="alert alert-info" style="margin: 1.5rem 0; border-left: 4px solid #17a2b8;">
                                ℹ️ Trop bien ! Tous les dons ont été dispatché.
                            </div>
                        <?php elseif ($dispatchStatus === 'error'): ?>
                            <div class="alert alert-danger" style="margin: 1.5rem 0; border-left: 4px solid #dc3545;">
                                <strong>❌ Erreur lors du dispatch :</strong>
                                <?php if (!empty($dispatchError)): ?>
                                    <br><small><?php echo htmlspecialchars($dispatchError); ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>


                    <!-- Donations Table Section -->
                    <div class="table-section">
                        <div class="table-header">
                            <div class="table-header-title">Listes des dons</div>
                            <div class="table-header-info">
                                <div class="table-count">
                                    <span>Total:</span>
                                    <span class="table-count-badge"><?php echo !empty($dons) ? count($dons) : '0'; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="table-container">
                            <?php if (!empty($dons) && is_array($dons)): ?>
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>📍 Ville</th>
                                            <th>📋 Type de Besoin</th>
                                            <th>📝 Libellé</th>
                                            <th style="text-align: center;">📊 Quantité</th>
                                            <th>📏 Unité</th>
                                            <th>📅 Date</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dons as $d): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($d['ville'] ?? 'N/A'); ?></strong>
                                                </td>
                                                <td>
                                                    <span class="badge badge-success">
                                                        <?php echo htmlspecialchars($d['besoin'] ?? 'N/A'); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($d['libellee'] ?? ''); ?></td>
                                                <td style="text-align: center;">
                                                    <strong><?php echo htmlspecialchars((string)($d['quantite'] ?? '')); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($d['unite'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($d['date'] ?? ''); ?></td>
                                                <td style="text-align: center;">
                                                    <div class="table-actions">
                                                        <button class="btn btn-sm btn-outline-primary" title="Voir">👁️</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="empty-state">
                                    <div class="empty-state-icon">📭</div>
                                    <div class="empty-state-title">Aucun don enregistré</div>
                                    <div class="empty-state-message">Commencez par <a href="<?php echo BASE_URL; ?>/dons">ajouter un don</a></div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($dons)): ?>
                            <div class="table-footer">
                                <div class="table-footer-info">
                                    Affichage de <strong><?php echo count($dons); ?></strong> don(s)
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Data Table Section -->
                    <div class="table-section">
                        <div class="table-header">
                            <div class="table-header-title">Liste des Besoins</div>
                            <div class="table-header-info">
                                <div class="table-count">
                                    <span>Total:</span>
                                    <span class="table-count-badge"><?php echo !empty($sinistres) ? count($sinistres) : '0'; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="table-container">
                            <?php if (!empty($sinistres) && is_array($sinistres)): ?>
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>📍 Ville</th>
                                            <th>📋 Type de Besoin</th>
                                            <th>📝 Libellé</th>
                                            <th style="text-align: center;">📊 Quantité</th>
                                            <th>📏 Unité</th>
                                            <th>📅 Date</th>
                                            <th>⚙️ Etat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($sinistres as $s): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($s['ville'] ?? 'N/A'); ?></strong>
                                                </td>
                                                <td>
                                                    <span class="badge badge-primary">
                                                        <?php echo htmlspecialchars($s['besoin'] ?? 'N/A'); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($s['libellee'] ?? ''); ?></td>
                                                <td style="text-align: center;">
                                                    <strong><?php echo htmlspecialchars((string)($s['quantite'] ?? '')); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($s['unite'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($s['date'] ?? ''); ?></td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        <?php echo htmlspecialchars($s['etat'] ?? ''); ?>
                                                    </span>
                                                </td>
                                                <td style="text-align: center;">
                                                    <div class="table-actions">
                                                        <button class="btn btn-sm btn-outline-primary" title="Voir les détails">👁️</button>
                                                        <button class="btn btn-sm btn-outline-secondary" title="Modifier">✏️</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="empty-state">
                                    <div class="empty-state-icon">📭</div>
                                    <div class="empty-state-title">Aucun besoin enregistré</div>
                                    <div class="empty-state-message">Commencez par <a href="<?php echo BASE_URL; ?>/">ajouter un nouveau besoin</a></div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($sinistres)): ?>
                            <div class="table-footer">
                                <div class="table-footer-info">
                                    Affichage de <strong><?php echo count($sinistres); ?></strong> besoin(s)
                                </div>
                                <div class="table-pagination">
                                    <button class="btn btn-sm btn-outline-secondary" disabled>← Précédent</button>
                                    <button class="btn btn-sm btn-outline-secondary" disabled>Suivant →</button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php include __DIR__ . '/templates/footer.php'; ?>
        </div>
    </div>
</body>

</html>