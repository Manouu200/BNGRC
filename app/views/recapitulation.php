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
    </style>
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