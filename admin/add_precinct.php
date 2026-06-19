<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['admin', 'election_officer']);
$pageTitle = 'Add Precinct';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $number = trim($_POST['precinct_number'] ?? '');
    $barangay = trim($_POST['barangay'] ?? '');
    $city = trim($_POST['city'] ?? '');
    if ($number === '' || $barangay === '' || $city === '') { $error = 'All fields are required.'; }
    else {
        $stmt = $pdo->prepare('INSERT INTO precincts (precinct_number, barangay, city) VALUES (?, ?, ?)');
        $stmt->execute([$number, $barangay, $city]);
        logAction($pdo, $_SESSION['user_id'], 'ADD_PRECINCT', 'Added precinct: ' . $number);
        redirect('/admin/precincts.php');
    }
}
include __DIR__ . '/../includes/header.php'; include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header"><h1>Add Precinct</h1></div>
<div class="card"><?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<form method="POST"><div class="form-grid"><div class="form-group"><label>Precinct Number</label><input type="text" name="precinct_number" required></div><div class="form-group"><label>Barangay</label><input type="text" name="barangay" required></div><div class="form-group"><label>City</label><input type="text" name="city" required></div></div><button type="submit">Save Precinct</button> <a class="btn btn-light" href="<?= BASE_URL ?>/admin/precincts.php">Cancel</a></form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
