<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['admin']);
$pageTitle = 'Innovation Features';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';

$features = [
    ['icon' => '▣', 'title' => 'QR Code Voter Verification', 'desc' => 'Generate a QR code per voter for faster identity checking before ballot access.'],
    ['icon' => '◉', 'title' => 'Biometric Verification', 'desc' => 'Optional fingerprint or face verification for stronger voter validation.'],
    ['icon' => '✉', 'title' => 'SMS / Email Confirmation', 'desc' => 'Send a confirmation notice after a voter successfully submits a ballot.'],
    ['icon' => '◆', 'title' => 'Blockchain Audit Trail', 'desc' => 'Create tamper-resistant vote submission records for transparent auditing.'],
    ['icon' => '↗', 'title' => 'Real-Time Results Dashboard', 'desc' => 'Display turnout and vote count trends through live visual charts.'],
    ['icon' => '⌖', 'title' => 'Precinct Map', 'desc' => 'Show precinct locations and turnout performance through an interactive map.'],
    ['icon' => '⇄', 'title' => 'Transmission Simulation', 'desc' => 'Simulate COMELEC-style precinct-to-canvassing result transmission for demo purposes.'],
    ['icon' => '▦', 'title' => 'Election Machine Simulation', 'desc' => 'Create a mock voting machine flow for scanning, counting, and printing election returns.'],
    ['icon' => '☑', 'title' => 'Fraud Detection Dashboard', 'desc' => 'Flag suspicious login attempts, duplicate activity, and abnormal voting patterns.']
];
?>
<div class="page-header">
    <div>
        <h1>Innovation Features</h1>
        <p>Future enhancements for a more advanced voting system demonstration.</p>
    </div>
</div>

<div class="feature-grid">
    <?php foreach ($features as $feature): ?>
        <div class="feature-card">
            <div class="feature-icon"><?= h($feature['icon']) ?></div>
            <h3><?= h($feature['title']) ?></h3>
            <p><?= h($feature['desc']) ?></p>
            <?= badge('Future Enhancement') ?>
        </div>
    <?php endforeach; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
