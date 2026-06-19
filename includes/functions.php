<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect($path) {
    header('Location: ' . BASE_URL . $path);
    exit;
}

function requireLogin() {
    if (empty($_SESSION['user_id'])) {
        redirect('/auth/login.php');
    }
}

function requireRoles(array $roles) {
    requireLogin();
    if (!in_array($_SESSION['role_name'] ?? '', $roles, true)) {
        redirect('/index.php');
    }
}

function redirectByRole() {
    $role = $_SESSION['role_name'] ?? '';
    if ($role === 'admin') {
        redirect('/admin/dashboard.php');
    } elseif ($role === 'election_officer') {
        redirect('/officer/dashboard.php');
    } elseif ($role === 'voter') {
        redirect('/voter/dashboard.php');
    } elseif ($role === 'auditor') {
        redirect('/auditor/dashboard.php');
    }
    redirect('/auth/login.php');
}

function logAction(PDO $pdo, $user_id, $action, $description) {
    $stmt = $pdo->prepare('INSERT INTO audit_logs (user_id, action, description) VALUES (?, ?, ?)');
    $stmt->execute([$user_id ?: null, $action, $description]);
}

function countRows(PDO $pdo, $sql, array $params = []) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

function getActiveElection(PDO $pdo) {
    $stmt = $pdo->query("SELECT * FROM elections WHERE status = 'open' AND NOW() BETWEEN start_datetime AND end_datetime ORDER BY election_id DESC LIMIT 1");
    return $stmt->fetch();
}

function getElectionById(PDO $pdo, $election_id) {
    $stmt = $pdo->prepare('SELECT * FROM elections WHERE election_id = ?');
    $stmt->execute([$election_id]);
    return $stmt->fetch();
}

function getVoterByUserId(PDO $pdo, $user_id) {
    $stmt = $pdo->prepare('SELECT v.*, p.precinct_number FROM voters v LEFT JOIN precincts p ON v.precinct_id = p.precinct_id WHERE v.user_id = ?');
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

function hasVoted(PDO $pdo, $voter_id, $election_id) {
    $stmt = $pdo->prepare('SELECT has_voted FROM voter_election_status WHERE voter_id = ? AND election_id = ?');
    $stmt->execute([$voter_id, $election_id]);
    $status = $stmt->fetch();
    return $status && (int)$status['has_voted'] === 1;
}

function fetchElections(PDO $pdo) {
    return $pdo->query('SELECT * FROM elections ORDER BY election_id DESC')->fetchAll();
}

function badge($text) {
    $class = strtolower(str_replace(' ', '-', $text));
    return '<span class="badge badge-' . h($class) . '">' . h($text) . '</span>';
}
?>
