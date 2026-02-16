<?php
// Vue : home
?>
<!-- Local Bootstrap CSS and sidebar styles -->
<link rel="stylesheet" href="/assets/css/bootstrap.min.css">
<link rel="stylesheet" href="/assets/css/sidebar.css">
<!-- Local Bootstrap Icons (minimal) -->
<link rel="stylesheet" href="/assets/css/bootstrap-icons.css">

<div class="container-fluid">
    <div class="row flex-nowrap">
        <?php include __DIR__ . '/templates/sidebar.php'; ?>

        <div class="col py-3">
            <main class="p-3">
                <div class="home-page">
                    <form method="post" action="/sinistre/create" class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Type de besoin</label>
                            <?php if (!empty($besoins) && is_array($besoins)): ?>
                                <select name="type_besoin" class="form-select">
                                    <?php foreach ($besoins as $b): ?>
                                        <option value="<?php echo htmlspecialchars($b['id'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($b['nom']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input type="text" name="type_besoin" class="form-control" value="Huile">
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Libellée</label>
                            <input type="text" name="libellee" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Ville</label>
                            <?php if (!empty($villes) && is_array($villes)): ?>
                                <select name="ville" class="form-select">
                                    <?php foreach ($villes as $v): ?>
                                        <option value="<?php echo htmlspecialchars($v['id'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($v['nom']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input type="text" name="ville" class="form-control">
                            <?php endif; ?>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Quantité</label>
                            <input type="number" name="quantite" class="form-control" value="200">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Unité</label>
                            <?php if (!empty($unites) && is_array($unites)): ?>
                                <select name="unite" class="form-select">
                                    <?php foreach ($unites as $u): ?>
                                        <option value="<?php echo htmlspecialchars($u['id'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($u['nom']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input type="text" name="unite" class="form-control" value="Litre">
                            <?php endif; ?>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Valider</button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
    <?php include __DIR__ . '/templates/footer.php'; ?>
</div>
</div>
<!-- Local Bootstrap-like JS for collapse/dropdown used by sidebar -->
<script src="/assets/js/bootstrap.bundle.min.js"></script>