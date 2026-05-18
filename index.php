<?php
require_once "controller.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobPortal Admin</title>
    <link rel="stylesheet" href="views/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<?php if (!isset($_SESSION['admin_id'])): ?>
    <?php include "views/login.php"; ?>
<?php else: ?>

<div class="app-layout">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <i class="fas fa-shield-halved"></i>
            <span>AdminPanel</span>
        </div>
        <div class="sidebar-user">
            <div class="avatar-placeholder-sm"><i class="fas fa-user-shield"></i></div>
            <div>
                <div class="sidebar-name"><?= htmlspecialchars($_SESSION['admin_name']) ?></div>
                <div class="sidebar-role">Super Admin</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="?page=dashboard"    class="nav-item <?= $page==='dashboard'   ?'active':'' ?>"><i class="fas fa-th-large"></i> Dashboard</a>

            <div class="nav-section">User Management</div>
            <a href="?page=employers"    class="nav-item <?= $page==='employers'   ?'active':'' ?>">
                <i class="fas fa-building"></i> Employers
                <?php
                global $conn;
                $pend = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM users WHERE role='employer' AND isverified=0 AND isactive=1"))['c'];
                if ($pend > 0): ?><span class="nav-badge"><?= $pend ?></span><?php endif; ?>
            </a>
            <a href="?page=recruiters"   class="nav-item <?= $page==='recruiters'  ?'active':'' ?>">
                <i class="fas fa-headset"></i> Recruiters
                <?php
                $pend2 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM users WHERE role='recruiter' AND isverified=0 AND isactive=1"))['c'];
                if ($pend2 > 0): ?><span class="nav-badge"><?= $pend2 ?></span><?php endif; ?>
            </a>
            <a href="?page=seekers"      class="nav-item <?= $page==='seekers'     ?'active':'' ?>"><i class="fas fa-users"></i> Seekers</a>

            <div class="nav-section">Content</div>
            <a href="?page=categories"   class="nav-item <?= $page==='categories'  ?'active':'' ?>"><i class="fas fa-tags"></i> Categories</a>
            <a href="?page=jobs"         class="nav-item <?= $page==='jobs'        ?'active':'' ?>"><i class="fas fa-briefcase"></i> Job Listings</a>

            <div class="nav-section">Governance</div>
            <a href="?page=complaints"   class="nav-item <?= $page==='complaints'  ?'active':'' ?>">
                <i class="fas fa-flag"></i> Complaints
                <?php
                $open = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM complaints WHERE status='open'"))['c'];
                if ($open > 0): ?><span class="nav-badge nav-badge-red"><?= $open ?></span><?php endif; ?>
            </a>
            <a href="?page=settings"     class="nav-item <?= $page==='settings'    ?'active':'' ?>"><i class="fas fa-sliders"></i> Settings</a>
            <a href="?page=announcements" class="nav-item <?= $page==='announcements'?'active':'' ?>"><i class="fas fa-bullhorn"></i> Announcements</a>

            <div class="nav-section">Reports</div>
            <a href="?page=analytics"    class="nav-item <?= $page==='analytics'   ?'active':'' ?>"><i class="fas fa-chart-bar"></i> Analytics</a>
            <a href="?page=user_growth"  class="nav-item <?= $page==='user_growth' ?'active':'' ?>"><i class="fas fa-chart-line"></i> User Growth</a>
            <a href="?page=report"       class="nav-item <?= $page==='report'      ?'active':'' ?>"><i class="fas fa-file-export"></i> Monthly Report</a>

            <div class="nav-section">Account</div>
            <a href="?logout=1" class="nav-item nav-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <!-- Main -->
    <main class="main-content">

        <?php if (!empty($_GET['msg'])): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e): ?>
            <div><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php
        switch ($page) {
            case 'dashboard':     include "views/dashboard.php";     break;
            case 'employers':     include "views/employers.php";     break;
            case 'recruiters':    include "views/recruiters.php";    break;
            case 'seekers':       include "views/seekers.php";       break;
            case 'categories':    include "views/categories.php";    break;
            case 'jobs':          include "views/jobs.php";          break;
            case 'complaints':    include "views/complaints.php";    break;
            case 'settings':      include "views/settings.php";      break;
            case 'announcements': include "views/announcements.php"; break;
            case 'analytics':     include "views/analytics.php";     break;
            case 'user_growth':   include "views/user_growth.php";   break;
            case 'report':        include "views/report.php";        break;
            default:              include "views/dashboard.php";     break;
        }
        ?>
    </main>
</div>

<?php endif; ?>

<script src="views/script.js"></script>
</body>
</html>
