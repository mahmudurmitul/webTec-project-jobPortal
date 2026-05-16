<?php
require_once "config.php";
require_once "model.php";

employerOnly();

$employerid = $_SESSION['userid'];
$stats = getEmployerStats($conn, $employerid);
$jobs = getEmployerJobs($conn, $employerid);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employer Dashboard</title>
    <link rel="stylesheet" href="views/style.css">
</head>
<body>
<div class="container">

    <div class="header">
        <h1>Employer Dashboard</h1>

        <div class="nav">
            <a href="index.php">Dashboard</a>
            <a href="views/company_profile.php">Company Profile</a>
            <a href="views/create_job.php">Create Job</a>
            <a href="views/shortlisted.php">Shortlisted</a>
            <a href="views/analytics.php">Analytics</a>
            <a href="views/complaints.php">Complaint</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="grid">
        <div class="stat-box">
            <h2><?php echo $stats['totaljobs']; ?></h2>
            <p>Total Jobs</p>
        </div>

        <div class="stat-box">
            <h2><?php echo $stats['activejobs']; ?></h2>
            <p>Active Jobs</p>
        </div>

        <div class="stat-box">
            <h2><?php echo $stats['totalapplications']; ?></h2>
            <p>Total Applications</p>
        </div>
    </div>

    <div class="card">
        <h2>My Job Postings</h2>

        <table>
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Applications</th>
                <th>Deadline</th>
                <th>Action</th>
            </tr>

            <?php while ($job = $jobs->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($job['title']); ?></td>

                <td>
                    <span id="status-<?php echo $job['id']; ?>" class="status-<?php echo $job['status']; ?>">
                        <?php echo ucfirst($job['status']); ?>
                    </span>
                </td>

                <td><?php echo $job['applicationcount']; ?></td>

                <td><?php echo htmlspecialchars($job['deadline']); ?></td>

                <td>
                    <a class="btn" href="views/applications.php?jobid=<?php echo $job['id']; ?>">Applicants</a>
                    <a class="btn warning" href="views/edit_job.php?id=<?php echo $job['id']; ?>">Edit</a>
                    <button onclick="toggleJobStatus(<?php echo $job['id']; ?>)">Toggle</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

</div>

<script src="views/script.js"></script>
</body>
</html>