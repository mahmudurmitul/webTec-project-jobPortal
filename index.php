<?php
require_once __DIR__ . "/controller/controller.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RecruiterHub Pro</title>
    <link rel="stylesheet" href="views/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<?php if (isset($_SESSION['recruiter_id'])): ?>

<div class="app-layout">

    
    <aside class="sidebar">
        <div class="sidebar-logo">
            <i class="fas fa-headset"></i>
            <span>RecruiterHub</span>
        </div>
        <div class="sidebar-user">
            <?php
            $picPath = $recruiterProfile['profilepic'] ?? '';
            ?>
            <?php if ($picPath): ?>
                <img src="<?= htmlspecialchars($picPath) ?>"
                     alt="Profile"
                     class="avatar-sm"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="avatar-placeholder-sm" style="display:none;"><i class="fas fa-user"></i></div>
            <?php else: ?>
                <div class="avatar-placeholder-sm"><i class="fas fa-user"></i></div>
            <?php endif; ?>
            <div>
                <div class="sidebar-name"><?= htmlspecialchars($_SESSION['recruiter_name']) ?></div>
                <div class="sidebar-role">Recruiter <?= $recruiterProfile && $recruiterProfile['isverified'] ? '<span class="badge-verified">✓ Verified</span>' : '<span class="badge-pending">Pending</span>' ?></div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="?page=dashboard" class="nav-item <?= $page==='dashboard'?'active':'' ?>"><i class="fas fa-th-large"></i> Dashboard</a>
            <a href="?page=profile" class="nav-item <?= $page==='profile'?'active':'' ?>"><i class="fas fa-id-card"></i> My Profile</a>
            <div class="nav-section">Clients & Jobs</div>
            <a href="?page=clients" class="nav-item <?= $page==='clients'?'active':'' ?>"><i class="fas fa-building"></i> Clients</a>
            <a href="?page=jobs" class="nav-item <?= $page==='jobs'?'active':'' ?>"><i class="fas fa-briefcase"></i> Job Postings</a>
            <a href="?page=job_form" class="nav-item <?= $page==='job_form'?'active':'' ?>"><i class="fas fa-plus-circle"></i> Post a Job</a>
            <div class="nav-section">Candidates</div>
            <a href="?page=seekers" class="nav-item <?= $page==='seekers'?'active':'' ?>"><i class="fas fa-search"></i> Find Seekers</a>
            <a href="?page=applications" class="nav-item <?= $page==='applications'?'active':'' ?>"><i class="fas fa-file-alt"></i> Applications</a>
            <a href="?page=pipeline" class="nav-item <?= $page==='pipeline'?'active':'' ?>"><i class="fas fa-stream"></i> Pipeline</a>
            <a href="?page=placements" class="nav-item <?= $page==='placements'?'active':'' ?>"><i class="fas fa-trophy"></i> Placements</a>
            <div class="nav-section">Communication</div>
            <a href="?page=outreach" class="nav-item <?= $page==='outreach'?'active':'' ?>"><i class="fas fa-envelope-open-text"></i> Outreach</a>
            <a href="?page=messages" class="nav-item <?= $page==='messages'?'active':'' ?>"><i class="fas fa-comments"></i> Messages</a>
            <div class="nav-section">Reports</div>
            <a href="?page=analytics" class="nav-item <?= $page==='analytics'?'active':'' ?>"><i class="fas fa-chart-bar"></i> Analytics</a>
            <a href="?page=complaint" class="nav-item <?= $page==='complaint'?'active':'' ?>"><i class="fas fa-flag"></i> Complaints</a>
            <div class="nav-section">Account</div>
            <a href="?logout=1" class="nav-item nav-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    
    <main class="main-content">

        <?php if (!empty($_GET['msg'])): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            <?php foreach ($errors as $e): ?><div><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php
        switch ($page) {
            case 'dashboard':   include "views/dashboard.php";    break;
            case 'profile':     include "views/profile.php";      break;
            case 'clients':     include "views/clients.php";      break;
            case 'jobs':        include "views/jobs.php";          break;
            case 'job_form':    include "views/job_form.php";      break;
            case 'seekers':     include "views/seekers.php";       break;
            case 'seeker_profile': include "views/seeker_profile.php"; break;
            case 'applications': include "views/applications.php"; break;
            case 'pipeline':    include "views/pipeline.php";      break;
            case 'placements':  include "views/placements.php";    break;
            case 'analytics':   include "views/analytics.php";     break;
            case 'outreach':    include "views/outreach.php";      break;
            case 'messages':    include "views/messages.php";      break;
            case 'complaint':   include "views/complaint.php";     break;
            default:            include "views/dashboard.php";     break;
        }
        ?>
    </main>
</div>

<?php else: ?>

<?php include "views/auth.php"; ?>
<?php endif; ?>

<script src="views/script.js"></script>
</body>
</html>