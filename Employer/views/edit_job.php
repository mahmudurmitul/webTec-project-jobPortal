<?php
require_once "../config.php";
require_once "../model.php";
require_once "../controller.php";

employerOnlyFrom();

$employerid = $_SESSION['userid'];
$jobid = $_GET['id'] ?? 0;
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (updateJob($conn, $jobid, $employerid)) {
        $message = "Job updated successfully.";
    } else {
        $message = "Failed to update job.";
    }
}

$job = getSingleJob($conn, $jobid, $employerid);
$categories = getCategories($conn);

if (!$job) {
    die("Job not found or access denied.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Job</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <div class="header">
        <h1>Edit Job</h1>
        <div class="nav">
            <a href="../index.php">Dashboard</a>
            <a href="create_job.php">Create Job</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="card">
        <h2>Edit Job Posting</h2>

        <?php if ($message != ""): ?>
            <div class="success-msg"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>Job Title</label>
                <input type="text" name="title" required
                       value="<?php echo htmlspecialchars($job['title']); ?>">
            </div>

            <div class="form-group">
                <label>Category</label>
                <select name="categoryid" required>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>"
                            <?php if ($cat['id'] == $job['categoryid']) echo "selected"; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" required><?php echo htmlspecialchars($job['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Requirements</label>
                <textarea name="requirements"><?php echo htmlspecialchars($job['requirements']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Benefits</label>
                <textarea name="benefits"><?php echo htmlspecialchars($job['benefits']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Minimum Salary</label>
                <input type="number" name="salarymin" required
                       value="<?php echo htmlspecialchars($job['salarymin']); ?>">
            </div>

            <div class="form-group">
                <label>Maximum Salary</label>
                <input type="number" name="salarymax" required
                       value="<?php echo htmlspecialchars($job['salarymax']); ?>">
            </div>

            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" required
                       value="<?php echo htmlspecialchars($job['location']); ?>">
            </div>

            <div class="form-group">
                <label>Job Type</label>
                <select name="jobtype" required>
                    <option value="full-time" <?php if ($job['jobtype'] == 'full-time') echo "selected"; ?>>Full-time</option>
                    <option value="part-time" <?php if ($job['jobtype'] == 'part-time') echo "selected"; ?>>Part-time</option>
                    <option value="remote" <?php if ($job['jobtype'] == 'remote') echo "selected"; ?>>Remote</option>
                    <option value="contract" <?php if ($job['jobtype'] == 'contract') echo "selected"; ?>>Contract</option>
                </select>
            </div>

            <div class="form-group">
                <label>Experience Level</label>
                <select name="experiencelevel" required>
                    <option value="entry" <?php if ($job['experiencelevel'] == 'entry') echo "selected"; ?>>Entry</option>
                    <option value="mid" <?php if ($job['experiencelevel'] == 'mid') echo "selected"; ?>>Mid</option>
                    <option value="senior" <?php if ($job['experiencelevel'] == 'senior') echo "selected"; ?>>Senior</option>
                </select>
            </div>

            <div class="form-group">
                <label>Deadline</label>
                <input type="date" name="deadline" required
                       value="<?php echo htmlspecialchars($job['deadline']); ?>">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="draft" <?php if ($job['status'] == 'draft') echo "selected"; ?>>Draft</option>
                    <option value="active" <?php if ($job['status'] == 'active') echo "selected"; ?>>Active</option>
                    <option value="closed" <?php if ($job['status'] == 'closed') echo "selected"; ?>>Closed</option>
                </select>
            </div>

            <button type="submit">Update Job</button>
        </form>
    </div>

</div>

</body>
</html>