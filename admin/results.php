<?php
require_once __DIR__ . '/../includes/functions.php';
requireRoles(['admin', 'election_officer', 'auditor']);
$pageTitle = 'Election Results';
$elections = fetchElections($pdo);
$selectedElectionId = (int)($_GET['election_id'] ?? ($elections[0]['election_id'] ?? 0));
$selectedElection = $selectedElectionId ? getElectionById($pdo, $selectedElectionId) : null;
$approvedVoters = countRows($pdo, "SELECT COUNT(*) FROM voters WHERE registration_status='approved'");
$votedCount = $selectedElectionId ? countRows($pdo, 'SELECT COUNT(*) FROM voter_election_status WHERE election_id=? AND has_voted=1', [$selectedElectionId]) : 0;
$turnout = $approvedVoters > 0 ? round(($votedCount / $approvedVoters) * 100, 2) : 0;
$positions = [];
if ($selectedElectionId) {
    $stmt = $pdo->prepare("SELECT * FROM positions WHERE election_id=? ORDER BY position_id");
    $stmt->execute([$selectedElectionId]);
    $positions = $stmt->fetchAll();
}
include __DIR__ . '/../includes/header.php'; include __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header">
    <div>
        <h1>Election Results</h1>
        <p>Review ranked candidates, vote counts, turnout, and winning positions.</p>
    </div>
    <button onclick="window.print()">Print Results</button>
</div>

<div class="card">
    <form method="GET" class="form-grid">
        <div class="form-group">
            <label>Select Election</label>
            <select name="election_id" onchange="this.form.submit()">
                <?php foreach ($elections as $e): ?>
                    <option value="<?= $e['election_id'] ?>" <?= $selectedElectionId==$e['election_id']?'selected':'' ?>><?= h($e['election_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
    <?php if ($selectedElection): ?>
        <div class="inline-spread">
            <p>Status: <?= badge($selectedElection['status']) ?> | Voted: <strong><?= $votedCount ?></strong> / Approved voters: <strong><?= $approvedVoters ?></strong> | Turnout: <strong><?= $turnout ?>%</strong></p>
        </div>
        <div class="progress-line"><div class="progress-fill" style="width:<?= min($turnout, 100) ?>%;"></div></div>
    <?php endif; ?>
</div>

<?php foreach ($positions as $pos): ?>
<div class="card">
    <div class="inline-spread">
        <h2><?= h($pos['position_name']) ?> <small>(Winner/s: <?= h($pos['max_vote']) ?>)</small></h2>
    </div>
    <div class="table-wrap">
        <table>
            <tr><th>Rank</th><th>Candidate</th><th>Party</th><th>Votes</th><th>Visual</th><th>Remarks</th></tr>
<?php
$stmt = $pdo->prepare('SELECT c.candidate_id, c.candidate_name, c.party_name, COUNT(v.vote_id) AS total_votes FROM candidates c LEFT JOIN votes v ON c.candidate_id=v.candidate_id AND v.election_id=? WHERE c.election_id=? AND c.position_id=? GROUP BY c.candidate_id ORDER BY total_votes DESC, c.candidate_name ASC');
$stmt->execute([$selectedElectionId, $selectedElectionId, $pos['position_id']]);
$candidates = $stmt->fetchAll();
$maxVotes = 0;
foreach ($candidates as $temp) { $maxVotes = max($maxVotes, (int)$temp['total_votes']); }
$rank = 1;
foreach ($candidates as $cand):
    $isWinner = $rank <= (int)$pos['max_vote'];
    $percent = $maxVotes > 0 ? round(((int)$cand['total_votes'] / $maxVotes) * 100) : 0;
?>
            <tr>
                <td><?= $rank ?></td>
                <td><strong><?= h($cand['candidate_name']) ?></strong></td>
                <td><?= h($cand['party_name']) ?></td>
                <td><?= h($cand['total_votes']) ?></td>
                <td><div class="result-bar"><div class="result-bar-track"><div class="result-bar-fill" style="width:<?= $percent ?>%;"></div></div><small><?= $percent ?>%</small></div></td>
                <td><?= $isWinner ? badge('Leading/Winner') : '' ?></td>
            </tr>
<?php $rank++; endforeach; ?>
        </table>
    </div>
</div>
<?php endforeach; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
