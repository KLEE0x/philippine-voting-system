<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['admin', 'election_officer']);
$pageTitle = 'Voters';
$voters = $pdo->query('SELECT v.*, u.username, u.status AS account_status, p.precinct_number FROM voters v INNER JOIN users u ON v.user_id=u.user_id INNER JOIN precincts p ON v.precinct_id=p.precinct_id ORDER BY v.voter_id DESC')->fetchAll();
include __DIR__ . '/../includes/header.php'; include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header"><h1>Voters</h1><a class="btn" href="<?= BASE_URL ?>/admin/add_voter.php">Add Voter</a></div>
<div class="card table-wrap"><table>
<tr><th>ID</th><th>Name</th><th>Username</th><th>Precinct</th><th>Address</th><th>Registration</th><th>Account</th><th>Action</th></tr>
<?php foreach ($voters as $v): ?>
<tr>
<td><?= h($v['voter_id']) ?></td><td><?= h($v['first_name'] . ' ' . $v['last_name']) ?></td><td><?= h($v['username']) ?></td><td><?= h($v['precinct_number']) ?></td><td><?= h($v['barangay'] . ', ' . $v['city']) ?></td><td><?= badge($v['registration_status']) ?></td><td><?= badge($v['account_status']) ?></td><td><a class="btn btn-light" href="<?= BASE_URL ?>/admin/edit_voter.php?id=<?= $v['voter_id'] ?>">Edit</a></td>
</tr>
<?php endforeach; ?>
</table></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
