<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['admin']);
$pageTitle = 'Admin Dashboard';
$totalVoters = countRows($pdo, 'SELECT COUNT(*) FROM voters');
$approvedVoters = countRows($pdo, "SELECT COUNT(*) FROM voters WHERE registration_status = 'approved'");
$totalCandidates = countRows($pdo, 'SELECT COUNT(*) FROM candidates');
$totalPositions = countRows($pdo, 'SELECT COUNT(*) FROM positions');
$totalVotes = countRows($pdo, 'SELECT COUNT(*) FROM votes');
$votedCount = countRows($pdo, "SELECT COUNT(*) FROM voter_election_status WHERE has_voted = 1");
$turnout = $approvedVoters > 0 ? round(($votedCount / $approvedVoters) * 100, 2) : 0;
$currentElection = getActiveElection($pdo);
$logs = $pdo->query('SELECT a.*, u.username FROM audit_logs a LEFT JOIN users u ON a.user_id = u.user_id ORDER BY a.created_at DESC LIMIT 8')->fetchAll();
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header">
    <div>
        <h1>Admin Dashboard</h1>
        <p>Monitor election readiness, voter turnout, candidates, and recent system activity.</p>
    </div>
    <a class="btn" href="<?= BASE_URL ?>/admin/innovation.php">Innovation Features</a>
</div>

<div class="grid">
    <div class="stat-card"><h3>Total Voters</h3><p><?= $totalVoters ?></p><div class="stat-note">All registered records</div></div>
    <div class="stat-card"><h3>Approved Voters</h3><p><?= $approvedVoters ?></p><div class="stat-note">Eligible to access ballot</div></div>
    <div class="stat-card"><h3>Total Candidates</h3><p><?= $totalCandidates ?></p><div class="stat-note">Candidates in system</div></div>
    <div class="stat-card"><h3>Total Positions</h3><p><?= $totalPositions ?></p><div class="stat-note">Election positions</div></div>
    <div class="stat-card"><h3>Votes Cast</h3><p><?= $totalVotes ?></p><div class="stat-note">Submitted vote records</div></div>
    <div class="stat-card"><h3>Turnout</h3><p><?= $turnout ?>%</p><div class="stat-note">Based on approved voters</div></div>
</div>

<div class="dashboard-split">
    <div class="card public-hero">
        <div class="inline-spread">
            <div>
                <h2>Current Election</h2>
                <p>Live election status and operating window.</p>
            </div>
            <?= $currentElection ? badge($currentElection['status']) : badge('No Open Election') ?>
        </div>
        <?php if ($currentElection): ?>
            <h2 style="font-size:32px;margin:18px 0 6px;"><?= h($currentElection['election_name']) ?></h2>
            <p><?= h($currentElection['start_datetime']) ?> to <?= h($currentElection['end_datetime']) ?></p>
            <div style="margin-top:20px;">
                <div class="inline-spread" style="color:rgba(255,255,255,.72);font-size:13px;margin-bottom:8px;">
                    <span>Voter Turnout</span><strong><?= $turnout ?>%</strong>
                </div>
                <div class="progress-line"><div class="progress-fill" style="width:<?= min($turnout, 100) ?>%;"></div></div>
            </div>
        <?php else: ?>
            <p>No election is currently open. Create or open an election from the Election Management page.</p>
            <a class="btn btn-light" href="<?= BASE_URL ?>/admin/elections.php">Manage Elections</a>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Recent Audit Logs</h2>
        <p>Latest recorded actions from users and system processes.</p>
        <div class="table-wrap">
            <table>
                <tr><th>Date</th><th>User</th><th>Action</th></tr>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= h($log['created_at']) ?></td>
                        <td><?= h($log['username'] ?? 'System') ?></td>
                        <td><?= h($log['action']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
