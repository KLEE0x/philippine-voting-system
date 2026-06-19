<?php
require_once __DIR__ . '/includes/functions.php';
$isLoggedIn = !empty($_SESSION['user_id']);
$dashboardUrl = BASE_URL . '/auth/login.php';
if ($isLoggedIn) {
    $role = $_SESSION['role_name'] ?? '';
    if ($role === 'admin') { $dashboardUrl = BASE_URL . '/admin/dashboard.php'; }
    elseif ($role === 'election_officer') { $dashboardUrl = BASE_URL . '/officer/dashboard.php'; }
    elseif ($role === 'voter') { $dashboardUrl = BASE_URL . '/voter/dashboard.php'; }
    elseif ($role === 'auditor') { $dashboardUrl = BASE_URL . '/auditor/dashboard.php'; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOTEHUB — Philippine Voting System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="landing-body">
    <nav class="landing-nav">
        <div class="pvms-logo">
            <div class="logo-mark">VH</div>
            <div>
                <div class="logo-title">VOTEHUB</div>
                <div class="logo-caption">Secure. Transparent. Organized.</div>
            </div>
        </div>
        <div class="landing-actions">
            <a class="btn btn-light" href="<?= BASE_URL ?>/public/results.php">View Public Results</a>
            <a class="btn" href="<?= $dashboardUrl ?>"><?= $isLoggedIn ? 'Go to Dashboard' : 'Login' ?></a>
        </div>
    </nav>

    <section class="hero">
        <div>
            <div class="hero-kicker">Philippine-inspired voting system demo</div>
            <h1>Modern election management for secure school demonstrations.</h1>
            <p>
                A digital voting management platform designed to organize voter registration,
                candidate management, ballot access, vote counting, result monitoring, and audit logging.
            </p>
            <div class="hero-buttons">
                <a class="btn" href="<?= BASE_URL ?>/auth/login.php">Access System</a>
                <a class="btn btn-light" href="<?= BASE_URL ?>/public/results.php">Public Results</a>
            </div>
            <div class="hero-metrics">
                <div class="hero-metric"><strong>4</strong><span>User roles</span></div>
                <div class="hero-metric"><strong>1x</strong><span>Vote limit per voter</span></div>
                <div class="hero-metric"><strong>100%</strong><span>Audit-focused demo</span></div>
            </div>
        </div>

        <div class="hero-panel">
            <div class="panel-card">
                <div class="panel-head">
                    <div>
                        <strong>2026 Student Council Election</strong><br>
                        <small>Live election overview</small>
                    </div>
                    <span class="panel-status">● Open</span>
                </div>
                <div class="panel-stat-grid">
                    <div class="panel-stat"><span>Registered Voters</span><strong>1,250</strong></div>
                    <div class="panel-stat"><span>Votes Cast</span><strong>875</strong></div>
                    <div class="panel-stat"><span>Candidates</span><strong>42</strong></div>
                    <div class="panel-stat"><span>Positions</span><strong>12</strong></div>
                </div>
                <div style="margin-top:18px;">
                    <div class="inline-spread" style="color:rgba(255,255,255,.74);font-size:13px;margin-bottom:8px;">
                        <span>Voter Turnout</span><strong>74.15%</strong>
                    </div>
                    <div class="progress-line"><div class="progress-fill" style="width:74.15%;"></div></div>
                </div>
            </div>
        </div>
    </section>

    <section class="section center">
        <div class="section-kicker">Capabilities</div>
        <h2>Everything needed for a complete voting system demo.</h2>
        <p>Built for PHP, MySQL, XAMPP, and phpMyAdmin, while keeping advanced COMELEC-level features as future innovation modules.</p>
        <div class="landing-feature-grid">
            <?php
            $features = [
                ['✓', 'Voter Registration', 'Manage voter records, approval status, precinct assignment, and voting status.'],
                ['♛', 'Candidate Management', 'Organize candidates by election, position, party, and platform.'],
                ['◉', 'Secure Voting', 'Allow approved voters to access an active ballot and vote once only.'],
                ['▣', 'Automated Counting', 'Generate ranked results by candidate, position, and election.'],
                ['☑', 'Audit Trail', 'Track important system actions like login, updates, and vote submission.'],
                ['✦', 'Innovation Features', 'Show future QR, biometric, blockchain, transmission, and fraud detection ideas.']
            ];
            foreach ($features as $f): ?>
                <div class="landing-feature-card">
                    <div class="feature-icon"><?= h($f[0]) ?></div>
                    <h3><?= h($f[1]) ?></h3>
                    <p><?= h($f[2]) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <footer class="landing-footer">
        <div>© 2026 VOTEHUB · School Project</div>
        <div>This system is created for educational purposes only and is not an official COMELEC platform.</div>
    </footer>
</body>
</html>