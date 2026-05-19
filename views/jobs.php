<?php
$filterClient = $_GET['client'] ?? '';
$filterStatus = $_GET['status'] ?? '';
$filterCat    = $_GET['cat']    ?? '';
$jobsList = getRecruiterJobs(
    $_SESSION['recruiter_id'],
    $filterClient,
    $filterStatus,
    $filterCat
);
?>

<div class="page-header">
    <div class="flex-between">
        <div>
            <h2><i class="fas fa-briefcase"></i> Job Postings</h2>
            <p>All jobs posted across your clients</p>
        </div>
        <a href="index.php?page=job_form" class="btn btn-primary">
            <i class="fas fa-plus"></i> Post New Job
        </a>
    </div>
</div>

<<<<<<< HEAD

=======
<!-- ── Filters ── -->
>>>>>>> 0e5b1b3773d675329dd7049dc83f42cfc694ce5f
<div class="search-bar">
    <form method="GET" style="display:contents;">
        <input type="hidden" name="page" value="jobs">

        <div class="form-group">
            <label>Filter by Client</label>
            <select name="client">
                <option value="">All Clients</option>
                <?php foreach ($clients_list as $c): ?>
                <option value="<?= $c['id'] ?>"
                        <?= $filterClient == $c['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['companynameoverride'] ?: 'Unnamed Client') ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="">All</option>
                <option value="active"  <?= $filterStatus === 'active'  ? 'selected' : '' ?>>Active</option>
                <option value="closed"  <?= $filterStatus === 'closed'  ? 'selected' : '' ?>>Closed</option>
                <option value="draft"   <?= $filterStatus === 'draft'   ? 'selected' : '' ?>>Draft</option>
            </select>
        </div>

        <div class="form-group">
            <label>Category</label>
            <select name="cat">
                <option value="">All Categories</option>
                <?php foreach ($categories_list as $cat): ?>
                <option value="<?= $cat['id'] ?>"
                        <?= $filterCat == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="display:flex;align-items:flex-end;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filter
            </button>
        </div>

        <?php if ($filterClient || $filterStatus || $filterCat): ?>
        <div class="form-group" style="display:flex;align-items:flex-end;">
            <a href="index.php?page=jobs" class="btn btn-ghost">
                <i class="fas fa-times"></i> Clear
            </a>
        </div>
        <?php endif; ?>
    </form>
</div>

<<<<<<< HEAD

=======
<!-- ── Table ── -->
>>>>>>> 0e5b1b3773d675329dd7049dc83f42cfc694ce5f
<div class="card">
    <h3><i class="fas fa-list"></i> Jobs (<?= count($jobsList) ?>)</h3>

    <?php if (empty($jobsList)): ?>
    <div class="empty-state">
        <i class="fas fa-briefcase"></i>
        <p>No jobs found.
            <?php if ($filterClient || $filterStatus || $filterCat): ?>
                Try clearing the filters, or
            <?php endif; ?>
            <a href="index.php?page=job_form" style="color:var(--accent2);">post a job →</a>
        </p>
    </div>

    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Title &amp; Location</th>
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

                <td>
                    <strong><?= htmlspecialchars($job['title']) ?></strong><br>
                    <span class="text-muted" style="font-size:12px;">
                        <i class="fas fa-map-marker-alt"></i>
                        <?= htmlspecialchars($job['location']) ?>
                    </span>
                </td>

                <!-- clientname comes from COALESCE in getRecruiterJobs() -->
                <td>
                    <span style="font-size:13px;">
                        <?= htmlspecialchars($job['clientname'] ?? '—') ?>
                    </span>
                </td>

                <td><?= htmlspecialchars($job['catname']) ?></td>

                <td>
                    <span style="font-size:12px;">
                        <?= ucwords(str_replace('-', ' ', $job['jobtype'])) ?>
                    </span>
                </td>

                <td>
                    <span style="font-weight:600;color:var(--accent2);">
                        <?= (int)$job['appcount'] ?>
                    </span>
                </td>

                <td>
                    <?php
                    $deadlineTs  = strtotime($job['deadline']);
                    $isExpired   = $deadlineTs < time();
                    $deadlineStr = date('d M Y', $deadlineTs);
                    ?>
                    <span style="font-size:12px;<?= $isExpired ? 'color:var(--red);' : '' ?>">
                        <?= $deadlineStr ?>
                        <?php if ($isExpired): ?>
                        <br><span style="font-size:10px;">Expired</span>
                        <?php endif; ?>
                    </span>
                </td>

                <td>
                    <span class="badge badge-<?= $job['status'] ?>"
                          id="status-badge-<?= $job['id'] ?>">
                        <?= ucfirst($job['status']) ?>
                    </span>
                </td>

                <td style="white-space:nowrap;">
                    <?php if ($job['status'] === 'active'): ?>
                    <button class="btn btn-ghost btn-xs"
                            onclick="toggleStatus(<?= $job['id'] ?>, 'closed')">
                        <i class="fas fa-pause"></i> Close
                    </button>

                    <?php elseif ($job['status'] === 'closed'): ?>
                    <button class="btn btn-success btn-xs"
                            onclick="toggleStatus(<?= $job['id'] ?>, 'active')">
                        <i class="fas fa-play"></i> Reopen
                    </button>

                    <?php else: /* draft */ ?>
                    <button class="btn btn-primary btn-xs"
                            onclick="toggleStatus(<?= $job['id'] ?>, 'active')">
                        <i class="fas fa-upload"></i> Publish
                    </button>
                    <?php endif; ?>

                    <a href="index.php?page=job_form&edit=<?= $job['id'] ?>"
                       class="btn btn-ghost btn-xs" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>

                    <a href="index.php?delete_job=<?= $job['id'] ?>"
                       class="btn btn-danger btn-xs" title="Delete"
                       onclick="return confirm('Delete this job and all its applications? This cannot be undone.')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>