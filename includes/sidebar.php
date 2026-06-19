<?php
$role = $_SESSION['role_name'] ?? '';
$name = $_SESSION['username'] ?? 'User';
$roleLabel = ucwords(str_replace('_', ' ', $role));
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$pageTitleDisplay = $pageTitle ?? 'Dashboard';

$isActive = function ($path) use ($currentPath) {
    return strpos($currentPath, $path) !== false ? 'active' : '';
};
?>
<div class="layout">
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <div class="brand-mark">PV</div>
            <div>
                <h2>PVMS</h2>
                <p><?= h($roleLabel ?: 'User') ?></p>
            </div>
        </div>

        <div class="system-label">
            Philippine Voting Management System
            <span>Secure. Transparent. Organized.</span>
        </div>

        <nav class="sidebar-nav">
            <?php if ($role === 'admin'): ?>
                <a class="<?= $isActive('/admin/dashboard.php') ?>" href="<?= BASE_URL ?>/admin/dashboard.php"><span>▦</span>Dashboard</a>
                <a class="<?= $isActive('/admin/elections.php') ?>" href="<?= BASE_URL ?>/admin/elections.php"><span>◉</span>Elections</a>
                <a class="<?= $isActive('/admin/positions.php') ?>" href="<?= BASE_URL ?>/admin/positions.php"><span>♛</span>Positions</a>
                <a class="<?= $isActive('/admin/candidates.php') ?>" href="<?= BASE_URL ?>/admin/candidates.php"><span>☷</span>Candidates</a>
                <a class="<?= $isActive('/admin/voters.php') ?>" href="<?= BASE_URL ?>/admin/voters.php"><span>◍</span>Voters</a>
                <a class="<?= $isActive('/admin/precincts.php') ?>" href="<?= BASE_URL ?>/admin/precincts.php"><span>⌖</span>Precincts</a>
                <a class="<?= $isActive('/admin/results.php') ?>" href="<?= BASE_URL ?>/admin/results.php"><span>▣</span>Results</a>
                <a class="<?= $isActive('/admin/audit_logs.php') ?>" href="<?= BASE_URL ?>/admin/audit_logs.php"><span>☑</span>Audit Logs</a>
                <a class="<?= $isActive('/admin/innovation.php') ?>" href="<?= BASE_URL ?>/admin/innovation.php"><span>✦</span>Innovation</a>
            <?php elseif ($role === 'election_officer'): ?>
                <a class="<?= $isActive('/officer/dashboard.php') ?>" href="<?= BASE_URL ?>/officer/dashboard.php"><span>▦</span>Dashboard</a>
                <a class="<?= $isActive('/admin/voters.php') ?>" href="<?= BASE_URL ?>/admin/voters.php"><span>◍</span>Voters</a>
                <a class="<?= $isActive('/admin/candidates.php') ?>" href="<?= BASE_URL ?>/admin/candidates.php"><span>☷</span>Candidates</a>
                <a class="<?= $isActive('/admin/positions.php') ?>" href="<?= BASE_URL ?>/admin/positions.php"><span>♛</span>Positions</a>
                <a class="<?= $isActive('/admin/precincts.php') ?>" href="<?= BASE_URL ?>/admin/precincts.php"><span>⌖</span>Precincts</a>
                <a class="<?= $isActive('/admin/results.php') ?>" href="<?= BASE_URL ?>/admin/results.php"><span>▣</span>Results</a>
            <?php elseif ($role === 'voter'): ?>
                <a class="<?= $isActive('/voter/dashboard.php') ?>" href="<?= BASE_URL ?>/voter/dashboard.php"><span>▦</span>Dashboard</a>
                <a class="<?= $isActive('/voter/ballot.php') ?>" href="<?= BASE_URL ?>/voter/ballot.php"><span>✓</span>Ballot</a>
                <a class="<?= $isActive('/public/results.php') ?>" href="<?= BASE_URL ?>/public/results.php"><span>▣</span>Public Results</a>
            <?php elseif ($role === 'auditor'): ?>
                <a class="<?= $isActive('/auditor/dashboard.php') ?>" href="<?= BASE_URL ?>/auditor/dashboard.php"><span>▦</span>Dashboard</a>
                <a class="<?= $isActive('/auditor/results.php') ?>" href="<?= BASE_URL ?>/auditor/results.php"><span>▣</span>Results</a>
                <a class="<?= $isActive('/auditor/audit_logs.php') ?>" href="<?= BASE_URL ?>/auditor/audit_logs.php"><span>☑</span>Audit Logs</a>
                <a class="<?= $isActive('/public/results.php') ?>" href="<?= BASE_URL ?>/public/results.php"><span>◌</span>Public Results</a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <div class="user-box">
                <div class="user-avatar"><?= h(strtoupper(substr($name, 0, 1))) ?></div>
                <div>
                    <p>Logged in as</p>
                    <strong><?= h($name) ?></strong>
                </div>
            </div>
            <a class="logout" href="<?= BASE_URL ?>/auth/logout.php">Logout</a>
        </div>
    </aside>

    <main class="content">
        <header class="app-topbar">
            <div class="topbar-title">
                <button class="mobile-menu-btn" type="button" data-toggle-sidebar>☰</button>
                <div>
                    <h1><?= h($pageTitleDisplay) ?></h1>
                    <p>PVMS Demo · Role-based election workspace</p>
                </div>
            </div>
            <div class="topbar-actions">
                <div class="search-box"><input type="search" placeholder="Search records..."></div>
                <div class="notification-dot">◔</div>
                <div class="topbar-user">
                    <div class="user-avatar"><?= h(strtoupper(substr($name, 0, 1))) ?></div>
                    <strong><?= h($name) ?></strong>
                </div>
            </div>
        </header>
