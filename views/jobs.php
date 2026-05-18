<?php
$search = $_GET['search'] ?? '';
$statusF = $_GET['status'] ?? '';
$empF    = (int)($_GET['emp'] ?? 0);
$recF    = (int)($_GET['rec'] ?? 0);
$jobs = getAllJobs($search, $statusF, $empF, $recF);
?>
<div class="page-header">
    <h2><i class="fas fa-briefcase"></i> All Job Listings</h2>
    <p>Review, moderate, and feature job postings across the platform</p>
</div>

<div class="search-bar">
    <form method="GET" style="display:contents;">
        <input type="hidden" name="page" value="jobs">
        <div class="form-group" style="flex:2;">
            <label>Search</label>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Title, company, location...">
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="">All</option>
                <option value="active" <?= $statusF==='active'?'selected':'' ?>>Active</option>
                <option value="closed" <?= $statusF==='closed'?'selected':'' ?>>Closed</option>
                <option value="draft"  <?= $statusF==='draft'?'selected':'' ?>>Draft</option>
            </select>
        </div>
        <div class="form-group" style="display:flex;align-items:flex-end;">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
        </div>
    </form>
</div>

<div class="card">
    <h3><i class="fas fa-list"></i> Jobs (<?= count($jobs) ?>)
        <span style="font-size:11px;color:var(--muted);font-weight:400;margin-left:8px;">Toggle ★ to feature/unfeature, or remove policy-violating listings</span>
    </h3>
    <?php if (empty($jobs)): ?>
    <div class="empty-state"><i class="fas fa-briefcase"></i><p>No jobs found.</p></div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Title</th><th>Company</th><th>Category</th><th>Type</th><th>Apps</th><th>Status</th><th>Featured</th><th>Posted</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($jobs as $job): ?>
            <tr id="jrow-<?= $job['id'] ?>">
                <td>
                    <strong><?= htmlspecialchars($job['title']) ?></strong><br>
                    <span class="text-muted"><?= htmlspecialchars($job['location']) ?></span>
                </td>
                <td>
                    <?= htmlspecialchars($job['companyname'] ?? $job['empname']) ?>
                    <?php if ($job['agencyname']): ?>
                    <br><span style="color:var(--purple);font-size:11px;"><i class="fas fa-headset"></i> <?= htmlspecialchars($job['agencyname']) ?></span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($job['catname']) ?></td>
                <td><?= ucwords(str_replace('-',' ',$job['jobtype'])) ?></td>
                <td><?= $job['appcount'] ?></td>
                <td><span class="badge badge-<?= $job['status'] ?>"><?= ucfirst($job['status']) ?></span></td>
                <td>
                    <button id="feat-btn-<?= $job['id'] ?>" class="btn btn-xs <?= $job['isfeatured'] ? 'btn-primary' : 'btn-ghost' ?>"
                            onclick="toggleFeatured(<?= $job['id'] ?>, <?= $job['isfeatured'] ? 0 : 1 ?>)"
                            title="<?= $job['isfeatured'] ? 'Remove featured' : 'Mark as featured' ?>">
                        <i class="fas fa-star"></i> <?= $job['isfeatured'] ? 'Featured' : 'Feature' ?>
                    </button>
                </td>
                <td class="text-muted"><?= date('d M Y', strtotime($job['createdat'])) ?></td>
                <td>
                    <?php if ($job['status'] !== 'closed'): ?>
                    <a href="?remove_job=<?= $job['id'] ?>" class="btn btn-danger btn-xs" onclick="return confirm('Remove this job (close it)?')">
                        <i class="fas fa-ban"></i> Remove
                    </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
