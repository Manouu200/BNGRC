<?php
// Sidebar template
?>
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$displayName = $username ?? ($_SESSION['username'] ?? 'Utilisateur');
?>
<div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark">
    <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
        <a class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <span class="fs-5 d-none d-sm-inline">Menu</span>
        </a>
        <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
            <li class="nav-item">
                <a href="/home" class="nav-link align-middle px-0 text-white">
                    <i class="fs-4 bi bi-house"></i> <span class="ms-1 d-none d-sm-inline">Inserer besoins</span>
                </a>
            </li>
        </ul>
        <hr>
    </div>
</div>