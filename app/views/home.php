<?php
// Vue : home
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/sidebar.css">
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

                    <?php if (isset($_GET['created'])): ?>
                        <?php if ($_GET['created'] == '1'): ?>
                            <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #28a745;">Besoin enregistré avec succès.</div>
                        <?php else: ?>
                            <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #dc3545;">Erreur lors de l'enregistrement.</div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (isset($_GET['reset'])): ?>
                        <div class="alert alert-info" style="background: #d1ecf1; color: #0c5460; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #17a2b8;">
                            <?php echo (int)$_GET['reset']; ?> besoin(s) supprimé(s) de cette session.
                        </div>
                    <?php endif; ?>
    
                    <!-- Form Section -->
                    <div class="form-section-home">

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
                                                <option value="">Sélectionner un type</option>
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
                                                <option value="">Sélectionner un objet</option>
                                                <?php foreach ($objets as $o): ?>
                                                    <option value="<?php echo htmlspecialchars($o['id'] ?? '', ENT_QUOTES); ?>" data-besoin="<?php echo htmlspecialchars($o['id_besoins'] ?? '', ENT_QUOTES); ?>" data-unite="<?php echo htmlspecialchars($o['id_unite'] ?? '', ENT_QUOTES); ?>" data-prix="<?php echo htmlspecialchars($o['prix_unitaire'] ?? '', ENT_QUOTES); ?>">
                                                        <?php echo htmlspecialchars($o['libellee'] ?? ''); ?>
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
                                            <option value="">Sélectionner une ville</option>
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
                                        <label class="form-label">Total Estimé</label>
                                        <small style="display: block; color: #666; margin-bottom: 0.5rem;">Quantité × Prix Unitaire</small>
                                        <input type="text" id="total-display" class="form-control" disabled placeholder="—" style="background-color: #e8f5e9; font-weight: 600; color: #2e7d32;">
                                    </div>

                                    <div class="form-group-wrapper">
                                        <label class="form-label">Date de Besoin</label>
                                        <small style="display: block; color: #666; margin-bottom: 0.5rem;">Quand ce besoin a-t-il été identifié?</small>
                                        <input type="datetime-local" name="date" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="submit-button-wrapper" style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem; flex-wrap: wrap;">
                                <button type="reset" class="btn btn-outline-secondary" style="padding: 0.75rem 2rem;">🔄 Réinitialiser le formulaire</button>
                                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight: 600;">✓ Enregistrer ce Besoin</button>
                            </div>
                        </form>

                        <!-- Bouton Réinitialisation Session -->
                        <?php
                        $sessionBesoinsCount = isset($_SESSION['created_besoins']) ? count($_SESSION['created_besoins']) : 0;
                        ?>
                        <?php if ($sessionBesoinsCount > 0): ?>
                        <form method="post" action="<?php echo BASE_URL; ?>/besoins/reset" style="margin-top: 1.5rem; text-align: center;">
                            <button type="submit" onclick="return confirm('Voulez-vous vraiment supprimer les <?php echo $sessionBesoinsCount; ?> besoin(s) créé(s) pendant cette session ?');" style="
                                background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
                                color: #fff;
                                border: none;
                                padding: 0.75rem 1.5rem;
                                border-radius: 8px;
                                font-size: 0.95rem;
                                font-weight: 600;
                                cursor: pointer;
                                box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
                                transition: all 0.3s ease;
                            " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(220, 53, 69, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(220, 53, 69, 0.3)';">
                                ↩️ Annuler les <?php echo $sessionBesoinsCount; ?> besoin(s) de cette session
                            </button>
                        </form>
                        <?php endif; ?>
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
                    var totalDisplay = document.getElementById('total-display');
                    var quantiteInput = document.querySelector('input[name="quantite"]');

                    function formatPrix(v) {
                        var n = Number(v);
                        if (!isFinite(n)) return '';
                        return n.toLocaleString('fr-FR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) + ' Ar';
                    }

                    function calculateTotal() {
                        if (!totalDisplay || !quantiteInput || !prixHidden) return;
                        var quantite = parseFloat(quantiteInput.value) || 0;
                        var prix = parseFloat(prixHidden.value) || 0;
                        var total = quantite * prix;
                        if (total > 0) {
                            totalDisplay.value = formatPrix(total);
                        } else {
                            totalDisplay.value = '';
                        }
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
                        calculateTotal();
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

                    if (quantiteInput) {
                        quantiteInput.addEventListener('input', calculateTotal);
                    }
                })();
            </script>
            <?php include __DIR__ . '/templates/footer.php'; ?>
        </div>
    </div>
</body>

</html>