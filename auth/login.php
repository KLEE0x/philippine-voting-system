<?php
require_once __DIR__ . '/../includes/functions.php';

if (!empty($_SESSION['user_id'])) {
    redirectByRole();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter username and password.';
    } else {
        $stmt = $pdo->prepare('SELECT u.*, r.role_name FROM users u INNER JOIN roles r ON u.role_id = r.role_id WHERE u.username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && $user['status'] === 'active' && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_name'] = $user['role_name'];
            logAction($pdo, $user['user_id'], 'LOGIN', 'User logged in.');
            redirectByRole();
        } else {
            $error = 'Invalid login or inactive account.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — VOTEHUB Philippine Voting System</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="login-body">
    <section class="login-hero">
        <div class="pvms-logo">
            <div class="logo-mark">VH</div>
            <div>
                <div class="logo-title">VOTEHUB</div>
                <div class="logo-caption" style="color:rgba(255,255,255,.62);">Secure. Transparent. Organized.</div>
            </div>
        </div>
        <div>
            <div class="hero-kicker">Authorized access only</div>
            <h1>Secure access for election users and watchers.</h1>
            <p>VOTEHUB separates admin, election officer, voter, and auditor workflows so every action is organized, controlled, and recorded.</p>
            <div class="security-grid">
                <div class="security-card"><strong>✓ One vote per voter</strong><span>Duplicate voting prevention is enforced by the system.</span></div>
                <div class="security-card"><strong>☑ Full audit log</strong><span>Important actions are stored for monitoring.</span></div>
                <div class="security-card"><strong>◉ Role access</strong><span>Users only access pages allowed for their role.</span></div>
                <div class="security-card"><strong>▣ Result monitoring</strong><span>Admins and auditors can review counts and turnout.</span></div>
            </div>
        </div>
        <div style="font-size:12px;color:rgba(255,255,255,.54);">Educational demo only. Not affiliated with COMELEC.</div>
    </section>

    <section class="login-panel-wrap">
        <div class="login-card">
            <div class="pvms-logo">
                <div class="logo-mark">VH</div>
                <div>
                    <div class="logo-title">Sign in to VOTEHUB</div>
                    <div class="logo-caption">Enter your credentials to continue</div>
                </div>
            </div>

            <h1>Welcome back</h1>
            <p>Access your voting dashboard based on your assigned role.</p>

            <div class="role-pills">
                <span>Admin</span><span>Election Officer</span><span>Voter</span><span>Auditor</span>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= h($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required autofocus placeholder="Enter username">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Enter password">
                </div>
                <button type="submit" style="width:100%;">Sign in →</button>
            </form>

            <div class="login-links">
                <a class="btn btn-light" href="<?= BASE_URL ?>/index.php">Back Home</a>
                <a class="btn btn-light" href="<?= BASE_URL ?>/public/results.php">Public Results</a>
            </div>

            <div class="alert alert-info" style="margin-top:18px;">
                <strong>Default accounts:</strong><br>
                admin / admin123 · officer / officer123<br>
                auditor / auditor123 · voter1 / voter123
            </div>
        </div>
    </section>
</body>
</html>
