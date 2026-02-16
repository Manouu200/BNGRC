<?php
// Vue : inserer dons
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insérer Dons - BNGRC</title>
    <!-- Stylesheets (same stack as home) -->
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="/assets/css/layout.css">
    <link rel="stylesheet" href="/assets/css/forms.css">
    <link rel="stylesheet" href="/assets/css/buttons.css">
    <link rel="stylesheet" href="/assets/css/dons.css">
    <link rel="stylesheet" href="/assets/css/bootstrap-icons.css">
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

                    <div class="form-section-dons">
                        <h2 class="form-title">🧾 Détails du don</h2>

                        <form method="post" action="<?php echo BASE_URL; ?>/dons/create" class="form-container">
                            <div class="form-grid">
                                <div class="form-group-wrapper">
                                    <label class="form-label">Type de besoin</label>
                                    <?php if (!empty($besoins) && is_array($besoins)): ?>
                                        <select name="type_besoin" class="form-select">
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
                                        <select name="unite" class="form-select">
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
                                <label class="form-label">Libellée</label>
                                <input type="text" name="libellee" class="form-control">
                            </div>

                            <div class="submit-button-wrapper">
                                <button type="reset" class="btn btn-outline-secondary">🔄 Réinitialiser</button>
                                <button type="submit" class="btn btn-primary">✓ Enregistrer le don</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php include __DIR__ . '/templates/footer.php'; ?>
        </div>
    </div>
</body>

</html>