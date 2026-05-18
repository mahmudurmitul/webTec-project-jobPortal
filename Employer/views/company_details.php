<?php
require_once "../config.php";
require_once "../model.php";
require_once "../controller.php";

employerOnlyFrom();

$userid  = $_SESSION['userid'];
$message = "";
$msgtype = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (saveCompanyProfile($conn, $userid)) {
        $message = "Company profile updated successfully!";
        $msgtype = "success";
    } else {
        $message = "Failed to update company profile.";
        $msgtype = "error";
    }
}

$profile = getCompanyProfile($conn, $userid);

$activePage = 'company_details';
$basePath   = '..';

$sizes = ['1-10', '11-50', '51-200', '200+'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Company Profile</title>
</head>
<body>
<div class="app-layout">
    <?php include "sidebar.php"; ?>

    <main class="main-content">
        <div class="page-header">
            <h2>
                <i class="fas fa-building" style="color:var(--accent2);margin-right:8px"></i>
                Manage Company Profile
            </h2>
            <p>Update your company information shown to job seekers.</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $msgtype ?>">
                <i class="fas fa-<?= $msgtype === 'success' ? 'check' : 'circle-exclamation' ?>"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($profile['logopath'])): ?>
            <div class="card" style="display:flex;align-items:center;gap:16px;padding:16px 24px;margin-bottom:16px;">
                <img src="../../<?= htmlspecialchars($profile['logopath']) ?>" alt="Company Logo"
                     style="height:60px;border-radius:10px;border:1px solid var(--border)">
                <div>
                    <strong><?= htmlspecialchars($profile['companyname'] ?? '') ?></strong><br>
                    <span class="text-muted"><?= htmlspecialchars($profile['industry'] ?? '') ?></span>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <form method="POST" enctype="multipart/form-data">
                <h3 style="margin-bottom:16px;">
                    <i class="fas fa-building"></i> Company Details
                </h3>

                <div class="form-row">
                    <div class="form-group">
                        <label>Company Name *</label>
                        <input type="text" name="companyname" required
                               value="<?= htmlspecialchars($profile['companyname'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Industry</label>
                        <input type="text" name="industry"
                               value="<?= htmlspecialchars($profile['industry'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Company Size</label>
                        <select name="companysize">
                            <?php foreach ($sizes as $sz): ?>
                                <option value="<?= $sz ?>" <?= ($profile['companysize'] ?? '') === $sz ? 'selected' : '' ?>>
                                    <?= $sz ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Website</label>
                        <input type="text" name="website"
                               value="<?= htmlspecialchars($profile['website'] ?? '') ?>"
                               placeholder="https://example.com">
                    </div>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4"><?= htmlspecialchars($profile['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" rows="2"><?= htmlspecialchars($profile['address'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Company Logo</label>
                    <input type="file" name="logo" accept="image/*">
                    <small class="text-muted">Accepted: JPG, PNG, WEBP</small>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Company Profile
                </button>
            </form>
        </div>
    </main>
</div>
</body>
</html>