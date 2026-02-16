<?php
// Vue : dashboard
?>
<link rel="stylesheet" href="/assets/css/bootstrap.min.css">
<link rel="stylesheet" href="/assets/css/sidebar.css">
<link rel="stylesheet" href="/assets/css/bootstrap-icons.css">

<div class="container-fluid">
    <div class="row flex-nowrap">
        <?php include __DIR__ . '/templates/sidebar.php'; ?>

        <div class="col py-3">
            <main class="p-3">
                <div class="table-responsive mt-3">
                    <style>
                        /* Forcer affichage de bordure si le CSS bootstrap local n'applique pas */
                        .table-bordered,
                        .table-bordered th,
                        .table-bordered td {
                            border: 1px solid #dee2e6 !important;
                        }

                        .table {
                            border-collapse: collapse !important;
                        }
                    </style>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Ville</th>
                                <th>Besoin</th>
                                <th>Libellée</th>
                                <th>Quantité</th>
                                <th>Unité</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($sinistres) && is_array($sinistres)): ?>
                                <?php foreach ($sinistres as $s): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($s['ville'] ?? $s['ville'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($s['besoin'] ?? $s['besoin'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($s['libellee'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars((string)($s['quantite'] ?? '')); ?></td>
                                        <td><?php echo htmlspecialchars($s['unite'] ?? ''); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">Aucun sinistre trouvé.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
    <?php include __DIR__ . '/templates/footer.php'; ?>
</div>

<script src="/assets/js/bootstrap.bundle.min.js"></script>