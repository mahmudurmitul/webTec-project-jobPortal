<?php
require_once "../config.php";
require_once "../model.php";
employerOnlyFrom();

$employerid = $_SESSION['userid'];
$recruiters = getMyRecruiters($conn, $employerid);

$activePage = 'recruiters'; $basePath = '..';
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>My Recruiters</title></head>
<body>
<div class="app-layout">
    <?php include "sidebar.php"; ?>
    <main class="main-content">
        <div class="page-header">
            <h2><i class="fas fa-user-tie" style="color:var(--accent2);margin-right:8px"></i>Recruiter Agencies</h2>
            <p>Recruiters currently posting jobs on behalf of your company.</p>
        </div>

        <div class="card">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Recruiter</th>
                            <th>Agency</th>
                            <th>Email</th>
                            <th>Jobs Posted</th>
                            <th>Added</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $has = false; while ($rec = $recruiters->fetch_assoc()): $has = true; ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($rec['recname']) ?></strong></td>
                        <td><?= htmlspecialchars($rec['agencyname'] ?? '—') ?></td>
                        <td class="text-muted"><?= htmlspecialchars($rec['recemail']) ?></td>
                        <td><?= $rec['jobscount'] ?></td>
                        <td class="text-muted" style="font-size:12px"><?= date('d M Y', strtotime($rec['addedat'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if (!$has): ?>
                        <tr><td colspan="5" class="empty-state"><i class="fas fa-user-tie"></i><p>No recruiters linked to your company yet.</p></td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
