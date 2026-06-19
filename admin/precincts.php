<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['admin', 'election_officer']);
$pageTitle = 'Precincts';
$precincts = $pdo->query('SELECT p.*, COUNT(v.voter_id) AS voter_count FROM precincts p LEFT JOIN voters v ON p.precinct_id=v.precinct_id GROUP BY p.precinct_id ORDER BY p.city, p.barangay, p.precinct_number')->fetchAll();
include __DIR__ . '/../includes/header.php'; include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header"><h1>Precincts</h1><a class="btn" href="<?= BASE_URL ?>/admin/add_precinct.php">Add Precinct</a></div>
<div class="card table-wrap"><table>
<tr><th>ID</th><th>Precinct Number</th><th>Barangay</th><th>City</th><th>Voters</th><th>Action</th></tr>
<?php foreach ($precincts as $p): ?>
<tr><td><?= h($p['precinct_id']) ?></td><td><?= h($p['precinct_number']) ?></td><td><?= h($p['barangay']) ?></td><td><?= h($p['city']) ?></td><td><?= h($p['voter_count']) ?></td><td><a class="btn btn-light" href="<?= BASE_URL ?>/admin/edit_precinct.php?id=<?= $p['precinct_id'] ?>">Edit</a></td></tr>
<?php endforeach; ?>
</table></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
