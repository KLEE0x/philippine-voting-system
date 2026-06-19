<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['admin', 'election_officer']);
$pageTitle = 'Add Position';
$elections = fetchElections($pdo);
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $election_id = (int)$_POST['election_id'];
    $position_name = trim($_POST['position_name'] ?? '');
    $max_vote = max(1, (int)($_POST['max_vote'] ?? 1));
    if ($position_name === '') { $error = 'Position name is required.'; }
    else {
        $stmt = $pdo->prepare('INSERT INTO positions (election_id, position_name, max_vote, status) VALUES (?, ?, ?, ?)');
        $stmt->execute([$election_id, $position_name, $max_vote, 'active']);
        logAction($pdo, $_SESSION['user_id'], 'ADD_POSITION', 'Added position: ' . $position_name);
        redirect('/admin/positions.php');
    }
}
include __DIR__ . '/../includes/header.php'; include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header"><h1>Add Position</h1></div>
<div class="card">
<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<form method="POST">
<div class="form-grid">
<div class="form-group"><label>Election</label><select name="election_id" required><?php foreach ($elections as $e): ?><option value="<?= $e['election_id'] ?>"><?= h($e['election_name']) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Position Name</label><input type="text" name="position_name" required></div>
<div class="form-group"><label>Max Vote Allowed</label><input type="number" name="max_vote" min="1" value="1" required></div>
</div>
<button type="submit">Save Position</button> <a class="btn btn-light" href="<?= BASE_URL ?>/admin/positions.php">Cancel</a>
</form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
