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

                </div>
            </main>
        </div>
    </div>
    <?php include __DIR__ . '/templates/footer.php'; ?>
</div>
</div>
<!-- Local Bootstrap-like JS for collapse/dropdown used by sidebar -->
<script src="/assets/js/bootstrap.bundle.min.js"></script>