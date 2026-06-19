<?php
require_once __DIR__ . '/../includes/functions.php';
if (!empty($_SESSION['user_id'])) {
    logAction($pdo, $_SESSION['user_id'], 'LOGOUT', 'User logged out.');
}
session_destroy();
redirect('/auth/login.php');
?>
