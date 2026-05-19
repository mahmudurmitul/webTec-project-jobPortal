<?php
require_once __DIR__ . "/controllers/JobController.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JobPortal – Find Your Dream Job</title>
    <link rel="stylesheet" href="views/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php if (!isset($_SESSION['seeker_id'])): ?>

<div class="auth-wrapper">
    <div class="auth-header">
        <h1><i class="fas fa-briefcase"></i> JobPortal Pro</h1>
        <p>Find Your Dream Job</p>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <?php $msgs = [
            'registered'      => 'Welcome! Your account has been created.',
            'loggedin'        => 'Logged in successfully.',
            'profile_updated' => 'Profile updated successfully.',
        ]; ?>
        <?php if (isset($msgs[$_GET['msg']])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($msgs[$_GET['msg']]) ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php foreach ($errors as $e): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <?= htmlspecialchars($e) ?>
    </div>
    <?php endforeach; ?>

    <div class="auth-cards">

        <div class="card auth-card">
            <h3><i class="fas fa-sign-in-alt" style="color:#a855f7;"></i> Login</h3>
            <p class="card-sub">Welcome back — log in to continue</p>
            <form method="POST" id="loginForm" novalidate>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" id="loginEmail" placeholder="your@email.com" required>
                    <span class="field-error" id="loginEmailErr"></span>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="loginPass" placeholder="••••••••" required>
                    <span class="field-error" id="loginPassErr"></span>
                </div>
                <button type="submit" name="login" onclick="return validateLogin();" style="width:100%;">
                    <i class="fas fa-arrow-right"></i> Login
                </button>
            </form>
        </div>

        <div class="card auth-card">
            <h3><i class="fas fa-user-plus" style="color:#a855f7;"></i> Create Account</h3>
            <p class="card-sub">New here? Register as a job seeker</p>
            <form method="POST" id="regForm" novalidate>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" id="regName" placeholder="John Doe" required>
                    <span class="field-error" id="regNameErr"></span>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" id="regEmail" placeholder="your@email.com" required>
                    <span class="field-error" id="regEmailErr"></span>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" id="regPhone" placeholder="+88017xxxxxxxx" required>
                    <span class="field-error" id="regPhoneErr"></span>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="regPass" placeholder="Min 6 characters" required>
                    <span class="field-error" id="regPassErr"></span>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" id="regConfirm" placeholder="Re-enter password" required>
                    <span class="field-error" id="regConfirmErr"></span>
                </div>
                <button type="submit" name="register" onclick="return validateRegister();" style="width:100%;">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>
        </div>

    </div>
</div>

<?php else: ?>


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
                <span><?= !empty($user_profile['headline']) ? htmlspecialchars(mb_strimwidth($user_profile['headline'], 0, 28, '…')) : 'Job Seeker' ?></span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="/webtech/webTec-project-jobPortal/job_seeker/index.php" class="active"><i class="fas fa-briefcase"></i> Job Listings</a>
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/profile.php"><i class="fas fa-user-edit"></i> Edit Profile</a>
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/applications.php"><i class="fas fa-file-alt"></i> My Applications</a>
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/saved.php">
                <i class="fas fa-bookmark"></i> Saved Jobs
                <span class="nav-badge"><?= $saved_count ?></span>
            </a>
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

        <?php if (isset($_GET['msg'])): ?>
            <?php $msgs = [
                'registered'      => 'Welcome! Your account has been created.',
                'loggedin'        => 'Logged in successfully.',
                'profile_updated' => 'Profile updated successfully.',
            ]; ?>
            <?php if (isset($msgs[$_GET['msg']])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($msgs[$_GET['msg']]) ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php foreach ($errors as $e): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            <?= htmlspecialchars($e) ?>
        </div>
        <?php endforeach; ?>

        <div class="page-heading">
            <h2><i class="fas fa-briefcase"></i> Job Listings</h2>
            <p>Browse and apply to active positions</p>
        </div>

        <div class="card">
            <h3><i class="fas fa-search" style="color:#a855f7;"></i> Search &amp; Filter Jobs</h3>
            <div class="filters">
                <div class="filter-field">
                    <label>Keyword</label>
                    <input type="text" id="keyword" placeholder="Title, skill, company…">
                </div>
                <div class="filter-field">
                    <label>Category</label>
                    <select id="category">
                        <option value="">All Categories</option>
                        <?php $cats = getCategories(); while ($cat = mysqli_fetch_assoc($cats)): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="filter-field">
                    <label>Location</label>
                    <input type="text" id="location" placeholder="Dhaka, Chittagong…">
                </div>
                <div class="filter-field">
                    <label>Job Type</label>
                    <select id="type">
                        <option value="">All Types</option>
                        <option value="full-time">Full Time</option>
                        <option value="part-time">Part Time</option>
                        <option value="remote">Remote</option>
                        <option value="contract">Contract</option>
                    </select>
                </div>
                <div class="filter-field">
                    <label>Experience</label>
                    <select id="exp">
                        <option value="">All Levels</option>
                        <option value="entry">Entry</option>
                        <option value="mid">Mid</option>
                        <option value="senior">Senior</option>
                    </select>
                </div>
                <div class="filter-field" style="max-width:130px;">
                    <label>Min Salary (৳)</label>
                    <input type="number" id="sal_min" placeholder="0" min="0">
                </div>
                <div class="filter-field" style="max-width:130px;">
                    <label>Max Salary (৳)</label>
                    <input type="number" id="sal_max" placeholder="Any" min="0">
                </div>
                <div class="filter-actions">
                    <button onclick="filterJobs()"><i class="fas fa-search"></i> Search</button>
                    <button onclick="resetFilters()" class="btn-secondary-plain">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <div class="card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h3><i class="fas fa-list" style="color:#a855f7;"></i>
                    Active Jobs <span id="job-count" style="color:#a855f7;font-weight:700;"></span>
                </h3>
                <button onclick="loadJobs()" class="btn-secondary-plain" style="font-size:13px;padding:9px 18px;border-radius:8px;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.05);color:#aaa;cursor:pointer;">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
            <div class="jobs-table">
                <div id="jobs">
                    <div style="text-align:center;padding:50px;color:#555;">
                        <i class="fas fa-circle-notch fa-spin" style="font-size:36px;color:#a855f7;display:block;margin-bottom:16px;"></i>
                        <p>Loading jobs…</p>
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>

<?php endif; ?>

<script src="views/script.js"></script>
</body>
</html>