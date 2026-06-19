<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['voter']);
$pageTitle = 'Official Ballot';
$voter = getVoterByUserId($pdo, $_SESSION['user_id']);
$election = getActiveElection($pdo);
$error = '';

if (!$voter || $voter['registration_status'] !== 'approved') {
    $error = 'Only approved voters can vote.';
} elseif (!$election) {
    $error = 'There is no open election right now.';
} elseif (hasVoted($pdo, $voter['voter_id'], $election['election_id'])) {
    redirect('/voter/confirmation.php');
}

$positions = [];
if (!$error) {
    $stmt = $pdo->prepare("SELECT * FROM positions WHERE election_id=? AND status='active' ORDER BY position_id");
    $stmt->execute([$election['election_id']]);
    $positions = $stmt->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $choices = $_POST['choices'] ?? [];
    $validated = [];
    $totalSelected = 0;

    foreach ($positions as $pos) {
        $position_id = (int)$pos['position_id'];
        $selected = $choices[$position_id] ?? [];
        if (!is_array($selected)) {
            $selected = [$selected];
        }
        $selected = array_filter(array_map('intval', $selected));
        $selected = array_values(array_unique($selected));
        if (count($selected) > (int)$pos['max_vote']) {
            $error = 'You selected too many candidates for ' . $pos['position_name'] . '.';
            break;
        }
        foreach ($selected as $candidate_id) {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM candidates WHERE candidate_id=? AND election_id=? AND position_id=? AND status="active"');
            $stmt->execute([$candidate_id, $election['election_id'], $position_id]);
            if ((int)$stmt->fetchColumn() !== 1) {
                $error = 'Invalid candidate selection detected.';
                break 2;
            }
        }
        if ($selected) {
            $validated[$position_id] = $selected;
            $totalSelected += count($selected);
        }
    }

    if (!$error && $totalSelected === 0) {
        $error = 'Please select at least one candidate.';
    }

    if (!$error) {
        $_SESSION['vote_review'] = [
            'election_id' => $election['election_id'],
            'voter_id' => $voter['voter_id'],
            'choices' => $validated
        ];
        redirect('/voter/review_vote.php');
    }
}

include __DIR__ . '/../includes/header.php'; include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header">
    <div>
        <h1>Official Ballot</h1>
        <p>Choose your candidates carefully. You will review your selections before final submission.</p>
    </div>
</div>

<div class="flow-steps">
    <span class="flow-step active">1. Select Candidates</span>
    <span class="flow-step">2. Review Vote</span>
    <span class="flow-step">3. Confirmation</span>
</div>

<?php if ($error): ?>
<div class="alert alert-error"><?= h($error) ?></div>
<?php else: ?>
<div class="card public-hero">
    <h2><?= h($election['election_name']) ?></h2>
    <p>Select your candidate/s. You may leave a position blank, but you must select at least one candidate before submitting.</p>
</div>
<form method="POST">
<?php foreach ($positions as $pos): ?>
<div class="ballot-position" data-max-selection="<?= h($pos['max_vote']) ?>">
    <div class="inline-spread">
        <div>
            <h2><?= h($pos['position_name']) ?></h2>
            <p>Maximum allowed vote/s: <strong><?= h($pos['max_vote']) ?></strong></p>
        </div>
        <?= badge(((int)$pos['max_vote'] === 1 ? 'Choose 1' : 'Choose up to ' . $pos['max_vote'])) ?>
    </div>
    <div class="alert alert-warning selection-warning">You reached the maximum allowed selections for this position.</div>
<?php
$stmt = $pdo->prepare("SELECT * FROM candidates WHERE election_id=? AND position_id=? AND status='active' ORDER BY candidate_name");
$stmt->execute([$election['election_id'], $pos['position_id']]);
$candidates = $stmt->fetchAll();
$inputType = (int)$pos['max_vote'] === 1 ? 'radio' : 'checkbox';
$fieldName = 'choices[' . $pos['position_id'] . ']' . ($inputType === 'checkbox' ? '[]' : '');
?>
<?php foreach ($candidates as $cand): ?>
<label class="candidate-option">
    <input type="<?= $inputType ?>" name="<?= h($fieldName) ?>" value="<?= $cand['candidate_id'] ?>">
    <div class="candidate-card-info">
        <strong><?= h($cand['candidate_name']) ?></strong>
        <span><?= h($cand['party_name']) ?></span>
        <small><?= h($cand['platform']) ?></small>
    </div>
</label>
<?php endforeach; ?>
</div>
<?php endforeach; ?>
<button type="submit">Review Vote →</button>
</form>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
