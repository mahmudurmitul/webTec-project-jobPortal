<?php
require_once "../config.php";

header("Content-Type: application/json");

employerOnlyFromApi();

/** @var mysqli $conn */

$employerid = $_SESSION['userid'];
$applicationid = $_POST['applicationid'] ?? 0;
$status = $_POST['status'] ?? "";

$allowedStatus = [
    'submitted',
    'reviewed',
    'shortlisted',
    'interview',
    'rejected'
];

if ($applicationid == 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid application ID."
    ]);
    exit();
}

if (!in_array($status, $allowedStatus)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid application status."
    ]);
    exit();
}

$stmt = $conn->prepare("
    UPDATE applications a
    JOIN jobs j ON a.jobid = j.id
    SET a.status = ?
    WHERE a.id = ? AND j.employerid = ?
");

$stmt->bind_param("sii", $status, $applicationid, $employerid);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Application status updated successfully."
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to update application status."
    ]);
}
?>