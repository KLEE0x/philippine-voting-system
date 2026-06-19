<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['admin', 'election_officer']);
$pageTitle = 'Add Candidate';
$elections = fetchElections($pdo);
$positions = $pdo->query("SELECT p.*, e.election_name FROM positions p INNER JOIN elections e ON p.election_id=e.election_id WHERE p.status='active' ORDER BY e.election_id DESC, p.position_name")->fetchAll();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $election_id = (int)$_POST['election_id'];
    $position_id = (int)$_POST['position_id'];
    $name = trim($_POST['candidate_name'] ?? '');
    $party = trim($_POST['party_name'] ?? 'Independent');
    $platform = trim($_POST['platform'] ?? '');
    if ($name === '') { $error = 'Candidate name is required.'; }
    else {
        $stmt = $pdo->prepare('INSERT INTO candidates (election_id, position_id, candidate_name, party_name, platform, status) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$election_id, $position_id, $name, $party, $platform, 'active']);
        logAction($pdo, $_SESSION['user_id'], 'ADD_CANDIDATE', 'Added candidate: ' . $name);
        redirect('/admin/candidates.php');
    }
}
include __DIR__ . '/../includes/header.php'; include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header"><h1>Add Candidate</h1></div>
<div class="card">
<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<form method="POST"><div class="form-grid">
<div class="form-group"><label>Election</label><select name="election_id" required><?php foreach ($elections as $e): ?><option value="<?= $e['election_id'] ?>"><?= h($e['election_name']) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Position</label><select name="position_id" required><?php foreach ($positions as $p): ?><option value="<?= $p['position_id'] ?>"><?= h($p['election_name'] . ' - ' . $p['position_name']) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Candidate Name</label><input type="text" name="candidate_name" required></div>
<div class="form-group"><label>Party Name</label><input type="text" name="party_name" value="Independent"></div>
</div><div class="form-group"><label>Platform</label><textarea name="platform"></textarea></div>
<button type="submit">Save Candidate</button> <a class="btn btn-light" href="<?= BASE_URL ?>/admin/candidates.php">Cancel</a></form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
