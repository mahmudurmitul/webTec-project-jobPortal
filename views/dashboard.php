<div class="page-header">
    <h2><i class="fas fa-th-large"></i> Dashboard</h2>
    <p>Welcome back, <?= htmlspecialchars($_SESSION['recruiter_name']) ?>!</p>
</div>



<?php $announcements = getAnnouncements(); ?>
<?php if (!empty($announcements)): ?>
<div style="margin-bottom:24px;">
    <?php foreach ($announcements as $ann): ?>
    <div style="background:linear-gradient(90deg,rgba(245,158,11,0.08),rgba(245,158,11,0.03));
                border:1px solid rgba(245,158,11,0.25);
                border-left:4px solid var(--accent2);
                border-radius:12px;padding:16px 20px;margin-bottom:10px;
                display:flex;align-items:flex-start;gap:14px;">
        <div style="flex-shrink:0;width:36px;height:36px;border-radius:50%;
                    background:rgba(245,158,11,0.15);
                    display:flex;align-items:center;justify-content:center;
                    color:var(--accent2);font-size:16px;margin-top:2px;">
            <i class="fas fa-bullhorn"></i>
        </div>
        <div style="flex:1;">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:6px;margin-bottom:5px;">
                <strong style="font-size:14px;color:var(--accent2);"><?= htmlspecialchars($ann['title']) ?></strong>
                <span style="font-size:11px;color:var(--muted);">
                    <i class="fas fa-clock"></i> <?= date('d M Y, h:i A', strtotime($ann['createdat'])) ?>
                </span>
            </div>
            <p style="font-size:13px;color:var(--text);line-height:1.7;margin:0;">
                <?= nl2br(htmlspecialchars($ann['body'])) ?>
            </p>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>



<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-briefcase"></i></div>
        <div>
            <div class="stat-num"><?= $stats['active_jobs'] ?? 0 ?></div>
            <div class="stat-label">Active Jobs</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-building"></i></div>
        <div>
            <div class="stat-num"><?= $stats['clients'] ?? 0 ?></div>
            <div class="stat-label">Clients</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-file-alt"></i></div>
        <div>
            <div class="stat-num"><?= $stats['total_apps'] ?? 0 ?></div>
            <div class="stat-label">Total Applications</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="fas fa-envelope-open-text"></i></div>
        <div>
            <div class="stat-num"><?= $stats['outreach'] ?? 0 ?></div>
            <div class="stat-label">Outreach Sent</div>
        </div>
    </div>
</div>



<div class="card">
    <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="?page=job_form" class="btn btn-primary"><i class="fas fa-plus"></i> Post a Job</a>
        <a href="?page=clients" class="btn btn-ghost"><i class="fas fa-building"></i> Add Client</a>
        <a href="?page=seekers" class="btn btn-ghost"><i class="fas fa-search"></i> Find Candidates</a>
        <a href="?page=analytics" class="btn btn-ghost"><i class="fas fa-chart-bar"></i> View Analytics</a>
    </div>
</div>



<?php
$recentJobs = getRecruiterJobs($_SESSION['recruiter_id']);
$recentJobs = array_slice($recentJobs, 0, 5);
?>
<div class="card">
    <div class="flex-between">
        <h3><i class="fas fa-briefcase"></i> Recent Jobs</h3>
        <a href="?page=jobs" class="btn btn-ghost btn-sm">View All</a>
    </div>
    <?php if (empty($recentJobs)): ?>
    <div class="empty-state"><i class="fas fa-briefcase"></i><p>No jobs posted yet. <a href="?page=job_form" style="color:var(--accent2);">Post your first job →</a></p></div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Title</th><th>Client</th><th>Status</th><th>Applications</th><th>Deadline</th></tr></thead>
            <tbody>
            <?php foreach ($recentJobs as $job): ?>
            <tr>
                <td><strong><?= htmlspecialchars($job['title']) ?></strong></td>
                <td><?= htmlspecialchars($job['companyname'] ?? '—') ?></td>
                <td><span class="badge badge-<?= $job['status'] ?>"><?= ucfirst($job['status']) ?></span></td>
                <td><?= $job['appcount'] ?></td>
                <td><?= date('d M Y', strtotime($job['deadline'])) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>



<?php $recentOutreach = array_slice(getOutreachByRecruiter($_SESSION['recruiter_id']), 0, 5); ?>
<div class="card">
    <div class="flex-between">
        <h3><i class="fas fa-envelope-open-text"></i> Recent Outreach</h3>
        <a href="?page=outreach" class="btn btn-ghost btn-sm">View All</a>
    </div>
    <?php if (empty($recentOutreach)): ?>
    <div class="empty-state"><i class="fas fa-envelope"></i><p>No outreach sent yet.</p></div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Seeker</th><th>Job</th><th>Status</th><th>Sent</th></tr></thead>
            <tbody>
            <?php foreach ($recentOutreach as $o): ?>
            <tr>
                <td><?= htmlspecialchars($o['seekername']) ?></td>
                <td><?= htmlspecialchars($o['jobtitle'] ?? '—') ?></td>
                <td><span class="badge badge-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
                <td><?= date('d M Y', strtotime($o['sentat'])) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>