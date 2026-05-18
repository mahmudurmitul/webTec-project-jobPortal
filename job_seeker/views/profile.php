<?php
require_once __DIR__ . "/../controllers/JobController.php";

if (!isset($_SESSION['seeker_id'])) {
    header("Location: /job_seeker/index.php");
    exit();
}

$profile_errors = [];

if (isset($_POST['update_profile'])) {
    $headline           = trim($_POST['headline']           ?? '');
    $years_experience   = intval($_POST['yearsexperience']  ?? 0);
    $current_salary     = intval($_POST['currentsalary']    ?? 0);
    $expected_salary    = intval($_POST['expectedsalary']   ?? 0);
    $preferred_location = trim($_POST['preferredlocation']  ?? '');
    $education_level    = trim($_POST['educationlevel']     ?? '');
    $skills             = trim($_POST['skills']             ?? '');
    $summary            = trim($_POST['summary']            ?? '');

    if (empty($headline))        $profile_errors[] = "Headline is required.";
    if (empty($skills))          $profile_errors[] = "At least one skill is required.";
    if ($expected_salary <= 0)   $profile_errors[] = "Expected salary must be greater than 0.";
    if (empty($education_level)) $profile_errors[] = "Education level is required.";

    // Profile picture
    if (empty($profile_errors) && isset($_FILES['profilepic']) && $_FILES['profilepic']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['profilepic']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
            $profile_errors[] = "Profile picture must be an image file (jpg, png, gif, webp).";
        } elseif ($_FILES['profilepic']['size'] > 3 * 1024 * 1024) {
            $profile_errors[] = "Profile picture must be under 3 MB.";
        } else {
            if (!is_dir("../uploads")) mkdir("../uploads", 0777, true);
            $filename = "profile_" . $_SESSION['seeker_id'] . "_" . time() . "." . $ext;
            $disk_path = "../uploads/" . $filename;
            $web_path  = "/job_seeker/uploads/" . $filename;
            move_uploaded_file($_FILES['profilepic']['tmp_name'], $disk_path);
            updateProfilePic($_SESSION['seeker_id'], $web_path);
        }
    }

    // Resume
    if (empty($profile_errors) && isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            $profile_errors[] = "Resume must be a PDF file.";
        } elseif ($_FILES['resume']['size'] > 5 * 1024 * 1024) {
            $profile_errors[] = "Resume must be under 5 MB.";
        } else {
            if (!is_dir("../uploads")) mkdir("../uploads", 0777, true);
            $resfilename = "resume_" . $_SESSION['seeker_id'] . "_" . time() . ".pdf";
            $disk_res    = "../uploads/" . $resfilename;
            $web_res     = "/job_seeker/uploads/" . $resfilename;
            move_uploaded_file($_FILES['resume']['tmp_name'], $disk_res);
            updateResume($_SESSION['seeker_id'], $web_res);
        }
    }

    if (empty($profile_errors)) {
        updateProfile(
            $_SESSION['seeker_id'], $headline, $years_experience,
            $current_salary, $expected_salary, $preferred_location,
            $education_level, $skills, $summary
        );
        header("Location: /job_seeker/index.php?msg=profile_updated");
        exit();
    }
    $user_profile = getSeekerById($_SESSION['seeker_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profile – JobPortal</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="container">

    <div class="header">
        <div class="logo">
            <h1><i class="fas fa-briefcase"></i> JobPortal Pro</h1>
            <p>Edit Profile</p>
        </div>
    </div>

    <div class="nav-links">
        <a href="/job_seeker/index.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        <a href="applications.php"><i class="fas fa-file-alt"></i> My Applications</a>
        <a href="saved.php"><i class="fas fa-bookmark"></i> Saved Jobs</a>
    </div>

    <?php foreach ($profile_errors as $e): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($e) ?>
    </div>
    <?php endforeach; ?>

    <div class="card" style="max-width:860px;margin:0 auto;">
        <h3><i class="fas fa-user-edit" style="color:#00d4ff;"></i> Complete Your Profile</h3>
        <p style="color:#666;font-size:13px;margin-bottom:28px;">Fill in all fields to improve your job matching score.</p>

        <form method="POST" enctype="multipart/form-data">

            <div class="form-2col">

                <div class="form-group">
                    <label>Professional Headline *</label>
                    <input type="text" name="headline"
                        placeholder="e.g. PHP Developer with 3 years experience"
                        value="<?= htmlspecialchars($user_profile['headline'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Years of Experience</label>
                    <select name="yearsexperience">
                        <?php foreach ([0,1,2,3,5,10] as $y): ?>
                        <option value="<?= $y ?>" <?= ($user_profile['yearsexperience'] ?? 0) == $y ? 'selected' : '' ?>>
                            <?= $y == 0 ? '0 Years' : ($y >= 10 ? '10+ Years' : ($y >= 5 ? '5+ Years' : "$y Year" . ($y>1?'s':''))) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Current Salary (৳)</label>
                    <input type="number" name="currentsalary" min="0"
                        placeholder="0"
                        value="<?= htmlspecialchars($user_profile['currentsalary'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Expected Salary (৳) *</label>
                    <input type="number" name="expectedsalary" min="0"
                        placeholder="50000"
                        value="<?= htmlspecialchars($user_profile['expectedsalary'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Preferred Location</label>
                    <input type="text" name="preferredlocation"
                        placeholder="Dhaka"
                        value="<?= htmlspecialchars($user_profile['preferredlocation'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Education Level *</label>
                    <select name="educationlevel">
                        <option value="">— Select —</option>
                        <?php foreach (['HSC','Bachelor','Master'] as $edu): ?>
                        <option value="<?= $edu ?>" <?= ($user_profile['educationlevel'] ?? '') === $edu ? 'selected' : '' ?>>
                            <?= $edu ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Skills * <small style="color:#555;">(comma-separated)</small></label>
                    <input type="text" name="skills"
                        placeholder="PHP, MySQL, HTML, CSS, JavaScript"
                        value="<?= htmlspecialchars($user_profile['skills'] ?? '') ?>">
                </div>

            </div>

            <div class="form-group" style="margin-top:10px;">
                <label>Professional Summary</label>
                <textarea name="summary" rows="4"
                    placeholder="Brief professional summary…"><?= htmlspecialchars($user_profile['summary'] ?? '') ?></textarea>
            </div>

            <div class="upload-row">
                <div class="form-group">
                    <label>Resume (PDF, max 5 MB)</label>
                    <?php if (!empty($user_profile['resumepath'])): ?>
                    <p style="font-size:12px;color:#00d4ff;margin-bottom:8px;">
                        <i class="fas fa-file-pdf"></i>
                        Current resume on file.
                        <a href="<?= htmlspecialchars($user_profile['resumepath']) ?>" target="_blank" style="color:#00d4ff;">View</a>
                    </p>
                    <?php endif; ?>
                    <input type="file" name="resume" accept=".pdf">
                </div>

                <div class="form-group">
                    <label>Profile Picture</label>
                    <?php if (!empty($user_profile['profilepic'])): ?>
                    <div style="margin-bottom:10px;">
                        <img src="<?= htmlspecialchars($user_profile['profilepic']) ?>"
                            alt="Profile"
                            style="width:60px;height:60px;border-radius:50%;border:2px solid #00d4ff;object-fit:cover;">
                    </div>
                    <?php endif; ?>
                    <input type="file" name="profilepic" accept="image/*">
                </div>
            </div>

            <div style="text-align:center;margin-top:30px;">
                <button type="submit" name="update_profile" style="width:240px;padding:16px;font-size:16px;">
                    <i class="fas fa-save"></i> Save Profile
                </button>
            </div>
        </form>
    </div>

</div>
</body>
</html>