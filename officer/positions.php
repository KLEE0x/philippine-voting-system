<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['election_officer']);
redirect('/admin/positions.php');
?>
