<?php
require_once "../config.php";

header("Content-Type: application/json");

employerOnlyFromApi();

/** @var mysqli $conn */

$employerid = $_SESSION['userid'];
$jobid = $_POST['jobid'] ?? 0;

if ($jobid == 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid job ID."
    ]);
    exit();
}

$stmt = $conn->prepare("
    SELECT status 
    FROM jobs 
    WHERE id = ? AND employerid = ?
");

$stmt->bind_param("ii", $jobid, $employerid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "message" => "Job not found or access denied."
    ]);
    exit();
}

$job = $result->fetch_assoc();

if ($job['status'] === 'active') {
    $newstatus = 'closed';
} else {
    $newstatus = 'active';
}

$stmt = $conn->prepare("
    UPDATE jobs 
    SET status = ? 
    WHERE id = ? AND employerid = ?
");

$stmt->bind_param("sii", $newstatus, $jobid, $employerid);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "newstatus" => $newstatus
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to update job status."
    ]);
}
?>