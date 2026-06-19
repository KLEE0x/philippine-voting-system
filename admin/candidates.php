<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['admin', 'election_officer']);
$pageTitle = 'Candidates';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle'])) {
    $id = (int)$_POST['candidate_id'];
    $status = $_POST['status'] === 'active' ? 'inactive' : 'active';
    $stmt = $pdo->prepare('UPDATE candidates SET status=? WHERE candidate_id=?');
    $stmt->execute([$status, $id]);
    logAction($pdo, $_SESSION['user_id'], 'UPDATE_CANDIDATE_STATUS', 'Changed candidate status.');
    redirect('/admin/candidates.php');
}
$candidates = $pdo->query('SELECT c.*, e.election_name, p.position_name FROM candidates c INNER JOIN elections e ON c.election_id=e.election_id INNER JOIN positions p ON c.position_id=p.position_id ORDER BY e.election_id DESC, p.position_name, c.candidate_name')->fetchAll();
include __DIR__ . '/../includes/header.php'; include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header"><h1>Candidates</h1><a class="btn" href="<?= BASE_URL ?>/admin/add_candidate.php">Add Candidate</a></div>
<div class="card table-wrap"><table>
<tr><th>ID</th><th>Candidate</th><th>Party</th><th>Election</th><th>Position</th><th>Status</th><th>Action</th></tr>
<?php foreach ($candidates as $c): ?>
<tr>
<td><?= h($c['candidate_id']) ?></td><td><strong><?= h($c['candidate_name']) ?></strong><br><small><?= h($c['platform']) ?></small></td><td><?= h($c['party_name']) ?></td><td><?= h($c['election_name']) ?></td><td><?= h($c['position_name']) ?></td><td><?= badge($c['status']) ?></td>
<td class="actions"><a class="btn btn-light" href="<?= BASE_URL ?>/admin/edit_candidate.php?id=<?= $c['candidate_id'] ?>">Edit</a><form method="POST" class="confirm-form"><input type="hidden" name="candidate_id" value="<?= $c['candidate_id'] ?>"><input type="hidden" name="status" value="<?= $c['status'] ?>"><button type="submit" name="toggle"><?= $c['status']==='active'?'Deactivate':'Activate' ?></button></form></td>
</tr>
<?php endforeach; ?>
</table></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
