<?php
require_once __DIR__ . "/../config/config.php";

function ensureHiredStatus() {
    global $conn;
    $r = mysqli_query($conn, "SHOW COLUMNS FROM applications LIKE 'status'");
    $row = mysqli_fetch_assoc($r);
    if ($row && strpos($row['Type'], 'hired') === false) {
        mysqli_query($conn,
            "ALTER TABLE applications MODIFY COLUMN status
             ENUM('submitted','reviewed','shortlisted','interview','rejected','withdrawn','hired')
             DEFAULT 'submitted'");
    }
}

function ensureAnnouncementsTable() {
    global $conn;
    mysqli_query($conn,
        "CREATE TABLE IF NOT EXISTS announcements (
            id INT PRIMARY KEY AUTO_INCREMENT,
            adminid INT NOT NULL,
            title VARCHAR(200) NOT NULL,
            body TEXT NOT NULL,
            createdat DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
}


function registerRecruiter($name, $email, $phone, $password) {
    global $conn;
    $hash = md5($password);
    $stmt = mysqli_prepare($conn,
        "INSERT INTO users (name, email, passwordhash, phone, role, isactive, isverified)
         VALUES (?, ?, ?, ?, 'recruiter', 1, 0)");
    mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $hash, $phone);
    mysqli_stmt_execute($stmt);
    $id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    return $id;
}

function loginRecruiter($email, $password) {
    global $conn;
   
    $stmt = mysqli_prepare($conn,
        "SELECT id, name, email, role, isactive, isverified, passwordhash
         FROM users WHERE email = ? AND role = 'recruiter'");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$user) return false;

  
    $md5Match  = ($user['passwordhash'] === md5($password));
    $hashMatch = password_verify($password, $user['passwordhash']);

    if (!$md5Match && !$hashMatch) return false;

    return $user;
}

function emailExists($email) {
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $exists = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $exists;
}


function getRecruiterProfile($userId) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "SELECT rp.*, u.name, u.email, u.phone, u.profilepic, u.isverified
         FROM users u
         LEFT JOIN recruiterprofiles rp ON rp.userid = u.id
         WHERE u.id = ? AND u.role = 'recruiter'");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $row;
}

function upsertRecruiterProfile($userId, $agencyName, $specialization, $description, $website) {
    global $conn;
    $existing = getRecruiterProfile($userId);
    if ($existing && $existing['agencyname']) {
        $stmt = mysqli_prepare($conn,
            "UPDATE recruiterprofiles SET agencyname=?, specialization=?, description=?, website=? WHERE userid=?");
        mysqli_stmt_bind_param($stmt, "ssssi", $agencyName, $specialization, $description, $website, $userId);
    } else {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO recruiterprofiles (userid, agencyname, specialization, description, website) VALUES (?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "issss", $userId, $agencyName, $specialization, $description, $website);
    }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function updateRecruiterPic($userId, $picPath) {
    global $conn;
    $stmt = mysqli_prepare($conn, "UPDATE users SET profilepic=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "si", $picPath, $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function updateRecruiterPhone($userId, $phone) {
    global $conn;
    $stmt = mysqli_prepare($conn, "UPDATE users SET phone=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "si", $phone, $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}


function getRecruiterClients($recruiterId) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "SELECT rc.*, ep.companyname as regcompany, u.email as empmail
         FROM recruiterclients rc
         LEFT JOIN employerprofiles ep ON rc.employerid = ep.userid
         LEFT JOIN users u ON rc.employerid = u.id
         WHERE rc.recruiterid = ?
         ORDER BY rc.addedat DESC");
    mysqli_stmt_bind_param($stmt, "i", $recruiterId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    mysqli_stmt_close($stmt);
    return $rows;
}

function addRecruiterClient($recruiterId, $employerId, $companyNameOverride) {
    global $conn;
    $empId = $employerId ?: null;
    $stmt = mysqli_prepare($conn,
        "INSERT INTO recruiterclients (recruiterid, employerid, companynameoverride) VALUES (?,?,?)");
    mysqli_stmt_bind_param($stmt, "iis", $recruiterId, $empId, $companyNameOverride);
    mysqli_stmt_execute($stmt);
    $id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    return $id;
}

function deleteRecruiterClient($clientId, $recruiterId) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "DELETE FROM recruiterclients WHERE id=? AND recruiterid=?");
    mysqli_stmt_bind_param($stmt, "ii", $clientId, $recruiterId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function getRegisteredEmployers() {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "SELECT u.id, u.name, ep.companyname
         FROM users u JOIN employerprofiles ep ON u.id=ep.userid
         WHERE u.role='employer' AND u.isactive=1");
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    mysqli_stmt_close($stmt);
    return $rows;
}


function getCategories() {
    global $conn;
    $result = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    return $rows;
}


function createJob($recruiterId, $employerId, $clientName, $categoryId, $title, $description,
                   $requirements, $benefits, $salaryMin, $salaryMax, $location,
                   $jobType, $expLevel, $deadline, $status) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "INSERT INTO jobs (employerid, recruiterid, categoryid, title, description, requirements,
         benefits, salarymin, salarymax, location, jobtype, experiencelevel, deadline, status)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "iiissssddsssss",
        $employerId, $recruiterId, $categoryId, $title, $description, $requirements,
        $benefits, $salaryMin, $salaryMax, $location, $jobType, $expLevel, $deadline, $status);
    mysqli_stmt_execute($stmt);
    $id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    return $id;
}

function updateJob($jobId, $recruiterId, $categoryId, $title, $description, $requirements,
                   $benefits, $salaryMin, $salaryMax, $location, $jobType, $expLevel, $deadline, $status) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "UPDATE jobs SET categoryid=?, title=?, description=?, requirements=?, benefits=?,
         salarymin=?, salarymax=?, location=?, jobtype=?, experiencelevel=?, deadline=?, status=?
         WHERE id=? AND recruiterid=?");
    mysqli_stmt_bind_param($stmt, "issssddsssssii",
        $categoryId, $title, $description, $requirements, $benefits,
        $salaryMin, $salaryMax, $location, $jobType, $expLevel, $deadline, $status,
        $jobId, $recruiterId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function deleteJob($jobId, $recruiterId) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "DELETE FROM jobs WHERE id=? AND recruiterid=?");
    mysqli_stmt_bind_param($stmt, "ii", $jobId, $recruiterId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function getRecruiterJobs($recruiterId, $clientFilter = '', $statusFilter = '', $catFilter = '') {
    global $conn;
    $sql = "SELECT j.*, c.name as catname,
                   COALESCE(
                       (SELECT rc2.companynameoverride FROM recruiterclients rc2
                        WHERE rc2.recruiterid = j.recruiterid
                          AND rc2.employerid  = j.employerid
                        LIMIT 1),
                       ep.companyname,
                       'Unknown'
                   ) as clientname,
                   (SELECT COUNT(*) FROM applications a WHERE a.jobid=j.id) as appcount
            FROM jobs j
            JOIN categories c ON j.categoryid=c.id
            LEFT JOIN employerprofiles ep ON j.employerid=ep.userid
            WHERE j.recruiterid=?";
    $params = [$recruiterId]; $types = "i";
    if ($clientFilter) {
        $sql .= " AND j.employerid=?";
        $params[] = (int)$clientFilter; $types .= "i";
    }
    if ($statusFilter) {
        $sql .= " AND j.status=?";
        $params[] = $statusFilter; $types .= "s";
    }
    if ($catFilter) {
        $sql .= " AND j.categoryid=?";
        $params[] = (int)$catFilter; $types .= "i";
    }
    $sql .= " ORDER BY j.createdat DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    mysqli_stmt_close($stmt);
    return $rows;
}

function getJobById($jobId, $recruiterId = null) {
    global $conn;
    if ($recruiterId) {
        $stmt = mysqli_prepare($conn,
            "SELECT j.*, c.name as catname,
                    COALESCE(rc.companynameoverride, ep.companyname, 'Unknown') as clientname
             FROM jobs j
             JOIN categories c ON j.categoryid=c.id
             LEFT JOIN employerprofiles ep ON j.employerid=ep.userid
             LEFT JOIN recruiterclients rc ON rc.employerid=j.employerid AND rc.recruiterid=j.recruiterid
             WHERE j.id=? AND j.recruiterid=?");
        mysqli_stmt_bind_param($stmt, "ii", $jobId, $recruiterId);
    } else {
        $stmt = mysqli_prepare($conn,
            "SELECT j.*, c.name as catname,
                    COALESCE(rc.companynameoverride, ep.companyname, 'Unknown') as clientname
             FROM jobs j
             JOIN categories c ON j.categoryid=c.id
             LEFT JOIN employerprofiles ep ON j.employerid=ep.userid
             LEFT JOIN recruiterclients rc ON rc.employerid=j.employerid AND rc.recruiterid=j.recruiterid
             WHERE j.id=?");
        mysqli_stmt_bind_param($stmt, "i", $jobId);
    }
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $row;
}

function updateJobStatus($jobId, $recruiterId, $status) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "UPDATE jobs SET status=? WHERE id=? AND recruiterid=?");
    mysqli_stmt_bind_param($stmt, "sii", $status, $jobId, $recruiterId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}


function searchSeekers($keyword = '', $location = '', $expLevel = '', $salaryMax = '') {
    global $conn;
    $sql = "SELECT u.id, u.name, u.email, u.profilepic,
                   COALESCE(sp.headline,'') as headline,
                   COALESCE(sp.skills,'') as skills,
                   COALESCE(sp.yearsexperience,0) as yearsexperience,
                   COALESCE(sp.educationlevel,'') as educationlevel,
                   COALESCE(sp.expectedsalary,0) as expectedsalary,
                   COALESCE(sp.preferredlocation,'') as preferredlocation,
                   COALESCE(sp.resumepath,'') as resumepath
            FROM users u
            LEFT JOIN seekerprofiles sp ON u.id = sp.userid
            WHERE u.role='seeker' AND u.isactive=1";
    $params = []; $types = "";
    if ($keyword) {
        $kw = "%$keyword%";
        $sql .= " AND (COALESCE(sp.skills,'') LIKE ? OR COALESCE(sp.headline,'') LIKE ? OR u.name LIKE ?)";
        $params[] = $kw; $params[] = $kw; $params[] = $kw; $types .= "sss";
    }
    if ($location) {
        $loc = "%$location%";
        $sql .= " AND COALESCE(sp.preferredlocation,'') LIKE ?";
        $params[] = $loc; $types .= "s";
    }
    if ($expLevel) {
        $sql .= " AND COALESCE(sp.yearsexperience,0) >= ?";
        $params[] = (int)$expLevel; $types .= "i";
    }
    if ($salaryMax) {
        $sql .= " AND COALESCE(sp.expectedsalary,0) <= ?";
        $params[] = (float)$salaryMax; $types .= "d";
    }
    $sql .= " ORDER BY u.name";
    if ($types) {
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $result = mysqli_query($conn, $sql);
    }
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    return $rows;
}

function getSeekerPublicProfile($seekerId) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "SELECT u.id, u.name, u.email, u.profilepic,
                COALESCE(sp.headline,'') as headline,
                COALESCE(sp.summary,'') as summary,
                COALESCE(sp.skills,'') as skills,
                COALESCE(sp.yearsexperience,0) as yearsexperience,
                COALESCE(sp.educationlevel,'') as educationlevel,
                COALESCE(sp.expectedsalary,0) as expectedsalary,
                COALESCE(sp.preferredlocation,'') as preferredlocation,
                COALESCE(sp.resumepath,'') as resumepath
         FROM users u
         LEFT JOIN seekerprofiles sp ON u.id = sp.userid
         WHERE u.id = ? AND u.role = 'seeker' AND u.isactive = 1");
    mysqli_stmt_bind_param($stmt, "i", $seekerId);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $row;
}


function sendOutreach($recruiterId, $seekerId, $jobId, $message) {
    global $conn;
    $jid = $jobId ? (int)$jobId : null;

    
    $stmt = mysqli_prepare($conn,
        "INSERT INTO recruiteroutreach (recruiterid, seekerid, jobid, message) VALUES (?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "iiis", $recruiterId, $seekerId, $jid, $message);
    mysqli_stmt_execute($stmt);
    $outreachId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

   
    $body = $message;
    if ($jid) {
        $jr = mysqli_prepare($conn, "SELECT title, location FROM jobs WHERE id=?");
        mysqli_stmt_bind_param($jr, "i", $jid);
        mysqli_stmt_execute($jr);
        $jobRow = mysqli_fetch_assoc(mysqli_stmt_get_result($jr));
        mysqli_stmt_close($jr);
        if ($jobRow) {
            $body = "[Job Opportunity: " . $jobRow['title'] . " — " . $jobRow['location'] . "]\n\n" . $message;
        }
    }

   
    $stmt2 = mysqli_prepare($conn,
        "INSERT INTO messages (senderid, recipientid, applicationid, body, isread)
         VALUES (?, ?, NULL, ?, 0)");
    mysqli_stmt_bind_param($stmt2, "iis", $recruiterId, $seekerId, $body);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);

    return $outreachId;
}

function sendMessage($senderId, $recipientId, $body) {
    global $conn;
    
    $stmt = mysqli_prepare($conn,
        "INSERT INTO messages (senderid, recipientid, applicationid, body, isread)
         VALUES (?, ?, NULL, ?, 0)");
    mysqli_stmt_bind_param($stmt, "iis", $senderId, $recipientId, $body);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function getMessagesByRecruiter($recruiterId) {
    global $conn;
 
    $stmt = mysqli_prepare($conn,
        "SELECT m.*,
                u.name as recipientname, u.email as recipientemail,
                'sent' as direction
         FROM messages m
         JOIN users u ON m.recipientid = u.id
         WHERE m.senderid = ?
         AND NOT EXISTS (
             SELECT 1 FROM recruiteroutreach ro
             WHERE ro.recruiterid = m.senderid
               AND ro.seekerid   = m.recipientid
               AND ro.message    = m.body
         )
         ORDER BY m.sentat DESC");
    mysqli_stmt_bind_param($stmt, "i", $recruiterId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    mysqli_stmt_close($stmt);
    return $rows;
}

function getReceivedMessages($recruiterId) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "SELECT m.*, u.name as sendername, u.email as senderemail, u.role as senderrole
         FROM messages m
         JOIN users u ON m.senderid = u.id
         WHERE m.recipientid = ?
         ORDER BY m.sentat DESC");
    mysqli_stmt_bind_param($stmt, "i", $recruiterId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    mysqli_stmt_close($stmt);
    return $rows;
}

function markMessageRead($msgId, $userId) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "UPDATE messages SET isread=1 WHERE id=? AND recipientid=?");
    mysqli_stmt_bind_param($stmt, "ii", $msgId, $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function getOutreachByRecruiter($recruiterId) {
    global $conn;
    // Sync: if seeker has read the message in inbox, mark outreach as read
    $stmt = mysqli_prepare($conn,
        "UPDATE recruiteroutreach ro
         JOIN messages m
           ON m.senderid   = ro.recruiterid
          AND m.recipientid = ro.seekerid
          AND m.isread = 1
         SET ro.status = CASE WHEN ro.status = 'sent' THEN 'read' ELSE ro.status END
         WHERE ro.recruiterid = ? AND ro.status = 'sent'");
    mysqli_stmt_bind_param($stmt, "i", $recruiterId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt2 = mysqli_prepare($conn,
        "SELECT ro.*, u.name as seekername, u.email as seekeremail,
                j.title as jobtitle
         FROM recruiteroutreach ro
         JOIN users u ON ro.seekerid = u.id
         LEFT JOIN jobs j ON ro.jobid = j.id
         WHERE ro.recruiterid = ?
         ORDER BY ro.sentat DESC");
    mysqli_stmt_bind_param($stmt2, "i", $recruiterId);
    mysqli_stmt_execute($stmt2);
    $result = mysqli_stmt_get_result($stmt2);
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    mysqli_stmt_close($stmt2);
    return $rows;
}

function markOutreachRead($outreachId, $seekerId) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "UPDATE recruiteroutreach SET status='read' WHERE id=? AND seekerid=?");
    mysqli_stmt_bind_param($stmt, "ii", $outreachId, $seekerId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function replyToOutreach($outreachId, $seekerId) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "UPDATE recruiteroutreach SET status='responded' WHERE id=? AND seekerid=?");
    mysqli_stmt_bind_param($stmt, "ii", $outreachId, $seekerId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function getOutreachForSeeker($seekerId) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "SELECT ro.*, u.name as recruitername, rp.agencyname,
                j.title as jobtitle, j.location, j.jobtype
         FROM recruiteroutreach ro
         JOIN users u ON ro.recruiterid=u.id
         LEFT JOIN recruiterprofiles rp ON ro.recruiterid=rp.userid
         LEFT JOIN jobs j ON ro.jobid=j.id
         WHERE ro.seekerid=?
         ORDER BY ro.sentat DESC");
    mysqli_stmt_bind_param($stmt, "i", $seekerId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    mysqli_stmt_close($stmt);
    return $rows;
}


function getApplicationsByRecruiterJobs($recruiterId, $jobFilter = '', $statusFilter = '') {
    global $conn;
    $sql = "SELECT a.*, j.title as jobtitle,
                   COALESCE(rc.companynameoverride, ep.companyname, 'Unknown') as clientname,
                   u.name as seekername, u.email as seekeremail,
                   sp.headline, sp.skills, sp.yearsexperience, sp.resumepath as seekerresume
            FROM applications a
            JOIN jobs j ON a.jobid=j.id
            LEFT JOIN employerprofiles ep ON j.employerid=ep.userid
            LEFT JOIN recruiterclients rc ON rc.employerid=j.employerid AND rc.recruiterid=j.recruiterid
            JOIN users u ON a.seekerid=u.id
            LEFT JOIN seekerprofiles sp ON a.seekerid=sp.userid
            WHERE j.recruiterid=?";
    $params = [$recruiterId]; $types = "i";
    if ($jobFilter) {
        $sql .= " AND a.jobid=?";
        $params[] = (int)$jobFilter; $types .= "i";
    }
    if ($statusFilter) {
        $sql .= " AND a.status=?";
        $params[] = $statusFilter; $types .= "s";
    }
    $sql .= " ORDER BY a.appliedat DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    mysqli_stmt_close($stmt);
    return $rows;
}

function updateApplicationStatus($appId, $recruiterId, $status) {
    global $conn;
    $allowed = ['submitted','reviewed','shortlisted','interview','rejected','hired'];
    if (!in_array($status, $allowed)) return;
    if ($status === 'hired') ensureHiredStatus();
    $stmt = mysqli_prepare($conn,
        "UPDATE applications a
         JOIN jobs j ON a.jobid = j.id
         SET a.status = ?
         WHERE a.id = ? AND j.recruiterid = ?");
    mysqli_stmt_bind_param($stmt, "sii", $status, $appId, $recruiterId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function markAsHired($appId, $recruiterId) {
    global $conn;
    ensureHiredStatus();
    $stmt = mysqli_prepare($conn,
        "UPDATE applications a
         JOIN jobs j ON a.jobid = j.id
         SET a.status = 'hired'
         WHERE a.id = ? AND j.recruiterid = ?");
    mysqli_stmt_bind_param($stmt, "ii", $appId, $recruiterId);
    mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $affected > 0;
}


function getPipeline($recruiterId) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "SELECT a.*, j.title as jobtitle,
                COALESCE(rc.companynameoverride, ep.companyname, 'Unknown') as companyname,
                u.name as seekername, sp.headline, sp.skills
         FROM applications a
         JOIN jobs j ON a.jobid=j.id
         LEFT JOIN employerprofiles ep ON j.employerid=ep.userid
         LEFT JOIN recruiterclients rc ON rc.employerid=j.employerid AND rc.recruiterid=j.recruiterid
         JOIN users u ON a.seekerid=u.id
         LEFT JOIN seekerprofiles sp ON a.seekerid=sp.userid
         WHERE j.recruiterid=? AND a.status NOT IN ('rejected','withdrawn','hired')
         ORDER BY a.appliedat DESC");
    mysqli_stmt_bind_param($stmt, "i", $recruiterId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    mysqli_stmt_close($stmt);
    return $rows;
}

function getPlacementHistory($recruiterId) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "SELECT a.*, j.title as jobtitle,
                COALESCE(rc.companynameoverride, ep.companyname, 'Unknown') as companyname,
                u.name as seekername, u.email as seekeremail,
                sp.headline, sp.skills, sp.yearsexperience
         FROM applications a
         JOIN jobs j ON a.jobid=j.id
         LEFT JOIN employerprofiles ep ON j.employerid=ep.userid
         LEFT JOIN recruiterclients rc ON rc.employerid=j.employerid AND rc.recruiterid=j.recruiterid
         JOIN users u ON a.seekerid=u.id
         LEFT JOIN seekerprofiles sp ON a.seekerid=sp.userid
         WHERE j.recruiterid=? AND a.status='hired'
         ORDER BY a.appliedat DESC");
    mysqli_stmt_bind_param($stmt, "i", $recruiterId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    mysqli_stmt_close($stmt);
    return $rows;
}


function getDashboardStats($recruiterId) {
    global $conn;
    $stats = [];
    foreach ([
        'active_jobs'  => "SELECT COUNT(*) as c FROM jobs WHERE recruiterid=? AND status='active'",
        'clients'      => "SELECT COUNT(*) as c FROM recruiterclients WHERE recruiterid=?",
        'total_apps'   => "SELECT COUNT(*) as c FROM applications a JOIN jobs j ON a.jobid=j.id WHERE j.recruiterid=?",
        'outreach'     => "SELECT COUNT(*) as c FROM recruiteroutreach WHERE recruiterid=?",
    ] as $key => $sql) {
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $recruiterId);
        mysqli_stmt_execute($stmt);
        $stats[$key] = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['c'];
        mysqli_stmt_close($stmt);
    }
    return $stats;
}

function getRecruiterAnalytics($recruiterId) {
    global $conn;
    $data = [];
    $stmt = mysqli_prepare($conn,
        "SELECT COUNT(*) as total, SUM(status='read') as readcount, SUM(status='responded') as responded
         FROM recruiteroutreach WHERE recruiterid=?");
    mysqli_stmt_bind_param($stmt, "i", $recruiterId);
    mysqli_stmt_execute($stmt);
    $r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    $data['outreach_total'] = (int)$r['total'];
    $data['outreach_responded'] = (int)$r['responded'];
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conn,
        "SELECT COUNT(*) as c FROM applications a JOIN jobs j ON a.jobid=j.id WHERE j.recruiterid=?");
    mysqli_stmt_bind_param($stmt, "i", $recruiterId);
    mysqli_stmt_execute($stmt);
    $data['apps_total'] = (int)mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['c'];
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conn,
        "SELECT COUNT(*) as c FROM applications a JOIN jobs j ON a.jobid=j.id WHERE j.recruiterid=? AND a.status='hired'");
    mysqli_stmt_bind_param($stmt, "i", $recruiterId);
    mysqli_stmt_execute($stmt);
    $data['placed'] = (int)mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['c'];
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conn,
        "SELECT COUNT(*) as total, SUM(status='active') as active, SUM(status='closed') as closed
         FROM jobs WHERE recruiterid=?");
    mysqli_stmt_bind_param($stmt, "i", $recruiterId);
    mysqli_stmt_execute($stmt);
    $r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    $data['jobs_total']  = (int)$r['total'];
    $data['jobs_active'] = (int)$r['active'];
    $data['jobs_closed'] = (int)$r['closed'];
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as c FROM recruiterclients WHERE recruiterid=?");
    mysqli_stmt_bind_param($stmt, "i", $recruiterId);
    mysqli_stmt_execute($stmt);
    $data['clients_total'] = (int)mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['c'];
    mysqli_stmt_close($stmt);

    return $data;
}

function getPlacementPerClient($recruiterId) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "SELECT COALESCE(rc.companynameoverride, ep.companyname, 'Unknown') as clientname,
                j.employerid,
                COUNT(a.id) as total_apps,
                SUM(a.status='hired') as placed,
                SUM(a.status='rejected') as rejected
         FROM jobs j
         LEFT JOIN employerprofiles ep ON j.employerid=ep.userid
         LEFT JOIN recruiterclients rc ON rc.employerid=j.employerid AND rc.recruiterid=j.recruiterid
         LEFT JOIN applications a ON a.jobid=j.id
         WHERE j.recruiterid=?
         GROUP BY j.employerid, clientname
         ORDER BY placed DESC");
    mysqli_stmt_bind_param($stmt, "i", $recruiterId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    mysqli_stmt_close($stmt);
    return $rows;
}

function getClientReport($recruiterId, $clientEmployerId) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "SELECT j.id, j.title, j.status, j.createdat,
                COUNT(a.id) as appcount,
                SUM(a.status='submitted') as submitted,
                SUM(a.status='reviewed') as reviewed,
                SUM(a.status='shortlisted') as shortlisted,
                SUM(a.status='interview') as interview,
                SUM(a.status='hired') as hired,
                SUM(a.status='rejected') as rejected
         FROM jobs j
         LEFT JOIN applications a ON a.jobid=j.id
         WHERE j.recruiterid=? AND j.employerid=?
         GROUP BY j.id ORDER BY j.createdat DESC");
    mysqli_stmt_bind_param($stmt, "ii", $recruiterId, $clientEmployerId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    mysqli_stmt_close($stmt);
    return $rows;
}


function getAnnouncements() {
    global $conn;
    ensureAnnouncementsTable();
    $result = mysqli_query($conn,
        "SELECT * FROM announcements ORDER BY createdat DESC LIMIT 5");
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    return $rows;
}


function submitComplaint($submitterId, $subjectId, $description) {
    global $conn;
    $stmt = mysqli_prepare($conn,
        "INSERT INTO complaints (submitterid, subjectid, description) VALUES (?,?,?)");
    mysqli_stmt_bind_param($stmt, "iis", $submitterId, $subjectId, $description);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
?>