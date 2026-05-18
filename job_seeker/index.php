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
<div class="container">

    <!-- HEADER -->
    <div class="header">
        <div class="logo">
            <h1><i class="fas fa-briefcase"></i> JobPortal Pro</h1>
            <p>Find Your Dream Job</p>
        </div>
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

    <?php if (!isset($_SESSION['seeker_id'])): ?>
    <!-- ── AUTH CARDS ── -->
    <div style="display:flex;gap:24px;flex-wrap:wrap;justify-content:center;">

        <div class="card auth-card">
            <h3><i class="fas fa-sign-in-alt" style="color:#00d4ff;"></i> Login</h3>
            <p style="color:#666;font-size:13px;margin-bottom:20px;">Welcome back — log in to continue</p>
            <form method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="your@email.com" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" name="login" style="width:100%;">
                    <i class="fas fa-arrow-right"></i> Login
                </button>
            </form>
        </div>

        <div class="card auth-card">
            <h3><i class="fas fa-user-plus" style="color:#00d4ff;"></i> Create Account</h3>
            <p style="color:#666;font-size:13px;margin-bottom:20px;">New here? Register as a job seeker</p>
            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="your@email.com" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" placeholder="+88017xxxxxxxx" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Min 6 characters" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" placeholder="Re-enter password" required>
                </div>
                <button type="submit" name="register" style="width:100%;">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>
        </div>

    </div>

    <?php else: ?>
    <!-- ── LOGGED IN DASHBOARD ── -->

    <div class="welcome">
        <div class="welcome-inner">
            <div class="welcome-user">
                <?php if (!empty($user_profile['profilepic'])): ?>
                    <img src="<?= htmlspecialchars($user_profile['profilepic']) ?>" class="profile-avatar" alt="Profile">
                <?php else: ?>
                    <div class="profile-avatar placeholder"><i class="fas fa-user"></i></div>
                <?php endif; ?>
                <div>
                    <h2>Welcome back, <?= htmlspecialchars($_SESSION['seeker_name']) ?>!</h2>
                    <p><?= !empty($user_profile['headline']) ? htmlspecialchars($user_profile['headline']) : 'Complete your profile to improve job matches' ?></p>
                </div>
            </div>
            <a href="controllers/JobController.php?logout=1" class="btn btn-secondary" style="font-size:13px;padding:10px 20px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Quick nav -->
    <div class="nav-links">
        <a href="index.php" class="active"><i class="fas fa-briefcase"></i> Job Listings</a>
        <a href="views/profile.php"><i class="fas fa-user-edit"></i> Edit Profile</a>
        <a href="views/applications.php"><i class="fas fa-file-alt"></i> My Applications</a>
        <a href="views/saved.php"><i class="fas fa-bookmark"></i> Saved Jobs
            <span id="saved-count" style="background:rgba(0,212,255,0.2);padding:1px 7px;border-radius:10px;font-size:12px;margin-left:4px;">
                <?= $saved_count ?>
            </span>
        </a>
        <a href="views/messages.php">
            <i class="fas fa-envelope"></i> Messages
            <?php if ($unread_msgs > 0): ?>
            <span style="background:rgba(239,68,68,0.85);color:#fff;padding:1px 7px;border-radius:10px;font-size:12px;margin-left:4px;">
                <?= $unread_msgs ?>
            </span>
            <?php endif; ?>
        </a>
    </div>

    <!-- Filters -->
    <div class="card">
        <h3><i class="fas fa-search" style="color:#00d4ff;"></i> Search & Filter Jobs</h3>
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
            <div class="filter-field" style="max-width:120px;">
                <label>Min Salary (৳)</label>
                <input type="number" id="sal_min" placeholder="0" min="0">
            </div>
            <div class="filter-field" style="max-width:120px;">
                <label>Max Salary (৳)</label>
                <input type="number" id="sal_max" placeholder="Any" min="0">
            </div>
            <div class="filter-actions">
                <button onclick="filterJobs()"><i class="fas fa-search"></i> Search</button>
                <button onclick="resetFilters()" class="btn-secondary" style="background:rgba(60,60,60,0.9);border:none;padding:13px 20px;border-radius:10px;color:#aaa;font-size:15px;cursor:pointer;font-weight:600;">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Job listings (AJAX) -->
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3><i class="fas fa-list" style="color:#00d4ff;"></i>
                Active Jobs <span id="job-count" style="color:#00d4ff;font-weight:700;"></span>
            </h3>
            <button onclick="loadJobs()" class="btn btn-secondary" style="font-size:13px;padding:9px 18px;">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
        <div class="jobs-table">
            <div id="jobs">
                <div style="text-align:center;padding:50px;color:#555;">
                    <i class="fas fa-circle-notch fa-spin" style="font-size:36px;color:#00d4ff;display:block;margin-bottom:16px;"></i>
                    <p>Loading jobs…</p>
                </div>
            </div>
        </div>
    </div>

    <?php endif; ?>

</div>

<script src="views/script.js"></script>
</body>
</html>