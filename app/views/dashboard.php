<?php
// Vue : dashboard
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - BNGRC</title>
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
                        </div>
                    </div>

                    <!-- Stats Section -->
                    <div class="dashboard-stats">
                        <div class="stat-item">
                            <div class="stat-item-header">
                                <div class="stat-item-title">Total Besoins</div>
                                <div class="stat-item-icon">📋</div>
                            </div>
                            <div class="stat-item-value"><?php echo !empty($sinistres) ? count($sinistres) : '0'; ?></div>
                            <div class="stat-item-change">↑ Actualisé</div>
                        </div>

                        <div class="stat-item">
                            <div class="stat-item-header">
                                <div class="stat-item-title">Villes Touchées</div>
                                <div class="stat-item-icon">🗺️</div>
                            </div>
                            <div class="stat-item-value"><?php
                                                            $villes_count = 0;
                                                            if (!empty($sinistres)) {
                                                                $villes_uniques = array_unique(array_column($sinistres, 'ville'));
                                                                $villes_count = count($villes_uniques);
                                                            }
                                                            echo $villes_count;
                                                            ?></div>
                            <div class="stat-item-change">Zones affectées</div>
                        </div>

                        <div class="stat-item">
                            <div class="stat-item-header">
                                <div class="stat-item-title">Types de Besoins</div>
                                <div class="stat-item-icon">🏷️</div>
                            </div>
                            <div class="stat-item-value"><?php
                                                            $types_count = 0;
                                                            if (!empty($sinistres)) {
                                                                $types_uniques = array_unique(array_column($sinistres, 'besoin'));
                                                                $types_count = count($types_uniques);
                                                            }
                                                            echo $types_count;
                                                            ?></div>
                            <div class="stat-item-change">Catégories</div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="filters-bar">
                        <div class="filters-row">
                            <div class="filter-group">
                                <label>🔍 Rechercher</label>
                                <input type="text" class="form-control" placeholder="Rechercher...">
                            </div>
                            <div class="filter-group">
                                <label>📍 Ville</label>
                                <select class="form-select">
                                    <option>Toutes les villes</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label>📋 Type</label>
                                <select class="form-select">
                                    <option>Tous les types</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <button type="button" class="btn btn-primary" style="margin-top: auto;">
                                    <span>🔄</span> Actualiser
                                </button>
                            </div>
                        </div>
                    </div>

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