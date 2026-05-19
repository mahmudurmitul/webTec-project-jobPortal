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

<?php if (!isset($_SESSION['recruiter_id'])): ?>
    <?php include __DIR__ . "/views/auth.php"; ?>
<?php else: ?>

<div class="app-layout">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <i class="fas fa-headset"></i>
            <span>RecruiterHub</span>
        </div>
        <div class="sidebar-user">
            <?php $pic = $recruiterProfile['profilepic'] ?? ''; ?>
            <?php if ($pic): ?>
                <img src="<?= htmlspecialchars($pic) ?>" alt="Profile" class="avatar-sm"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="avatar-placeholder-sm" style="display:none;"><i class="fas fa-user"></i></div>
            <?php else: ?>
                <div class="avatar-placeholder-sm"><i class="fas fa-user"></i></div>
            <?php endif; ?>
            <div>
                <div class="sidebar-name"><?= htmlspecialchars($_SESSION['recruiter_name']) ?></div>
                <div class="sidebar-role">
                    <?php if ($recruiterProfile && $recruiterProfile['isverified']): ?>
                        <span class="badge-verified">&#10003; Verified</span>
                    <?php else: ?>
                        <span class="badge-pending">Pending Approval</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="index.php"                    class="nav-item <?= $page==='dashboard'    ?'active':'' ?>"><i class="fas fa-th-large"></i> Dashboard</a>
            <a href="index.php?page=profile"       class="nav-item <?= $page==='profile'      ?'active':'' ?>"><i class="fas fa-id-card"></i> My Profile</a>

            <div class="nav-section">Clients &amp; Jobs</div>
            <a href="index.php?page=clients"       class="nav-item <?= $page==='clients'      ?'active':'' ?>"><i class="fas fa-building"></i> Clients</a>
            <a href="index.php?page=jobs"          class="nav-item <?= $page==='jobs'         ?'active':'' ?>"><i class="fas fa-briefcase"></i> Job Postings</a>
            <a href="index.php?page=job_form"      class="nav-item <?= $page==='job_form'     ?'active':'' ?>"><i class="fas fa-plus-circle"></i> Post a Job</a>

            <div class="nav-section">Candidates</div>
            <a href="index.php?page=seekers"       class="nav-item <?= $page==='seekers'      ?'active':'' ?>"><i class="fas fa-search"></i> Find Seekers</a>
            <a href="index.php?page=applications"  class="nav-item <?= $page==='applications' ?'active':'' ?>"><i class="fas fa-file-alt"></i> Applications</a>
            <a href="index.php?page=pipeline"      class="nav-item <?= $page==='pipeline'     ?'active':'' ?>"><i class="fas fa-stream"></i> Pipeline</a>
            <a href="index.php?page=placements"    class="nav-item <?= $page==='placements'   ?'active':'' ?>"><i class="fas fa-trophy"></i> Placements</a>

            <div class="nav-section">Communication</div>
            <a href="index.php?page=outreach" class="nav-item <?= $page==='outreach' ?'active':'' ?>"><i class="fas fa-envelope-open-text"></i> Outreach Messages</a>

            <div class="nav-section">Reports</div>
            <a href="index.php?page=analytics"     class="nav-item <?= $page==='analytics'    ?'active':'' ?>"><i class="fas fa-chart-bar"></i> Analytics</a>

            <div class="nav-section">Account</div>
            <a href="index.php?page=complaint"     class="nav-item <?= $page==='complaint'    ?'active':'' ?>"><i class="fas fa-flag"></i> Complaints</a>
            <a href="index.php?logout=1"           class="nav-item nav-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">

        <?php if (!empty($_GET['msg'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_GET['msg']) ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e): ?>
            <div><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php
        $viewFile = __DIR__ . "/views/{$page}.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            include __DIR__ . "/views/dashboard.php";
        }
        ?>
    </main>
</div>

<?php endif; ?>
<script src="views/script.js"></script>
</body>
</html>