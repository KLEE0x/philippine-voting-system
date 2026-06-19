<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['admin']);
$pageTitle = 'Add Election';
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
        $stmt = $pdo->prepare('INSERT INTO elections (election_name, election_type, start_datetime, end_datetime, status) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $type, $start, $end, $status]);
        logAction($pdo, $_SESSION['user_id'], 'ADD_ELECTION', 'Added election: ' . $name);
        redirect('/admin/elections.php');
    }
}
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header"><h1>Add Election</h1></div>
<div class="card">
    <?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
    <form method="POST">
        <div class="form-grid">
            <div class="form-group"><label>Election Name</label><input type="text" name="election_name" required></div>
            <div class="form-group"><label>Election Type</label><input type="text" name="election_type" placeholder="Local Election" required></div>
            <div class="form-group"><label>Start Date/Time</label><input type="datetime-local" name="start_datetime" required></div>
            <div class="form-group"><label>End Date/Time</label><input type="datetime-local" name="end_datetime" required></div>
            <div class="form-group"><label>Status</label><select name="status"><option value="upcoming">Upcoming</option><option value="open">Open</option><option value="closed">Closed</option></select></div>
        </div>
        <button type="submit">Save Election</button>
        <a class="btn btn-light" href="<?= BASE_URL ?>/admin/elections.php">Cancel</a>
    </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
