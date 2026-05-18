<?php
require_once __DIR__ . "/../controllers/JobController.php";

if (!isset($_SESSION['seeker_id'])) {
    header("Location: ../index.php");
    exit();
}

$saved = getSavedJobs($_SESSION['seeker_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Saved Jobs – JobPortal</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="container">

    <div class="header">
        <div class="logo">
            <h1><i class="fas fa-briefcase"></i> JobPortal Pro</h1>
        </div>
    </div>

    <div class="nav-links">
        <a href="../index.php"><i class="fas fa-arrow-left"></i> Back to Jobs</a>
        <a href="profile.php"><i class="fas fa-user-edit"></i> Edit Profile</a>
        <a href="applications.php"><i class="fas fa-file-alt"></i> My Applications</a>
    </div>

    <div class="card">
        <h3 style="margin-bottom:6px;">
            <i class="fas fa-bookmark" style="color:#00d4ff;"></i> Saved Jobs
        </h3>
        <p style="color:#666;font-size:13px;margin-bottom:22px;">
            Your bookmarked job listings. (<?= count($saved) ?> saved)
        </p>

        <?php if (empty($saved)): ?>
        <div class="empty-state">
            <i class="far fa-bookmark"></i>
            <h3>No saved jobs yet</h3>
            <p>Click the bookmark icon on any job listing to save it here.</p>
            <a href="../index.php" class="btn" style="margin-top:16px;display:inline-block;">
                <i class="fas fa-search"></i> Browse Jobs
            </a>
        </div>

        <?php else: ?>
        <div class="jobs-table">
            <table>
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Company</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Salary</th>
                        <th>Type</th>
                        <th>Deadline</th>
                        <th>Saved On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($saved as $job):
                    $company  = $job['company_name'] ?? $job['employer_name'];
                    $deadline = date('d M Y', strtotime($job['deadline']));
                    $savedOn  = date('d M Y', strtotime($job['saved_at']));
                    $expired  = strtotime($job['deadline']) < time();
                ?>
                <tr id="saved-row-<?= $job['id'] ?>">
                    <td style="font-weight:600;">
                        <a href="job.php?id=<?= $job['id'] ?>" style="color:#e0e0e0;text-decoration:none;">
                            <?= htmlspecialchars($job['title']) ?>
                            <?php if ($job['is_featured']): ?>
                            <span class="featured-badge">FEATURED</span>
                            <?php endif; ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($company) ?></td>
                    <td><span class="badge"><?= htmlspecialchars($job['catname']) ?></span></td>
                    <td><?= htmlspecialchars($job['location']) ?></td>
                    <td class="salary">৳<?= number_format($job['salary_min']) ?> – ৳<?= number_format($job['salary_max']) ?></td>
                    <td>
                        <span class="type-badge type-<?= htmlspecialchars($job['job_type']) ?>">
                            <?= htmlspecialchars(str_replace('-',' ',strtoupper($job['job_type']))) ?>
                        </span>
                    </td>
                    <td style="color:<?= $expired ? '#e74c3c' : '#ccc' ?>">
                        <?= $deadline ?>
                        <?php if ($expired): ?><br><small>Expired</small><?php endif; ?>
                    </td>
                    <td style="color:#888;font-size:13px;"><?= $savedOn ?></td>
                    <td>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <?php if (!$expired): ?>
                            <a href="job.php?id=<?= $job['id'] ?>" class="apply-btn">
                                <i class="fas fa-paper-plane"></i> Apply
                            </a>
                            <?php endif; ?>
                            <button class="save-btn-icon saved"
                                onclick="removeSaved(this, <?= $job['id'] ?>)"
                                title="Remove bookmark">
                                <i class="fas fa-bookmark"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

</div>

<script>
function removeSaved(btn, jobId) {
    fetch(`../controllers/JobController.php?action=toggleSave&job_id=${jobId}`)
        .then(r => r.json())
        .then(data => {
            if (!data.saved) {
                const row = document.getElementById(`saved-row-${jobId}`);
                if (row) {
                    row.style.transition = 'opacity 0.3s';
                    row.style.opacity = '0';
                    setTimeout(() => row.remove(), 300);
                }
            }
        });
}
</script>
</body>
</html>
