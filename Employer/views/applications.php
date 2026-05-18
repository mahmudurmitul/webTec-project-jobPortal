<?php
require_once "../config.php";
require_once "../model.php";

employerOnlyFrom();

$employerid = $_SESSION['userid'];
$jobid      = (int)($_GET['jobid'] ?? 0);

// Filters
$filterStatus = $_GET['status'] ?? '';
$filterExp    = $_GET['experience'] ?? '';
$filterDate   = $_GET['date'] ?? '';

$applicationsResult = getApplicationsByJob($conn, $jobid, $employerid);

$applications = [];

while ($row = $applicationsResult->fetch_assoc()) {
    if ($filterStatus !== '' && ($row['status'] ?? '') !== $filterStatus) {
        continue;
    }

    if ($filterExp !== '') {
        $exp = (int)($row['yearsexperience'] ?? 0);

        if ($filterExp === 'entry' && $exp > 2) {
            continue;
        }

        if ($filterExp === 'mid' && ($exp < 3 || $exp > 5)) {
            continue;
        }

        if ($filterExp === 'senior' && $exp < 6) {
            continue;
        }
    }

    if ($filterDate !== '' && strtotime($row['appliedat']) < strtotime($filterDate)) {
        continue;
    }

    $applications[] = $row;
}

$activePage = 'dashboard';
$basePath   = '..';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applications</title>
</head>
<body>
<div class="app-layout">
    <?php include "sidebar.php"; ?>

    <main class="main-content">
        <div class="page-header flex-between">
            <div>
                <h2><i class="fas fa-users" style="color:var(--accent2);margin-right:8px"></i>Applications</h2>
                <p>Review and manage applicants for this job posting.</p>
            </div>

            <a href="../index.php" class="btn btn-ghost btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <!-- Filters -->
        <div class="card" style="padding:16px 24px;margin-bottom:16px">
            <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
                <input type="hidden" name="jobid" value="<?= htmlspecialchars($jobid) ?>">

                <div class="form-group" style="margin-bottom:0;min-width:150px">
                    <label style="font-size:12px">Status</label>
                    <select name="status">
                        <option value="">All Statuses</option>
                        <?php foreach (['submitted','reviewed','shortlisted','interview','rejected','withdrawn'] as $s): ?>
                            <option value="<?= $s ?>" <?= $filterStatus === $s ? 'selected' : '' ?>>
                                <?= ucfirst($s) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:0;min-width:150px">
                    <label style="font-size:12px">Experience Level</label>
                    <select name="experience">
                        <option value="">All Levels</option>
                        <?php foreach (['entry','mid','senior'] as $e): ?>
                            <option value="<?= $e ?>" <?= $filterExp === $e ? 'selected' : '' ?>>
                                <?= ucfirst($e) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:0;min-width:160px">
                    <label style="font-size:12px">Applied After</label>
                    <input type="date" name="date" value="<?= htmlspecialchars($filterDate) ?>">
                </div>

                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-filter"></i> Filter
                </button>

                <a href="applications.php?jobid=<?= htmlspecialchars($jobid) ?>" class="btn btn-ghost btn-sm">
                    Reset
                </a>
            </form>
        </div>

        <div class="card">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Candidate</th>
                            <th>Headline</th>
                            <th>Experience</th>
                            <th>Skills</th>
                            <th>Cover Letter</th>
                            <th>Resume</th>
                            <th>Status</th>
                            <th>Message</th>
                            <th>Applied</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (!empty($applications)): ?>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($app['name']) ?></strong><br>
                                    <span class="text-muted"><?= htmlspecialchars($app['email']) ?></span>
                                </td>

                                <td><?= htmlspecialchars($app['headline'] ?? '—') ?></td>

                                <td><?= htmlspecialchars($app['yearsexperience'] ?? '0') ?> yrs</td>

                                <td style="max-width:160px;font-size:12px">
                                    <?= htmlspecialchars($app['skills'] ?? '—') ?>
                                </td>

                                <td>
                                    <?php if (!empty($app['coverletter'])): ?>
                                        <button class="btn btn-ghost btn-xs" onclick="showCoverLetter(<?= (int)$app['id'] ?>)">
                                            <i class="fas fa-file-lines"></i> View
                                        </button>

                                        <div id="cl-<?= (int)$app['id'] ?>" style="display:none">
                                            <?= htmlspecialchars($app['coverletter']) ?>
                                        </div>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if (!empty($app['resumepath'])): ?>
                                        <a class="btn btn-ghost btn-xs" href="../../<?= htmlspecialchars($app['resumepath']) ?>" target="_blank">
                                            <i class="fas fa-download"></i> Resume
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">None</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <select class="status-select" onchange="updateApplicationStatus(<?= (int)$app['id'] ?>, this.value)">
                                        <?php foreach (['submitted','reviewed','shortlisted','interview','rejected'] as $s): ?>
                                            <option value="<?= $s ?>" <?= ($app['status'] ?? '') === $s ? 'selected' : '' ?>>
                                                <?= ucfirst($s) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>

                                <td>
                                    <a href="messages.php?recipientid=<?= (int)$app['seekerid'] ?>&applicationid=<?= (int)$app['id'] ?>" class="btn btn-ghost btn-xs">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                </td>

                                <td class="text-muted" style="font-size:12px">
                                    <?= date('d M Y', strtotime($app['appliedat'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>No applications match your filters.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Cover Letter Modal -->
<div id="cl-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:1000;align-items:center;justify-content:center">
    <div style="background:var(--bg2);border:1px solid var(--border);border-radius:14px;padding:28px;max-width:560px;width:90%;max-height:70vh;overflow-y:auto;position:relative">
        <button onclick="closeCoverLetter()" style="position:absolute;top:12px;right:16px;background:none;border:none;color:var(--text);font-size:20px;cursor:pointer">&times;</button>
        <h3 style="margin-bottom:16px">
            <i class="fas fa-file-lines" style="color:var(--accent2)"></i> Cover Letter
        </h3>
        <p id="cl-modal-body" style="line-height:1.7;white-space:pre-wrap;font-size:14px"></p>
    </div>
</div>

<script src="script.js"></script>
<script>
function showCoverLetter(id) {
    const text = document.getElementById('cl-' + id).textContent;
    document.getElementById('cl-modal-body').textContent = text;
    document.getElementById('cl-modal').style.display = 'flex';
}

function closeCoverLetter() {
    document.getElementById('cl-modal').style.display = 'none';
}

document.getElementById('cl-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCoverLetter();
    }
});
</script>
</body>
</html>