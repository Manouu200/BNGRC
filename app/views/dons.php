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
                        <h2 class="form-title">🧾 Détails du don</h2>

                        <form method="post" action="<?php echo BASE_URL; ?>/dons/create" class="form-container">
                            <div class="form-grid">
                                <div class="form-group-wrapper">
                                    <label class="form-label">Type de besoin</label>
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
                                    <label class="form-label">Ville</label>
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

                                <div class="form-group-wrapper">
                                    <label class="form-label">Unité</label>
                                    <?php if (!empty($unites) && is_array($unites)): ?>
                                        <select name="unite" id="unite-select" class="form-select">
                                            <option value="">-- Sélectionner une unité --</option>
                                            <?php foreach ($unites as $u): ?>
                                                <option value="<?php echo htmlspecialchars($u['id'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($u['nom']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <input type="text" name="unite" class="form-control">
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-grid">
                                <div class="form-group-wrapper">
                                    <label class="form-label">Quantité</label>
                                    <input type="number" name="quantite" class="form-control" value="1" min="0" step="1">
                                </div>

                                <div class="form-group-wrapper">
                                    <label class="form-label">Date</label>
                                    <input type="datetime-local" name="date" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>">
                                </div>
                            </div>

                            <div class="form-group-wrapper">
                                <label class="form-label">Objet</label>
                                <?php if (!empty($objets) && is_array($objets)): ?>
                                    <select name="objet" id="objet-select" class="form-select">
                                        <option value="">-- Sélectionner un objet --</option>
                                        <?php foreach ($objets as $o): ?>
                                            <option value="<?php echo htmlspecialchars($o['id'], ENT_QUOTES); ?>" data-besoin="<?php echo htmlspecialchars($o['id_besoins'], ENT_QUOTES); ?>" data-unite="<?php echo htmlspecialchars($o['id_unite'], ENT_QUOTES); ?>" data-prix="<?php echo htmlspecialchars($o['prix_unitaire'], ENT_QUOTES); ?>">
                                                <?php echo htmlspecialchars($o['libellee']); ?> — <?php echo htmlspecialchars($o['besoin']); ?> (<?php echo htmlspecialchars($o['unite']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <input type="text" name="libellee" class="form-control">
                                <?php endif; ?>
                            </div>

                            <div class="form-group-wrapper">
                                <label class="form-label">Prix unitaire</label>
                                <input type="text" id="prix-display" class="form-control" disabled placeholder="—">
                                <input type="hidden" name="prix_unitaire" id="prix-hidden" value="">
                            </div>

                            <div class="submit-button-wrapper">
                                <button type="reset" class="btn btn-outline-secondary">🔄 Réinitialiser</button>
                                <button
                                    type="submit"
                                    id="btn-dispatch-dons"
                                    class="btn btn-outline-primary"
                                    title="Distribuer les dons vers les besoins correspondants"
                                    formaction="<?php echo BASE_URL; ?>/dons/dispatch"
                                    formmethod="post"
                                    formnovalidate>
                                    ⤴ Dispatcher les dons
                                </button>
                                <button type="submit" class="btn btn-primary">✓ Enregistrer le don</button>
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

                    function syncFromObjet() {
                        if (!objetSelect) return;
                        var opt = objetSelect.options[objetSelect.selectedIndex];
                        if (!opt || !opt.value) return;
                        var besoinId = opt.getAttribute('data-besoin');
                        var uniteId = opt.getAttribute('data-unite');
                        var prix = opt.getAttribute('data-prix');
                        if (besoinId && besoinSelect) {
                            besoinSelect.value = besoinId;
                        }
                        if (uniteId && uniteSelect) {
                            uniteSelect.value = uniteId;
                        }
                        if (prixDisplay) {
                            prixDisplay.value = formatPrix(prix);
                        }
                        if (prixHidden) {
                            prixHidden.value = prix || '';
                        }
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