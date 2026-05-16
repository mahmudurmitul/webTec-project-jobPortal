<?php

function getEmployerStats($conn, $employerid) {
    $stats = [];

    $stmt = $conn->prepare("SELECT COUNT(*) AS totaljobs FROM jobs WHERE employerid = ?");
    $stmt->bind_param("i", $employerid);
    $stmt->execute();
    $stats['totaljobs'] = $stmt->get_result()->fetch_assoc()['totaljobs'];

    $stmt = $conn->prepare("SELECT COUNT(*) AS activejobs FROM jobs WHERE employerid = ? AND status = 'active'");
    $stmt->bind_param("i", $employerid);
    $stmt->execute();
    $stats['activejobs'] = $stmt->get_result()->fetch_assoc()['activejobs'];

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS totalapplications
        FROM applications a
        JOIN jobs j ON a.jobid = j.id
        WHERE j.employerid = ?
    ");
    $stmt->bind_param("i", $employerid);
    $stmt->execute();
    $stats['totalapplications'] = $stmt->get_result()->fetch_assoc()['totalapplications'];

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

function getEmployerProfile($conn, $userid) {
    $stmt = $conn->prepare("SELECT * FROM employerprofiles WHERE userid = ?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}

function getCategories($conn) {
    return $conn->query("SELECT * FROM categories ORDER BY name ASC");
}

function getSingleJob($conn, $jobid, $employerid) {
    $stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ? AND employerid = ?");
    $stmt->bind_param("ii", $jobid, $employerid);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}

function getApplicationsByJob($conn, $jobid, $employerid) {
    $stmt = $conn->prepare("
        SELECT 
            a.*, 
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

?>