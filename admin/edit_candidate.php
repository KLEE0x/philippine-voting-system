<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['admin', 'election_officer']);
$pageTitle = 'Edit Candidate';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM candidates WHERE candidate_id=?');
$stmt->execute([$id]);
$candidate = $stmt->fetch();
if (!$candidate) { redirect('/admin/candidates.php'); }
$elections = fetchElections($pdo);
$positions = $pdo->query("SELECT p.*, e.election_name FROM positions p INNER JOIN elections e ON p.election_id=e.election_id ORDER BY e.election_id DESC, p.position_name")->fetchAll();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $election_id = (int)$_POST['election_id'];
    $position_id = (int)$_POST['position_id'];
    $name = trim($_POST['candidate_name'] ?? '');
    $party = trim($_POST['party_name'] ?? 'Independent');
    $platform = trim($_POST['platform'] ?? '');
    $status = $_POST['status'] ?? 'active';
    if ($name === '') { $error = 'Candidate name is required.'; }
    else {
        $stmt = $pdo->prepare('UPDATE candidates SET election_id=?, position_id=?, candidate_name=?, party_name=?, platform=?, status=? WHERE candidate_id=?');
        $stmt->execute([$election_id, $position_id, $name, $party, $platform, $status, $id]);
        logAction($pdo, $_SESSION['user_id'], 'EDIT_CANDIDATE', 'Edited candidate: ' . $name);
        redirect('/admin/candidates.php');
    }
}
include __DIR__ . '/../includes/header.php'; include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header"><h1>Edit Candidate</h1></div>
<div class="card">
<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<form method="POST"><div class="form-grid">
<div class="form-group"><label>Election</label><select name="election_id"><?php foreach ($elections as $e): ?><option value="<?= $e['election_id'] ?>" <?= $e['election_id']==$candidate['election_id']?'selected':'' ?>><?= h($e['election_name']) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Position</label><select name="position_id"><?php foreach ($positions as $p): ?><option value="<?= $p['position_id'] ?>" <?= $p['position_id']==$candidate['position_id']?'selected':'' ?>><?= h($p['election_name'] . ' - ' . $p['position_name']) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Candidate Name</label><input type="text" name="candidate_name" value="<?= h($candidate['candidate_name']) ?>" required></div>
<div class="form-group"><label>Party Name</label><input type="text" name="party_name" value="<?= h($candidate['party_name']) ?>"></div>
<div class="form-group"><label>Status</label><select name="status"><option value="active" <?= $candidate['status']==='active'?'selected':'' ?>>Active</option><option value="inactive" <?= $candidate['status']==='inactive'?'selected':'' ?>>Inactive</option></select></div>
</div><div class="form-group"><label>Platform</label><textarea name="platform"><?= h($candidate['platform']) ?></textarea></div>
<button type="submit">Update Candidate</button> <a class="btn btn-light" href="<?= BASE_URL ?>/admin/candidates.php">Cancel</a></form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
