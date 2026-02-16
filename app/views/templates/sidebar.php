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
            <h1>📊 BNGRC</h1>
            <p>Gestion des Besoins</p>
        </div>
        
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="/" class="nav-link <?php echo $currentPage === '' ? 'active' : ''; ?>">
                    <span class="nav-icon">🏠</span>
                    <span class="nav-label">Insérer Besoins</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/dashboard" class="nav-link <?php echo strpos($currentPage, 'dashboard') !== false ? 'active' : ''; ?>">
                    <span class="nav-icon">📈</span>
                    <span class="nav-label">Tableau de Bord</span>
                </a>
            </li>
        </ul>
        
        <hr style="border-color: rgba(255, 255, 255, 0.1); margin: 2rem 1rem;">
        
        <div style="padding: var(--spacing-lg); text-align: center; color: rgba(255, 255, 255, 0.7); font-size: var(--font-size-xs);">
            <p style="margin: 0;">👤 <?php echo htmlspecialchars($displayName); ?></p>
        </div>
    </div>
</div>