<?php
require_once __DIR__ . "/../controllers/JobController.php";

if (!isset($_SESSION['seeker_id'])) {
    header("Location: /webtech/webTec-project-jobPortal/job_seeker/index.php");
    exit();
}

$complaints = getSeekerComplaints($_SESSION['seeker_id']);


$jobs_result = getJobs();
$jobs_list = [];
while ($j = mysqli_fetch_assoc($jobs_result)) $jobs_list[] = $j;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Report Issue – JobPortal</title>
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
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/complaint.php" class="active"><i class="fas fa-flag"></i> Report Issue</a>
        </nav>
        <div class="sidebar-footer">
            <a href="/webtech/webTec-project-jobPortal/job_seeker/controllers/JobController.php?logout=1" class="sidebar-logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </aside>

    <main class="main-content">

        <div class="page-heading">
            <h2><i class="fas fa-flag"></i> Report Issue</h2>
            <p>Report a problem with a job listing or employer.</p>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'submitted'): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Your complaint has been submitted. We'll review it shortly.
        </div>
        <?php endif; ?>

        <?php foreach ($errors as $e): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>

        
        <div class="card" style="max-width:700px;">
            <h3><i class="fas fa-exclamation-circle" style="color:#a855f7;"></i> Submit a Complaint</h3>
            <p style="color:#666;font-size:13px;margin-bottom:20px;">Select the job you want to report and describe the issue.</p>

            <form method="POST" action="/webtech/webTec-project-jobPortal/job_seeker/controllers/JobController.php" id="complaintForm" novalidate>

                <div class="form-group">
                    <label>Select Job / Employer *</label>
                    <select name="subject_id" id="comp_subject">
                        <option value="">— Select a job —</option>
                        <?php foreach ($jobs_list as $j):
                            $company = $j['company_name'] ?? $j['employer_name'];
                        ?>
                        <option value="<?= $j['id'] ?>">
                            <?= htmlspecialchars($j['title']) ?> — <?= htmlspecialchars($company) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="field-error" id="compSubjErr"></span>
                </div>

                <div class="form-group">
                    <label>Description * <small style="color:#555;">(min 20 characters)</small></label>
                    <textarea name="description" id="comp_description" rows="5"
                        placeholder="Describe the issue in detail…" required></textarea>
                    <span class="field-error" id="compDescErr"></span>
                </div>

                <div style="margin-top:20px;">
                    <button type="submit" name="submit_complaint" onclick="return validateComplaint();" style="padding:15px 40px;font-size:15px;">
                        <i class="fas fa-paper-plane"></i> Submit Complaint
                    </button>
                </div>
            </form>
        </div>

        <!-- Previous Complaints -->
        <?php if (!empty($complaints)): ?>
        <div class="card">
            <h3><i class="fas fa-history" style="color:#a855f7;"></i> Previous Complaints
                <span style="color:#a855f7;font-weight:700;">(<?= count($complaints) ?>)</span>
            </h3>
            <div class="jobs-table">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Admin Note</th>
                            <th>Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($complaints as $c): ?>
                    <tr>
                        <td style="color:#888;font-size:13px;"><?= $c['id'] ?></td>
                        <td><?= htmlspecialchars(mb_strimwidth($c['description'], 0, 80, '…')) ?></td>
                        <td>
                            <span class="status-badge status-<?= $c['status'] === 'open' ? 'submitted' : 'shortlisted' ?>">
                                <?= ucfirst($c['status']) ?>
                            </span>
                        </td>
                        <td style="color:#888;font-size:13px;">
                            <?= $c['adminnote'] ? htmlspecialchars($c['adminnote']) : '—' ?>
                        </td>
                        <td style="color:#888;font-size:13px;"><?= date('d M Y', strtotime($c['createdat'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

    </main>
</div>

<script src="script.js"></script>
</body>
</html>