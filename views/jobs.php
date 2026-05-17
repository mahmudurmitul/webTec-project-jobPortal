<?php
$filterClient = $_GET['client'] ?? '';
$filterStatus = $_GET['status'] ?? '';
$filterCat    = $_GET['cat'] ?? '';
$jobsList = getRecruiterJobs($_SESSION['recruiter_id'], $filterClient, $filterStatus, $filterCat);
?>
<div class="page-header">
    <div class="flex-between">
        <div>
            <h2><i class="fas fa-briefcase"></i> Job Postings</h2>
            <p>All jobs posted across your clients</p>
        </div>
        <a href="?page=job_form" class="btn btn-primary"><i class="fas fa-plus"></i> Post New Job</a>
    </div>
</div>



<div class="search-bar">
    <form method="GET" style="display:contents;">
        <input type="hidden" name="page" value="jobs">
        <div class="form-group">
            <label>Filter by Client</label>
            <select name="client">
                <option value="">All Clients</option>
                <?php foreach ($clients_list as $c): ?>
                <option value="<?= $c['employerid'] ?? '' ?>" <?= $filterClient == $c['employerid'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['companynameoverride']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="">All</option>
                <option value="active" <?= $filterStatus==='active'?'selected':'' ?>>Active</option>
                <option value="closed" <?= $filterStatus==='closed'?'selected':'' ?>>Closed</option>
                <option value="draft"  <?= $filterStatus==='draft'?'selected':'' ?>>Draft</option>
            </select>
        </div>
        <div class="form-group">
            <label>Category</label>
            <select name="cat">
                <option value="">All Categories</option>
                <?php foreach ($categories_list as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $filterCat==$cat['id']?'selected':'' ?>><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="display:flex;align-items:flex-end;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
        </div>
    </form>
</div>

<div class="card">
    <h3><i class="fas fa-list"></i> Jobs (<?= count($jobsList) ?>)</h3>
    <?php if (empty($jobsList)): ?>
    <div class="empty-state">
        <i class="fas fa-briefcase"></i>
        <p>No jobs found. <a href="?page=job_form" style="color:var(--accent2);">Post a job →</a></p>
    </div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Client</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Apps</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($jobsList as $job): ?>
            <tr id="job-row-<?= $job['id'] ?>">
                <td><strong><?= htmlspecialchars($job['title']) ?></strong><br><span class="text-muted"><?= htmlspecialchars($job['location']) ?></span></td>
                <td><?= htmlspecialchars($job['companyname'] ?? '—') ?></td>
                <td><?= htmlspecialchars($job['catname']) ?></td>
                <td><?= ucfirst(str_replace('-',' ',$job['jobtype'])) ?></td>
                <td><?= $job['appcount'] ?></td>
                <td><?= date('d M Y', strtotime($job['deadline'])) ?></td>
                <td>
                    <span class="badge badge-<?= $job['status'] ?>" id="status-badge-<?= $job['id'] ?>">
                        <?= ucfirst($job['status']) ?>
                    </span>
                </td>
                <td style="white-space:nowrap;">
                    <?php if ($job['status'] === 'active'): ?>
                    <button class="btn btn-ghost btn-xs" onclick="toggleStatus(<?= $job['id'] ?>, 'closed')"><i class="fas fa-pause"></i> Close</button>
                    <?php elseif ($job['status'] === 'closed'): ?>
                    <button class="btn btn-success btn-xs" onclick="toggleStatus(<?= $job['id'] ?>, 'active')"><i class="fas fa-play"></i> Reopen</button>
                    <?php else: ?>
                    <button class="btn btn-primary btn-xs" onclick="toggleStatus(<?= $job['id'] ?>, 'active')"><i class="fas fa-upload"></i> Publish</button>
                    <?php endif; ?>
                    <a href="?page=job_form&edit=<?= $job['id'] ?>" class="btn btn-ghost btn-xs"><i class="fas fa-edit"></i></a>
                    <a href="?delete_job=<?= $job['id'] ?>" class="btn btn-danger btn-xs" onclick="return confirm('Delete this job?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>