<?php
require_once "../config.php";

employerOnlyFrom();

$employerid = $_SESSION['userid'];

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
$data = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employer Analytics</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <div class="header">
        <h1>Hiring Analytics</h1>
        <div class="nav">
            <a href="../index.php">Dashboard</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="grid">
        <div class="stat-box">
            <h2><?php echo $data['totaljobs']; ?></h2>
            <p>Total Jobs</p>
        </div>

        <div class="stat-box">
            <h2><?php echo $data['totalapplications']; ?></h2>
            <p>Total Applications</p>
        </div>

        <div class="stat-box">
            <h2><?php echo $data['submitted'] ?? 0; ?></h2>
            <p>Submitted</p>
        </div>

        <div class="stat-box">
            <h2><?php echo $data['reviewed'] ?? 0; ?></h2>
            <p>Reviewed</p>
        </div>

        <div class="stat-box">
            <h2><?php echo $data['shortlisted'] ?? 0; ?></h2>
            <p>Shortlisted</p>
        </div>

        <div class="stat-box">
            <h2><?php echo $data['interviews'] ?? 0; ?></h2>
            <p>Interview</p>
        </div>

        <div class="stat-box">
            <h2><?php echo $data['rejected'] ?? 0; ?></h2>
            <p>Rejected</p>
        </div>
    </div>

</div>

</body>
</html>