<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['auditor']);
include __DIR__ . '/../admin/audit_logs.php';
?>
