<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['admin', 'election_officer']);
$pageTitle = 'Add Voter';
$precincts = $pdo->query('SELECT * FROM precincts ORDER BY city, barangay, precinct_number')->fetchAll();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $birthdate = $_POST['birthdate'] ?? '';
    $gender = $_POST['gender'] ?? 'Other';
    $address = trim($_POST['address'] ?? '');
    $barangay = trim($_POST['barangay'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $precinct_id = (int)($_POST['precinct_id'] ?? 0);
    $registration_status = $_POST['registration_status'] ?? 'pending';
    if ($username === '' || $password === '' || $first_name === '' || $last_name === '' || $birthdate === '' || $address === '' || $barangay === '' || $city === '' || $precinct_id <= 0) {
        $error = 'All fields are required.';
    } else {
        try {
            $pdo->beginTransaction();
            $roleStmt = $pdo->prepare("SELECT role_id FROM roles WHERE role_name='voter'");
            $roleStmt->execute();
            $role_id = $roleStmt->fetchColumn();
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, password, role_id, status) VALUES (?, ?, ?, ?)');
            $stmt->execute([$username, $hash, $role_id, 'active']);
            $user_id = $pdo->lastInsertId();
            $stmt = $pdo->prepare('INSERT INTO voters (user_id, precinct_id, first_name, last_name, birthdate, gender, address, barangay, city, registration_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$user_id, $precinct_id, $first_name, $last_name, $birthdate, $gender, $address, $barangay, $city, $registration_status]);
            $voter_id = $pdo->lastInsertId();
            $elections = $pdo->query('SELECT election_id FROM elections')->fetchAll();
            foreach ($elections as $e) {
                $stmt = $pdo->prepare('INSERT IGNORE INTO voter_election_status (voter_id, election_id, has_voted) VALUES (?, ?, 0)');
                $stmt->execute([$voter_id, $e['election_id']]);
            }
            logAction($pdo, $_SESSION['user_id'], 'ADD_VOTER', 'Added voter: ' . $first_name . ' ' . $last_name);
            $pdo->commit();
            redirect('/admin/voters.php');
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Failed to add voter. Username may already exist.';
        }
    }
}
include __DIR__ . '/../includes/header.php'; include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header"><h1>Add Voter</h1></div>
<div class="card"><?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<form method="POST"><div class="form-grid">
<div class="form-group"><label>Username</label><input type="text" name="username" required></div>
<div class="form-group"><label>Password</label><input type="password" name="password" required></div>
<div class="form-group"><label>First Name</label><input type="text" name="first_name" required></div>
<div class="form-group"><label>Last Name</label><input type="text" name="last_name" required></div>
<div class="form-group"><label>Birthdate</label><input type="date" name="birthdate" required></div>
<div class="form-group"><label>Gender</label><select name="gender"><option>Male</option><option>Female</option><option>Other</option></select></div>
<div class="form-group"><label>Address</label><input type="text" name="address" required></div>
<div class="form-group"><label>Barangay</label><input type="text" name="barangay" required></div>
<div class="form-group"><label>City</label><input type="text" name="city" required></div>
<div class="form-group"><label>Precinct</label><select name="precinct_id"><?php foreach ($precincts as $p): ?><option value="<?= $p['precinct_id'] ?>"><?= h($p['precinct_number'] . ' - ' . $p['barangay'] . ', ' . $p['city']) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Registration Status</label><select name="registration_status"><option value="pending">Pending</option><option value="approved">Approved</option><option value="rejected">Rejected</option></select></div>
</div><button type="submit">Save Voter</button> <a class="btn btn-light" href="<?= BASE_URL ?>/admin/voters.php">Cancel</a></form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
