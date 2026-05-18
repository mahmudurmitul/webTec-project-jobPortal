<?php
require_once __DIR__ . "/../config/database.php";

// ─── AUTH ────────────────────────────────────────────────────────────────────

function insertSeeker($name, $email, $phone, $password) {
    $conn = $GLOBALS['conn'];
    $hash = md5($password);
    $stmt = mysqli_prepare($conn,
        "INSERT INTO users (name, email, passwordhash, phone, role, isactive, isverified, createdat)
         VALUES (?, ?, ?, ?, 'seeker', 1, 0, NOW())");
    mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $hash, $phone);
    mysqli_stmt_execute($stmt);
    $id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    $stmt2 = mysqli_prepare($conn, "INSERT INTO seekerprofiles (userid) VALUES (?)");
    mysqli_stmt_bind_param($stmt2, "i", $id);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);

    return $id;
}

function loginSeeker($email, $password) {
    $conn = $GLOBALS['conn'];
    $hash = md5($password);
    $stmt = mysqli_prepare($conn,
        "SELECT id, name, email FROM users
         WHERE email=? AND passwordhash=? AND role='seeker' AND isactive=1");
    mysqli_stmt_bind_param($stmt, "ss", $email, $hash);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $user;
}

function emailExists($email) {
    $conn = $GLOBALS['conn'];
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email=?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $exists = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $exists;
}

// ─── PROFILE ─────────────────────────────────────────────────────────────────

function getSeekerById($user_id) {
    $conn = $GLOBALS['conn'];
    $stmt = mysqli_prepare($conn,
        "SELECT u.id, u.name, u.email, u.phone, u.profilepic,
                sp.headline, sp.summary, sp.skills, sp.yearsexperience,
                sp.educationlevel, sp.currentsalary, sp.expectedsalary,
                sp.preferredlocation, sp.resumepath
         FROM users u
         LEFT JOIN seekerprofiles sp ON sp.userid = u.id
         WHERE u.id=? AND u.role='seeker'");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $row;
}

function updateProfile($user_id, $headline, $years_experience, $current_salary,
                        $expected_salary, $preferred_location, $education_level,
                        $skills, $summary) {
    $conn = $GLOBALS['conn'];
    $stmt = mysqli_prepare($conn,
        "UPDATE seekerprofiles SET
            headline=?, yearsexperience=?, currentsalary=?,
            expectedsalary=?, preferredlocation=?, educationlevel=?,
            skills=?, summary=?
         WHERE userid=?");
    mysqli_stmt_bind_param($stmt, "sissssssi",
        $headline, $years_experience, $current_salary,
        $expected_salary, $preferred_location, $education_level,
        $skills, $summary, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function updateProfilePic($user_id, $pic_path) {
    $conn = $GLOBALS['conn'];
    // Store web-accessible path (strip leading ../ if present)
    $web_path = preg_replace('#^\.\./+#', '/job_seeker/', $pic_path);
    $stmt = mysqli_prepare($conn, "UPDATE users SET profilepic=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "si", $web_path, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function updateResume($user_id, $resume_path) {
    $conn = $GLOBALS['conn'];
    // Store web-accessible path (strip leading ../ if present)
    $web_path = preg_replace('#^\.\./+#', '/job_seeker/', $resume_path);
    $stmt = mysqli_prepare($conn, "UPDATE seekerprofiles SET resumepath=? WHERE userid=?");
    mysqli_stmt_bind_param($stmt, "si", $web_path, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// ─── JOBS ─────────────────────────────────────────────────────────────────────

function getJobs() {
    $conn = $GLOBALS['conn'];
    $sql = "SELECT j.*, c.name as catname,
                   u.name as employer_name,
                   ep.companyname as company_name
            FROM jobs j
            JOIN categories c ON c.id = j.categoryid
            JOIN users u ON u.id = j.employerid
            LEFT JOIN employerprofiles ep ON ep.userid = j.employerid
            WHERE j.status='active' AND j.deadline >= CURDATE()
            ORDER BY j.isfeatured DESC, j.createdat DESC";
    return mysqli_query($conn, $sql);
}

function filterJobs($cat, $loc, $type, $exp, $salary_min, $salary_max, $keyword) {
    $conn = $GLOBALS['conn'];
    $sql = "SELECT j.*, c.name as catname,
                   u.name as employer_name,
                   ep.companyname as company_name
            FROM jobs j
            JOIN categories c ON c.id = j.categoryid
            JOIN users u ON u.id = j.employerid
            LEFT JOIN employerprofiles ep ON ep.userid = j.employerid
            WHERE j.status='active' AND j.deadline >= CURDATE()";

    $params = [];
    $types  = "";

    if ($cat) {
        $sql .= " AND j.categoryid=?";
        $params[] = $cat; $types .= "i";
    }
    if ($loc) {
        $loc_like = "%$loc%";
        $sql .= " AND j.location LIKE ?";
        $params[] = $loc_like; $types .= "s";
    }
    if ($type) {
        $sql .= " AND j.jobtype=?";
        $params[] = $type; $types .= "s";
    }
    if ($exp) {
        $sql .= " AND j.experiencelevel=?";
        $params[] = $exp; $types .= "s";
    }
    if ($salary_min !== '') {
        $sql .= " AND j.salarymax >= ?";
        $params[] = $salary_min; $types .= "i";
    }
    if ($salary_max !== '') {
        $sql .= " AND j.salarymin <= ?";
        $params[] = $salary_max; $types .= "i";
    }
    if ($keyword) {
        $kw = "%$keyword%";
        $sql .= " AND (j.title LIKE ? OR j.description LIKE ? OR ep.companyname LIKE ?)";
        $params[] = $kw; $params[] = $kw; $params[] = $kw;
        $types .= "sss";
    }

    $sql .= " ORDER BY j.isfeatured DESC, j.createdat DESC";

    $stmt = mysqli_prepare($conn, $sql);
    if ($types) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $jobs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $jobs[] = $row;
    }
    mysqli_stmt_close($stmt);
    return $jobs;
}

function getJobById($job_id) {
    $conn = $GLOBALS['conn'];
    $stmt = mysqli_prepare($conn,
        "SELECT j.*, c.name as catname,
                u.name as employer_name,
                ep.companyname as company_name,
                ep.industry, ep.website,
                ep.description as company_desc
         FROM jobs j
         JOIN categories c ON c.id = j.categoryid
         JOIN users u ON u.id = j.employerid
         LEFT JOIN employerprofiles ep ON ep.userid = j.employerid
         WHERE j.id=? AND j.status='active'");
    mysqli_stmt_bind_param($stmt, "i", $job_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $row;
}

function getCategories() {
    return mysqli_query($GLOBALS['conn'], "SELECT * FROM categories ORDER BY name");
}

// ─── APPLICATIONS ─────────────────────────────────────────────────────────────

function hasApplied($seeker_id, $job_id) {
    $conn = $GLOBALS['conn'];
    $stmt = mysqli_prepare($conn,
        "SELECT id FROM applications WHERE seekerid=? AND jobid=?");
    mysqli_stmt_bind_param($stmt, "ii", $seeker_id, $job_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $exists = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $exists;
}

function applyToJob($seeker_id, $job_id, $cover_letter, $resume_path) {
    $conn = $GLOBALS['conn'];
    $stmt = mysqli_prepare($conn,
        "INSERT INTO applications (jobid, seekerid, coverletter, resumepath, status, appliedat)
         VALUES (?, ?, ?, ?, 'submitted', NOW())");
    mysqli_stmt_bind_param($stmt, "iiss", $job_id, $seeker_id, $cover_letter, $resume_path);
    mysqli_stmt_execute($stmt);
    $id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    return $id;
}

function withdrawApplication($app_id, $seeker_id) {
    $conn = $GLOBALS['conn'];
    $stmt = mysqli_prepare($conn,
        "UPDATE applications SET status='withdrawn'
         WHERE id=? AND seekerid=? AND status='submitted'");
    mysqli_stmt_bind_param($stmt, "ii", $app_id, $seeker_id);
    mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $affected > 0;
}

function getSeekerApplications($seeker_id) {
    $conn = $GLOBALS['conn'];
    $stmt = mysqli_prepare($conn,
        "SELECT a.*, j.title, j.location, j.jobtype as job_type, j.deadline,
                ep.companyname as company_name, u.name as employer_name
         FROM applications a
         JOIN jobs j ON j.id = a.jobid
         JOIN users u ON u.id = j.employerid
         LEFT JOIN employerprofiles ep ON ep.userid = j.employerid
         WHERE a.seekerid=?
         ORDER BY a.appliedat DESC");
    mysqli_stmt_bind_param($stmt, "i", $seeker_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $apps = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['applied_at'] = $row['appliedat'];
        $row['job_id']     = $row['jobid'];
        $apps[] = $row;
    }
    mysqli_stmt_close($stmt);
    return $apps;
}

// ─── SAVED JOBS ───────────────────────────────────────────────────────────────

function isJobSaved($user_id, $job_id) {
    $conn = $GLOBALS['conn'];
    $stmt = mysqli_prepare($conn,
        "SELECT id FROM savedjobs WHERE userid=? AND jobid=?");
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $job_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $exists = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $exists;
}

function saveJob($user_id, $job_id) {
    $conn = $GLOBALS['conn'];
    if (isJobSaved($user_id, $job_id)) return false;
    $stmt = mysqli_prepare($conn,
        "INSERT INTO savedjobs (userid, jobid, savedat) VALUES (?, ?, NOW())");
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $job_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return true;
}

function unsaveJob($user_id, $job_id) {
    $conn = $GLOBALS['conn'];
    $stmt = mysqli_prepare($conn,
        "DELETE FROM savedjobs WHERE userid=? AND jobid=?");
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $job_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function getSavedJobs($user_id) {
    $conn = $GLOBALS['conn'];
    $stmt = mysqli_prepare($conn,
        "SELECT j.*, c.name as catname,
                ep.companyname as company_name,
                u.name as employer_name,
                sj.savedat as saved_at
         FROM savedjobs sj
         JOIN jobs j ON j.id = sj.jobid
         JOIN categories c ON c.id = j.categoryid
         JOIN users u ON u.id = j.employerid
         LEFT JOIN employerprofiles ep ON ep.userid = j.employerid
         WHERE sj.userid=?
         ORDER BY sj.savedat DESC");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $jobs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['is_featured'] = $row['isfeatured'];
        $row['job_type']    = $row['jobtype'];
        $row['salary_min']  = $row['salarymin'];
        $row['salary_max']  = $row['salarymax'];
        $jobs[] = $row;
    }
    mysqli_stmt_close($stmt);
    return $jobs;
}

function getSavedJobsCount($user_id) {
    $conn = $GLOBALS['conn'];
    $stmt = mysqli_prepare($conn,
        "SELECT COUNT(*) as cnt FROM savedjobs WHERE userid=?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $row['cnt'] ?? 0;
}

// ─── MESSAGES (Admin → Seeker) ────────────────────────────────────────────────

function getSeekerMessages($seeker_id) {
    $conn = $GLOBALS['conn'];
    $stmt = mysqli_prepare($conn,
        "SELECT m.*, u.name as sender_name, u.role as sender_role
         FROM messages m
         JOIN users u ON u.id = m.senderid
         WHERE m.recipientid = ?
         ORDER BY m.sentat DESC");
    mysqli_stmt_bind_param($stmt, "i", $seeker_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $msgs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $msgs[] = $row;
    }
    mysqli_stmt_close($stmt);
    return $msgs;
}

function markMessageRead($message_id, $seeker_id) {
    $conn = $GLOBALS['conn'];
    $stmt = mysqli_prepare($conn,
        "UPDATE messages SET isread=1 WHERE id=? AND recipientid=?");
    mysqli_stmt_bind_param($stmt, "ii", $message_id, $seeker_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function getUnreadMessageCount($seeker_id) {
    $conn = $GLOBALS['conn'];
    $stmt = mysqli_prepare($conn,
        "SELECT COUNT(*) as cnt FROM messages WHERE recipientid=? AND isread=0");
    mysqli_stmt_bind_param($stmt, "i", $seeker_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $row['cnt'] ?? 0;
}