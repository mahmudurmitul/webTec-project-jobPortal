<?php
require_once __DIR__ . "/../controllers/JobController.php";

if (!isset($_SESSION['seeker_id'])) {
    header("Location: /webtech/webTec-project-jobPortal/job_seeker/index.php");
    exit();
}

$job_id = intval($_GET['id'] ?? 0);
if ($job_id <= 0) {
    header("Location: /webtech/webTec-project-jobPortal/job_seeker/index.php");
    exit();
}

$job = getJobById($job_id);
if (!$job) {
    header("Location: /webtech/webTec-project-jobPortal/job_seeker/index.php");
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

<div class="app-layout">

    <aside class="sidebar">
        <div class="sidebar-logo">
            <i class="fas fa-briefcase"></i>
            <span>JobPortal Pro</span>
        </div>
        <div class="sidebar-user">
            <?php if (!empty($user_profile['profilepic'])): ?>
                <img src="<?= htmlspecialchars($user_profile['profilepic']) ?>" class="sidebar-avatar" alt="Profile">
            <?php else: ?>
                <div class="sidebar-avatar placeholder"><i class="fas fa-user"></i></div>
            <?php endif; ?>
            <div class="sidebar-user-info">
                <strong><?= htmlspecialchars($_SESSION['seeker_name']) ?></strong>
                <span><?= !empty($user_profile['headline']) ? htmlspecialchars(mb_strimwidth($user_profile['headline'],0,28,'…')) : 'Job Seeker' ?></span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="/webtech/webTec-project-jobPortal/job_seeker/index.php"><i class="fas fa-briefcase"></i> Job Listings</a>
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/profile.php"><i class="fas fa-user-edit"></i> Edit Profile</a>
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/applications.php"><i class="fas fa-file-alt"></i> My Applications</a>
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/saved.php"><i class="fas fa-bookmark"></i> Saved Jobs <span class="nav-badge"><?= $saved_count ?></span></a>
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/messages.php">
                <i class="fas fa-envelope"></i> Messages
                <?php if ($unread_msgs > 0): ?>
                <span class="nav-badge red"><?= $unread_msgs ?></span>
                <?php endif; ?>
            </a>
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/alerts.php"><i class="fas fa-bell"></i> Job Alerts</a>
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/complaint.php"><i class="fas fa-flag"></i> Report Issue</a>
        </nav>
        <div class="sidebar-footer">
            <a href="/webtech/webTec-project-jobPortal/job_seeker/controllers/JobController.php?logout=1" class="sidebar-logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </aside>

    <main class="main-content">

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
                    <span style="color:#ef4444;font-size:12px;">(Expired)</span>
                    <?php endif; ?>
                </div>
                <div class="meta-pill"><i class="fas fa-tag"></i> <?= htmlspecialchars($job['catname']) ?></div>
            </div>

            
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
                    <a href="<?= htmlspecialchars($job['website']) ?>" target="_blank" style="color:#a855f7;font-size:14px;">
                        <i class="fas fa-globe"></i> Company Website
                    </a>
                </p>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="card" style="max-width:700px;">
            <?php if ($already_applied): ?>
            <div class="alert alert-success" style="margin:0;">
                <i class="fas fa-check-circle"></i>
                You have already applied to this job.
                <a href="/webtech/webTec-project-jobPortal/job_seeker/views/applications.php" style="color:#10b981;margin-left:8px;">View your applications →</a>
            </div>
            <?php elseif (strtotime($job['deadline']) < time()): ?>
            <div class="alert alert-error" style="margin:0;">
                <i class="fas fa-calendar-times"></i> This job's deadline has passed. Applications are closed.
            </div>
            <?php else: ?>
            <h3><i class="fas fa-paper-plane" style="color:#a855f7;"></i> Apply for This Position</h3>
            <p style="color:#666;font-size:13px;margin-bottom:24px;">Write a compelling cover letter and attach your resume.</p>

            <form method="POST" action="/webtech/webTec-project-jobPortal/job_seeker/controllers/JobController.php" enctype="multipart/form-data" id="applyForm" novalidate>
                <input type="hidden" name="job_id" value="<?= $job_id ?>">

                <div class="form-group">
                    <label>Cover Letter * <small style="color:#555;">(min 30 characters)</small></label>
                    <textarea name="cover_letter" id="coverLetter" rows="6"
                        placeholder="Tell the employer why you're the right candidate for this role…" required></textarea>
                    <span class="field-error" id="coverErr"></span>
                </div>

                <div class="form-group">
                    <label>Resume (PDF, max 5 MB)</label>
                    <?php if (!empty($user_profile['resumepath'])): ?>
                    <p style="font-size:12px;color:#666;margin-bottom:8px;">
                        <i class="fas fa-info-circle" style="color:#a855f7;"></i>
                        Your profile resume will be used if you don't upload a new one.
                        <a href="<?= htmlspecialchars($user_profile['resumepath']) ?>" target="_blank" style="color:#a855f7;">View current</a>
                    </p>
                    <?php else: ?>
                    <p style="font-size:12px;color:#f59e0b;margin-bottom:8px;">
                        <i class="fas fa-exclamation-triangle"></i>
                        No resume on profile — please upload one below.
                    </p>
                    <?php endif; ?>
                    <input type="file" name="resume_upload" id="resumeUpload" accept=".pdf">
                    <span class="field-error" id="resumeErr"></span>
                </div>

                <div style="margin-top:20px;">
                    <button type="submit" name="apply_job" onclick="return validateApply();" style="padding:15px 40px;font-size:15px;">
                        <i class="fas fa-paper-plane"></i> Submit Application
                    </button>
                </div>
            </form>
            <?php endif; ?>
        </div>

    </main>
</div>

<script src="script.js"></script>
<script>
function validateApply() {
    showErr('coverErr', '');
    showErr('resumeErr', '');
    var ok = true;
    var cl = document.getElementById('coverLetter');
    var ru = document.getElementById('resumeUpload');
    var hasProfile = <?= !empty($user_profile['resumepath']) ? 'true' : 'false' ?>;

    if (!cl.value.trim()) {
        showErr('coverErr', 'Cover letter is required.'); ok = false;
    } else if (cl.value.trim().length < 30) {
        showErr('coverErr', 'Cover letter must be at least 30 characters.'); ok = false;
    }
    if (!hasProfile && ru.files.length === 0) {
        showErr('resumeErr', 'Please upload your resume (PDF).'); ok = false;
    } else if (ru.files.length > 0) {
        var file = ru.files[0];
        if (!file.name.toLowerCase().endsWith('.pdf')) {
            showErr('resumeErr', 'Resume must be a PDF file.'); ok = false;
        } else if (file.size > 5 * 1024 * 1024) {
            showErr('resumeErr', 'Resume must be under 5 MB.'); ok = false;
        }
    }
    return ok;
}
</script>
</body>
</html>