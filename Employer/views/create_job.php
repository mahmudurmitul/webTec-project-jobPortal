<?php
require_once "../config.php";
require_once "../model.php";
require_once "../controller.php";
employerOnlyFrom();

$employerid = $_SESSION['userid'];
$message    = $msgtype = "";
$categories = getCategories($conn);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (createJob($conn, $employerid)) {
        $message = "Job posted successfully!"; $msgtype = "success";
    } else {
        $message = "Failed to create job. Fill in all required fields."; $msgtype = "error";
    }
}

$activePage = 'create_job'; $basePath = '..';
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Post a Job</title></head>
<body>
<div class="app-layout">
    <?php include "sidebar.php"; ?>
    <main class="main-content">
        <div class="page-header">
            <h2><i class="fas fa-plus-circle" style="color:var(--accent2);margin-right:8px"></i>Post a New Job</h2>
            <p>Fill in the details below to create a job listing.</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $msgtype ?>"><i class="fas fa-<?= $msgtype==='success'?'check':'circle-exclamation' ?>"></i><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="card">
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Job Title *</label>
                        <input type="text" name="title" required placeholder="e.g. Senior PHP Developer">
                    </div>
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="categoryid" required>
                            <option value="">Select Category</option>
                            <?php while ($cat = $categories->fetch_assoc()): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Job Description *</label>
                    <textarea name="description" required placeholder="Describe the role and responsibilities..."></textarea>
                </div>

                <div class="form-group">
                    <label>Requirements</label>
                    <textarea name="requirements" placeholder="List skills, qualifications..."></textarea>
                </div>

                <div class="form-group">
                    <label>Benefits</label>
                    <textarea name="benefits" placeholder="Health insurance, remote work..."></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Min Salary (BDT)</label>
                        <input type="number" name="salarymin" placeholder="30000">
                    </div>
                    <div class="form-group">
                        <label>Max Salary (BDT)</label>
                        <input type="number" name="salarymax" placeholder="60000">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Location *</label>
                        <input type="text" name="location" required placeholder="e.g. Dhaka">
                    </div>
                    <div class="form-group">
                        <label>Job Type</label>
                        <select name="jobtype">
                            <option value="full-time">Full-time</option>
                            <option value="part-time">Part-time</option>
                            <option value="remote">Remote</option>
                            <option value="contract">Contract</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Experience Level</label>
                        <select name="experiencelevel">
                            <option value="entry">Entry</option>
                            <option value="mid">Mid</option>
                            <option value="senior">Senior</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Application Deadline *</label>
                        <input type="date" name="deadline" required>
                    </div>
                    <div class="form-group">
                        <label>Publish Status</label>
                        <select name="status">
                            <option value="draft">Save as Draft</option>
                            <option value="active">Publish Now</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Post Job</button>
                <a href="../index.php" class="btn btn-ghost" style="margin-left:8px">Cancel</a>
            </form>
        </div>
    </main>
</div>
</body>
</html>
