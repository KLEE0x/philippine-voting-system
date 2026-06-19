<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['admin']);
$pageTitle = 'Edit Election';
$id = (int)($_GET['id'] ?? 0);
$election = getElectionById($pdo, $id);
if (!$election) { redirect('/admin/elections.php'); }
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['election_name'] ?? '');
    $type = trim($_POST['election_type'] ?? '');
    $start = $_POST['start_datetime'] ?? '';
    $end = $_POST['end_datetime'] ?? '';
    $status = $_POST['status'] ?? 'upcoming';
    if ($name === '' || $type === '' || $start === '' || $end === '') {
        $error = 'All fields are required.';
    } else {
        $stmt = $pdo->prepare('UPDATE elections SET election_name=?, election_type=?, start_datetime=?, end_datetime=?, status=? WHERE election_id=?');
        $stmt->execute([$name, $type, $start, $end, $status, $id]);
        logAction($pdo, $_SESSION['user_id'], 'EDIT_ELECTION', 'Edited election: ' . $name);
        redirect('/admin/elections.php');
    }
}
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header"><h1>Edit Election</h1></div>
<div class="card">
    <?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
    <form method="POST">
        <div class="form-grid">
            <div class="form-group"><label>Election Name</label><input type="text" name="election_name" value="<?= h($election['election_name']) ?>" required></div>
            <div class="form-group"><label>Election Type</label><input type="text" name="election_type" value="<?= h($election['election_type']) ?>" required></div>
            <div class="form-group"><label>Start Date/Time</label><input type="datetime-local" name="start_datetime" value="<?= h(str_replace(' ', 'T', substr($election['start_datetime'], 0, 16))) ?>" required></div>
            <div class="form-group"><label>End Date/Time</label><input type="datetime-local" name="end_datetime" value="<?= h(str_replace(' ', 'T', substr($election['end_datetime'], 0, 16))) ?>" required></div>
            <div class="form-group"><label>Status</label><select name="status"><option value="upcoming" <?= $election['status']==='upcoming'?'selected':'' ?>>Upcoming</option><option value="open" <?= $election['status']==='open'?'selected':'' ?>>Open</option><option value="closed" <?= $election['status']==='closed'?'selected':'' ?>>Closed</option></select></div>
        </div>
        <button type="submit">Update Election</button>
        <a class="btn btn-light" href="<?= BASE_URL ?>/admin/elections.php">Cancel</a>
    </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
