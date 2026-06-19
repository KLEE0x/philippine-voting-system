<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['auditor']);
$pageTitle = 'Auditor Dashboard';
$totalVotes = countRows($pdo, 'SELECT COUNT(*) FROM votes');
$totalLogs = countRows($pdo, 'SELECT COUNT(*) FROM audit_logs');
$closedElections = countRows($pdo, "SELECT COUNT(*) FROM elections WHERE status='closed'");
include __DIR__ . '/../includes/header.php'; include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header"><h1>Auditor Dashboard</h1></div>
<div class="grid">
<div class="stat-card"><h3>Total Votes Cast</h3><p><?= $totalVotes ?></p></div>
<div class="stat-card"><h3>Audit Logs</h3><p><?= $totalLogs ?></p></div>
<div class="stat-card"><h3>Closed Elections</h3><p><?= $closedElections ?></p></div>
</div>
<div class="card"><h2>Auditor Access</h2><p>Auditors can view results and audit logs, but cannot edit election records.</p></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
