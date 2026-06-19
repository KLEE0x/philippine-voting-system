<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['admin', 'election_officer']);
$pageTitle = 'Positions';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle'])) {
    $id = (int)$_POST['position_id'];
    $status = $_POST['status'] === 'active' ? 'inactive' : 'active';
    $stmt = $pdo->prepare('UPDATE positions SET status=? WHERE position_id=?');
    $stmt->execute([$status, $id]);
    logAction($pdo, $_SESSION['user_id'], 'UPDATE_POSITION_STATUS', 'Changed position status.');
    redirect('/admin/positions.php');
}
$positions = $pdo->query('SELECT p.*, e.election_name FROM positions p INNER JOIN elections e ON p.election_id=e.election_id ORDER BY e.election_id DESC, p.position_id ASC')->fetchAll();
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header"><h1>Positions</h1><a class="btn" href="<?= BASE_URL ?>/admin/add_position.php">Add Position</a></div>
<div class="card table-wrap">
<table>
<tr><th>ID</th><th>Election</th><th>Position</th><th>Max Vote</th><th>Status</th><th>Action</th></tr>
<?php foreach ($positions as $pos): ?>
<tr>
<td><?= h($pos['position_id']) ?></td><td><?= h($pos['election_name']) ?></td><td><?= h($pos['position_name']) ?></td><td><?= h($pos['max_vote']) ?></td><td><?= badge($pos['status']) ?></td>
<td class="actions"><a class="btn btn-light" href="<?= BASE_URL ?>/admin/edit_position.php?id=<?= $pos['position_id'] ?>">Edit</a><form method="POST" class="confirm-form"><input type="hidden" name="position_id" value="<?= $pos['position_id'] ?>"><input type="hidden" name="status" value="<?= $pos['status'] ?>"><button name="toggle" type="submit"><?= $pos['status']==='active'?'Deactivate':'Activate' ?></button></form></td>
</tr>
<?php endforeach; ?>
</table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
