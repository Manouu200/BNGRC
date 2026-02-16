<?php
// Sidebar template
?>
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$displayName = $username ?? ($_SESSION['username'] ?? 'Utilisateur');
$currentPage = basename($_SERVER['REQUEST_URI'], '?');
?>
<div class="sidebar-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <div style="width: 170px; height: 170px; border-radius: 50%; background-color: #fff; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <img src="<?php echo BASE_URL; ?>/assets/images/BNGRC.png" alt="BNGRC" style="width: 160px; height: auto; display: block;">
            </div>
            <!-- <p>Gestion des Besoins</p> -->
        </div>

        <ul class="nav-menu">
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>/" class="nav-link <?php echo $currentPage === '' ? 'active' : ''; ?>">
                    <span class="nav-icon">🏠</span>
                    <span class="nav-label">Insérer Besoins</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>/dashboard" class="nav-link <?php echo strpos($currentPage, 'dashboard') !== false ? 'active' : ''; ?>">
                    <span class="nav-icon">📈</span>
                    <span class="nav-label">Tableau de Bord</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>/dons" class="nav-link <?php echo strpos($currentPage, 'dons') !== false ? 'active' : ''; ?>">
                    <span class="nav-icon">🤲</span>
                    <span class="nav-label">Inserer dons</span>
                </a>
            </li>
        </ul>

        <!-- <hr style="border-color: rgba(255, 255, 255, 0.1); margin: 2rem 1rem;"> -->
    </div>
</div>