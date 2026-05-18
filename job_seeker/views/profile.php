<?php
require_once __DIR__ . "/../controllers/JobController.php";

if (!isset($_SESSION['seeker_id'])) {
    header("Location: /webtech/webTec-project-jobPortal/job_seeker/index.php");
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

    if (empty($headline))            $profile_errors[] = "Headline is required.";
    elseif (strlen($headline) > 200) $profile_errors[] = "Headline must be under 200 characters.";
    if (empty($skills))              $profile_errors[] = "At least one skill is required.";
    if ($expected_salary <= 0)       $profile_errors[] = "Expected salary must be greater than 0.";
    if (empty($education_level))     $profile_errors[] = "Education level is required.";

    // Profile picture upload
    if (empty($profile_errors) && isset($_FILES['profilepic']) && $_FILES['profilepic']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['profilepic']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
            $profile_errors[] = "Profile picture must be an image file (jpg, png, gif, webp).";
        } elseif ($_FILES['profilepic']['size'] > 3 * 1024 * 1024) {
            $profile_errors[] = "Profile picture must be under 3 MB.";
        } else {
            $upload_dir = __DIR__ . "/../uploads/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $filename  = "profile_" . $_SESSION['seeker_id'] . "_" . time() . "." . $ext;
            $disk_path = $upload_dir . $filename;
            $web_path  = BASE_PATH . "/uploads/" . $filename;
            if (move_uploaded_file($_FILES['profilepic']['tmp_name'], $disk_path)) {
                updateProfilePic($_SESSION['seeker_id'], $web_path);
                // Update session-level cache immediately
                $_SESSION['seeker_profilepic'] = $web_path;
            } else {
                $profile_errors[] = "Failed to upload profile picture.";
            }
        }
    }

    // Resume upload
    if (empty($profile_errors) && isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            $profile_errors[] = "Resume must be a PDF file.";
        } elseif ($_FILES['resume']['size'] > 5 * 1024 * 1024) {
            $profile_errors[] = "Resume must be under 5 MB.";
        } else {
            $upload_dir = __DIR__ . "/../uploads/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $resfilename = "resume_" . $_SESSION['seeker_id'] . "_" . time() . ".pdf";
            $disk_res    = $upload_dir . $resfilename;
            $web_res     = BASE_PATH . "/uploads/" . $resfilename;
            if (move_uploaded_file($_FILES['resume']['tmp_name'], $disk_res)) {
                updateResume($_SESSION['seeker_id'], $web_res);
            } else {
                $profile_errors[] = "Failed to upload resume.";
            }
        }
    }

    if (empty($profile_errors)) {
        updateProfile(
            $_SESSION['seeker_id'], $headline, $years_experience,
            $current_salary, $expected_salary, $preferred_location,
            $education_level, $skills, $summary
        );
        header("Location: /webtech/webTec-project-jobPortal/job_seeker/index.php?msg=profile_updated");
        exit();
    }
    // Reload profile data to show current values after error
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
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/profile.php" class="active"><i class="fas fa-user-edit"></i> Edit Profile</a>
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/applications.php"><i class="fas fa-file-alt"></i> My Applications</a>
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/saved.php"><i class="fas fa-bookmark"></i> Saved Jobs <span class="nav-badge"><?= $saved_count ?></span></a>
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

        <div class="page-heading">
            <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
            <p>Fill in all fields to improve your job matching score.</p>
        </div>

        <?php foreach ($profile_errors as $e): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($e) ?>
        </div>
        <?php endforeach; ?>

        <div class="card">
            <form method="POST" enctype="multipart/form-data" id="profileForm" novalidate>

                <div class="form-2col">

                    <div class="form-group">
                        <label>Professional Headline *</label>
                        <input type="text" name="headline" id="profHeadline"
                            placeholder="e.g. PHP Developer with 3 years experience"
                            value="<?= htmlspecialchars($user_profile['headline'] ?? '') ?>">
                        <span class="field-error" id="headlineErr"></span>
                    </div>

                    <div class="form-group">
                        <label>Years of Experience</label>
                        <select name="yearsexperience">
                            <?php foreach ([0,1,2,3,5,10] as $y): ?>
                            <option value="<?= $y ?>" <?= ($user_profile['yearsexperience'] ?? 0) == $y ? 'selected' : '' ?>>
                                <?= $y == 0 ? '0 Years' : ($y >= 10 ? '10+ Years' : ($y >= 5 ? '5+ Years' : "$y Year" . ($y > 1 ? 's' : ''))) ?>
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
                        <input type="number" name="expectedsalary" id="profExpSalary" min="1"
                            placeholder="50000"
                            value="<?= htmlspecialchars($user_profile['expectedsalary'] ?? '') ?>">
                        <span class="field-error" id="expSalaryErr"></span>
                    </div>

                    <div class="form-group">
                        <label>Preferred Location</label>
                        <input type="text" name="preferredlocation"
                            placeholder="Dhaka"
                            value="<?= htmlspecialchars($user_profile['preferredlocation'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Education Level *</label>
                        <select name="educationlevel" id="profEdu">
                            <option value="">— Select —</option>
                            <?php foreach (['HSC','Bachelor','Master'] as $edu): ?>
                            <option value="<?= $edu ?>" <?= ($user_profile['educationlevel'] ?? '') === $edu ? 'selected' : '' ?>>
                                <?= $edu ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="field-error" id="eduErr"></span>
                    </div>

                    <div class="form-group">
                        <label>Skills * <small style="color:#555;">(comma-separated)</small></label>
                        <input type="text" name="skills" id="profSkills"
                            placeholder="PHP, MySQL, HTML, CSS, JavaScript"
                            value="<?= htmlspecialchars($user_profile['skills'] ?? '') ?>">
                        <span class="field-error" id="skillsErr"></span>
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
                        <p style="font-size:12px;color:#a855f7;margin-bottom:8px;">
                            <i class="fas fa-file-pdf"></i> Current resume on file.
                            <a href="<?= htmlspecialchars($user_profile['resumepath']) ?>" target="_blank" style="color:#a855f7;">View</a>
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
                                style="width:60px;height:60px;border-radius:50%;border:2px solid #a855f7;object-fit:cover;">
                        </div>
                        <?php endif; ?>
                        <input type="file" name="profilepic" accept="image/*">
                    </div>
                </div>

                <div style="margin-top:30px;">
                    <button type="submit" name="update_profile" onclick="return validateProfile();" style="padding:16px 40px;font-size:15px;">
                        <i class="fas fa-save"></i> Save Profile
                    </button>
                </div>
            </form>
        </div>

    </main>
</div>

<script src="script.js"></script>
<script>
function validateProfile() {
    var ok = true;
    var headline   = document.getElementById('profHeadline');
    var expSalary  = document.getElementById('profExpSalary');
    var edu        = document.getElementById('profEdu');
    var skills     = document.getElementById('profSkills');

    showErr('headlineErr', '');
    showErr('expSalaryErr', '');
    showErr('eduErr', '');
    showErr('skillsErr', '');

    if (!headline.value.trim()) {
        showErr('headlineErr', 'Headline is required.'); ok = false;
    } else if (headline.value.trim().length > 200) {
        showErr('headlineErr', 'Max 200 characters.'); ok = false;
    }
    if (!expSalary.value || parseInt(expSalary.value) <= 0) {
        showErr('expSalaryErr', 'Expected salary must be greater than 0.'); ok = false;
    }
    if (!edu.value) {
        showErr('eduErr', 'Please select an education level.'); ok = false;
    }
    if (!skills.value.trim()) {
        showErr('skillsErr', 'At least one skill is required.'); ok = false;
    }
    return ok;
}
</script>
</body>
</html>