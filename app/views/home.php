<?php
// Vue : home
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insérer Besoins - BNGRC</title>
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

                    <!-- Welcome Box -->
                    <div class="welcome-box">
                        <h2>Bienvenue dans le formulaire d'enregistrement</h2>
                        <p>Utilisez ce formulaire pour enregistrer un nouveau besoin en secours. Tous les champs marqués d'une astérisque (*) sont obligatoires.</p>
                    </div>

                    <!-- Form Section -->
                    <div class="form-section-home">
                        <h2 class="form-title">💼 Détails du Besoin</h2>

                        <form method="post" action="<?php echo BASE_URL; ?>/sinistre/create" class="form-container" style="padding: 0; box-shadow: none; border: none; background: transparent;">
                            <!-- First Row -->
                            <div class="form-grid">
                                <div class="form-group-wrapper">
                                    <label class="form-label required">Type de Besoin</label>
                                    <?php if (!empty($besoins) && is_array($besoins)): ?>
                                        <select name="type_besoin" class="form-select" required>
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
                                    <label class="form-label required">Libellé</label>
                                    <input type="text" name="libellee" class="form-control" placeholder="Description du besoin" required>
                                </div>

                                <div class="form-group-wrapper">
                                    <label class="form-label required">Ville</label>
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

                            <!-- Second Row -->
                            <div class="form-grid">
                                <div class="form-group-wrapper">
                                    <label class="form-label required">Quantité</label>
                                    <input type="number" name="quantite" class="form-control" placeholder="Ex: 200" value="200" min="0" step="0.01" required>
                                </div>
                                <div class="form-group-wrapper">
                                    <label class="form-label required">Unité</label>
                                    <?php if (!empty($unites) && is_array($unites)): ?>
                                        <select name="unite" class="form-select" required>
                                            <option value="">-- Sélectionner une unité --</option>
                                            <?php foreach ($unites as $u): ?>
                                                <option value="<?php echo htmlspecialchars($u['id'], ENT_QUOTES); ?>">
                                                    <?php echo htmlspecialchars($u['nom']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <input type="text" name="unite" class="form-control" placeholder="Unité" value="Litre">
                                    <?php endif; ?>
                                </div>

                                <div class="form-group-wrapper">
                                    <label class="form-label">Date</label>
                                    <input type="datetime-local" name="date" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>">
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="submit-button-wrapper">
                                <button type="reset" class="btn btn-outline-secondary">🔄 Réinitialiser</button>
                                <button type="submit" class="btn btn-primary">✓ Enregistrer Besoin</button>
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

            <?php include __DIR__ . '/templates/footer.php'; ?>
        </div>
    </div>
</body>

</html>