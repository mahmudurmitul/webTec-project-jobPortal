<?php
session_start();
require_once __DIR__ . "/../models/JobModel.php";

$errors = [];

// ─── AJAX ENDPOINTS ──────────────────────────────────────────────────────────

if (isset($_GET['action'])) {
    header("Content-Type: application/json");

    switch ($_GET['action']) {

        case 'getJobs':
            $result = getJobs();
            $jobs = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $row['is_featured'] = $row['isfeatured'];
                $row['job_type']    = $row['jobtype'];
                $row['salary_min']  = $row['salarymin'];
                $row['salary_max']  = $row['salarymax'];
                $jobs[] = $row;
            }
            echo json_encode($jobs);
            exit();

        case 'filterJobs':
            $cat     = $_GET['category'] ?? '';
            $loc     = $_GET['location'] ?? '';
            $typ     = $_GET['type']     ?? '';
            $exp     = $_GET['exp']      ?? '';
            $sal_min = $_GET['sal_min']  ?? '';
            $sal_max = $_GET['sal_max']  ?? '';
            $keyword = $_GET['keyword']  ?? '';
            $jobs = filterJobs($cat, $loc, $typ, $exp, $sal_min, $sal_max, $keyword);
            foreach ($jobs as &$row) {
                $row['is_featured'] = $row['isfeatured'];
                $row['job_type']    = $row['jobtype'];
                $row['salary_min']  = $row['salarymin'];
                $row['salary_max']  = $row['salarymax'];
            }
            unset($row);
            echo json_encode($jobs);
            exit();

        case 'toggleSave':
            if (!isset($_SESSION['seeker_id'])) {
                echo json_encode(['error' => 'Not logged in']);
                exit();
            }
            $job_id = intval($_GET['job_id'] ?? 0);
            $uid = $_SESSION['seeker_id'];
            if (isJobSaved($uid, $job_id)) {
                unsaveJob($uid, $job_id);
                echo json_encode(['saved' => false]);
            } else {
                saveJob($uid, $job_id);
                echo json_encode(['saved' => true]);
            }
            exit();

        case 'markRead':
            if (!isset($_SESSION['seeker_id'])) {
                echo json_encode(['error' => 'Not logged in']);
                exit();
            }
            $msg_id = intval($_GET['msg_id'] ?? 0);
            markMessageRead($msg_id, $_SESSION['seeker_id']);
            echo json_encode(['success' => true]);
            exit();

        case 'withdraw':
            if (!isset($_SESSION['seeker_id'])) {
                echo json_encode(['error' => 'Not logged in']);
                exit();
            }
            $app_id = intval($_GET['app_id'] ?? 0);
            $ok = withdrawApplication($app_id, $_SESSION['seeker_id']);
            echo json_encode(['success' => $ok]);
            exit();
    }
}

// ─── LOGOUT ───────────────────────────────────────────────────────────────────

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: /job_seeker/index.php");
    exit();
}

// ─── REGISTER ─────────────────────────────────────────────────────────────────

if (isset($_POST['register'])) {
    $name             = trim($_POST['name']            ?? '');
    $email            = trim($_POST['email']           ?? '');
    $phone            = trim($_POST['phone']           ?? '');
    $password         = $_POST['password']             ?? '';
    $confirm_password = $_POST['confirm_password']     ?? '';

    if (empty($name))              $errors[] = "Name is required.";
    if (empty($email))             $errors[] = "Email is required.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (empty($phone))             $errors[] = "Phone is required.";
    if (empty($password))          $errors[] = "Password is required.";
    elseif (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";
    if (empty($errors) && emailExists($email)) $errors[] = "This email is already registered.";

    if (empty($errors)) {
        $userid = insertSeeker($name, $email, $phone, $password);
        $_SESSION['seeker_id']   = $userid;
        $_SESSION['seeker_name'] = $name;
        header("Location: /job_seeker/index.php?msg=registered");
        exit();
    }
}

// ─── LOGIN ────────────────────────────────────────────────────────────────────

if (isset($_POST['login'])) {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';

    if (empty($email))    $errors[] = "Email is required.";
    if (empty($password)) $errors[] = "Password is required.";

    if (empty($errors)) {
        $user = loginSeeker($email, $password);
        if ($user) {
            $_SESSION['seeker_id']   = $user['id'];
            $_SESSION['seeker_name'] = $user['name'];
            header("Location: /job_seeker/index.php?msg=loggedin");
            exit();
        } else {
            $errors[] = "Incorrect email or password.";
        }
    }
}

// ─── APPLY TO JOB ─────────────────────────────────────────────────────────────

if (isset($_POST['apply_job']) && isset($_SESSION['seeker_id'])) {
    $job_id       = intval($_POST['job_id'] ?? 0);
    $cover_letter = trim($_POST['cover_letter'] ?? '');
    $seeker_id    = $_SESSION['seeker_id'];

    if (empty($cover_letter)) $errors[] = "Cover letter is required.";
    if ($job_id <= 0)         $errors[] = "Invalid job.";

    if (empty($errors) && hasApplied($seeker_id, $job_id)) {
        $errors[] = "You have already applied to this job.";
    }

    $resume_path = '';
    if (empty($errors)) {
        if (isset($_FILES['resume_upload']) && $_FILES['resume_upload']['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES['resume_upload']['name'], PATHINFO_EXTENSION));
            if ($ext !== 'pdf') {
                $errors[] = "Resume must be a PDF file.";
            } elseif ($_FILES['resume_upload']['size'] > 5 * 1024 * 1024) {
                $errors[] = "Resume must be under 5 MB.";
            } else {
                if (!is_dir("../uploads")) mkdir("../uploads", 0777, true);
                $resfilename = "resume_{$seeker_id}_" . time() . ".pdf";
                $disk_path   = "../uploads/" . $resfilename;
                $resume_path = "/job_seeker/uploads/" . $resfilename;
                move_uploaded_file($_FILES['resume_upload']['tmp_name'], $disk_path);
            }
        }
        if (empty($resume_path)) {
            $profile     = getSeekerById($seeker_id);
            $resume_path = $profile['resumepath'] ?? '';
        }
    }

    if (empty($errors)) {
        applyToJob($seeker_id, $job_id, $cover_letter, $resume_path);
        header("Location: /job_seeker/views/applications.php?msg=applied");
        exit();
    }
}

// ─── LOAD COMMON DATA ─────────────────────────────────────────────────────────

$user_profile  = null;
$saved_count   = 0;
$unread_msgs   = 0;
if (isset($_SESSION['seeker_id'])) {
    $user_profile = getSeekerById($_SESSION['seeker_id']);
    $saved_count  = getSavedJobsCount($_SESSION['seeker_id']);
    $unread_msgs  = getUnreadMessageCount($_SESSION['seeker_id']);
}