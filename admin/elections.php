<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['admin']);
$pageTitle = 'Elections';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $election_id = (int)$_POST['election_id'];
    $status = $_POST['status'];
    if (in_array($status, ['upcoming','open','closed'], true)) {
        $stmt = $pdo->prepare('UPDATE elections SET status = ? WHERE election_id = ?');
        $stmt->execute([$status, $election_id]);
        logAction($pdo, $_SESSION['user_id'], 'UPDATE_ELECTION_STATUS', 'Changed election status to ' . $status . '.');
    }
    redirect('/admin/elections.php');
}

$elections = fetchElections($pdo);
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header">
    <h1>Elections</h1>
    <a class="btn" href="<?= BASE_URL ?>/admin/add_election.php">Add Election</a>
</div>
<div class="card table-wrap">
    <table>
        <tr><th>ID</th><th>Name</th><th>Type</th><th>Start</th><th>End</th><th>Status</th><th>Action</th></tr>
        <?php foreach ($elections as $election): ?>
            <tr>
                <td><?= h($election['election_id']) ?></td>
                <td><?= h($election['election_name']) ?></td>
                <td><?= h($election['election_type']) ?></td>
                <td><?= h($election['start_datetime']) ?></td>
                <td><?= h($election['end_datetime']) ?></td>
                <td><?= badge($election['status']) ?></td>
                <td class="actions">
                    <a class="btn btn-light" href="<?= BASE_URL ?>/admin/edit_election.php?id=<?= $election['election_id'] ?>">Edit</a>
                    <form method="POST">
                        <input type="hidden" name="election_id" value="<?= $election['election_id'] ?>">
                        <select name="status" onchange="this.form.submit()">
                            <option value="upcoming" <?= $election['status'] === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
                            <option value="open" <?= $election['status'] === 'open' ? 'selected' : '' ?>>Open</option>
                            <option value="closed" <?= $election['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                        </select>
                        <input type="hidden" name="update_status" value="1">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
