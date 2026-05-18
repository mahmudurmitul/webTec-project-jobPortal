<?php
session_start();
require_once "model.php";

$errors = [];
$msg    = "";
$page   = $_GET['page'] ?? 'dashboard';


if (isset($_GET['action'])) {
    header("Content-Type: application/json");
    requireLogin();


    if ($_GET['action'] === 'getDashboardStats') {
        echo json_encode(getDashboardStats());
        exit();
    }


    if ($_GET['action'] === 'toggleUser') {
        $uid    = (int)($_GET['uid'] ?? 0);
        $active = (int)($_GET['active'] ?? 0);
        if ($uid) {
            setUserActive($uid, $active);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit();
    }


    if ($_GET['action'] === 'toggleFeatured') {
        $jid = (int)($_GET['job_id'] ?? 0);
        $val = (int)($_GET['val'] ?? 0);
        if ($jid) {
            setJobFeatured($jid, $val);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit();
    }

  
    if ($_GET['action'] === 'getAnalytics') {
        echo json_encode([
            'jobsPerCategory' => getJobsPerCategory(),
            'appsOverTime'    => getAppsOverTime(),
            'topEmployers'    => getTopEmployers(),
            'activeRecruiters'=> getMostActiveRecruiters(),
            'popularLocations'=> getPopularLocations(),
            'popularJobTypes' => getPopularJobTypes(),
        ]);
        exit();
    }

    
    if ($_GET['action'] === 'getUserGrowth') {
        echo json_encode(getUserGrowth());
        exit();
    }
}


function requireLogin() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: index.php?page=login");
        exit();
    }
}

if (isset($_POST['login'])) {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $user = loginAdmin($email, $password);
    if ($user) {
        $_SESSION['admin_id']   = $user['id'];
        $_SESSION['admin_name'] = $user['name'];
        header("Location: index.php");
        exit();
    } else {
        $errors[] = "Invalid admin credentials.";
        $page = 'login';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php?page=login");
    exit();
}



if (isset($_GET['approve_employer'])) {
    requireLogin();
    setUserVerified((int)$_GET['approve_employer'], 1);
    header("Location: index.php?page=employers&msg=Employer+approved");
    exit();
}

if (isset($_GET['reject_employer'])) {
    requireLogin();
    setUserVerified((int)$_GET['reject_employer'], 0);
    setUserActive((int)$_GET['reject_employer'], 0);
    header("Location: index.php?page=employers&msg=Employer+rejected");
    exit();
}

if (isset($_GET['suspend_user'])) {
    requireLogin();
    setUserActive((int)$_GET['suspend_user'], 0);
    $back = $_GET['back'] ?? 'employers';
    header("Location: index.php?page=$back&msg=Account+suspended");
    exit();
}

if (isset($_GET['reactivate_user'])) {
    requireLogin();
    setUserActive((int)$_GET['reactivate_user'], 1);
    $back = $_GET['back'] ?? 'employers';
    header("Location: index.php?page=$back&msg=Account+reactivated");
    exit();
}



if (isset($_GET['approve_recruiter'])) {
    requireLogin();
    setUserVerified((int)$_GET['approve_recruiter'], 1);
    header("Location: index.php?page=recruiters&msg=Recruiter+approved");
    exit();
}

if (isset($_GET['reject_recruiter'])) {
    requireLogin();
    setUserVerified((int)$_GET['reject_recruiter'], 0);
    setUserActive((int)$_GET['reject_recruiter'], 0);
    header("Location: index.php?page=recruiters&msg=Recruiter+rejected");
    exit();
}



if (isset($_POST['add_category'])) {
    requireLogin();
    $name = trim($_POST['cat_name'] ?? '');
    $desc = trim($_POST['cat_desc'] ?? '');
    if (empty($name)) $errors[] = "Category name is required.";
    if (empty($errors)) {
        addCategory($name, $desc);
        header("Location: index.php?page=categories&msg=Category+added");
        exit();
    }
    $page = 'categories';
}

if (isset($_POST['update_category'])) {
    requireLogin();
    $id   = (int)($_POST['cat_id'] ?? 0);
    $name = trim($_POST['cat_name'] ?? '');
    $desc = trim($_POST['cat_desc'] ?? '');
    if (empty($name)) $errors[] = "Category name is required.";
    if (empty($errors)) {
        updateCategory($id, $name, $desc);
        header("Location: index.php?page=categories&msg=Category+updated");
        exit();
    }
    $page = 'categories';
}

if (isset($_GET['delete_category'])) {
    requireLogin();
    $ok = deleteCategory((int)$_GET['delete_category']);
    if ($ok) {
        header("Location: index.php?page=categories&msg=Category+deleted");
    } else {
        header("Location: index.php?page=categories&msg=Cannot+delete+category+with+active+jobs");
    }
    exit();
}



if (isset($_GET['remove_job'])) {
    requireLogin();
    removeJob((int)$_GET['remove_job']);
    header("Location: index.php?page=jobs&msg=Job+removed");
    exit();
}

if (isset($_GET['feature_job'])) {
    requireLogin();
    setJobFeatured((int)$_GET['feature_job'], 1);
    header("Location: index.php?page=jobs&msg=Job+featured");
    exit();
}

if (isset($_GET['unfeature_job'])) {
    requireLogin();
    setJobFeatured((int)$_GET['unfeature_job'], 0);
    header("Location: index.php?page=jobs&msg=Featured+removed");
    exit();
}



if (isset($_POST['resolve_complaint'])) {
    requireLogin();
    $id   = (int)($_POST['complaint_id'] ?? 0);
    $note = trim($_POST['admin_note'] ?? '');
    if (empty($note)) $errors[] = "Resolution note is required.";
    if (empty($errors)) {
        resolveComplaint($id, $note);
        header("Location: index.php?page=complaints&msg=Complaint+resolved");
        exit();
    }
    $page = 'complaints';
}



if (isset($_POST['save_settings'])) {
    requireLogin();
    $keys = ['max_jobs_per_employer', 'max_apps_per_seeker', 'resume_visibility_default'];
    foreach ($keys as $k) {
        if (isset($_POST[$k])) {
            setSetting($k, trim($_POST[$k]));
        }
    }
    header("Location: index.php?page=settings&msg=Settings+saved");
    exit();
}



if (isset($_POST['post_announcement'])) {
    requireLogin();
    $title = trim($_POST['ann_title'] ?? '');
    $body  = trim($_POST['ann_body'] ?? '');
    if (empty($title)) $errors[] = "Announcement title is required.";
    if (empty($body))  $errors[] = "Announcement body is required.";
    if (empty($errors)) {
        postAnnouncement($_SESSION['admin_id'], $title, $body);
        header("Location: index.php?page=announcements&msg=Announcement+posted");
        exit();
    }
    $page = 'announcements';
}

if (isset($_GET['delete_announcement'])) {
    requireLogin();
    deleteAnnouncement((int)$_GET['delete_announcement']);
    header("Location: index.php?page=announcements&msg=Announcement+deleted");
    exit();
}



$publicPages = ['login'];
if (!in_array($page, $publicPages) && !isset($_SESSION['admin_id'])) {
    header("Location: index.php?page=login");
    exit();
}
?>
