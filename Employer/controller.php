<?php

function sendMessage($conn, $senderid, $recipientid, $applicationid, $body) {
    $stmt = $conn->prepare("
        INSERT INTO messages (senderid, recipientid, applicationid, body)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param("iiis", $senderid, $recipientid, $applicationid, $body);

    return $stmt->execute();
}

function submitComplaint($conn, $userid, $subjectid, $description) {
    $stmt = $conn->prepare("
        INSERT INTO complaints (submitterid, subjectid, description)
        VALUES (?, ?, ?)
    ");

    $stmt->bind_param("iis", $userid, $subjectid, $description);

    return $stmt->execute();
}

/* Edit My Profile page: only employer personal details */
function saveEmployerProfile($conn, $userid) {
    $name            = trim($_POST['name'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $phone           = trim($_POST['phone'] ?? '');
    $password        = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if ($name === '' || $email === '') {
        return false;
    }

    if ($password !== '') {
        if (strlen($password) < 6 || $password !== $confirmPassword) {
            return false;
        }

        $hash = md5($password);

        $stmt = $conn->prepare("
            UPDATE users
            SET name = ?, email = ?, phone = ?, passwordhash = ?
            WHERE id = ? AND role = 'employer'
        ");

        $stmt->bind_param("ssssi", $name, $email, $phone, $hash, $userid);
    } else {
        $stmt = $conn->prepare("
            UPDATE users
            SET name = ?, email = ?, phone = ?
            WHERE id = ? AND role = 'employer'
        ");

        $stmt->bind_param("sssi", $name, $email, $phone, $userid);
    }

    if ($stmt->execute()) {
        $_SESSION['name']  = $name;
        $_SESSION['email'] = $email;
        return true;
    }

    return false;
}

/* Manage Company Profile page: only company details */
function saveCompanyProfile($conn, $userid) {
    $companyname = trim($_POST['companyname'] ?? '');
    $industry    = trim($_POST['industry'] ?? '');
    $companysize = trim($_POST['companysize'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $website     = trim($_POST['website'] ?? '');
    $address     = trim($_POST['address'] ?? '');

    if ($companyname === '') {
        return false;
    }

    $logopath = '';

    if (!empty($_FILES['logo']['name'])) {
        $uploadDir = "../uploads/logos/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $safeName   = preg_replace("/[^a-zA-Z0-9._-]/", "_", basename($_FILES['logo']['name']));
        $filename   = time() . "_" . $safeName;
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetPath)) {
            $logopath = "uploads/logos/" . $filename;
        }
    }

    $check = $conn->prepare("
        SELECT id 
        FROM employerprofiles 
        WHERE userid = ?
    ");

    $check->bind_param("i", $userid);
    $check->execute();

    $exists = $check->get_result()->num_rows > 0;

    if ($exists) {
        if ($logopath !== '') {
            $stmt = $conn->prepare("
                UPDATE employerprofiles
                SET companyname = ?, 
                    industry = ?, 
                    companysize = ?, 
                    description = ?, 
                    website = ?, 
                    address = ?, 
                    logopath = ?
                WHERE userid = ?
            ");

            $stmt->bind_param(
                "sssssssi",
                $companyname,
                $industry,
                $companysize,
                $description,
                $website,
                $address,
                $logopath,
                $userid
            );
        } else {
            $stmt = $conn->prepare("
                UPDATE employerprofiles
                SET companyname = ?, 
                    industry = ?, 
                    companysize = ?, 
                    description = ?, 
                    website = ?, 
                    address = ?
                WHERE userid = ?
            ");

            $stmt->bind_param(
                "ssssssi",
                $companyname,
                $industry,
                $companysize,
                $description,
                $website,
                $address,
                $userid
            );
        }
    } else {
        $stmt = $conn->prepare("
            INSERT INTO employerprofiles
            (userid, companyname, industry, companysize, description, website, address, logopath)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isssssss",
            $userid,
            $companyname,
            $industry,
            $companysize,
            $description,
            $website,
            $address,
            $logopath
        );
    }

    return $stmt->execute();
}

function createJob($conn, $employerid) {
    $categoryid       = (int)($_POST['categoryid'] ?? 0);
    $title            = trim($_POST['title'] ?? '');
    $description      = trim($_POST['description'] ?? '');
    $requirements     = trim($_POST['requirements'] ?? '');
    $benefits         = trim($_POST['benefits'] ?? '');
    $salarymin        = (float)($_POST['salarymin'] ?? 0);
    $salarymax        = (float)($_POST['salarymax'] ?? 0);
    $location         = trim($_POST['location'] ?? '');
    $jobtype          = $_POST['jobtype'] ?? '';
    $experiencelevel  = $_POST['experiencelevel'] ?? '';
    $deadline         = $_POST['deadline'] ?? null;
    $status           = $_POST['status'] ?? 'active';

    if ($categoryid <= 0 || $title === '' || $description === '' || $location === '') {
        return false;
    }

    $stmt = $conn->prepare("
        INSERT INTO jobs
        (employerid, recruiterid, categoryid, title, description, requirements, benefits, salarymin, salarymax, location, jobtype, experiencelevel, deadline, status, isfeatured, createdat)
        VALUES (?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())
    ");

    $stmt->bind_param(
        "iissssddsssss",
        $employerid,
        $categoryid,
        $title,
        $description,
        $requirements,
        $benefits,
        $salarymin,
        $salarymax,
        $location,
        $jobtype,
        $experiencelevel,
        $deadline,
        $status
    );

    return $stmt->execute();
}

function updateJob($conn, $jobid, $employerid) {
    $categoryid       = (int)($_POST['categoryid'] ?? 0);
    $title            = trim($_POST['title'] ?? '');
    $description      = trim($_POST['description'] ?? '');
    $requirements     = trim($_POST['requirements'] ?? '');
    $benefits         = trim($_POST['benefits'] ?? '');
    $salarymin        = (float)($_POST['salarymin'] ?? 0);
    $salarymax        = (float)($_POST['salarymax'] ?? 0);
    $location         = trim($_POST['location'] ?? '');
    $jobtype          = $_POST['jobtype'] ?? '';
    $experiencelevel  = $_POST['experiencelevel'] ?? '';
    $deadline         = $_POST['deadline'] ?? null;
    $status           = $_POST['status'] ?? 'active';

    if ($categoryid <= 0 || $title === '' || $description === '' || $location === '') {
        return false;
    }

    $stmt = $conn->prepare("
        UPDATE jobs
        SET categoryid = ?, 
            title = ?, 
            description = ?, 
            requirements = ?, 
            benefits = ?, 
            salarymin = ?, 
            salarymax = ?, 
            location = ?, 
            jobtype = ?, 
            experiencelevel = ?, 
            deadline = ?, 
            status = ?
        WHERE id = ? AND employerid = ?
    ");

    $stmt->bind_param(
        "issssddsssssii",
        $categoryid,
        $title,
        $description,
        $requirements,
        $benefits,
        $salarymin,
        $salarymax,
        $location,
        $jobtype,
        $experiencelevel,
        $deadline,
        $status,
        $jobid,
        $employerid
    );

    return $stmt->execute();
}

function deleteJob($conn, $jobid, $employerid) {
    $stmt = $conn->prepare("
        DELETE FROM jobs
        WHERE id = ? AND employerid = ?
    ");

    $stmt->bind_param("ii", $jobid, $employerid);

    return $stmt->execute();
}

?>