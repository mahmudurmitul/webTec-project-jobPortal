<?php
require_once "../config.php";
require_once "../model.php";

employerOnlyFrom();

$employerid = $_SESSION['userid'];
$jobid = $_GET['jobid'] ?? 0;

$applications = getApplicationsByJob($conn, $jobid, $employerid);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Applications</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <div class="header">
        <h1>Applications</h1>
        <div class="nav">
            <a href="../index.php">Dashboard</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="card">
        <h2>Applicants List</h2>

        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Headline</th>
                <th>Experience</th>
                <th>Skills</th>
                <th>Status</th>
                <th>Resume</th>
            </tr>

            <?php while ($app = $applications->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($app['name']); ?></td>
                <td><?php echo htmlspecialchars($app['email']); ?></td>
                <td><?php echo htmlspecialchars($app['headline'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($app['yearsexperience'] ?? '0'); ?> years</td>
                <td><?php echo htmlspecialchars($app['skills'] ?? ''); ?></td>

                <td>
                    <select onchange="updateApplicationStatus(<?php echo $app['id']; ?>, this.value)">
                        <option value="submitted" <?php if ($app['status'] == 'submitted') echo "selected"; ?>>Submitted</option>
                        <option value="reviewed" <?php if ($app['status'] == 'reviewed') echo "selected"; ?>>Reviewed</option>
                        <option value="shortlisted" <?php if ($app['status'] == 'shortlisted') echo "selected"; ?>>Shortlisted</option>
                        <option value="interview" <?php if ($app['status'] == 'interview') echo "selected"; ?>>Interview</option>
                        <option value="rejected" <?php if ($app['status'] == 'rejected') echo "selected"; ?>>Rejected</option>
                    </select>
                </td>

                <td>
                    <?php if (!empty($app['resumepath'])): ?>
                        <a class="btn" href="../../<?php echo htmlspecialchars($app['resumepath']); ?>" target="_blank">Download</a>
                    <?php else: ?>
                        No Resume
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

</div>

<script>
function updateApplicationStatus(applicationId, status) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../api/update_application_status.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        const response = JSON.parse(xhr.responseText);

        if (response.success) {
            alert("Application status updated.");
        } else {
            alert(response.message);
        }
    };

    xhr.send("applicationid=" + applicationId + "&status=" + status);
}
</script>

</body>
</html>