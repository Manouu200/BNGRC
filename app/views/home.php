<?php
// Vue : home
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insérer Besoins - BNGRC</title>
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/BNGRC.png">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/theme.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/layout.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/forms.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/buttons.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/home.css">
</head>

<body>
    <div class="container-main">
        <?php include __DIR__ . '/templates/sidebar.php'; ?>

        <div class="main-content">
            <div class="page-wrapper">
                <div class="home-page">
                    <!-- Page Header -->
                    <div class="page-header">
                        <h1 class="page-title">📝 Enregistrement de Besoin</h1>
                        <p class="page-subtitle">Remplissez le formulaire ci-dessous pour ajouter un nouveau besoin</p>
                    </div>
                    <!-- Form Section -->
                    <div class="form-section-home">
                        <h2 class="form-title">💼 Détails du Besoin</h2>
                        <p style="color: #666; margin-bottom: 2rem; font-size: 0.95rem;">Veuillez remplir les informations ci-dessous pour enregistrer un nouveau besoin. Les champs marqués avec <span style="color: #dc3545;">*</span> sont obligatoires.</p>

                        <form method="post" action="<?php echo BASE_URL; ?>/sinistre/create" class="form-container" style="padding: 0; box-shadow: none; border: none; background: transparent;">
                            <!-- Section 1: Classification -->
                            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; border-left: 4px solid #007bff;">
                                <h3 style="margin-top: 0; color: #333; font-size: 1.1rem; margin-bottom: 1rem;">📂 Classification du besoin</h3>
                                <div class="form-grid">
                                    <div class="form-group-wrapper">
                                        <label class="form-label required">Type de Besoin</label>
                                        <small style="display: block; color: #666; margin-bottom: 0.5rem;">Catégories disponibles: Nature, Matériaux, Argent</small>
                                        <?php if (!empty($besoins) && is_array($besoins)): ?>
                                            <select name="type_besoin" id="type-besoin-select" class="form-select" required>
                                                <option value="">-- Sélectionner un type --</option>
                                                <?php foreach ($besoins as $b): ?>
                                                    <option value="<?php echo htmlspecialchars($b['id'], ENT_QUOTES); ?>">
                                                        <?php echo htmlspecialchars($b['nom']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php else: ?>
                                            <input type="text" name="type_besoin" class="form-control" value="Huile" required>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-wrapper">
                                        <label class="form-label required">Objet Spécifique</label>
                                        <small style="display: block; color: #666; margin-bottom: 0.5rem;">Sélectionnez l'article exact</small>
                                        <?php if (!empty($objets) && is_array($objets)): ?>
                                            <select name="objet" id="objet-select" class="form-select" required>
                                                <option value="">-- Sélectionner un objet --</option>
                                                <?php foreach ($objets as $o): ?>
                                                    <option value="<?php echo htmlspecialchars($o['id'] ?? '', ENT_QUOTES); ?>" data-besoin="<?php echo htmlspecialchars($o['id_besoins'] ?? '', ENT_QUOTES); ?>" data-unite="<?php echo htmlspecialchars($o['id_unite'] ?? '', ENT_QUOTES); ?>" data-prix="<?php echo htmlspecialchars($o['prix_unitaire'] ?? '', ENT_QUOTES); ?>">
                                                        <?php echo htmlspecialchars($o['libellee'] ?? ''); ?> — <?php echo htmlspecialchars($o['besoin'] ?? ''); ?> (<?php echo htmlspecialchars($o['unite'] ?? ''); ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php else: ?>
                                            <input type="text" name="libellee" class="form-control" placeholder="Description du besoin" required>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Localisation -->
                            <div style="background: #fff8f0; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; border-left: 4px solid #fd7e14;">
                                <h3 style="margin-top: 0; color: #333; font-size: 1.1rem; margin-bottom: 1rem;">📍 Localisation</h3>
                                <div class="form-group-wrapper">
                                    <label class="form-label required">Ville Affectée</label>
                                    <small style="display: block; color: #666; margin-bottom: 0.5rem;">Sélectionnez la ville où le besoin est identifié</small>
                                    <?php if (!empty($villes) && is_array($villes)): ?>
                                        <select name="ville" class="form-select" required>
                                            <option value="">-- Sélectionner une ville --</option>
                                            <?php foreach ($villes as $v): ?>
                                                <option value="<?php echo htmlspecialchars($v['id'], ENT_QUOTES); ?>">
                                                    <?php echo htmlspecialchars($v['nom']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <input type="text" name="ville" class="form-control" placeholder="Nom de la ville">
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Section 3: Quantité et Timing -->
                            <div style="background: #f0f8ff; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; border-left: 4px solid #17a2b8;">
                                <h3 style="margin-top: 0; color: #333; font-size: 1.1rem; margin-bottom: 1rem;">📊 Quantité et Données</h3>
                                <div class="form-grid">
                                    <div class="form-group-wrapper">
                                        <label class="form-label required">Quantité Nécessaire</label>
                                        <small style="display: block; color: #666; margin-bottom: 0.5rem;">Nombre d'unités requises</small>
                                        <input type="number" name="quantite" class="form-control" placeholder="Exemple: 200" value="200" min="0" step="0.01" required>
                                    </div>

                                    <div class="form-group-wrapper">
                                        <label class="form-label required">Unité de Mesure</label>
                                        <small style="display: block; color: #666; margin-bottom: 0.5rem;">Automatiquement filtrée selon le besoin</small>
                                        <?php if (!empty($unites) && is_array($unites)): ?>
                                            <select name="unite" id="unite-select" class="form-select" required>
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
                                                    <option value="<?php echo htmlspecialchars($u['id'], ENT_QUOTES); ?>" data-besoins="<?php echo htmlspecialchars($besoinIdsStr, ENT_QUOTES); ?>">
                                                        <?php echo htmlspecialchars($u['nom']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php else: ?>
                                            <input type="text" name="unite" class="form-control" placeholder="Unité" value="Litre">
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-wrapper">
                                        <label class="form-label">Prix Unitaire (Établi)</label>
                                        <small style="display: block; color: #666; margin-bottom: 0.5rem;">Informationnel - calculé automatiquement</small>
                                        <input type="text" id="prix-display" class="form-control" disabled placeholder="—" style="background-color: #f0f0f0;">
                                        <input type="hidden" name="prix_unitaire" id="prix-hidden" value="">
                                    </div>

                                    <div class="form-group-wrapper">
                                        <label class="form-label">Date de Besoin</label>
                                        <small style="display: block; color: #666; margin-bottom: 0.5rem;">Quand ce besoin a-t-il été identifié?</small>
                                        <input type="datetime-local" name="date" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="submit-button-wrapper" style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem;">
                                <button type="reset" class="btn btn-outline-secondary" style="padding: 0.75rem 2rem;">🔄 Réinitialiser le formulaire</button>
                                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight: 600;">✓ Enregistrer ce Besoin</button>
                            </div>
                        </form>
                    </div>

                    <!-- Info Cards -->
                    <div class="stats-section">
                        <div class="stat-card">
                            <div class="stat-icon">📋</div>
                            <div class="stat-value">Besoin</div>
                            <div class="stat-label">Enregistrer un nouveau besoin</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">📊</div>
                            <div class="stat-value">Vue</div>
                            <div class="stat-label">Consulter le tableau de bord</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">🔍</div>
                            <div class="stat-value">Filtre</div>
                            <div class="stat-label">Rechercher par paramètres</div>
                        </div>
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