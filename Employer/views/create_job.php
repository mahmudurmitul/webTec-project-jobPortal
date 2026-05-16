<?php
require_once "../config.php";
require_once "../model.php";
require_once "../controller.php";

employerOnlyFrom();

$employerid = $_SESSION['userid'];
$message = "";
$categories = getCategories($conn);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (createJob($conn, $employerid)) {
        $message = "Job created successfully.";
    } else {
        $message = "Failed to create job. Please check all required fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Job</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <div class="header">
        <h1>Create Job Posting</h1>
        <div class="nav">
            <a href="../index.php">Dashboard</a>
            <a href="company_profile.php">Company Profile</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="card">
        <h2>New Job</h2>

        <?php if ($message != ""): ?>
            <div class="success-msg"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>Job Title</label>
                <input type="text" name="title" required>
            </div>

            <div class="form-group">
                <label>Category</label>
                <select name="categoryid" required>
                    <option value="">Select Category</option>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" required></textarea>
            </div>

            <div class="form-group">
                <label>Requirements</label>
                <textarea name="requirements"></textarea>
            </div>

            <div class="form-group">
                <label>Benefits</label>
                <textarea name="benefits"></textarea>
            </div>

            <div class="form-group">
                <label>Minimum Salary</label>
                <input type="number" name="salarymin" required>
            </div>

            <div class="form-group">
                <label>Maximum Salary</label>
                <input type="number" name="salarymax" required>
            </div>

            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" required>
            </div>

            <div class="form-group">
                <label>Job Type</label>
                <select name="jobtype" required>
                    <option value="full-time">Full-time</option>
                    <option value="part-time">Part-time</option>
                    <option value="remote">Remote</option>
                    <option value="contract">Contract</option>
                </select>
            </div>

            <div class="form-group">
                <label>Experience Level</label>
                <select name="experiencelevel" required>
                    <option value="entry">Entry</option>
                    <option value="mid">Mid</option>
                    <option value="senior">Senior</option>
                </select>
            </div>

            <div class="form-group">
                <label>Application Deadline</label>
                <input type="date" name="deadline" required>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="draft">Save as Draft</option>
                    <option value="active">Publish Active</option>
                </select>
            </div>

            <button type="submit">Create Job</button>
        </form>
    </div>

</div>

</body>
</html>