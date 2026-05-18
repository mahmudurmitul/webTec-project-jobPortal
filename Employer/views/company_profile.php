<?php
require_once "../config.php";
require_once "../model.php";
require_once "../controller.php";

employerOnlyFrom();

$userid  = $_SESSION['userid'];
$message = "";
$msgtype = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (saveEmployerProfile($conn, $userid)) {
        $message = "Profile updated successfully!";
        $msgtype = "success";
    } else {
        $message = "Failed to update profile. Please check your input.";
        $msgtype = "error";
    }
}

$profile = getEmployerProfile($conn, $userid);

$activePage = 'edit_profile';
$basePath   = '..';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit My Profile</title>
</head>
<body>
<div class="app-layout">
    <?php include "sidebar.php"; ?>

    <main class="main-content">
        <div class="page-header">
            <h2>
                <i class="fas fa-user-edit" style="color:var(--accent2);margin-right:8px"></i>
                Edit My Profile
            </h2>
            <p>Update your employer registration information.</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $msgtype ?>">
                <i class="fas fa-<?= $msgtype === 'success' ? 'check' : 'circle-exclamation' ?>"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <form method="POST">
                <h3 style="margin-bottom:16px;">
                    <i class="fas fa-user"></i> Employer Details
                </h3>

                <div class="form-row">
                    <div class="form-group">
                        <label>Employer Name *</label>
                        <input type="text" name="name" required
                               value="<?= htmlspecialchars($profile['name'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" required
                               value="<?= htmlspecialchars($profile['email'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone"
                           value="<?= htmlspecialchars($profile['phone'] ?? '') ?>">
                </div>

                <hr style="margin:24px 0;border:0;border-top:1px solid var(--border);">

                <h3 style="margin-bottom:16px;">
                    <i class="fas fa-lock"></i> Change Password
                </h3>

                <p class="text-muted" style="margin-bottom:12px;">
                    Leave password fields blank if you do not want to change your password.
                </p>

                <div class="form-row">
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="password" placeholder="Minimum 6 characters">
                    </div>

                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" placeholder="Re-enter password">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Profile
                </button>
            </form>
        </div>
    </main>
</div>
</body>
</html>