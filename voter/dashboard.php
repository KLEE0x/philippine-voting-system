<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['voter']);
$pageTitle = 'Voter Dashboard';
$voter = getVoterByUserId($pdo, $_SESSION['user_id']);
$election = getActiveElection($pdo);
$alreadyVoted = $voter && $election ? hasVoted($pdo, $voter['voter_id'], $election['election_id']) : false;
include __DIR__ . '/../includes/header.php'; include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header">
    <div>
        <h1>Voter Dashboard</h1>
        <p>Check your voter profile, active election status, and ballot availability.</p>
    </div>
    <?php if ($voter && $election && $voter['registration_status']==='approved' && !$alreadyVoted): ?>
        <a class="btn" href="<?= BASE_URL ?>/voter/ballot.php">Proceed to Ballot</a>
    <?php endif; ?>
</div>

<div class="dashboard-split">
    <div class="card">
        <h2>Voter Information</h2>
        <?php if ($voter): ?>
            <div class="grid" style="margin-bottom:0;">
                <div class="stat-card"><h3>Name</h3><p style="font-size:22px;"><?= h($voter['first_name'] . ' ' . $voter['last_name']) ?></p></div>
                <div class="stat-card"><h3>Precinct</h3><p style="font-size:22px;"><?= h($voter['precinct_number']) ?></p></div>
                <div class="stat-card"><h3>Registration</h3><p style="font-size:18px;"><?= badge($voter['registration_status']) ?></p></div>
            </div>
        <?php else: ?>
            <div class="alert alert-error">No voter profile found for this account.</div>
        <?php endif; ?>
    </div>

    <div class="card public-hero">
        <h2>Election Status</h2>
        <?php if ($election): ?>
            <h2 style="font-size:30px;margin:12px 0 6px;"><?= h($election['election_name']) ?></h2>
            <p>Status: <?= badge($election['status']) ?></p>
            <p>Your voting status: <?= $alreadyVoted ? badge('Voted') : badge('Not Yet Voted') ?></p>
            <?php if ($voter && $voter['registration_status']==='approved' && !$alreadyVoted): ?>
                <a class="btn btn-light" href="<?= BASE_URL ?>/voter/ballot.php">Proceed to Ballot</a>
            <?php elseif ($alreadyVoted): ?>
                <a class="btn btn-light" href="<?= BASE_URL ?>/voter/confirmation.php">View Confirmation</a>
            <?php endif; ?>
        <?php else: ?>
            <p>No open election right now. Please wait for the election officer or admin to open voting.</p>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <h2>Voting Reminders</h2>
    <div class="flow-steps">
        <span class="flow-step active">1. Vote only once</span>
        <span class="flow-step">2. Review choices</span>
        <span class="flow-step">3. Submit final vote</span>
    </div>
    <p class="mb-0">Once your vote is submitted, it is locked and cannot be edited. Please review carefully before final submission.</p>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
