<?php
$filterJob    = $_GET['job_filter'] ?? '';
$filterStatus = $_GET['status_filter'] ?? '';
$appsList = getApplicationsByRecruiterJobs($_SESSION['recruiter_id'], $filterJob, $filterStatus);
$myJobs   = getRecruiterJobs($_SESSION['recruiter_id']);
?>
<div class="page-header">
    <h2><i class="fas fa-file-alt"></i> Applications</h2>
    <p>Review and manage all candidate applications to your clients' jobs</p>
</div>

<!-- Filters -->
<div class="search-bar">
    <form method="GET" style="display:contents;">
        <input type="hidden" name="page" value="applications">
        <div class="form-group">
            <label>Filter by Job</label>
            <select name="job_filter">
                <option value="">All Jobs</option>
                <?php foreach ($myJobs as $j): ?>
                <option value="<?= $j['id'] ?>" <?= $filterJob == $j['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($j['title']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status_filter">
                <option value="">All</option>
                <?php foreach (['submitted','reviewed','shortlisted','interview','rejected','hired'] as $s): ?>
                <option value="<?= $s ?>" <?= $filterStatus===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="display:flex;align-items:flex-end;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
        </div>
    </form>
</div>

<div class="card">
    <h3><i class="fas fa-list"></i> Applications (<?= count($appsList) ?>)</h3>
    <?php if (empty($appsList)): ?>
    <div class="empty-state"><i class="fas fa-file-alt"></i><p>No applications found.</p></div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Applicant</th>
                    <th>Job</th>
                    <th>Skills</th>
                    <th>Experience</th>
                    <th>Applied</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($appsList as $app): ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($app['seekername']) ?></strong><br>
                    <span class="text-muted" style="font-size:12px;"><?= htmlspecialchars($app['seekeremail']) ?></span>
                </td>
                <td><?= htmlspecialchars($app['jobtitle']) ?></td>
                <td>
                    <?php
                    $skills = array_slice(array_map('trim', explode(',', $app['skills'] ?? '')), 0, 3);
                    foreach ($skills as $s): if ($s): ?>
                    <span style="background:rgba(59,130,246,0.15);color:var(--blue);padding:2px 8px;border-radius:20px;font-size:11px;margin-right:3px;"><?= htmlspecialchars($s) ?></span>
                    <?php endif; endforeach; ?>
                </td>
                <td><?= $app['yearsexperience'] ?? '—' ?> yrs</td>
                <td><?= date('d M Y', strtotime($app['appliedat'])) ?></td>
                <td>
                    <select class="status-select" onchange="updateAppStatus(<?= $app['id'] ?>, this.value, this)"
                            <?= $app['status']==='hired' ? 'disabled' : '' ?>>
                        <?php foreach (['submitted','reviewed','shortlisted','interview','rejected'] as $s): ?>
                        <option value="<?= $s ?>" <?= $app['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                        <option value="hired" <?= $app['status']==='hired'?'selected':'' ?> style="color:#fbbf24;">★ Hired</option>
                    </select>
                </td>
                <td>
                    <a href="?page=seeker_profile&seeker_id=<?= $app['seekerid'] ?>" class="btn btn-ghost btn-xs"><i class="fas fa-user"></i> Profile</a>
                    <?php if ($app['resumepath']): ?>
                    <a href="<?= htmlspecialchars($app['resumepath']) ?>" target="_blank" class="btn btn-ghost btn-xs"><i class="fas fa-file-pdf"></i></a>
                    <?php endif; ?>
                    <?php if ($app['status'] === 'interview'): ?>
                    <button class="btn btn-xs" style="background:#fbbf24;color:#000;font-weight:700;"
                            onclick="markHired(<?= $app['id'] ?>, this)">
                        <i class="fas fa-check-double"></i> Hire
                    </button>
                    <?php elseif ($app['status'] === 'hired'): ?>
                    <span class="badge badge-hired"><i class="fas fa-star"></i> Hired</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>