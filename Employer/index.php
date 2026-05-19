<?php
require_once "config.php";
require_once "model.php";
require_once "controller.php"; 
employerOnly();

$employerid = $_SESSION['userid'];
$stats      = getEmployerStats($conn, $employerid);
$jobs       = getEmployerJobs($conn, $employerid);

// Handle delete
if (isset($_GET['delete'])) {
    require_once "controller.php";
    deleteJob($conn, (int)$_GET['delete'], $employerid);
    header("Location: index.php"); exit();
}

$activePage = 'dashboard';
$basePath   = '.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employer Dashboard</title>
</head>
<body>
<div class="app-layout">
    <?php include "views/sidebar.php"; ?>

    <main class="main-content">
        <div class="page-header">
            <h2><i class="fas fa-home" style="color:var(--accent2);margin-right:8px"></i>Dashboard</h2>
            <p>Welcome back, <?= htmlspecialchars($_SESSION['name']) ?>! Here's your hiring overview.</p>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-briefcase"></i></div>
                <div><div class="stat-num"><?= $stats['totaljobs'] ?></div><div class="stat-label">Total Jobs</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-circle-check"></i></div>
                <div><div class="stat-num"><?= $stats['activejobs'] ?></div><div class="stat-label">Active Jobs</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-users"></i></div>
                <div><div class="stat-num"><?= $stats['totalapplications'] ?></div><div class="stat-label">Total Applications</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow"><i class="fas fa-star"></i></div>
                <div><div class="stat-num"><?= $stats['shortlisted'] ?></div><div class="stat-label">Shortlisted</div></div>
            </div>
        </div>

        <!-- Jobs Table -->
        <div class="card">
            <div class="flex-between" style="margin-bottom:18px;">
                <h3 style="margin:0"><i class="fas fa-list"></i> My Job Postings</h3>
                <a href="views/create_job.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Post New Job</a>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Status</th>
                            <th>Applications</th>
                            <th>Deadline</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $hasJobs = false; while ($job = $jobs->fetch_assoc()): $hasJobs = true; ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($job['title']) ?></strong></td>
                        <td>
                            <span id="status-<?= $job['id'] ?>" class="badge badge-<?= $job['status'] ?>">
                                <?= ucfirst($job['status']) ?>
                            </span>
                        </td>
                        <td><?= $job['applicationcount'] ?></td>
                        <td><?= htmlspecialchars($job['deadline']) ?></td>
                        <td style="display:flex;gap:6px;flex-wrap:wrap;">
                            <a href="views/applications.php?jobid=<?= $job['id'] ?>" class="btn btn-ghost btn-xs"><i class="fas fa-users"></i> Applicants</a>
                            <a href="views/edit_job.php?id=<?= $job['id'] ?>"        class="btn btn-warning btn-xs"><i class="fas fa-pen"></i> Edit</a>
                            <button onclick="toggleJobStatus(<?= $job['id'] ?>)"     class="btn btn-sm" style="background:var(--bg3);border:1px solid var(--border);color:var(--text);padding:4px 10px;font-size:11px;">
                                <i class="fas fa-toggle-on"></i> Toggle
                            </button>
                            <a href="?delete=<?= $job['id'] ?>" class="btn btn-danger btn-xs"
                               onclick="return confirm('Delete this job?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if (!$hasJobs): ?>
                    <tr><td colspan="5" class="empty-state"><i class="fas fa-inbox"></i><p>No job postings yet. <a href="views/create_job.php" style="color:var(--accent2)">Post your first job →</a></p></td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<script src="views/script.js"></script>
</body>
</html>
