<?php

function saveEmployerProfile($conn, $userid) {
    $companyname = trim($_POST['companyname']);
    $industry = trim($_POST['industry']);
    $companysize = trim($_POST['companysize']);
    $description = trim($_POST['description']);
    $website = trim($_POST['website']);
    $address = trim($_POST['address']);

    if ($companyname === "") {
        return false;
    }

    $logopath = "";

    if (!empty($_FILES['logo']['name'])) {
        $targetdir = "../uploads/logos/";

        if (!is_dir($targetdir)) {
            mkdir($targetdir, 0777, true);
        }

        $filename = time() . "_" . basename($_FILES['logo']['name']);
        $targetfile = $targetdir . $filename;

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetfile)) {
            $logopath = "Employer/uploads/logos/" . $filename;
        }
    }

    $check = $conn->prepare("SELECT id FROM employerprofiles WHERE userid = ?");
    $check->bind_param("i", $userid);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        if ($logopath !== "") {
            $stmt = $conn->prepare("
                UPDATE employerprofiles
                SET companyname = ?, industry = ?, companysize = ?, description = ?, website = ?, address = ?, logopath = ?
                WHERE userid = ?
            ");
            $stmt->bind_param("sssssssi", $companyname, $industry, $companysize, $description, $website, $address, $logopath, $userid);
        } else {
            $stmt = $conn->prepare("
                UPDATE employerprofiles
                SET companyname = ?, industry = ?, companysize = ?, description = ?, website = ?, address = ?
                WHERE userid = ?
            ");
            $stmt->bind_param("ssssssi", $companyname, $industry, $companysize, $description, $website, $address, $userid);
        }
    } else {
        $stmt = $conn->prepare("
            INSERT INTO employerprofiles
            (userid, companyname, industry, companysize, description, website, address, logopath)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isssssss", $userid, $companyname, $industry, $companysize, $description, $website, $address, $logopath);
    }

    return $stmt->execute();
}

function createJob($conn, $employerid) {
    $categoryid = $_POST['categoryid'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $requirements = trim($_POST['requirements']);
    $benefits = trim($_POST['benefits']);
    $salarymin = $_POST['salarymin'];
    $salarymax = $_POST['salarymax'];
    $location = trim($_POST['location']);
    $jobtype = $_POST['jobtype'];
    $experiencelevel = $_POST['experiencelevel'];
    $deadline = $_POST['deadline'];
    $status = $_POST['status'];

    if ($title === "" || $description === "" || $location === "") {
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
    $categoryid = $_POST['categoryid'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $requirements = trim($_POST['requirements']);
    $benefits = trim($_POST['benefits']);
    $salarymin = $_POST['salarymin'];
    $salarymax = $_POST['salarymax'];
    $location = trim($_POST['location']);
    $jobtype = $_POST['jobtype'];
    $experiencelevel = $_POST['experiencelevel'];
    $deadline = $_POST['deadline'];
    $status = $_POST['status'];

    if ($title === "" || $description === "" || $location === "") {
        return false;
    }

    $stmt = $conn->prepare("
        UPDATE jobs
        SET categoryid = ?, title = ?, description = ?, requirements = ?, benefits = ?, salarymin = ?, salarymax = ?, location = ?, jobtype = ?, experiencelevel = ?, deadline = ?, status = ?
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

?>