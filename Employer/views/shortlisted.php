<?php
require_once "../config.php";

employerOnlyFrom();

$employerid = $_SESSION['userid'];

$stmt = $conn->prepare("
    SELECT a.*, u.name, u.email, j.title
    FROM applications a
    JOIN users u ON a.seekerid = u.id
    JOIN jobs j ON a.jobid = j.id
    WHERE j.employerid = ? AND a.status = 'shortlisted'
    ORDER BY a.appliedat DESC
");

$stmt->bind_param("i", $employerid);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shortlisted Candidates</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <div class="header">
        <h1>Shortlisted Candidates</h1>
        <div class="nav">
            <a href="../index.php">Dashboard</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="card">
        <h2>All Shortlisted Candidates</h2>

        <table>
            <tr>
                <th>Candidate</th>
                <th>Email</th>
                <th>Job Title</th>
                <th>Applied Date</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['appliedat']); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

</div>

</body>
</html>