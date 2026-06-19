<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['admin', 'auditor']);
$pageTitle = 'Audit Logs';
$logs = $pdo->query('SELECT a.*, u.username FROM audit_logs a LEFT JOIN users u ON a.user_id=u.user_id ORDER BY a.created_at DESC LIMIT 300')->fetchAll();
include __DIR__ . '/../includes/header.php'; include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header"><h1>Audit Logs</h1></div>
<div class="card table-wrap"><table>
<tr><th>ID</th><th>Date</th><th>User</th><th>Action</th><th>Description</th></tr>
<?php foreach ($logs as $log): ?>
<tr><td><?= h($log['log_id']) ?></td><td><?= h($log['created_at']) ?></td><td><?= h($log['username'] ?? 'System') ?></td><td><?= h($log['action']) ?></td><td><?= h($log['description']) ?></td></tr>
<?php endforeach; ?>
</table></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
