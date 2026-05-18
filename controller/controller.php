<?php
session_start();
require_once __DIR__ . "/../model/model.php";

$errors = [];
$msg    = "";
$page   = $_GET['page'] ?? 'dashboard';


ensureHiredStatus();

function requireLogin() {
    if (!isset($_SESSION['recruiter_id'])) {
        if (isset($_GET['action'])) {
            header("Content-Type: application/json");
            echo json_encode(['error' => 'unauthenticated']);
        } else {
            header("Location: index.php?page=auth");
        }
        exit();
    }
}



if (isset($_GET['action'])) {
    requireLogin();
    header("Content-Type: application/json");

    if ($_GET['action'] === 'searchSeekers') {
        $kw  = $_GET['keyword'] ?? '';
        $loc = $_GET['location'] ?? '';
        $exp = $_GET['exp'] ?? '';
        $sal = $_GET['salary'] ?? '';
        echo json_encode(searchSeekers($kw, $loc, $exp, $sal));
        exit();
    }


    if ($_GET['action'] === 'toggleJobStatus') {
        $jobId  = (int)($_GET['job_id'] ?? 0);
        $status = $_GET['status'] ?? 'active';
        if ($jobId && in_array($status, ['active','closed','draft'])) {
            updateJobStatus($jobId, $_SESSION['recruiter_id'], $status);
            echo json_encode(['success' => true, 'status' => $status]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit();
    }

    
    if ($_GET['action'] === 'updateAppStatus') {
        $appId  = (int)($_GET['app_id'] ?? 0);
        $status = $_GET['status'] ?? '';
        $allowed = ['submitted','reviewed','shortlisted','interview','rejected','hired'];
        if ($appId && in_array($status, $allowed)) {
            updateApplicationStatus($appId, $_SESSION['recruiter_id'], $status);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit();
    }

   
    if ($_GET['action'] === 'getAnalytics') {
        echo json_encode(getRecruiterAnalytics($_SESSION['recruiter_id']));
        exit();
    }

    
    if ($_GET['action'] === 'getPipeline') {
        echo json_encode(getPipeline($_SESSION['recruiter_id']));
        exit();
    }


    if ($_GET['action'] === 'markHired') {
        $appId = (int)($_GET['app_id'] ?? 0);
        if ($appId) {
            $ok = markAsHired($appId, $_SESSION['recruiter_id']);
            echo json_encode(['success' => $ok, 'app_id' => $appId]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid app_id']);
        }
        exit();
    }

  
    echo json_encode(['error' => 'unknown action']);
    exit();
}



if (isset($_POST['register'])) {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    $agency   = trim($_POST['agency_name'] ?? '');

    if (empty($name))     $errors[] = "Full name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email required.";
    if (empty($phone))    $errors[] = "Phone number is required.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";
    if (empty($agency))   $errors[] = "Agency/recruiter name is required.";
    if (emailExists($email)) $errors[] = "Email already registered.";

    if (empty($errors)) {
        $uid = registerRecruiter($name, $email, $phone, $password);
        upsertRecruiterProfile($uid, $agency, '', '', '');
        $_SESSION['recruiter_id']   = $uid;
        $_SESSION['recruiter_name'] = $name;
        header("Location: index.php?msg=registered");
        exit();
    }
    $page = 'auth';
}

if (isset($_POST['login'])) {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $user = loginRecruiter($email, $password);
    if ($user) {
        $_SESSION['recruiter_id']   = $user['id'];
        $_SESSION['recruiter_name'] = $user['name'];
        header("Location: index.php");
        exit();
    } else {
        $errors[] = "Invalid email or password.";
        $page = 'auth';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}



if (isset($_POST['update_profile'])) {
    requireLogin();
    $agencyName     = trim($_POST['agency_name'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $description    = trim($_POST['description'] ?? '');
    $website        = trim($_POST['website'] ?? '');
    $phone          = trim($_POST['phone'] ?? '');

    if (empty($agencyName)) $errors[] = "Agency name is required.";

    if (empty($errors)) {
        upsertRecruiterProfile($_SESSION['recruiter_id'], $agencyName, $specialization, $description, $website);

       
        if ($phone) {
            global $conn;
            $stmt = mysqli_prepare($conn, "UPDATE users SET phone=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "si", $phone, $_SESSION['recruiter_id']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

     
        if (!empty($_FILES['profilepic']['name'])) {
            $ext = strtolower(pathinfo($_FILES['profilepic']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                $errors[] = "Invalid image format.";
            } else {
                $dir = "uploads/pics/";
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $filename = "rec_" . $_SESSION['recruiter_id'] . "_" . time() . "." . $ext;
                move_uploaded_file($_FILES['profilepic']['tmp_name'], $dir . $filename);
                updateRecruiterPic($_SESSION['recruiter_id'], $dir . $filename);
            }
        }

        if (empty($errors)) {
            header("Location: index.php?page=profile&msg=Profile+updated+successfully");
            exit();
        }
    }
    $page = 'profile';
}



if (isset($_POST['add_client'])) {
    requireLogin();
    $empId    = (int)($_POST['employer_id'] ?? 0);
    $override = trim($_POST['company_name_override'] ?? '');

    if (empty($override)) $errors[] = "Company name is required.";
    if (empty($errors)) {
        addRecruiterClient($_SESSION['recruiter_id'], $empId ?: null, $override);
        header("Location: index.php?page=clients&msg=Client+added");
        exit();
    }
    $page = 'clients';
}

if (isset($_GET['delete_client'])) {
    requireLogin();
    deleteRecruiterClient((int)$_GET['delete_client'], $_SESSION['recruiter_id']);
    header("Location: index.php?page=clients&msg=Client+removed");
    exit();
}



if (isset($_POST['save_job'])) {
    requireLogin();
    $jobId       = (int)($_POST['job_id'] ?? 0);
    $clientEmpId = (int)($_POST['client_employer_id'] ?? 0);
    $categoryId  = (int)($_POST['category_id'] ?? 0);
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $requirements= trim($_POST['requirements'] ?? '');
    $benefits    = trim($_POST['benefits'] ?? '');
    $salaryMin   = (float)($_POST['salary_min'] ?? 0);
    $salaryMax   = (float)($_POST['salary_max'] ?? 0);
    $location    = trim($_POST['location'] ?? '');
    $jobType     = $_POST['job_type'] ?? '';
    $expLevel    = $_POST['exp_level'] ?? '';
    $deadline    = $_POST['deadline'] ?? '';
    $status      = $_POST['status'] ?? 'draft';

    if (empty($title))    $errors[] = "Job title is required.";
    if (!$categoryId)     $errors[] = "Category is required.";
    if (!$clientEmpId)    $errors[] = "Client/employer is required.";
    if (empty($location)) $errors[] = "Location is required.";
    if (empty($deadline)) $errors[] = "Deadline is required.";
    if (!in_array($jobType, ['full-time','part-time','remote','contract'])) $errors[] = "Invalid job type.";
    if (!in_array($expLevel, ['entry','mid','senior'])) $errors[] = "Invalid experience level.";

    if (empty($errors)) {
        if ($jobId) {
            updateJob($jobId, $_SESSION['recruiter_id'], $categoryId, $title, $description, $requirements, $benefits, $salaryMin, $salaryMax, $location, $jobType, $expLevel, $deadline, $status);
        } else {
            createJobFull($_SESSION['recruiter_id'], $clientEmpId, $categoryId, $title, $description, $requirements, $benefits, $salaryMin, $salaryMax, $location, $jobType, $expLevel, $deadline, $status);
        }
        header("Location: index.php?page=jobs&msg=Job+saved");
        exit();
    }
    $page = 'job_form';
}

if (isset($_GET['delete_job'])) {
    requireLogin();
    deleteJob((int)$_GET['delete_job'], $_SESSION['recruiter_id']);
    header("Location: index.php?page=jobs&msg=Job+deleted");
    exit();
}



if (isset($_POST['send_outreach'])) {
    requireLogin();
    $seekerId = (int)($_POST['seeker_id'] ?? 0);
    $jobId    = (int)($_POST['job_id'] ?? 0) ?: null;
    $message  = trim($_POST['message'] ?? '');

    if (empty($message)) $errors[] = "Message cannot be empty.";
    if (!$seekerId)       $errors[] = "Invalid seeker.";

    if (empty($errors)) {
        sendOutreach($_SESSION['recruiter_id'], $seekerId, $jobId, $message);
        header("Location: index.php?page=seeker_profile&seeker_id={$seekerId}&msg=Outreach+sent+successfully");
        exit();
    }
    $page = 'seeker_profile';
}



if (isset($_POST['send_message'])) {
    requireLogin();
    $recipientId = (int)($_POST['recipient_id'] ?? 0);
    $appId       = (int)($_POST['app_id'] ?? 0) ?: null;
    $body        = trim($_POST['body'] ?? '');

    if (empty($body)) $errors[] = "Message body is required.";
    if (empty($errors)) {
        sendMessage($_SESSION['recruiter_id'], $recipientId, $appId, $body);
        header("Location: index.php?page=messages&msg=Message+sent");
        exit();
    }
    $page = 'messages';
}

if (isset($_GET['mark_read'])) {
    requireLogin();
    markMessageRead((int)$_GET['mark_read'], $_SESSION['recruiter_id']);
    header("Location: index.php?page=messages");
    exit();
}



if (isset($_POST['submit_complaint'])) {
    requireLogin();
    $subjectId   = (int)($_POST['subject_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    if (empty($description)) $errors[] = "Description is required.";
    if (!$subjectId)         $errors[] = "Subject ID is required.";
    if (empty($errors)) {
        submitComplaint($_SESSION['recruiter_id'], $subjectId, $description);
        header("Location: index.php?page=dashboard&msg=Complaint+submitted");
        exit();
    }
    $page = 'complaint';
}



$protectedPages = ['dashboard','profile','clients','jobs','job_form','seekers','seeker_profile','applications','pipeline','placements','analytics','outreach','messages','complaint'];
if (in_array($page, $protectedPages) && !isset($_SESSION['recruiter_id'])) {
    header("Location: index.php?page=auth");
    exit();
}



$recruiterProfile = null;
$stats            = [];
$categories_list  = getCategories();
$clients_list     = [];
$employers_list   = [];

if (isset($_SESSION['recruiter_id'])) {
    $recruiterProfile = getRecruiterProfile($_SESSION['recruiter_id']);
    $stats            = getDashboardStats($_SESSION['recruiter_id']);
    $clients_list     = getRecruiterClients($_SESSION['recruiter_id']);
    $employers_list   = getRegisteredEmployers();
}
?>