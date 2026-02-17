<?php
// Vue de récapitulation des besoins
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulation - BNGRC</title>
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/BNGRC.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/theme.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/layout.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dashboard.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/buttons.css">
    <style>
        .recap-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .recap-card {
            padding: 1.5rem;
            border: 1px solid #e2e6ea;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05);
        }

        .recap-label {
            font-size: 0.95rem;
            color: #6c757d;
            margin-bottom: 0.35rem;
        }

        .recap-value {
            font-size: 2rem;
            font-weight: 600;
            color: #0d6efd;
        }

        .recap-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem;
            margin-top: 1rem;
        }

        .last-updated {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .table-section {
            margin-top: 2rem;
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e2e6ea;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }

        .table-section-header {
            padding: 1rem 1.5rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #e2e6ea;
            font-weight: 600;
            font-size: 1.1rem;
            color: #333;
        }

        .table-section-body {
            padding: 0;
            max-height: 400px;
            overflow-y: auto;
        }

        .table-section table {
            margin-bottom: 0;
            width: 100%;
        }

        .badge-satisfait {
            background-color: #28a745;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }

        .badge-insatisfait {
            background-color: #ffc107;
            color: black;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }
    </style>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/tables.css">
</head>

<body>
    <div class="container-main">
        <?php include __DIR__ . '/templates/sidebar.php'; ?>
        <div class="main-content">
            <div class="page-wrapper">
                <div class="recap-page">
                    <div class="page-header" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
                        <div>
                            <h1 class="page-title">🧾 Récapitulation</h1>
                            <div style="width:100%; height:1px; background:linear-gradient(90deg, #0d6efd, #6cb2eb); margin-top:0.5rem;"></div>
                        </div>
                        <div class="recap-actions">
                            <button id="refresh-recap" class="btn btn-outline-primary">Actualiser</button>
                            <span class="last-updated" id="recap-updated">Dernière mise à jour : <?php echo htmlspecialchars($lastUpdated ?? date('Y-m-d H:i:s')); ?></span>
                        </div>
                    </div>

                    <div class="recap-grid" id="recap-grid"
                        data-total-besoins="<?php echo htmlspecialchars((string)($totalBesoins ?? 0), ENT_QUOTES); ?>"
                        data-total-satisfaits="<?php echo htmlspecialchars((string)($totalSatisfaits ?? 0), ENT_QUOTES); ?>"
                        data-total-restants="<?php echo htmlspecialchars((string)($totalRestants ?? 0), ENT_QUOTES); ?>">
                        <div class="recap-card" id="card-total-besoins">
                            <p class="recap-label">Prix total de besoins</p>
                            <div class="recap-value" data-field="total-besoins">0 Ar</div>
                        </div>
                        <div class="recap-card" id="card-total-satisfaits">
                            <p class="recap-label">Prix total de besoins satisfaits</p>
                            <div class="recap-value" data-field="total-satisfaits">0 Ar</div>
                        </div>
                        <div class="recap-card" id="card-total-restants">
                            <p class="recap-label">Montant des besoins restants</p>
                            <div class="recap-value" data-field="total-restants">0 Ar</div>
                        </div>
                    </div>

                    <!-- Tableau des Besoins -->
                    <div class="table-section">
                        <div class="table-section-header">
                            📋 Liste des Besoins (<?php echo count($besoins ?? []); ?>)
                        </div>
                        <div class="table-section-body">
                            <?php if (!empty($besoins) && is_array($besoins)): ?>
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>📍 Ville</th>
                                            <th>📋 Type</th>
                                            <th>📝 Libellé</th>
                                            <th style="text-align: center;">📊 Quantité</th>
                                            <th>📏 Unité</th>
                                            <th style="text-align: right;">💰 Prix Total</th>
                                            <th>� Ordre</th>
                                            <th>�📅 Date</th>
                                            <th style="text-align: center;">État</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($besoins as $b): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($b['ville'] ?? 'N/A'); ?></strong></td>
                                                <td>
                                                    <span class="badge badge-success">
                                                        <?php echo htmlspecialchars($b['besoin'] ?? 'N/A'); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($b['libellee'] ?? ''); ?></td>
                                                <td style="text-align: center;">
                                                    <strong><?php echo htmlspecialchars((string)($b['quantite_initiale'] ?? '')); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($b['unite'] ?? ''); ?></td>
                                                <td style="text-align: right; font-weight: 600; color: #2e7d32;">
                                                    <?php 
                                                        $prixTotal = (float)($b['quantite_initiale'] ?? 0) * (float)($b['prix_unitaire'] ?? 0);
                                                        echo $prixTotal > 0 ? number_format($prixTotal, 2, ',', ' ') . ' Ar' : '—';
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars((string)($b['ordre'] ?? '0')); ?></td>
                                                <td><?php echo htmlspecialchars($b['date'] ?? ''); ?></td>
                                                <td style="text-align: center;">
                                                    <span class="<?php echo ($b['etat'] ?? '') === 'satisfait' ? 'badge-satisfait' : 'badge-insatisfait'; ?>">
                                                        <?php echo htmlspecialchars($b['etat'] ?? 'insatisfait'); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div style="padding: 2rem; text-align: center; color: #6c757d;">
                                    Aucun besoin enregistré
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tableau des Dons -->
                    <div class="table-section">
                        <div class="table-section-header">
                            🎁 Liste des Dons (<?php echo count($dons ?? []); ?>)
                        </div>
                        <div class="table-section-body">
                            <?php if (!empty($dons) && is_array($dons)): ?>
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>📍 Ville</th>
                                            <th>📋 Type</th>
                                            <th>📝 Libellé</th>
                                            <th style="text-align: center;">📊 Quantité</th>
                                            <th>📏 Unité</th>
                                            <th style="text-align: right;">💰 Prix Total</th>
                                            <th>📅 Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dons as $d): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($d['ville'] ?? 'N/A'); ?></strong></td>
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
                                                <td style="text-align: right; font-weight: 600; color: #2e7d32;">
                                                    <?php 
                                                        $prixTotal = (float)($d['quantite'] ?? 0) * (float)($d['prix_unitaire'] ?? 0);
                                                        echo $prixTotal > 0 ? number_format($prixTotal, 2, ',', ' ') . ' Ar' : '—';
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($d['date'] ?? ''); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div style="padding: 2rem; text-align: center; color: #6c757d;">
                                    Aucun don enregistré
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php include __DIR__ . '/templates/footer.php'; ?>
        </div>
    </div>

    <script>
        (function() {
            function formatAr(value) {
                return Number(value || 0).toLocaleString('fr-FR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' Ar';
            }

            function updateCards(values) {
                document.querySelector('[data-field="total-besoins"]').textContent = formatAr(values.totalBesoins);
                document.querySelector('[data-field="total-satisfaits"]').textContent = formatAr(values.totalSatisfaits);
                document.querySelector('[data-field="total-restants"]').textContent = formatAr(values.totalRestants);
            }

            var grid = document.getElementById('recap-grid');
            updateCards({
                totalBesoins: parseFloat(grid.getAttribute('data-total-besoins')) || 0,
                totalSatisfaits: parseFloat(grid.getAttribute('data-total-satisfaits')) || 0,
                totalRestants: parseFloat(grid.getAttribute('data-total-restants')) || 0
            });

            var refreshBtn = document.getElementById('refresh-recap');
            var updatedLabel = document.getElementById('recap-updated');
            var refreshUrl = '<?php echo BASE_URL; ?>/recapitulation/stats';

            refreshBtn.addEventListener('click', function() {
                refreshBtn.disabled = true;
                refreshBtn.textContent = 'Actualisation...';
                fetch(refreshUrl, {
                        credentials: 'same-origin'
                    })
                    .then(function(resp) {
                        return resp.json();
                    })
                    .then(function(json) {
                        if (!json || json.success === false) {
                            alert(json && json.message ? json.message : 'Impossible de récupérer les données');
                            return;
                        }
                        updateCards({
                            totalBesoins: json.totalBesoins || 0,
                            totalSatisfaits: json.totalSatisfaits || 0,
                            totalRestants: json.totalRestants || 0
                        });
                        if (updatedLabel && json.lastUpdated) {
                            updatedLabel.textContent = 'Dernière mise à jour : ' + json.lastUpdated;
                        }
                    })
                    .catch(function(err) {
                        console.error(err);
                        alert('Erreur lors de l\'actualisation');
                    })
                    .finally(function() {
                        refreshBtn.disabled = false;
                        refreshBtn.textContent = 'Actualiser';
                    });
            });
        })();
    </script>
</body>

</html>