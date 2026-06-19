<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['admin', 'election_officer']);
$pageTitle = 'Edit Precinct';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM precincts WHERE precinct_id=?');
$stmt->execute([$id]);
$precinct = $stmt->fetch();
if (!$precinct) { redirect('/admin/precincts.php'); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $number = trim($_POST['precinct_number'] ?? '');
    $barangay = trim($_POST['barangay'] ?? '');
    $city = trim($_POST['city'] ?? '');
    if ($number === '' || $barangay === '' || $city === '') { $error = 'All fields are required.'; }
    else {
        $stmt = $pdo->prepare('UPDATE precincts SET precinct_number=?, barangay=?, city=? WHERE precinct_id=?');
        $stmt->execute([$number, $barangay, $city, $id]);
        logAction($pdo, $_SESSION['user_id'], 'EDIT_PRECINCT', 'Edited precinct: ' . $number);
        redirect('/admin/precincts.php');
    }
}
include __DIR__ . '/../includes/header.php'; include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header"><h1>Edit Precinct</h1></div>
<div class="card"><?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<form method="POST"><div class="form-grid"><div class="form-group"><label>Precinct Number</label><input type="text" name="precinct_number" value="<?= h($precinct['precinct_number']) ?>" required></div><div class="form-group"><label>Barangay</label><input type="text" name="barangay" value="<?= h($precinct['barangay']) ?>" required></div><div class="form-group"><label>City</label><input type="text" name="city" value="<?= h($precinct['city']) ?>" required></div></div><button type="submit">Update Precinct</button> <a class="btn btn-light" href="<?= BASE_URL ?>/admin/precincts.php">Cancel</a></form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
