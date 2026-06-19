<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['admin', 'election_officer']);
$pageTitle = 'Edit Position';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM positions WHERE position_id=?');
$stmt->execute([$id]);
$position = $stmt->fetch();
if (!$position) { redirect('/admin/positions.php'); }
$elections = fetchElections($pdo);
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $election_id = (int)$_POST['election_id'];
    $position_name = trim($_POST['position_name'] ?? '');
    $max_vote = max(1, (int)($_POST['max_vote'] ?? 1));
    $status = $_POST['status'] ?? 'active';
    if ($position_name === '') { $error = 'Position name is required.'; }
    else {
        $stmt = $pdo->prepare('UPDATE positions SET election_id=?, position_name=?, max_vote=?, status=? WHERE position_id=?');
        $stmt->execute([$election_id, $position_name, $max_vote, $status, $id]);
        logAction($pdo, $_SESSION['user_id'], 'EDIT_POSITION', 'Edited position: ' . $position_name);
        redirect('/admin/positions.php');
    }
}
include __DIR__ . '/../includes/header.php'; include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header"><h1>Edit Position</h1></div>
<div class="card">
<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<form method="POST"><div class="form-grid">
<div class="form-group"><label>Election</label><select name="election_id"><?php foreach ($elections as $e): ?><option value="<?= $e['election_id'] ?>" <?= $e['election_id']==$position['election_id']?'selected':'' ?>><?= h($e['election_name']) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Position Name</label><input type="text" name="position_name" value="<?= h($position['position_name']) ?>" required></div>
<div class="form-group"><label>Max Vote</label><input type="number" min="1" name="max_vote" value="<?= h($position['max_vote']) ?>" required></div>
<div class="form-group"><label>Status</label><select name="status"><option value="active" <?= $position['status']==='active'?'selected':'' ?>>Active</option><option value="inactive" <?= $position['status']==='inactive'?'selected':'' ?>>Inactive</option></select></div>
</div><button type="submit">Update Position</button> <a class="btn btn-light" href="<?= BASE_URL ?>/admin/positions.php">Cancel</a></form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
