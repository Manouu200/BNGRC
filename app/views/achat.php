<?php
// Vue : Achat - total des dons en argent
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achat - BNGRC</title>
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/BNGRC.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/theme.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/layout.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dashboard.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/buttons.css">
    <style>
        /* Bordures pour les tableaux de la page Achat */
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

        /* Assure que la table responsive garde le scroll si nécessaire */
        .table-responsive {
            overflow: auto;
        }
    </style>
</head>

<body>
    <div class="container-main">
        <?php include __DIR__ . '/templates/sidebar.php'; ?>
        <div class="main-content">
            <div class="page-wrapper">
                <div class="achat-page">
                    <div class="page-header">
                        <h1 class="page-title">🛒 Achat</h1>
                        <div style="width:100%; height:1px; background:linear-gradient(90deg, #0d6efd, #6cb2eb); margin-top:0.5rem;"></div>
                    </div>

                    <div class="card" style="max-width:1400px; width:95%; margin:1.5rem auto;">
                        <div class="card-body" style="padding:1.75rem;">
                            <table style="width:100%; border:1px solid #d5dce5; border-collapse:separate; border-spacing:1.5rem;">
                                <tr>
                                    <td colspan="2" style="text-align:center;">
                                        <div class="achat-total-card" data-total-argent="<?php echo htmlspecialchars((string)($totalArgent ?? 0), ENT_QUOTES); ?>">
                                            <h2 id="achat-total-value" style="margin:0; font-size:2.25rem;">
                                                <?php
                                                $total = isset($totalArgent) ? (float)$totalArgent : 0.0;
                                                echo number_format($total, 2, ',', ' ');
                                                ?>
                                                <span style="font-size:0.9rem; opacity:0.85;">Ar</span>
                                            </h2>
                                            <?php $fraisPercent = isset($fraisPercent) ? (float)$fraisPercent : 0; ?>
                                            <p style="margin-top:0.5rem; color:var(--muted);">Somme des dons
                                                &nbsp;·&nbsp; <strong>Frais d'achat :</strong> <?php echo number_format($fraisPercent, 2, ',', ' '); ?>%</p>
                                            </p>
                                            <input type="hidden" id="achat-frais-percent" value="<?php echo htmlspecialchars((string)$fraisPercent, ENT_QUOTES); ?>">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top; width:50%;">
                                        <h3 style="margin-top:0;">Saisie – besoins à acheter</h3>
                                        <div class="table-responsive" style="margin-top:0.75rem;">
                                            <?php if (!empty($sinistresNonArgent) && is_array($sinistresNonArgent)): ?>
                                                <table class="table table-sm" style="border:1px solid #d5dce5; min-width:100%;">
                                                    <thead>
                                                        <tr>
                                                            <th>Ville</th>
                                                            <th>Besoin</th>
                                                            <th>Objet</th>
                                                            <th>Quantité</th>
                                                            <th>Unité</th>
                                                            <th>Prix unitaire</th>
                                                            <th>Date</th>
                                                            <th>État</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($sinistresNonArgent as $s): ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($s['ville'] ?? ''); ?></td>
                                                                <td><?php echo htmlspecialchars($s['besoin'] ?? ''); ?></td>
                                                                <td><?php echo htmlspecialchars($s['libellee'] ?? ''); ?></td>
                                                                <td><?php echo htmlspecialchars((string)($s['quantite'] ?? '0')); ?></td>
                                                                <td><?php echo htmlspecialchars($s['unite'] ?? ''); ?></td>
                                                                <td>
                                                                    <?php
                                                                    if (isset($s['prix_unitaire']) && $s['prix_unitaire'] !== null && $s['prix_unitaire'] !== '') {
                                                                        echo number_format((float)$s['prix_unitaire'], 2, ',', ' ') . ' Ar';
                                                                    } else {
                                                                        echo '—';
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td><?php echo htmlspecialchars($s['date'] ?? ''); ?></td>
                                                                <td><?php echo htmlspecialchars($s['etat'] ?? ''); ?></td>
                                                                <td>
                                                                    <?php
                                                                    $isPurchased = false;
                                                                    if (!empty($purchasedObjetIds) && isset($s['id_objet'])) {
                                                                        $isPurchased = in_array((int)$s['id_objet'], $purchasedObjetIds, true);
                                                                    }
                                                                    ?>
                                                                    <button
                                                                        type="button"
                                                                        class="btn btn-sm btn-primary achat-btn"
                                                                        data-id="<?php echo htmlspecialchars($s['id'] ?? ''); ?>"
                                                                        data-objet="<?php echo htmlspecialchars($s['id_objet'] ?? ''); ?>"
                                                                        data-prix="<?php echo htmlspecialchars($s['prix_unitaire'] ?? ''); ?>"
                                                                        data-quantite="<?php echo htmlspecialchars($s['quantite'] ?? '0'); ?>"
                                                                        <?php echo $isPurchased ? 'disabled' : ''; ?>><?php echo $isPurchased ? 'Acheté' : 'Acheter'; ?></button>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            <?php else: ?>
                                                <p style="margin:0; font-style:italic; color:var(--muted);">Aucun besoin en attente.</p>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td style="vertical-align:top; width:50%;">
                                        <h3 style="margin-top:0;">Liste des achats (déjà payés)</h3>
                                        <div class="table-responsive" style="margin-top:0.75rem;">
                                            <?php if (!empty($achatList) && is_array($achatList)): ?>
                                                <table class="table table-sm" style="border:1px solid #d5dce5; min-width:100%;">
                                                    <thead>
                                                        <tr>
                                                            <th>Ville</th>
                                                            <th>Date</th>
                                                            <th>Objet</th>
                                                            <th>Besoin</th>
                                                            <th>Unité</th>
                                                            <th>Prix unitaire</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($achatList as $achat): ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($achat['ville'] ?? ''); ?></td>
                                                                <td><?php echo htmlspecialchars($achat['date'] ?? ''); ?></td>
                                                                <td><?php echo htmlspecialchars($achat['objet'] ?? ''); ?></td>
                                                                <td><?php echo htmlspecialchars($achat['besoin'] ?? ''); ?></td>
                                                                <td><?php echo htmlspecialchars($achat['unite'] ?? ''); ?></td>
                                                                <td>
                                                                    <?php
                                                                    if (isset($achat['prix_unitaire']) && $achat['prix_unitaire'] !== null && $achat['prix_unitaire'] !== '') {
                                                                        echo number_format((float)$achat['prix_unitaire'], 2, ',', ' ') . ' Ar';
                                                                    } else {
                                                                        echo '—';
                                                                    }
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            <?php else: ?>
                                                <p style="margin:0; font-style:italic; color:var(--muted);">Aucun achat enregistré.</p>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <?php include __DIR__ . '/templates/footer.php'; ?>
        </div>
    </div>

    <script>
        (function() {
            function findAncestor(el, tag) {
                tag = tag.toUpperCase();
                while (el && el.parentNode) {
                    el = el.parentNode;
                    if (el.tagName === tag) return el;
                }
                return null;
            }

            function formatAr(v) {
                return Number(v || 0).toLocaleString('fr-FR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' <span style="font-size:0.9rem; opacity:0.85;">Ar</span>';
            }

            var totalCard = document.querySelector('.achat-total-card');
            var totalValueElem = document.getElementById('achat-total-value');
            var totalDisponible = totalCard ? parseFloat(totalCard.getAttribute('data-total-argent')) || 0 : 0;

            var fraisPercentElem = document.getElementById('achat-frais-percent');
            var fraisPercent = fraisPercentElem ? parseFloat(fraisPercentElem.value) || 0 : 0;

            var buttons = document.querySelectorAll('.achat-btn');
            buttons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var sinistreId = this.getAttribute('data-id');
                    var prix = parseFloat(this.getAttribute('data-prix')) || 0;
                    var quantite = parseInt(this.getAttribute('data-quantite')) || 0;
                    if (!sinistreId) return;

                    if (prix <= 0 || quantite <= 0) {
                        alert('Prix ou quantité invalide pour cet achat.');
                        return;
                    }
                    // Calculer montant requis en incluant les frais
                    var required = prix * quantite * (1 + (fraisPercent / 100));
                    // arrondir à entiers (ou garder 2 décimales) — ici on arrondit au nombre entier le plus proche
                    required = Math.round(required);

                    if (required > totalDisponible) {
                        alert('Fonds insuffisants. Il faut ' + required.toLocaleString('fr-FR') + ' Ar mais il reste seulement ' + totalDisponible.toLocaleString('fr-FR') + ' Ar.');
                        return;
                    }

                    var message = 'Confirmer l\'achat de ' + quantite + ' unité(s) (' + (prix.toLocaleString('fr-FR') + ' Ar/u') + ')\nFrais: ' + fraisPercent + '%\nTotal à payer: ' + required.toLocaleString('fr-FR') + ' Ar ?';
                    if (!confirm(message)) return;

                    this.disabled = true;
                    this.textContent = 'Traitement...';

                    var form = new FormData();
                    form.append('sinistre_id', sinistreId);

                    fetch('<?php echo BASE_URL; ?>/achat/buy', {
                        method: 'POST',
                        body: form,
                        credentials: 'same-origin'
                    }).then(function(resp) {
                        return resp.json();
                    }).then(function(json) {
                        if (json && json.success) {
                            // Mettre à jour l'affichage du total
                            if (typeof json.total !== 'undefined') {
                                totalDisponible = Number(json.total) || 0;
                                if (totalCard) {
                                    totalCard.setAttribute('data-total-argent', totalDisponible);
                                }
                                if (totalValueElem) {
                                    totalValueElem.innerHTML = formatAr(totalDisponible);
                                }
                            }
                            // Désactiver définitivement le bouton
                            btn.disabled = true;
                            btn.textContent = 'Acheté';
                            // Optionnel : marquer la ligne visuellement
                            var row = findAncestor(btn, 'tr');
                            if (row) row.style.opacity = '0.7';
                        } else {
                            alert((json && json.message) ? json.message : 'Erreur lors de l\'achat');
                            btn.disabled = false;
                            btn.textContent = 'Acheter';
                        }
                    }).catch(function(err) {
                        console.error(err);
                        alert('Erreur réseau');
                        btn.disabled = false;
                        btn.textContent = 'Acheter';
                    });
                });
            });
        })();
    </script>
</body>

</html>