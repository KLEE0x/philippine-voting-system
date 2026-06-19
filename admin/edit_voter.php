<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['admin', 'election_officer']);
$pageTitle = 'Edit Voter';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT v.*, u.username, u.status AS account_status FROM voters v INNER JOIN users u ON v.user_id=u.user_id WHERE v.voter_id=?');
$stmt->execute([$id]);
$voter = $stmt->fetch();
if (!$voter) { redirect('/admin/voters.php'); }
$precincts = $pdo->query('SELECT * FROM precincts ORDER BY city, barangay, precinct_number')->fetchAll();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $birthdate = $_POST['birthdate'] ?? '';
    $gender = $_POST['gender'] ?? 'Other';
    $address = trim($_POST['address'] ?? '');
    $barangay = trim($_POST['barangay'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $precinct_id = (int)$_POST['precinct_id'];
    $registration_status = $_POST['registration_status'] ?? 'pending';
    $account_status = $_POST['account_status'] ?? 'active';
    if ($first_name === '' || $last_name === '' || $birthdate === '' || $address === '' || $barangay === '' || $city === '') { $error = 'All fields are required.'; }
    else {
        $stmt = $pdo->prepare('UPDATE voters SET precinct_id=?, first_name=?, last_name=?, birthdate=?, gender=?, address=?, barangay=?, city=?, registration_status=? WHERE voter_id=?');
        $stmt->execute([$precinct_id, $first_name, $last_name, $birthdate, $gender, $address, $barangay, $city, $registration_status, $id]);
        $stmt = $pdo->prepare('UPDATE users SET status=? WHERE user_id=?');
        $stmt->execute([$account_status, $voter['user_id']]);
        logAction($pdo, $_SESSION['user_id'], 'EDIT_VOTER', 'Edited voter: ' . $first_name . ' ' . $last_name);
        redirect('/admin/voters.php');
    }
}
include __DIR__ . '/../includes/header.php'; include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header"><h1>Edit Voter</h1></div>
<div class="card"><?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<form method="POST"><div class="form-grid">
<div class="form-group"><label>Username</label><input type="text" value="<?= h($voter['username']) ?>" disabled></div>
<div class="form-group"><label>First Name</label><input type="text" name="first_name" value="<?= h($voter['first_name']) ?>" required></div>
<div class="form-group"><label>Last Name</label><input type="text" name="last_name" value="<?= h($voter['last_name']) ?>" required></div>
<div class="form-group"><label>Birthdate</label><input type="date" name="birthdate" value="<?= h($voter['birthdate']) ?>" required></div>
<div class="form-group"><label>Gender</label><select name="gender"><option <?= $voter['gender']==='Male'?'selected':'' ?>>Male</option><option <?= $voter['gender']==='Female'?'selected':'' ?>>Female</option><option <?= $voter['gender']==='Other'?'selected':'' ?>>Other</option></select></div>
<div class="form-group"><label>Address</label><input type="text" name="address" value="<?= h($voter['address']) ?>" required></div>
<div class="form-group"><label>Barangay</label><input type="text" name="barangay" value="<?= h($voter['barangay']) ?>" required></div>
<div class="form-group"><label>City</label><input type="text" name="city" value="<?= h($voter['city']) ?>" required></div>
<div class="form-group"><label>Precinct</label><select name="precinct_id"><?php foreach ($precincts as $p): ?><option value="<?= $p['precinct_id'] ?>" <?= $p['precinct_id']==$voter['precinct_id']?'selected':'' ?>><?= h($p['precinct_number'] . ' - ' . $p['barangay'] . ', ' . $p['city']) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Registration Status</label><select name="registration_status"><option value="pending" <?= $voter['registration_status']==='pending'?'selected':'' ?>>Pending</option><option value="approved" <?= $voter['registration_status']==='approved'?'selected':'' ?>>Approved</option><option value="rejected" <?= $voter['registration_status']==='rejected'?'selected':'' ?>>Rejected</option></select></div>
<div class="form-group"><label>Account Status</label><select name="account_status"><option value="active" <?= $voter['account_status']==='active'?'selected':'' ?>>Active</option><option value="inactive" <?= $voter['account_status']==='inactive'?'selected':'' ?>>Inactive</option></select></div>
</div><button type="submit">Update Voter</button> <a class="btn btn-light" href="<?= BASE_URL ?>/admin/voters.php">Cancel</a></form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
