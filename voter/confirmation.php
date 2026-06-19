<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['voter']);
$pageTitle = 'Voting Confirmation';
$voter = getVoterByUserId($pdo, $_SESSION['user_id']);
$stmt = $pdo->prepare('SELECT e.election_name, ves.voted_at FROM voter_election_status ves INNER JOIN elections e ON ves.election_id=e.election_id WHERE ves.voter_id=? AND ves.has_voted=1 ORDER BY ves.voted_at DESC LIMIT 1');
$stmt->execute([$voter['voter_id'] ?? 0]);
$record = $stmt->fetch();
include __DIR__ . '/../includes/header.php'; include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header">
    <div>
        <h1>Voting Confirmation</h1>
        <p>This page confirms that the voter has completed the voting process.</p>
    </div>
</div>

<div class="flow-steps">
    <span class="flow-step">1. Select Candidates</span>
    <span class="flow-step">2. Review Vote</span>
    <span class="flow-step active">3. Confirmation</span>
</div>

<div class="card success-panel">
<?php if ($record): ?>
    <div class="success-icon">✓</div>
    <h2>Your vote has been successfully submitted.</h2>
    <p>This confirmation verifies submission only. It does not reveal your selected candidates.</p>
    <div class="card" style="max-width:520px;margin:22px auto;text-align:left;">
        <p><strong>Election:</strong> <?= h($record['election_name']) ?></p>
        <p><strong>Date/Time Voted:</strong> <?= h($record['voted_at']) ?></p>
        <p><strong>Status:</strong> <?= badge('Voted') ?></p>
    </div>
    <a class="btn" href="<?= BASE_URL ?>/voter/dashboard.php">Return to Dashboard</a>
<?php else: ?>
    <div class="alert alert-info">No voting confirmation found yet.</div>
    <a class="btn" href="<?= BASE_URL ?>/voter/ballot.php">Go to Ballot</a>
<?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
