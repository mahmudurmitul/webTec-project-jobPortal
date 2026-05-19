<?php
require_once __DIR__ . "/../controllers/JobController.php";

if (!isset($_SESSION['seeker_id'])) {
    header("Location: /webtech/webTec-project-jobPortal/job_seeker/index.php");
    exit();
}

$alerts = getJobAlerts($_SESSION['seeker_id']);
$cats   = getCategories();
$cat_list = [];
while ($c = mysqli_fetch_assoc($cats)) $cat_list[] = $c;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Job Alerts – JobPortal</title>
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
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/alerts.php" class="active"><i class="fas fa-bell"></i> Job Alerts</a>
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
            <h2><i class="fas fa-bell"></i> Job Alerts</h2>
            <p>Get notified when new jobs match your preferences.</p>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'created'): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Job alert created successfully!
        </div>
        <?php endif; ?>

        <?php foreach ($errors as $e): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>

        
        <div class="card">
            <h3><i class="fas fa-plus-circle" style="color:#a855f7;"></i> Create New Alert</h3>
            <p style="color:#666;font-size:13px;margin-bottom:20px;">Fill at least one field to create an alert.</p>

            <form method="POST" action="/webtech/webTec-project-jobPortal/job_seeker/controllers/JobController.php" id="alertForm" novalidate>
                <div class="filters" style="gap:16px;">
                    <div class="filter-field">
                        <label>Keyword</label>
                        <input type="text" name="alert_keyword" id="alert_keyword" placeholder="PHP, Marketing…">
                    </div>
                    <div class="filter-field">
                        <label>Category</label>
                        <select name="alert_cat" id="alert_cat">
                            <option value="">All Categories</option>
                            <?php foreach ($cat_list as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-field">
                        <label>Location</label>
                        <input type="text" name="alert_location" id="alert_location" placeholder="Dhaka…">
                    </div>
                    <div class="filter-field">
                        <label>Job Type</label>
                        <select name="alert_type" id="alert_type">
                            <option value="">Any Type</option>
                            <option value="full-time">Full Time</option>
                            <option value="part-time">Part Time</option>
                            <option value="remote">Remote</option>
                            <option value="contract">Contract</option>
                        </select>
                    </div>
                    <div class="filter-actions" style="align-items:flex-end;">
                        <button type="submit" name="create_alert" onclick="return validateAlert();">
                            <i class="fas fa-bell"></i> Create Alert
                        </button>
                    </div>
                </div>
                <span class="field-error" id="alertErr" style="margin-top:8px;display:block;"></span>
            </form>
        </div>

        
        <div class="card">
            <h3><i class="fas fa-list" style="color:#a855f7;"></i> My Alerts
                <span style="color:#a855f7;font-weight:700;">(<?= count($alerts) ?>)</span>
            </h3>

            <?php if (empty($alerts)): ?>
            <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <h3>No alerts yet</h3>
                <p>Create an alert above to get notified about matching jobs.</p>
            </div>
            <?php else: ?>
            <div class="jobs-table">
                <table>
                    <thead>
                        <tr>
                            <th>Keyword</th>
                            <th>Category</th>
                            <th>Location</th>
                            <th>Job Type</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($alerts as $al): ?>
                    <tr id="alert-row-<?= $al['id'] ?>">
                        <td><?= $al['keyword'] ? htmlspecialchars($al['keyword']) : '<span style="color:#555;">—</span>' ?></td>
                        <td><?= $al['catname'] ? '<span class="badge">' . htmlspecialchars($al['catname']) . '</span>' : '<span style="color:#555;">All</span>' ?></td>
                        <td><?= $al['location'] ? htmlspecialchars($al['location']) : '<span style="color:#555;">Any</span>' ?></td>
                        <td>
                            <?php if ($al['jobtype']): ?>
                            <span class="type-badge type-<?= htmlspecialchars($al['jobtype']) ?>">
                                <?= strtoupper(str_replace('-', ' ', $al['jobtype'])) ?>
                            </span>
                            <?php else: ?>
                            <span style="color:#555;">Any</span>
                            <?php endif; ?>
                        </td>
                        <td style="color:#888;font-size:13px;"><?= date('d M Y', strtotime($al['createdat'])) ?></td>
                        <td>
                            <button class="btn btn-danger btn-sm" onclick="deleteAlert(this, <?= $al['id'] ?>)">
                                <i class="fas fa-trash"></i> Delete
                            </button>
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