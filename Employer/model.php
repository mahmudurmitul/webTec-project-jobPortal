<?php

function getEmployerStats($conn, $employerid) {
    $stats = [];

    $stmt = $conn->prepare("SELECT COUNT(*) AS totaljobs FROM jobs WHERE employerid = ?");
    $stmt->bind_param("i", $employerid);
    $stmt->execute();
    $stats['totaljobs'] = $stmt->get_result()->fetch_assoc()['totaljobs'] ?? 0;

    $stmt = $conn->prepare("SELECT COUNT(*) AS activejobs FROM jobs WHERE employerid = ? AND status = 'active'");
    $stmt->bind_param("i", $employerid);
    $stmt->execute();
    $stats['activejobs'] = $stmt->get_result()->fetch_assoc()['activejobs'] ?? 0;

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS totalapplications 
        FROM applications a 
        JOIN jobs j ON a.jobid = j.id 
        WHERE j.employerid = ?
    ");
    $stmt->bind_param("i", $employerid);
    $stmt->execute();
    $stats['totalapplications'] = $stmt->get_result()->fetch_assoc()['totalapplications'] ?? 0;

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS shortlisted 
        FROM applications a 
        JOIN jobs j ON a.jobid = j.id 
        WHERE j.employerid = ? AND a.status IN ('shortlisted','interview')
    ");
    $stmt->bind_param("i", $employerid);
    $stmt->execute();
    $stats['shortlisted'] = $stmt->get_result()->fetch_assoc()['shortlisted'] ?? 0;

    return $stats;
}

function getEmployerJobs($conn, $employerid) {
    $stmt = $conn->prepare("
        SELECT j.*, COUNT(a.id) AS applicationcount
        FROM jobs j
        LEFT JOIN applications a ON j.id = a.jobid
        WHERE j.employerid = ?
        GROUP BY j.id
        ORDER BY j.createdat DESC
    ");
    $stmt->bind_param("i", $employerid);
    $stmt->execute();

    return $stmt->get_result();
}

/* Edit My Profile page: only employer personal details */
function getEmployerProfile($conn, $userid) {
    $stmt = $conn->prepare("
        SELECT id, name, email, phone
        FROM users
        WHERE id = ? AND role = 'employer'
        LIMIT 1
    ");

    $stmt->bind_param("i", $userid);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}

/* Manage Company Profile page: only company details */
function getCompanyProfile($conn, $userid) {
    $stmt = $conn->prepare("
        SELECT *
        FROM employerprofiles
        WHERE userid = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $userid);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}

function getCategories($conn) {
    return $conn->query("SELECT * FROM categories ORDER BY name ASC");
}

function getSingleJob($conn, $jobid, $employerid) {
    $stmt = $conn->prepare("
        SELECT * 
        FROM jobs 
        WHERE id = ? AND employerid = ?
    ");

    $stmt->bind_param("ii", $jobid, $employerid);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}

function getApplicationsByJob($conn, $jobid, $employerid) {
    $stmt = $conn->prepare("
        SELECT a.*, 
               u.name, 
               u.email,
               sp.headline, 
               sp.skills, 
               sp.yearsexperience, 
               sp.educationlevel
        FROM applications a
        JOIN users u ON a.seekerid = u.id
        LEFT JOIN seekerprofiles sp ON sp.userid = u.id
        JOIN jobs j ON a.jobid = j.id
        WHERE a.jobid = ? AND j.employerid = ?
        ORDER BY a.appliedat DESC
    ");

    $stmt->bind_param("ii", $jobid, $employerid);
    $stmt->execute();

    return $stmt->get_result();
}

function getShortlistedCandidates($conn, $employerid) {
    $stmt = $conn->prepare("
        SELECT a.*, 
               u.name, 
               u.email, 
               j.title AS jobtitle, 
               sp.headline
        FROM applications a
        JOIN users u ON a.seekerid = u.id
        JOIN jobs j ON a.jobid = j.id
        LEFT JOIN seekerprofiles sp ON sp.userid = u.id
        WHERE j.employerid = ? AND a.status IN ('shortlisted','interview')
        ORDER BY a.appliedat DESC
    ");

    $stmt->bind_param("i", $employerid);
    $stmt->execute();

    return $stmt->get_result();
}

function getAnalyticsData($conn, $employerid) {
    $stmt = $conn->prepare("
        SELECT
            COUNT(DISTINCT j.id) AS totaljobs,
            COUNT(a.id) AS totalapplications,
            SUM(CASE WHEN a.status = 'submitted' THEN 1 ELSE 0 END) AS submitted,
            SUM(CASE WHEN a.status = 'reviewed' THEN 1 ELSE 0 END) AS reviewed,
            SUM(CASE WHEN a.status = 'shortlisted' THEN 1 ELSE 0 END) AS shortlisted,
            SUM(CASE WHEN a.status = 'interview' THEN 1 ELSE 0 END) AS interviews,
            SUM(CASE WHEN a.status = 'rejected' THEN 1 ELSE 0 END) AS rejected
        FROM jobs j
        LEFT JOIN applications a ON j.id = a.jobid
        WHERE j.employerid = ?
    ");

    $stmt->bind_param("i", $employerid);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}

function getMyRecruiters($conn, $employerid) {
    $stmt = $conn->prepare("
        SELECT rc.*, 
               u.name AS recname, 
               u.email AS recemail, 
               rp.agencyname,
               COUNT(DISTINCT j.id) AS jobscount
        FROM recruiterclients rc
        JOIN users u ON rc.recruiterid = u.id
        LEFT JOIN recruiterprofiles rp ON rp.userid = rc.recruiterid
        LEFT JOIN jobs j ON j.recruiterid = rc.recruiterid AND j.employerid = ?
        WHERE rc.employerid = ?
        GROUP BY rc.id
    ");

    $stmt->bind_param("ii", $employerid, $employerid);
    $stmt->execute();

    return $stmt->get_result();
}

function getMyComplaints($conn, $employerid) {
    $stmt = $conn->prepare("
        SELECT c.*, u.name AS subjectname
        FROM complaints c
        LEFT JOIN users u ON c.subjectid = u.id
        WHERE c.submitterid = ?
        ORDER BY c.createdat DESC
    ");

    $stmt->bind_param("i", $employerid);
    $stmt->execute();

    return $stmt->get_result();
}

function getMessages($conn, $employerid) {
    $stmt = $conn->prepare("
        SELECT m.*, 
               u.name AS sendername, 
               u2.name AS recipientname, 
               j.title AS jobtitle
        FROM messages m
        JOIN users u ON m.senderid = u.id
        JOIN users u2 ON m.recipientid = u2.id
        LEFT JOIN applications a ON m.applicationid = a.id
        LEFT JOIN jobs j ON a.jobid = j.id
        WHERE m.senderid = ? OR m.recipientid = ?
        ORDER BY m.sentat DESC
    ");

    $stmt->bind_param("ii", $employerid, $employerid);
    $stmt->execute();

    return $stmt->get_result();
}

?>