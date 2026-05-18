<?php
require_once "config.php";



function loginAdmin($email, $password) {
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email=? AND role='admin' AND isactive=1");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    if ($user && password_verify($password, $user['passwordhash'])) return $user;
    // fallback md5 for seeded test data
    if ($user && $user['passwordhash'] === md5($password)) return $user;
    return false;
}



function getDashboardStats() {
    global $conn;
    $stats = [];

    $roles = ['seeker','employer','recruiter','admin'];
    $stats['users_by_role'] = [];
    foreach ($roles as $role) {
        $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as c FROM users WHERE role=?");
        mysqli_stmt_bind_param($stmt, "s", $role);
        mysqli_stmt_execute($stmt);
        $stats['users_by_role'][$role] = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['c'];
        mysqli_stmt_close($stmt);
    }

    $r = mysqli_query($conn, "SELECT COUNT(*) as c FROM jobs WHERE status='active'");
    $stats['active_jobs'] = mysqli_fetch_assoc($r)['c'];

    $r = mysqli_query($conn, "SELECT COUNT(*) as c FROM applications WHERE DATE(appliedat)=CURDATE()");
    $stats['apps_today'] = mysqli_fetch_assoc($r)['c'];

    $r = mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE isverified=0 AND role IN ('employer','recruiter') AND isactive=1");
    $stats['pending_verification'] = mysqli_fetch_assoc($r)['c'];

    $r = mysqli_query($conn, "SELECT COUNT(*) as c FROM complaints WHERE status='open'");
    $stats['open_complaints'] = mysqli_fetch_assoc($r)['c'];

    return $stats;
}


function getEmployers($search = '', $verified = '') {
    global $conn;
    $sql = "SELECT u.*, ep.companyname, ep.industry, ep.companysize
            FROM users u
            LEFT JOIN employerprofiles ep ON u.id=ep.userid
            WHERE u.role='employer'";
    $params = []; $types = "";
    if ($search) {
        $kw = "%$search%";
        $sql .= " AND (u.name LIKE ? OR u.email LIKE ? OR ep.companyname LIKE ?)";
        $params[] = $kw; $params[] = $kw; $params[] = $kw;
        $types .= "sss";
    }
    if ($verified !== '') {
        $sql .= " AND u.isverified=?";
        $params[] = (int)$verified;
        $types .= "i";
    }
    $sql .= " ORDER BY u.createdat DESC";
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

function setUserVerified($userId, $status) {
    global $conn;
    $stmt = mysqli_prepare($conn, "UPDATE users SET isverified=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ii", $status, $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function setUserActive($userId, $status) {
    global $conn;
    $stmt = mysqli_prepare($conn, "UPDATE users SET isactive=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ii", $status, $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}



function getRecruiters($search = '', $verified = '') {
    global $conn;
    $sql = "SELECT u.*, rp.agencyname, rp.specialization
            FROM users u
            LEFT JOIN recruiterprofiles rp ON u.id=rp.userid
            WHERE u.role='recruiter'";
    $params = []; $types = "";
    if ($search) {
        $kw = "%$search%";
        $sql .= " AND (u.name LIKE ? OR u.email LIKE ? OR rp.agencyname LIKE ?)";
        $params[] = $kw; $params[] = $kw; $params[] = $kw;
        $types .= "sss";
    }
    if ($verified !== '') {
        $sql .= " AND u.isverified=?";
        $params[] = (int)$verified;
        $types .= "i";
    }
    $sql .= " ORDER BY u.createdat DESC";
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



function getSeekers($search = '') {
    global $conn;
    $sql = "SELECT u.*, sp.headline, sp.skills, sp.yearsexperience
            FROM users u
            LEFT JOIN seekerprofiles sp ON u.id=sp.userid
            WHERE u.role='seeker'";
    if ($search) {
        $kw = "%$search%";
        $stmt = mysqli_prepare($conn, $sql . " AND (u.name LIKE ? OR u.email LIKE ? OR sp.skills LIKE ?) ORDER BY u.createdat DESC");
        mysqli_stmt_bind_param($stmt, "sss", $kw, $kw, $kw);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $result = mysqli_query($conn, $sql . " ORDER BY u.createdat DESC");
    }
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    return $rows;
}



function getCategories() {
    global $conn;
    $result = mysqli_query($conn,
        "SELECT c.*, COUNT(j.id) as jobcount
         FROM categories c
         LEFT JOIN jobs j ON c.id=j.categoryid AND j.status='active'
         GROUP BY c.id
         ORDER BY c.name");
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    return $rows;
}

function addCategory($name, $description) {
    global $conn;
    $stmt = mysqli_prepare($conn, "INSERT INTO categories (name, description) VALUES (?,?)");
    mysqli_stmt_bind_param($stmt, "ss", $name, $description);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function updateCategory($id, $name, $description) {
    global $conn;
    $stmt = mysqli_prepare($conn, "UPDATE categories SET name=?, description=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssi", $name, $description, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function deleteCategory($id) {
    global $conn;
    // Check active jobs
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as c FROM jobs WHERE categoryid=? AND status='active'");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $count = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['c'];
    mysqli_stmt_close($stmt);
    if ($count > 0) return false;
    $stmt = mysqli_prepare($conn, "DELETE FROM categories WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return true;
}

function getCategoryById($id) {
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT * FROM categories WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $row;
}



function getAllJobs($search = '', $statusFilter = '', $empFilter = '', $recFilter = '') {
    global $conn;
    $sql = "SELECT j.*, c.name as catname,
                   eu.name as empname, ep.companyname,
                   ru.name as recname, rp.agencyname,
                   (SELECT COUNT(*) FROM applications a WHERE a.jobid=j.id) as appcount
            FROM jobs j
            JOIN categories c ON j.categoryid=c.id
            JOIN users eu ON j.employerid=eu.id
            LEFT JOIN employerprofiles ep ON j.employerid=ep.userid
            LEFT JOIN users ru ON j.recruiterid=ru.id
            LEFT JOIN recruiterprofiles rp ON j.recruiterid=rp.userid
            WHERE 1=1";
    $params = []; $types = "";
    if ($search) {
        $kw = "%$search%";
        $sql .= " AND (j.title LIKE ? OR ep.companyname LIKE ? OR j.location LIKE ?)";
        $params[] = $kw; $params[] = $kw; $params[] = $kw;
        $types .= "sss";
    }
    if ($statusFilter) {
        $sql .= " AND j.status=?";
        $params[] = $statusFilter; $types .= "s";
    }
    if ($empFilter) {
        $sql .= " AND j.employerid=?";
        $params[] = (int)$empFilter; $types .= "i";
    }
    if ($recFilter) {
        $sql .= " AND j.recruiterid=?";
        $params[] = (int)$recFilter; $types .= "i";
    }
    $sql .= " ORDER BY j.createdat DESC";
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

function removeJob($jobId) {
    global $conn;
    $stmt = mysqli_prepare($conn, "UPDATE jobs SET status='closed' WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $jobId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function setJobFeatured($jobId, $val) {
    global $conn;
    $stmt = mysqli_prepare($conn, "UPDATE jobs SET isfeatured=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ii", $val, $jobId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}


function getComplaints($statusFilter = '') {
    global $conn;
    $sql = "SELECT co.*, su.name as submittername, su.email as submitteremail, su.role as submitterrole,
                   sj.name as subjectname
            FROM complaints co
            JOIN users su ON co.submitterid=su.id
            LEFT JOIN users sj ON co.subjectid=sj.id
            WHERE 1=1";
    if ($statusFilter) {
        $stmt = mysqli_prepare($conn, $sql . " AND co.status=? ORDER BY co.createdat DESC");
        mysqli_stmt_bind_param($stmt, "s", $statusFilter);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $result = mysqli_query($conn, $sql . " ORDER BY co.createdat DESC");
    }
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    return $rows;
}

function resolveComplaint($id, $note) {
    global $conn;
    $stmt = mysqli_prepare($conn, "UPDATE complaints SET status='resolved', adminnote=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "si", $note, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}


function ensureSettingsTable() {
    global $conn;
    mysqli_query($conn,
        "CREATE TABLE IF NOT EXISTS platform_settings (
            `key` VARCHAR(100) PRIMARY KEY,
            `value` VARCHAR(500) NOT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )"
    );
}

function getSetting($key, $default = '') {
    global $conn;
    ensureSettingsTable();
    $stmt = mysqli_prepare($conn, "SELECT `value` FROM platform_settings WHERE `key`=?");
    mysqli_stmt_bind_param($stmt, "s", $key);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $row ? $row['value'] : $default;
}

function setSetting($key, $value) {
    global $conn;
    ensureSettingsTable();
    $stmt = mysqli_prepare($conn,
        "INSERT INTO platform_settings (`key`, `value`) VALUES (?,?)
         ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)");
    mysqli_stmt_bind_param($stmt, "ss", $key, $value);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function getAllSettings() {
    global $conn;
    ensureSettingsTable();
    $result = mysqli_query($conn, "SELECT * FROM platform_settings");
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[$r['key']] = $r['value'];
    return $rows;
}


function getJobsPerCategory() {
    global $conn;
    $result = mysqli_query($conn,
        "SELECT c.name, COUNT(j.id) as total
         FROM categories c
         LEFT JOIN jobs j ON c.id=j.categoryid
         GROUP BY c.id ORDER BY total DESC");
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    return $rows;
}

function getAppsOverTime() {
    global $conn;
    $result = mysqli_query($conn,
        "SELECT DATE_FORMAT(appliedat,'%Y-%m') as month, COUNT(*) as total
         FROM applications
         GROUP BY month ORDER BY month DESC LIMIT 12");
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    return array_reverse($rows);
}

function getTopEmployers() {
    global $conn;
    $result = mysqli_query($conn,
        "SELECT ep.companyname, u.name, COUNT(a.id) as total_apps
         FROM users u
         JOIN employerprofiles ep ON u.id=ep.userid
         LEFT JOIN jobs j ON j.employerid=u.id
         LEFT JOIN applications a ON a.jobid=j.id
         WHERE u.role='employer'
         GROUP BY u.id ORDER BY total_apps DESC LIMIT 5");
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    return $rows;
}

function getMostActiveRecruiters() {
    global $conn;
    $result = mysqli_query($conn,
        "SELECT rp.agencyname, u.name, COUNT(ro.id) as outreach_count, COUNT(DISTINCT j.id) as job_count
         FROM users u
         JOIN recruiterprofiles rp ON u.id=rp.userid
         LEFT JOIN recruiteroutreach ro ON ro.recruiterid=u.id
         LEFT JOIN jobs j ON j.recruiterid=u.id
         WHERE u.role='recruiter'
         GROUP BY u.id ORDER BY outreach_count DESC LIMIT 5");
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    return $rows;
}

function getPopularLocations() {
    global $conn;
    $result = mysqli_query($conn,
        "SELECT location, COUNT(*) as total FROM jobs WHERE location != '' GROUP BY location ORDER BY total DESC LIMIT 8");
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    return $rows;
}

function getPopularJobTypes() {
    global $conn;
    $result = mysqli_query($conn,
        "SELECT jobtype, COUNT(*) as total FROM jobs GROUP BY jobtype ORDER BY total DESC");
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    return $rows;
}

function getUserGrowth() {
    global $conn;
    $result = mysqli_query($conn,
        "SELECT DATE_FORMAT(createdat,'%Y-%m') as month, role, COUNT(*) as total
         FROM users
         GROUP BY month, role
         ORDER BY month DESC LIMIT 48");
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    return array_reverse($rows);
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
        )"
    );
}

function postAnnouncement($adminId, $title, $body) {
    global $conn;
    ensureAnnouncementsTable();
    $stmt = mysqli_prepare($conn, "INSERT INTO announcements (adminid, title, body) VALUES (?,?,?)");
    mysqli_stmt_bind_param($stmt, "iss", $adminId, $title, $body);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function getAnnouncements() {
    global $conn;
    ensureAnnouncementsTable();
    $result = mysqli_query($conn, "SELECT * FROM announcements ORDER BY createdat DESC");
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    return $rows;
}

function deleteAnnouncement($id) {
    global $conn;
    ensureAnnouncementsTable();
    $stmt = mysqli_prepare($conn, "DELETE FROM announcements WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}


function getMonthlySummary($year, $month) {
    global $conn;
    $data = [];
    $ym = sprintf('%04d-%02d', $year, $month);

    $stmt = mysqli_prepare($conn,
        "SELECT role, COUNT(*) as c FROM users WHERE DATE_FORMAT(createdat,'%Y-%m')=? GROUP BY role");
    mysqli_stmt_bind_param($stmt, "s", $ym);
    mysqli_stmt_execute($stmt);
    $r = mysqli_stmt_get_result($stmt);
    $data['new_users'] = [];
    while ($row = mysqli_fetch_assoc($r)) $data['new_users'][$row['role']] = $row['c'];
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conn,
        "SELECT COUNT(*) as c FROM jobs WHERE DATE_FORMAT(createdat,'%Y-%m')=?");
    mysqli_stmt_bind_param($stmt, "s", $ym);
    mysqli_stmt_execute($stmt);
    $data['new_jobs'] = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['c'];
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conn,
        "SELECT COUNT(*) as c FROM applications WHERE DATE_FORMAT(appliedat,'%Y-%m')=?");
    mysqli_stmt_bind_param($stmt, "s", $ym);
    mysqli_stmt_execute($stmt);
    $data['new_apps'] = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['c'];
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conn,
        "SELECT COUNT(*) as c FROM complaints WHERE DATE_FORMAT(createdat,'%Y-%m')=?");
    mysqli_stmt_bind_param($stmt, "s", $ym);
    mysqli_stmt_execute($stmt);
    $data['new_complaints'] = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['c'];
    mysqli_stmt_close($stmt);

    return $data;
}
?>
