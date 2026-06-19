<?php
require_once __DIR__ . '/../includes/functions.php';
$pageTitle = 'Public Results';
$stmt = $pdo->query("SELECT * FROM elections WHERE status='closed' ORDER BY election_id DESC LIMIT 1");
$selectedElection = $stmt->fetch();
$positions = [];
if ($selectedElection) {
    $stmt = $pdo->prepare('SELECT * FROM positions WHERE election_id=? ORDER BY position_id');
    $stmt->execute([$selectedElection['election_id']]);
    $positions = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Public Results — PVMS</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="public-wrap">
    <div class="page-header">
        <div>
            <h1>Public Election Results</h1>
            <p>Transparent public view for closed elections only.</p>
        </div>
        <a class="btn btn-light" href="<?= BASE_URL ?>/index.php">Back to System</a>
    </div>

<?php if (!$selectedElection): ?>
    <div class="card public-hero">
        <h2>No closed election results yet</h2>
        <p>Public results are available only after an election is officially closed.</p>
    </div>
<?php else: ?>
    <div class="card public-hero">
        <div class="inline-spread">
            <div>
                <h2><?= h($selectedElection['election_name']) ?></h2>
                <p>Displayed results are for school project demonstration only.</p>
            </div>
            <?= badge($selectedElection['status']) ?>
        </div>
    </div>
<?php foreach ($positions as $pos): ?>
    <div class="card">
        <h2><?= h($pos['position_name']) ?></h2>
        <div class="table-wrap">
            <table>
                <tr><th>Rank</th><th>Candidate</th><th>Party</th><th>Votes</th><th>Visual</th></tr>
<?php
$stmt = $pdo->prepare('SELECT c.candidate_name, c.party_name, COUNT(v.vote_id) AS total_votes FROM candidates c LEFT JOIN votes v ON c.candidate_id=v.candidate_id AND v.election_id=? WHERE c.election_id=? AND c.position_id=? GROUP BY c.candidate_id ORDER BY total_votes DESC, c.candidate_name ASC');
$stmt->execute([$selectedElection['election_id'], $selectedElection['election_id'], $pos['position_id']]);
$cands = $stmt->fetchAll();
$maxVotes = 0;
foreach ($cands as $temp) { $maxVotes = max($maxVotes, (int)$temp['total_votes']); }
$rank = 1;
foreach ($cands as $cand):
    $percent = $maxVotes > 0 ? round(((int)$cand['total_votes'] / $maxVotes) * 100) : 0;
?>
                <tr>
                    <td><?= $rank++ ?></td>
                    <td><strong><?= h($cand['candidate_name']) ?></strong></td>
                    <td><?= h($cand['party_name']) ?></td>
                    <td><?= h($cand['total_votes']) ?></td>
                    <td><div class="result-bar"><div class="result-bar-track"><div class="result-bar-fill" style="width:<?= $percent ?>%;"></div></div><small><?= $percent ?>%</small></div></td>
                </tr>
<?php endforeach; ?>
            </table>
        </div>
    </div>
<?php endforeach; ?>
<?php endif; ?>
</div>
</body>
</html>
