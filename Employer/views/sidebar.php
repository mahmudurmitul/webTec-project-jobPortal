<?php
// $activePage should be set before including this file
$activePage = $activePage ?? '';
$basePath   = $basePath ?? '..';   // '..' from views/, '.' from root
?>

<link rel="stylesheet" href="<?= $basePath ?>/views/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="sidebar">
    <div class="sidebar-logo">
        <i class="fas fa-briefcase"></i>
        <span>HireDesk</span>
    </div>

    <div class="sidebar-user">
        <div class="avatar-placeholder-sm">
            <i class="fas fa-user"></i>
        </div>
        <div>
            <div class="sidebar-name"><?= htmlspecialchars($_SESSION['name'] ?? 'Employer') ?></div>
            <div class="sidebar-role">Employer</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Main</div>

        <a href="<?= $basePath ?>/index.php"
           class="nav-item <?= $activePage === 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-home"></i> Dashboard
        </a>

        <a href="<?= $basePath ?>/views/company_profile.php"
           class="nav-item <?= $activePage === 'edit_profile' ? 'active' : '' ?>">
            <i class="fas fa-user-edit"></i> Edit My Profile
        </a>

        <a href="<?= $basePath ?>/views/create_job.php"
           class="nav-item <?= $activePage === 'create_job' ? 'active' : '' ?>">
            <i class="fas fa-plus-circle"></i> Post a Job
        </a>

        <a href="<?= $basePath ?>/views/shortlisted.php"
           class="nav-item <?= $activePage === 'shortlisted' ? 'active' : '' ?>">
            <i class="fas fa-star"></i> Shortlisted
        </a>

        <div class="nav-section">Company</div>

        <a href="<?= $basePath ?>/views/company_details.php"
           class="nav-item <?= $activePage === 'company_details' ? 'active' : '' ?>">
            <i class="fas fa-building"></i> Manage Company Profile
        </a>

        <a href="<?= $basePath ?>/views/analytics.php"
           class="nav-item <?= $activePage === 'analytics' ? 'active' : '' ?>">
            <i class="fas fa-chart-bar"></i> Analytics
        </a>

        <a href="<?= $basePath ?>/views/recruiters.php"
           class="nav-item <?= $activePage === 'recruiters' ? 'active' : '' ?>">
            <i class="fas fa-user-tie"></i> Recruiters
        </a>

        <a href="<?= $basePath ?>/views/messages.php"
           class="nav-item <?= $activePage === 'messages' ? 'active' : '' ?>">
            <i class="fas fa-envelope"></i> Messages
        </a>

        <div class="nav-section">Support</div>

        <a href="<?= $basePath ?>/views/complaints.php"
           class="nav-item <?= $activePage === 'complaints' ? 'active' : '' ?>">
            <i class="fas fa-flag"></i> Complaints
        </a>

        <a href="<?= $basePath ?>/logout.php"
           class="nav-item nav-logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</div>