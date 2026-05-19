<?php
require_once "../config.php";
require_once "../model.php";
employerOnlyFrom();

$employerid  = $_SESSION['userid'];
$shortlisted = getShortlistedCandidates($conn, $employerid);

$activePage = 'shortlisted'; $basePath = '..';
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Shortlisted Candidates</title></head>
<body>
<div class="app-layout">
    <?php include "sidebar.php"; ?>
    <main class="main-content">
        <div class="page-header">
            <h2><i class="fas fa-star" style="color:var(--accent2);margin-right:8px"></i>Shortlisted Candidates</h2>
            <p>All candidates marked as shortlisted or invited to interview.</p>
        </div>

        <div class="card">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Candidate</th>
                            <th>Headline</th>
                            <th>Job Applied For</th>
                            <th>Status</th>
                            <th>Applied Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $has = false; while ($row = $shortlisted->fetch_assoc()): $has = true; ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($row['name']) ?></strong><br>
                            <span class="text-muted"><?= htmlspecialchars($row['email']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($row['headline'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($row['jobtitle']) ?></td>
                        <td><span class="badge badge-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
                        <td class="text-muted" style="font-size:12px"><?= date('d M Y', strtotime($row['appliedat'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if (!$has): ?>
                        <tr><td colspan="5" class="empty-state"><i class="fas fa-star"></i><p>No shortlisted candidates yet.</p></td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
