<?php
// Vue : inserer dons
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insérer Dons - BNGRC</title>
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/BNGRC.png">
    <!-- Stylesheets (same stack as home) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/theme.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/layout.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/forms.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/buttons.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dons.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/bootstrap-icons.css">
</head>

<body>
    <div class="container-main">
        <?php include __DIR__ . '/templates/sidebar.php'; ?>
        <div class="main-content">
            <div class="page-wrapper">
                <div class="dons-page">
                    <div class="page-header">
                        <h1 class="page-title">🤲 Enregistrement d'un don</h1>
                        <p class="page-subtitle">Remplissez le formulaire ci-dessous pour enregistrer un don</p>
                    </div>

                    <?php if (isset($_GET['created'])): ?>
                        <?php if ($_GET['created'] == '1'): ?>
                            <div class="alert alert-success">Don enregistré.</div>
                        <?php else: ?>
                            <div class="alert alert-danger">Erreur lors de l'enregistrement.</div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (!empty($dispatchStatus)): ?>
                        <?php if ($dispatchStatus === 'success'): ?>
                            <div class="alert alert-info">
                                <strong>Résultats du dispatch</strong>
                                <?php if (!empty($dispatchResults)): ?>
                                    <div class="table-responsive" style="margin-top: 0.75rem;">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Don (libellé)</th>
                                                    <th>Besoin (libellé)</th>
                                                    <th>Quantité transférée</th>
                                                    <th>Reste don</th>
                                                    <th>Reste besoin</th>
                                                    <th>État besoin</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($dispatchResults as $item): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($item['don']['libellee'] ?? ''); ?> (<?php echo htmlspecialchars($item['don']['ville'] ?? ''); ?>)</td>
                                                        <td><?php echo htmlspecialchars($item['sinistre']['libellee'] ?? ''); ?> (<?php echo htmlspecialchars($item['sinistre']['ville'] ?? ''); ?>)</td>
                                                        <td><strong><?php echo htmlspecialchars((string)($item['dispatched'] ?? '0')); ?> <?php echo htmlspecialchars($item['don']['unite'] ?? ''); ?></strong></td>
                                                        <td><?php echo htmlspecialchars((string)($item['don']['quantite_apres'] ?? '0')); ?></td>
                                                        <td><?php echo htmlspecialchars((string)($item['sinistre']['quantite_apres'] ?? '0')); ?></td>
                                                        <td><?php echo htmlspecialchars($item['sinistre']['etat'] ?? ''); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php elseif ($dispatchStatus === 'no-match'): ?>
                            <div class="alert alert-warning">Aucun libellé identique n'a été trouvé entre les dons et les besoins.</div>
                        <?php elseif ($dispatchStatus === 'no-data'): ?>
                            <div class="alert alert-warning">Impossible de dispatcher : la liste des dons ou des besoins est vide.</div>
                        <?php elseif ($dispatchStatus === 'error'): ?>
                            <div class="alert alert-danger">
                                Une erreur est survenue pendant le dispatch.
                                <?php if (!empty($dispatchError)): ?>
                                    <br><small><?php echo htmlspecialchars($dispatchError); ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="form-section-dons">
                        <h2 class="form-title">🧾 Détails du Don</h2>
                        <p style="color: #666; margin-bottom: 2rem; font-size: 0.95rem;">Veuillez remplir les informations ci-dessous pour enregistrer un nouveau don. Faites correspondre le don avec les besoins existants.</p>

                        <form method="post" action="<?php echo BASE_URL; ?>/dons/create" class="form-container">
                            <!-- Section 1: Classification du Don -->
                            <div style="background: #e8f5e9; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; border-left: 4px solid #28a745;">
                                <h3 style="margin-top: 0; color: #333; font-size: 1.1rem; margin-bottom: 1rem;">📦 Identification du Don</h3>
                                <div class="form-grid">
                                    <div class="form-group-wrapper">
                                        <label class="form-label">Type de Don</label>
                                        <small style="display: block; color: #666; margin-bottom: 0.5rem;">Catégorie: Nature, Matériaux, ou Argent</small>
                                        <?php if (!empty($besoins) && is_array($besoins)): ?>
                                            <select name="type_besoin" id="type-besoin-select" class="form-select">
                                                <option value="">-- Sélectionner un type --</option>
                                                <?php foreach ($besoins as $b): ?>
                                                    <option value="<?php echo htmlspecialchars($b['id'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($b['nom']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php else: ?>
                                            <input type="text" name="type_besoin" class="form-control">
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-wrapper">
                                        <label class="form-label">Article Donné</label>
                                        <small style="display: block; color: #666; margin-bottom: 0.5rem;">Spécifier l'article exact du don</small>
                                        <?php if (!empty($objets) && is_array($objets)): ?>
                                            <select name="objet" id="objet-select" class="form-select">
                                                <option value="">-- Sélectionner un objet --</option>
                                                <?php foreach ($objets as $o): ?>
                                                    <option value="<?php echo htmlspecialchars($o['id'] ?? '', ENT_QUOTES); ?>" data-besoin="<?php echo htmlspecialchars($o['id_besoins'] ?? '', ENT_QUOTES); ?>" data-unite="<?php echo htmlspecialchars($o['id_unite'] ?? '', ENT_QUOTES); ?>" data-prix="<?php echo htmlspecialchars($o['prix_unitaire'] ?? '', ENT_QUOTES); ?>">
                                                        <?php echo htmlspecialchars($o['libellee'] ?? ''); ?> — <?php echo htmlspecialchars($o['besoin'] ?? ''); ?> (<?php echo htmlspecialchars($o['unite'] ?? ''); ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php else: ?>
                                            <input type="text" name="libellee" class="form-control">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Localisation du Don -->
                            <div style="background: #fff3cd; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; border-left: 4px solid #ffc107;">
                                <h3 style="margin-top: 0; color: #333; font-size: 1.1rem; margin-bottom: 1rem;">📍 Destination du Don</h3>
                                <div class="form-group-wrapper">
                                    <label class="form-label">Ville Destinataire</label>
                                    <small style="display: block; color: #666; margin-bottom: 0.5rem;">Vers quelle ville ce don sera-t-il envoyé?</small>
                                    <?php if (!empty($villes) && is_array($villes)): ?>
                                        <select name="ville" class="form-select">
                                            <option value="">-- Sélectionner une ville --</option>
                                            <?php foreach ($villes as $v): ?>
                                                <option value="<?php echo htmlspecialchars($v['id'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($v['nom']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <input type="text" name="ville" class="form-control">
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Section 3: Quantité et Détails -->
                            <div style="background: #e3f2fd; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; border-left: 4px solid #2196f3;">
                                <h3 style="margin-top: 0; color: #333; font-size: 1.1rem; margin-bottom: 1rem;">📊 Quantité et Détails</h3>
                                <div class="form-grid">
                                    <div class="form-group-wrapper">
                                        <label class="form-label">Quantité Donnée</label>
                                        <small style="display: block; color: #666; margin-bottom: 0.5rem;">Nombre d'unités offertes</small>
                                        <input type="number" name="quantite" class="form-control" value="1" min="0" step="1" placeholder="Exemple: 50">
                                    </div>

                                    <div class="form-group-wrapper">
                                        <label class="form-label">Unité de Mesure</label>
                                        <small style="display: block; color: #666; margin-bottom: 0.5rem;">Automatiquement filtrée selon le type</small>
                                        <?php if (!empty($unites) && is_array($unites)): ?>
                                            <select name="unite" id="unite-select" class="form-select">
                                                <option value="">-- Sélectionner une unité --</option>
                                                <?php foreach ($unites as $u): ?>
                                                    <?php 
                                                        $uniteId = $u['id'];
                                                        $besoinIds = [];
                                                        foreach ($unitesByBesoin as $bId => $uIds) {
                                                            if (in_array($uniteId, $uIds)) {
                                                                $besoinIds[] = $bId;
                                                            }
                                                        }
                                                        $besoinIdsStr = implode(',', $besoinIds);
                                                    ?>
                                                    <option value="<?php echo htmlspecialchars($u['id'], ENT_QUOTES); ?>" data-besoins="<?php echo htmlspecialchars($besoinIdsStr, ENT_QUOTES); ?>"><?php echo htmlspecialchars($u['nom']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php else: ?>
                                            <input type="text" name="unite" class="form-control">
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-wrapper">
                                        <label class="form-label">Date du Don</label>
                                        <small style="display: block; color: #666; margin-bottom: 0.5rem;">Quand ce don a-t-il été fourni?</small>
                                        <input type="datetime-local" name="date" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>">
                                    </div>

                                    <div class="form-group-wrapper">
                                        <label class="form-label">Prix Unitaire (Estimé)</label>
                                        <small style="display: block; color: #666; margin-bottom: 0.5rem;">Valeur estimée de l'unité</small>
                                        <input type="text" id="prix-display" class="form-control" disabled placeholder="—" style="background-color: #f0f0f0;">
                                        <input type="hidden" name="prix_unitaire" id="prix-hidden" value="">
                                    </div>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="submit-button-wrapper" style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem; flex-wrap: wrap;">
                                <button type="reset" class="btn btn-outline-secondary" style="padding: 0.75rem 1.5rem;">🔄 Réinitialiser</button>
                                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem; font-weight: 600;">✓ Enregistrer le Don</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                (function() {
                    var objetSelect = document.getElementById('objet-select');
                    var besoinSelect = document.getElementById('type-besoin-select');
                    var uniteSelect = document.getElementById('unite-select');
                    var prixDisplay = document.getElementById('prix-display');
                    var prixHidden = document.getElementById('prix-hidden');

                    function formatPrix(v) {
                        var n = Number(v);
                        if (!isFinite(n)) return '';
                        return n.toLocaleString('fr-FR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) + ' Ar';
                    }

                    function filterUnitesByBesoin() {
                        if (!besoinSelect || !uniteSelect) return;
                        var selectedBesoinId = besoinSelect.value;
                        var allOptions = uniteSelect.querySelectorAll('option');
                        var firstVisibleFound = false;

                        allOptions.forEach(function(option) {
                            if (option.value === '') {
                                option.style.display = 'block';
                                return;
                            }
                            var besoinIds = option.getAttribute('data-besoins');
                            if (besoinIds && selectedBesoinId) {
                                var besoinIdArray = besoinIds.split(',');
                                var isVisible = besoinIdArray.includes(selectedBesoinId);
                                option.style.display = isVisible ? 'block' : 'none';
                                if (isVisible && !firstVisibleFound) {
                                    firstVisibleFound = true;
                                }
                            } else {
                                option.style.display = 'block';
                            }
                        });

                        // Reset unite selection if current is not visible
                        if (uniteSelect.value !== '' && uniteSelect.selectedOptions[0] && uniteSelect.selectedOptions[0].style.display === 'none') {
                            uniteSelect.value = '';
                        }
                    }

                    function filterObjetsByBesoin() {
                        if (!besoinSelect || !objetSelect) return;
                        var selectedBesoinId = besoinSelect.value;
                        var allOptions = objetSelect.querySelectorAll('option');

                        allOptions.forEach(function(option) {
                            if (option.value === '') {
                                option.style.display = 'block';
                                return;
                            }
                            var besoinId = option.getAttribute('data-besoin');
                            if (besoinId && selectedBesoinId) {
                                var isVisible = besoinId === selectedBesoinId;
                                option.style.display = isVisible ? 'block' : 'none';
                            } else {
                                option.style.display = 'block';
                            }
                        });

                        // Reset objet selection if current is not visible
                        if (objetSelect.value !== '' && objetSelect.selectedOptions[0] && objetSelect.selectedOptions[0].style.display === 'none') {
                            objetSelect.value = '';
                        }
                    }

                    function syncFromObjet() {
                        if (!objetSelect) return;
                        var opt = objetSelect.options[objetSelect.selectedIndex];
                        if (!opt || !opt.value) return;
                        var besoinId = opt.getAttribute('data-besoin');
                        var uniteId = opt.getAttribute('data-unite');
                        var prix = opt.getAttribute('data-prix');
                        if (besoinId && besoinSelect) {
                            besoinSelect.value = besoinId;
                            filterUnitesByBesoin();
                            filterObjetsByBesoin();
                        }
                        if (uniteId && uniteSelect) {
                            uniteSelect.value = uniteId;
                        }
                        // If the selected besoin is 'Argent', do not show a unit price
                        var selectedBesoinText = '';
                        if (besoinSelect && besoinSelect.options[besoinSelect.selectedIndex]) {
                            selectedBesoinText = (besoinSelect.options[besoinSelect.selectedIndex].text || '').trim().toLowerCase();
                        }
                        if (selectedBesoinText === 'argent') {
                            if (prixDisplay) prixDisplay.value = '';
                            if (prixHidden) prixHidden.value = '';
                        } else {
                            if (prixDisplay) {
                                prixDisplay.value = formatPrix(prix);
                            }
                            if (prixHidden) {
                                prixHidden.value = prix || '';
                            }
                        }
                    }

                    if (besoinSelect) {
                        besoinSelect.addEventListener('change', function() {
                            filterUnitesByBesoin();
                            filterObjetsByBesoin();
                        });
                    }

                    if (objetSelect) {
                        objetSelect.addEventListener('change', syncFromObjet);
                        window.addEventListener('load', function() {
                            setTimeout(syncFromObjet, 10);
                        });
                    }
                })();
            </script>
            <?php include __DIR__ . '/templates/footer.php'; ?>
        </div>
    </div>
</body>

</html>