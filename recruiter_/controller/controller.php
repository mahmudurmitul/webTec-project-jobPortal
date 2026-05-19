<?php
session_start();
require_once __DIR__ . "/../model/model.php";

$errors = [];
<<<<<<< HEAD
$page   = $_GET['page'] ?? 'dashboard';

// Run once — ensure hired enum exists
ensureHiredStatus();

// ============================================================
// HELPERS
// ============================================================
=======
$msg    = "";
$page   = $_GET['page'] ?? 'dashboard';


ensureHiredStatus();

>>>>>>> 43a4b345e51f77b9bede491f9ae3f1139cea9d11
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

<<<<<<< HEAD
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// ============================================================
// AJAX ENDPOINTS
// ============================================================
=======


>>>>>>> 43a4b345e51f77b9bede491f9ae3f1139cea9d11
if (isset($_GET['action'])) {
    requireLogin();
    header("Content-Type: application/json");

<<<<<<< HEAD
    switch ($_GET['action']) {

        case 'searchSeekers':
            echo json_encode(searchSeekers(
                $_GET['keyword']  ?? '',
                $_GET['location'] ?? '',
                $_GET['exp']      ?? '',
                $_GET['salary']   ?? ''
            ));
            break;

        case 'toggleJobStatus':
            $jobId  = (int)($_GET['job_id'] ?? 0);
            $status = $_GET['status'] ?? '';
            if ($jobId && in_array($status, ['active','closed','draft'])) {
                updateJobStatus($jobId, $_SESSION['recruiter_id'], $status);
                echo json_encode(['success' => true, 'status' => $status]);
            } else {
                echo json_encode(['success' => false]);
            }
            break;

        case 'updateAppStatus':
            $appId  = (int)($_GET['app_id'] ?? 0);
            $status = $_GET['status'] ?? '';
            $allowed = ['submitted','reviewed','shortlisted','interview','rejected','hired'];
            if ($appId && in_array($status, $allowed)) {
                updateApplicationStatus($appId, $_SESSION['recruiter_id'], $status);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
            break;

        case 'markHired':
            $appId = (int)($_GET['app_id'] ?? 0);
            $ok = $appId ? markAsHired($appId, $_SESSION['recruiter_id']) : false;
            echo json_encode(['success' => $ok]);
            break;

        case 'getAnalytics':
            echo json_encode(getRecruiterAnalytics($_SESSION['recruiter_id']));
            break;

        case 'getPipeline':
            echo json_encode(getPipeline($_SESSION['recruiter_id']));
            break;

        default:
            echo json_encode(['error' => 'unknown action']);
    }
    exit();
}

// ============================================================
// AUTH
// ============================================================
if (isset($_POST['register'])) {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $phone    = trim($_POST['phone']    ?? '');
    $password = $_POST['password']      ?? '';
    $confirm  = $_POST['confirm']       ?? '';
    $agency   = trim($_POST['agency_name'] ?? '');

    if (empty($name))                                    $errors[] = "Full name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email required.";
    if (empty($phone))                                   $errors[] = "Phone number is required.";
    if (strlen($password) < 6)                           $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirm)                          $errors[] = "Passwords do not match.";
    if (empty($agency))                                  $errors[] = "Agency/recruiter name is required.";
    if (emailExists($email))                             $errors[] = "This email is already registered.";
=======
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
>>>>>>> 43a4b345e51f77b9bede491f9ae3f1139cea9d11

    if (empty($errors)) {
        $uid = registerRecruiter($name, $email, $phone, $password);
        upsertRecruiterProfile($uid, $agency, '', '', '');
<<<<<<< HEAD
        // DO NOT log in — must wait for admin verification
        redirect("index.php?page=auth&msg=registered");
=======
        $_SESSION['recruiter_id']   = $uid;
        $_SESSION['recruiter_name'] = $name;
        header("Location: index.php?msg=registered");
        exit();
>>>>>>> 43a4b345e51f77b9bede491f9ae3f1139cea9d11
    }
    $page = 'auth';
}

if (isset($_POST['login'])) {
<<<<<<< HEAD
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';

    if (empty($email) || empty($password)) {
        $errors[] = "Email and password are required.";
        $page = 'auth';
    } else {
        $user = loginRecruiter($email, $password);
        if (!$user) {
            $errors[] = "Invalid email or password.";
            $page = 'auth';
        } elseif ((int)$user['isactive'] !== 1) {
            $errors[] = "Your account is inactive or suspended. Please contact admin.";
            $page = 'auth';
        } elseif ((int)$user['isverified'] !== 1) {
            $errors[] = "Your account is pending admin verification. Please wait for approval before logging in.";
            $page = 'auth';
        } else {
            $_SESSION['recruiter_id']   = $user['id'];
            $_SESSION['recruiter_name'] = $user['name'];
            redirect("index.php");
        }
=======
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
>>>>>>> 43a4b345e51f77b9bede491f9ae3f1139cea9d11
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
<<<<<<< HEAD
    redirect("index.php?page=auth");
}

// ============================================================
// PROFILE UPDATE
// ============================================================
if (isset($_POST['update_profile'])) {
    requireLogin();
    $agencyName     = trim($_POST['agency_name']    ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $description    = trim($_POST['description']    ?? '');
    $website        = trim($_POST['website']        ?? '');
    $phone          = trim($_POST['phone']          ?? '');
=======
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
>>>>>>> 43a4b345e51f77b9bede491f9ae3f1139cea9d11

    if (empty($agencyName)) $errors[] = "Agency name is required.";

    if (empty($errors)) {
        upsertRecruiterProfile($_SESSION['recruiter_id'], $agencyName, $specialization, $description, $website);
<<<<<<< HEAD
        if ($phone) updateRecruiterPhone($_SESSION['recruiter_id'], $phone);

        if (!empty($_FILES['profilepic']['name'])) {
            $ext = strtolower(pathinfo($_FILES['profilepic']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                $errors[] = "Invalid image format. Use JPG, PNG, or WebP.";
            } elseif ($_FILES['profilepic']['size'] > 2 * 1024 * 1024) {
                $errors[] = "Image must be under 2 MB.";
            } else {
                $dir = __DIR__ . "/../uploads/pics/";
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $filename = "rec_" . $_SESSION['recruiter_id'] . "_" . time() . "." . $ext;
                if (move_uploaded_file($_FILES['profilepic']['tmp_name'], $dir . $filename)) {
                    updateRecruiterPic($_SESSION['recruiter_id'], "uploads/pics/" . $filename);
                }
            }
        }

        if (empty($errors)) redirect("index.php?page=profile&msg=Profile+updated+successfully");
=======

       
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
>>>>>>> 43a4b345e51f77b9bede491f9ae3f1139cea9d11
    }
    $page = 'profile';
}

<<<<<<< HEAD
// ============================================================
// CLIENTS
// ============================================================
if (isset($_POST['add_client'])) {
    requireLogin();
    $override = trim($_POST['company_name_override'] ?? '');
    $empId    = (int)($_POST['employer_id'] ?? 0);
    if (empty($override)) $errors[] = "Company name is required.";
    if (empty($errors)) {
        addRecruiterClient($_SESSION['recruiter_id'], $empId ?: null, $override);
        redirect("index.php?page=clients&msg=Client+added");
=======


if (isset($_POST['add_client'])) {
    requireLogin();
    $empId    = (int)($_POST['employer_id'] ?? 0);
    $override = trim($_POST['company_name_override'] ?? '');

    if (empty($override)) $errors[] = "Company name is required.";
    if (empty($errors)) {
        addRecruiterClient($_SESSION['recruiter_id'], $empId ?: null, $override);
        header("Location: index.php?page=clients&msg=Client+added");
        exit();
>>>>>>> 43a4b345e51f77b9bede491f9ae3f1139cea9d11
    }
    $page = 'clients';
}

if (isset($_GET['delete_client'])) {
    requireLogin();
    deleteRecruiterClient((int)$_GET['delete_client'], $_SESSION['recruiter_id']);
<<<<<<< HEAD
    redirect("index.php?page=clients&msg=Client+removed");
}

// ============================================================
// JOBS
// ============================================================
if (isset($_POST['save_job'])) {
    requireLogin();
    $jobId      = (int)($_POST['job_id']           ?? 0);
    $clientId   = (int)($_POST['client_id']        ?? 0); // recruiterclients.id
    $categoryId = (int)($_POST['category_id']      ?? 0);
    $title      = trim($_POST['title']             ?? '');
    $description= trim($_POST['description']       ?? '');
    $requirements=trim($_POST['requirements']      ?? '');
    $benefits   = trim($_POST['benefits']          ?? '');
    $salaryMin  = (float)($_POST['salary_min']     ?? 0);
    $salaryMax  = (float)($_POST['salary_max']     ?? 0);
    $location   = trim($_POST['location']          ?? '');
    $jobType    = $_POST['job_type']               ?? '';
    $expLevel   = $_POST['exp_level']              ?? '';
    $deadline   = $_POST['deadline']               ?? '';
    $status     = $_POST['status']                 ?? 'draft';

    if (empty($title))    $errors[] = "Job title is required.";
    if (!$categoryId)     $errors[] = "Please select a category.";
    if (!$jobId && !$clientId) $errors[] = "Please select a client company.";
    if (empty($location)) $errors[] = "Location is required.";
    if (empty($deadline)) $errors[] = "Application deadline is required.";
    if (!in_array($jobType, ['full-time','part-time','remote','contract']))
        $errors[] = "Invalid job type.";
    if (!in_array($expLevel, ['entry','mid','senior']))
        $errors[] = "Invalid experience level.";

    if (empty($errors)) {
        if ($jobId) {
            // Edit existing job
            updateJob($jobId, $_SESSION['recruiter_id'], $categoryId, $title, $description,
                      $requirements, $benefits, $salaryMin, $salaryMax, $location,
                      $jobType, $expLevel, $deadline, $status);
        } else {
            // New job — resolve employerid from client record
            $clients = getRecruiterClients($_SESSION['recruiter_id']);
            $empId = 0;
            $clientName = '';
            foreach ($clients as $c) {
                if ($c['id'] == $clientId) {
                    $empId = (int)($c['employerid'] ?? 0);
                    $clientName = $c['companynameoverride'];
                    break;
                }
            }
            if (!$empId) {
                $errors[] = "Selected client is not linked to a registered employer. Please link the client to an employer account first.";
            } else {
                createJob($_SESSION['recruiter_id'], $empId, $clientName, $categoryId, $title,
                          $description, $requirements, $benefits, $salaryMin, $salaryMax,
                          $location, $jobType, $expLevel, $deadline, $status);
                redirect("index.php?page=jobs&msg=Job+posted+successfully");
            }
        }
        if (empty($errors)) redirect("index.php?page=jobs&msg=Job+saved+successfully");
=======
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
>>>>>>> 43a4b345e51f77b9bede491f9ae3f1139cea9d11
    }
    $page = 'job_form';
}

if (isset($_GET['delete_job'])) {
    requireLogin();
    deleteJob((int)$_GET['delete_job'], $_SESSION['recruiter_id']);
<<<<<<< HEAD
    redirect("index.php?page=jobs&msg=Job+deleted");
}

// ============================================================
// OUTREACH
// ============================================================
if (isset($_POST['send_outreach'])) {
    requireLogin();
    $seekerId = (int)($_POST['seeker_id'] ?? 0);
    $jobId    = (int)($_POST['job_id']    ?? 0) ?: null;
    $message  = trim($_POST['message']    ?? '');
=======
    header("Location: index.php?page=jobs&msg=Job+deleted");
    exit();
}



if (isset($_POST['send_outreach'])) {
    requireLogin();
    $seekerId = (int)($_POST['seeker_id'] ?? 0);
    $jobId    = (int)($_POST['job_id'] ?? 0) ?: null;
    $message  = trim($_POST['message'] ?? '');
>>>>>>> 43a4b345e51f77b9bede491f9ae3f1139cea9d11

    if (empty($message)) $errors[] = "Message cannot be empty.";
    if (!$seekerId)       $errors[] = "Invalid seeker.";

    if (empty($errors)) {
        sendOutreach($_SESSION['recruiter_id'], $seekerId, $jobId, $message);
<<<<<<< HEAD
        redirect("index.php?page=seeker_profile&seeker_id={$seekerId}&msg=Outreach+sent+successfully");
=======
        header("Location: index.php?page=seeker_profile&seeker_id={$seekerId}&msg=Outreach+sent+successfully");
        exit();
>>>>>>> 43a4b345e51f77b9bede491f9ae3f1139cea9d11
    }
    $page = 'seeker_profile';
}

<<<<<<< HEAD
// ============================================================
// COMPLAINTS
// ============================================================
if (isset($_POST['submit_complaint'])) {
    requireLogin();
    $subjectId   = (int)($_POST['subject_id']  ?? 0);
    $description = trim($_POST['description']  ?? '');
    if (empty($description)) $errors[] = "Description is required.";
    if (!$subjectId)         $errors[] = "Subject user ID is required.";
    if (empty($errors)) {
        submitComplaint($_SESSION['recruiter_id'], $subjectId, $description);
        redirect("index.php?page=dashboard&msg=Complaint+submitted+to+admin");
=======


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
>>>>>>> 43a4b345e51f77b9bede491f9ae3f1139cea9d11
    }
    $page = 'complaint';
}

<<<<<<< HEAD
// ============================================================
// GUARD ALL PROTECTED PAGES
// ============================================================
$publicPages = ['auth'];
if (!in_array($page, $publicPages) && !isset($_SESSION['recruiter_id'])) {
    redirect("index.php?page=auth");
}

// ============================================================
// LOAD SHARED DATA FOR VIEWS
// ============================================================
$recruiterProfile = null;
$stats            = [];
$categories_list  = [];
=======


$protectedPages = ['dashboard','profile','clients','jobs','job_form','seekers','seeker_profile','applications','pipeline','placements','analytics','outreach','messages','complaint'];
if (in_array($page, $protectedPages) && !isset($_SESSION['recruiter_id'])) {
    header("Location: index.php?page=auth");
    exit();
}



$recruiterProfile = null;
$stats            = [];
$categories_list  = getCategories();
>>>>>>> 43a4b345e51f77b9bede491f9ae3f1139cea9d11
$clients_list     = [];
$employers_list   = [];

if (isset($_SESSION['recruiter_id'])) {
    $recruiterProfile = getRecruiterProfile($_SESSION['recruiter_id']);
    $stats            = getDashboardStats($_SESSION['recruiter_id']);
<<<<<<< HEAD
    $categories_list  = getCategories();
=======
>>>>>>> 43a4b345e51f77b9bede491f9ae3f1139cea9d11
    $clients_list     = getRecruiterClients($_SESSION['recruiter_id']);
    $employers_list   = getRegisteredEmployers();
}
?>