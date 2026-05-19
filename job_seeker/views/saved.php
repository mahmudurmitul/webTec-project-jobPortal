<?php
require_once __DIR__ . "/../controllers/JobController.php";

if (!isset($_SESSION['seeker_id'])) {
    header("Location: /webtech/webTec-project-jobPortal/job_seeker/index.php");
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
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/saved.php" class="active"><i class="fas fa-bookmark"></i> Saved Jobs <span class="nav-badge"><?= $saved_count ?></span></a>
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

        <div class="page-heading">
            <h2><i class="fas fa-bookmark"></i> Saved Jobs</h2>
            <p>Your bookmarked job listings. (<?= count($saved) ?> saved)</p>
        </div>

        <div class="card">
            <?php if (empty($saved)): ?>
            <div class="empty-state">
                <i class="far fa-bookmark"></i>
                <h3>No saved jobs yet</h3>
                <p>Click the bookmark icon on any job listing to save it here.</p>
                <a href="/webtech/webTec-project-jobPortal/job_seeker/index.php" class="btn" style="margin-top:16px;display:inline-block;">
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
                            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/job.php?id=<?= $job['id'] ?>" style="color:#e0e0e0;text-decoration:none;">
                                <?= htmlspecialchars($job['title']) ?>
                                <?php if ($job['is_featured']): ?><span class="featured-badge">FEATURED</span><?php endif; ?>
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
                        <td style="color:<?= $expired ? '#ef4444' : '#ccc' ?>">
                            <?= $deadline ?>
                            <?php if ($expired): ?><br><small>Expired</small><?php endif; ?>
                        </td>
                        <td style="color:#888;font-size:13px;"><?= $savedOn ?></td>
                        <td>
                            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                <?php if (!$expired): ?>
                                <a href="/webtech/webTec-project-jobPortal/job_seeker/views/job.php?id=<?= $job['id'] ?>" class="apply-btn">
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

    </main>
</div>

<script src="script.js"></script>
</body>
</html>