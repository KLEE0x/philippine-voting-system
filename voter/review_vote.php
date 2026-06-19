<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['voter']);
$pageTitle = 'Review Vote';
$review = $_SESSION['vote_review'] ?? null;
if (!$review) { redirect('/voter/ballot.php'); }
$voter = getVoterByUserId($pdo, $_SESSION['user_id']);
$election = getElectionById($pdo, $review['election_id']);
if (!$voter || !$election || $review['voter_id'] != $voter['voter_id'] || hasVoted($pdo, $voter['voter_id'], $election['election_id'])) {
    unset($_SESSION['vote_review']);
    redirect('/voter/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_vote'])) {
    try {
        $pdo->beginTransaction();
        $check = $pdo->prepare('SELECT has_voted FROM voter_election_status WHERE voter_id=? AND election_id=? FOR UPDATE');
        $check->execute([$voter['voter_id'], $election['election_id']]);
        $status = $check->fetch();
        if ($status && (int)$status['has_voted'] === 1) {
            throw new Exception('Duplicate voting is not allowed.');
        }
        foreach ($review['choices'] as $position_id => $candidate_ids) {
            foreach ($candidate_ids as $candidate_id) {
                $stmt = $pdo->prepare('INSERT INTO votes (election_id, voter_id, candidate_id, position_id) VALUES (?, ?, ?, ?)');
                $stmt->execute([$election['election_id'], $voter['voter_id'], $candidate_id, $position_id]);
            }
        }
        $stmt = $pdo->prepare('INSERT INTO voter_election_status (voter_id, election_id, has_voted, voted_at) VALUES (?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE has_voted=1, voted_at=NOW()');
        $stmt->execute([$voter['voter_id'], $election['election_id']]);
        logAction($pdo, $_SESSION['user_id'], 'VOTE_SUBMITTED', 'Voter submitted vote for election: ' . $election['election_name']);
        $pdo->commit();
        unset($_SESSION['vote_review']);
        redirect('/voter/confirmation.php');
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}
include __DIR__ . '/../includes/header.php'; include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header">
    <div>
        <h1>Review Your Vote</h1>
        <p>Please review your selected candidates. Once submitted, your vote cannot be changed.</p>
    </div>
</div>

<div class="flow-steps">
    <span class="flow-step">1. Select Candidates</span>
    <span class="flow-step active">2. Review Vote</span>
    <span class="flow-step">3. Confirmation</span>
</div>

<?php if (!empty($error)): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<div class="card public-hero">
    <h2><?= h($election['election_name']) ?></h2>
    <p>Please review carefully. The system will lock your vote immediately after final submission.</p>
</div>

<?php foreach ($review['choices'] as $position_id => $candidate_ids): ?>
<?php
$stmt = $pdo->prepare('SELECT position_name FROM positions WHERE position_id=?');
$stmt->execute([$position_id]);
$position_name = $stmt->fetchColumn();
$placeholders = implode(',', array_fill(0, count($candidate_ids), '?'));
$stmt = $pdo->prepare("SELECT candidate_name, party_name FROM candidates WHERE candidate_id IN ($placeholders)");
$stmt->execute($candidate_ids);
$candidates = $stmt->fetchAll();
?>
<div class="card">
    <h2><?= h($position_name) ?></h2>
    <ul class="review-list">
        <?php foreach ($candidates as $cand): ?>
            <li><strong><?= h($cand['candidate_name']) ?></strong> — <?= h($cand['party_name']) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endforeach; ?>

<div class="alert alert-warning">
    Final reminder: once you click confirm, your vote cannot be edited or resubmitted.
</div>
<form method="POST" class="confirm-form">
    <button class="btn-success" type="submit" name="confirm_vote">Confirm and Submit Vote</button>
    <a class="btn btn-light" href="<?= BASE_URL ?>/voter/ballot.php">Back to Ballot</a>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>
