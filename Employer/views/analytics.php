<?php
require_once "../config.php";
require_once "../model.php";
employerOnlyFrom();

$employerid = $_SESSION['userid'];
$data       = getAnalyticsData($conn, $employerid);

$activePage = 'analytics'; $basePath = '..';
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Hiring Analytics</title></head>
<body>
<div class="app-layout">
    <?php include "sidebar.php"; ?>
    <main class="main-content">
        <div class="page-header">
            <h2><i class="fas fa-chart-bar" style="color:var(--accent2);margin-right:8px"></i>Hiring Analytics</h2>
            <p>Track your recruitment performance across all job postings.</p>
        </div>

        <!-- Overview Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-briefcase"></i></div>
                <div><div class="stat-num"><?= $data['totaljobs'] ?></div><div class="stat-label">Total Jobs</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-users"></i></div>
                <div><div class="stat-num"><?= $data['totalapplications'] ?></div><div class="stat-label">Total Applications</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow"><i class="fas fa-star"></i></div>
                <div><div class="stat-num"><?= $data['shortlisted'] ?? 0 ?></div><div class="stat-label">Shortlisted</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-handshake"></i></div>
                <div><div class="stat-num"><?= $data['interviews'] ?? 0 ?></div><div class="stat-label">Interviews</div></div>
            </div>
        </div>

        <!-- Application Funnel -->
        <div class="card">
            <h3><i class="fas fa-filter"></i> Application Funnel</h3>
            <p class="text-muted" style="margin-bottom:16px">How candidates move through your hiring pipeline.</p>
            <div class="funnel">
                <div class="funnel-step s0">
                    <div class="fnum"><?= $data['submitted'] ?? 0 ?></div>
                    <div class="flabel">Submitted</div>
                </div>
                <div class="funnel-step s1">
                    <div class="fnum"><?= $data['reviewed'] ?? 0 ?></div>
                    <div class="flabel">Reviewed</div>
                </div>
                <div class="funnel-step s2">
                    <div class="fnum"><?= $data['shortlisted'] ?? 0 ?></div>
                    <div class="flabel">Shortlisted</div>
                </div>
                <div class="funnel-step s3">
                    <div class="fnum"><?= $data['interviews'] ?? 0 ?></div>
                    <div class="flabel">Interview</div>
                </div>
                <div class="funnel-step s4">
                    <div class="fnum"><?= $data['rejected'] ?? 0 ?></div>
                    <div class="flabel">Rejected</div>
                </div>
            </div>
        </div>

        <!-- Conversion Rates -->
        <?php if ($data['totalapplications'] > 0): ?>
        <div class="card">
            <h3><i class="fas fa-percent"></i> Conversion Rates</h3>
            <?php
            $total = $data['totalapplications'];
            $rows  = [
                ['Submitted → Reviewed',   $data['reviewed'],    $total],
                ['Reviewed → Shortlisted', $data['shortlisted'], max(1, $data['reviewed'])],
                ['Shortlisted → Interview',$data['interviews'],  max(1, $data['shortlisted'])],
            ];
            foreach ($rows as [$label, $num, $denom]):
                $pct = $denom > 0 ? round(($num / $denom) * 100) : 0;
            ?>
            <div style="margin-bottom:16px">
                <div class="flex-between" style="margin-bottom:6px">
                    <span style="font-size:13px"><?= $label ?></span>
                    <span style="font-size:13px;color:var(--accent2)"><?= $pct ?>%</span>
                </div>
                <div style="background:var(--bg3);border-radius:6px;height:8px;overflow:hidden">
                    <div style="background:var(--accent);width:<?= $pct ?>%;height:100%;border-radius:6px;transition:width 0.5s"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
