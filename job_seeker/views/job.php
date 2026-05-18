<?php
require_once __DIR__ . "/../controllers/JobController.php";

if (!isset($_SESSION['seeker_id'])) {
    header("Location: ../index.php");
    exit();
}

$job_id = intval($_GET['id'] ?? 0);
if ($job_id <= 0) {
    header("Location: ../index.php");
    exit();
}

$job = getJobById($job_id);
if (!$job) {
    header("Location: ../index.php");
    exit();
}

$already_applied = hasApplied($_SESSION['seeker_id'], $job_id);
$is_saved        = isJobSaved($_SESSION['seeker_id'], $job_id);

$job['is_featured'] = $job['isfeatured'];
$job['job_type']    = $job['jobtype'];
$job['salary_min']  = $job['salarymin'];
$job['salary_max']  = $job['salarymax'];

function skillTags($str) {
    if (empty($str)) return '';
    $tags = array_map('trim', explode(',', $str));
    return implode(' ', array_map(fn($t) => '<span class="badge">' . htmlspecialchars($t) . '</span>', $tags));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($job['title']) ?> – JobPortal</title>
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
        <a href="applications.php"><i class="fas fa-file-alt"></i> My Applications</a>
        <a href="saved.php"><i class="fas fa-bookmark"></i> Saved Jobs</a>
    </div>

    <?php foreach ($errors as $e): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>

    <!-- JOB HEADER -->
    <div class="card">
        <div class="job-detail-header">
            <div>
                <h2 style="font-size:22px;font-weight:700;margin-bottom:6px;">
                    <?= htmlspecialchars($job['title']) ?>
                    <?php if ($job['is_featured']): ?>
                    <span class="featured-badge">FEATURED</span>
                    <?php endif; ?>
                </h2>
                <p style="color:#90b4cc;font-size:15px;">
                    <?= htmlspecialchars($job['company_name'] ?? $job['employer_name']) ?>
                    <?php if (!empty($job['industry'])): ?>
                    &middot; <?= htmlspecialchars($job['industry']) ?>
                    <?php endif; ?>
                </p>
            </div>

            <div style="display:flex;gap:10px;align-items:flex-start;flex-wrap:wrap;">
                <button class="save-btn-icon <?= $is_saved ? 'saved' : '' ?>"
                    id="save-btn"
                    onclick="doToggleSave(this, <?= $job_id ?>)"
                    title="<?= $is_saved ? 'Remove bookmark' : 'Save job' ?>">
                    <i class="fa<?= $is_saved ? 's' : 'r' ?> fa-bookmark"></i>
                    <?= $is_saved ? ' Saved' : ' Save' ?>
                </button>
            </div>
        </div>

        <!-- Meta pills -->
        <div class="job-detail-meta">
            <div class="meta-pill"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['location']) ?></div>
            <div class="meta-pill"><i class="fas fa-clock"></i>
                <?= htmlspecialchars(str_replace('-', ' ', ucfirst($job['job_type']))) ?>
            </div>
            <div class="meta-pill"><i class="fas fa-user-tie"></i>
                <?= htmlspecialchars(ucfirst($job['experiencelevel'])) ?> level
            </div>
            <div class="meta-pill salary"><i class="fas fa-money-bill-wave"></i>
                ৳<?= number_format($job['salary_min']) ?> – ৳<?= number_format($job['salary_max']) ?> / month
            </div>
            <div class="meta-pill">
                <i class="fas fa-calendar-alt"></i>
                Deadline: <?= date('d M Y', strtotime($job['deadline'])) ?>
                <?php if (strtotime($job['deadline']) < time()): ?>
                <span style="color:#e74c3c;font-size:12px;">(Expired)</span>
                <?php endif; ?>
            </div>
            <div class="meta-pill"><i class="fas fa-tag"></i> <?= htmlspecialchars($job['catname']) ?></div>
        </div>

        <!-- Description -->
        <div class="job-desc">
            <?php if (!empty($job['description'])): ?>
            <p class="section-label">Job Description</p>
            <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
            <?php endif; ?>

            <?php if (!empty($job['requirements'])): ?>
            <p class="section-label">Requirements</p>
            <p><?= nl2br(htmlspecialchars($job['requirements'])) ?></p>
            <?php endif; ?>

            <?php if (!empty($job['benefits'])): ?>
            <p class="section-label">Benefits</p>
            <p><?= nl2br(htmlspecialchars($job['benefits'])) ?></p>
            <?php endif; ?>

            <?php if (!empty($job['company_desc'])): ?>
            <p class="section-label">About the Company</p>
            <p><?= nl2br(htmlspecialchars($job['company_desc'])) ?></p>
            <?php endif; ?>

            <?php if (!empty($job['website'])): ?>
            <p style="margin-top:10px;">
                <a href="<?= htmlspecialchars($job['website']) ?>" target="_blank" style="color:#00d4ff;font-size:14px;">
                    <i class="fas fa-globe"></i> Company Website
                </a>
            </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- APPLY FORM -->
    <div class="card" style="max-width:700px;margin:0 auto;">
        <?php if ($already_applied): ?>
        <div class="alert alert-success" style="margin:0;">
            <i class="fas fa-check-circle"></i>
            You have already applied to this job.
            <a href="applications.php" style="color:#2ecc71;margin-left:8px;">View your applications →</a>
        </div>
        <?php elseif (strtotime($job['deadline']) < time()): ?>
        <div class="alert alert-error" style="margin:0;">
            <i class="fas fa-calendar-times"></i> This job's deadline has passed. Applications are closed.
        </div>
        <?php else: ?>
        <h3><i class="fas fa-paper-plane" style="color:#00d4ff;"></i> Apply for This Position</h3>
        <p style="color:#666;font-size:13px;margin-bottom:24px;">Write a compelling cover letter and attach your resume.</p>

        <form method="POST" action="../controllers/JobController.php" enctype="multipart/form-data">
            <input type="hidden" name="job_id" value="<?= $job_id ?>">

            <div class="form-group">
                <label>Cover Letter *</label>
                <textarea name="cover_letter" rows="6"
                    placeholder="Tell the employer why you're the right candidate for this role…" required></textarea>
            </div>

            <div class="form-group">
                <label>Resume (PDF, max 5 MB)</label>
                <?php if (!empty($user_profile['resumepath'])): ?>
                <p style="font-size:12px;color:#666;margin-bottom:8px;">
                    <i class="fas fa-info-circle" style="color:#00d4ff;"></i>
                    Your profile resume will be used if you don't upload a new one.
                    <a href="<?= htmlspecialchars($user_profile['resumepath']) ?>" target="_blank" style="color:#00d4ff;">View current</a>
                </p>
                <?php else: ?>
                <p style="font-size:12px;color:#e67e22;margin-bottom:8px;">
                    <i class="fas fa-exclamation-triangle"></i>
                    No resume on profile — please upload one below.
                </p>
                <?php endif; ?>
                <input type="file" name="resume_upload" accept=".pdf">
            </div>

            <div style="text-align:center;margin-top:20px;">
                <button type="submit" name="apply_job" style="width:220px;padding:15px;font-size:15px;">
                    <i class="fas fa-paper-plane"></i> Submit Application
                </button>
            </div>
        </form>
        <?php endif; ?>
    </div>

</div>

<script>
function doToggleSave(btn, jobId) {
    fetch(`../controllers/JobController.php?action=toggleSave&job_id=${jobId}`)
        .then(r => r.json())
        .then(data => {
            if (data.error) { alert(data.error); return; }
            const icon = btn.querySelector('i');
            if (data.saved) {
                btn.classList.add('saved');
                icon.className = 'fas fa-bookmark';
                btn.childNodes[btn.childNodes.length - 1].textContent = ' Saved';
                btn.title = 'Remove bookmark';
            } else {
                btn.classList.remove('saved');
                icon.className = 'far fa-bookmark';
                btn.childNodes[btn.childNodes.length - 1].textContent = ' Save';
                btn.title = 'Save job';
            }
        });
}
</script>
</body>
</html>
