<?php
require_once __DIR__ . "/../controllers/JobController.php";

if (!isset($_SESSION['seeker_id'])) {
    header("Location: /webtech/webTec-project-jobPortal/job_seeker/index.php");
    exit();
}

$applications = getSeekerApplications($_SESSION['seeker_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Applications – JobPortal</title>
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
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/applications.php" class="active"><i class="fas fa-file-alt"></i> My Applications</a>
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

        <div class="page-heading">
            <h2><i class="fas fa-file-alt"></i> My Applications</h2>
            <p>Track all your job applications and their current status.</p>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'applied'): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Your application has been submitted successfully!
        </div>
        <?php endif; ?>

        <div class="card">

            <?php if (empty($applications)): ?>
            <div class="empty-state">
                <i class="fas fa-file-alt"></i>
                <h3>No applications yet</h3>
                <p>Browse jobs and apply to positions you're interested in.</p>
                <a href="/webtech/webTec-project-jobPortal/job_seeker/index.php" class="btn" style="margin-top:16px;display:inline-block;">
                    <i class="fas fa-search"></i> Browse Jobs
                </a>
            </div>

            <?php else:
                $counts = [];
                foreach ($applications as $a) {
                    $s = $a['status'];
                    $counts[$s] = ($counts[$s] ?? 0) + 1;
                }
                $statColors = [
                    'submitted'   => '#3b82f6',
                    'reviewed'    => '#f59e0b',
                    'shortlisted' => '#10b981',
                    'interview'   => '#a855f7',
                    'rejected'    => '#ef4444',
                    'withdrawn'   => '#64748b',
                ];
            ?>
            <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:22px;">
                <?php foreach ($statColors as $s => $c):
                    if (!isset($counts[$s])) continue; ?>
                <div style="background:rgba(35,35,35,0.9);border:1px solid rgba(255,255,255,0.07);border-radius:10px;padding:12px 18px;text-align:center;min-width:100px;">
                    <div style="font-size:22px;font-weight:700;color:<?= $c ?>;"><?= $counts[$s] ?></div>
                    <div style="font-size:12px;color:#888;text-transform:capitalize;"><?= $s ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="jobs-table">
                <table>
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Company</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Applied On</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($applications as $app):
                        $company  = $app['company_name'] ?? $app['employer_name'];
                        $applied  = date('d M Y', strtotime($app['appliedat']));
                        $deadline = date('d M Y', strtotime($app['deadline']));
                        $status   = $app['status'];
                    ?>
                    <tr>
                        <td style="font-weight:600;">
                            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/job.php?id=<?= $app['job_id'] ?>" style="color:#e0e0e0;text-decoration:none;">
                                <?= htmlspecialchars($app['title']) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($company) ?></td>
                        <td><?= htmlspecialchars($app['location']) ?></td>
                        <td>
                            <span class="type-badge type-<?= htmlspecialchars($app['job_type']) ?>">
                                <?= htmlspecialchars(str_replace('-',' ',strtoupper($app['job_type']))) ?>
                            </span>
                        </td>
                        <td><?= $applied ?></td>
                        <td style="color:<?= strtotime($app['deadline']) < time() ? '#ef4444' : '#ccc' ?>">
                            <?= $deadline ?>
                        </td>
                        <td>
                            <span class="status-badge status-<?= $status ?>">
                                <?= ucfirst($status) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($status === 'submitted'): ?>
                            <button class="btn btn-danger btn-sm"
                                onclick="withdrawApp(this, <?= $app['id'] ?>)">
                                <i class="fas fa-undo"></i> Withdraw
                            </button>
                            <?php else: ?>
                            <span style="color:#555;font-size:13px;">—</span>
                            <?php endif; ?>
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