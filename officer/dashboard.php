<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['election_officer']);
$pageTitle = 'Election Officer Dashboard';
$totalVoters = countRows($pdo, 'SELECT COUNT(*) FROM voters');
$pendingVoters = countRows($pdo, "SELECT COUNT(*) FROM voters WHERE registration_status='pending'");
$totalCandidates = countRows($pdo, 'SELECT COUNT(*) FROM candidates');
$totalPositions = countRows($pdo, 'SELECT COUNT(*) FROM positions');
include __DIR__ . '/../includes/header.php'; include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header"><h1>Election Officer Dashboard</h1></div>
<div class="grid">
<div class="stat-card"><h3>Total Voters</h3><p><?= $totalVoters ?></p></div>
<div class="stat-card"><h3>Pending Voters</h3><p><?= $pendingVoters ?></p></div>
<div class="stat-card"><h3>Candidates</h3><p><?= $totalCandidates ?></p></div>
<div class="stat-card"><h3>Positions</h3><p><?= $totalPositions ?></p></div>
</div>
<div class="card"><h2>Officer Tools</h2><p>You can manage voters, candidates, positions, precincts, and view election results.</p></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
