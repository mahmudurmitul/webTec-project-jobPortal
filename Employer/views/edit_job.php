<?php
require_once "../config.php";
require_once "../model.php";
require_once "../controller.php";
employerOnlyFrom();

$employerid = $_SESSION['userid'];
$jobid      = (int)($_GET['id'] ?? 0);
$message    = $msgtype = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (updateJob($conn, $jobid, $employerid)) {
        $message = "Job updated successfully!"; $msgtype = "success";
    } else {
        $message = "Failed to update job."; $msgtype = "error";
    }
}

$job        = getSingleJob($conn, $jobid, $employerid);
$categories = getCategories($conn);

if (!$job) die("<p style='color:#fff;padding:40px'>Job not found or access denied.</p>");

$activePage = 'dashboard'; $basePath = '..';
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Edit Job</title></head>
<body>
<div class="app-layout">
    <?php include "sidebar.php"; ?>
    <main class="main-content">
        <div class="page-header">
            <h2><i class="fas fa-pen" style="color:var(--accent2);margin-right:8px"></i>Edit Job</h2>
            <p>Update the details of your job listing.</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $msgtype ?>"><i class="fas fa-<?= $msgtype==='success'?'check':'circle-exclamation' ?>"></i><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="card">
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Job Title *</label>
                        <input type="text" name="title" required value="<?= htmlspecialchars($job['title']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="categoryid" required>
                            <?php while ($cat = $categories->fetch_assoc()): ?>
                                <option value="<?= $cat['id'] ?>" <?= $cat['id']==$job['categoryid']?'selected':'' ?>><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" required><?= htmlspecialchars($job['description']) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Requirements</label>
                    <textarea name="requirements"><?= htmlspecialchars($job['requirements']) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Benefits</label>
                    <textarea name="benefits"><?= htmlspecialchars($job['benefits']) ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Min Salary</label>
                        <input type="number" name="salarymin" value="<?= htmlspecialchars($job['salarymin']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Max Salary</label>
                        <input type="number" name="salarymax" value="<?= htmlspecialchars($job['salarymax']) ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Location *</label>
                        <input type="text" name="location" required value="<?= htmlspecialchars($job['location']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Job Type</label>
                        <select name="jobtype">
                            <?php foreach (['full-time','part-time','remote','contract'] as $jt): ?>
                                <option value="<?= $jt ?>" <?= $job['jobtype']==$jt?'selected':'' ?>><?= ucfirst($jt) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Experience Level</label>
                        <select name="experiencelevel">
                            <?php foreach (['entry','mid','senior'] as $el): ?>
                                <option value="<?= $el ?>" <?= $job['experiencelevel']==$el?'selected':'' ?>><?= ucfirst($el) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Deadline</label>
                        <input type="date" name="deadline" required value="<?= htmlspecialchars($job['deadline']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status">
                            <?php foreach (['draft','active','closed'] as $st): ?>
                                <option value="<?= $st ?>" <?= $job['status']==$st?'selected':'' ?>><?= ucfirst($st) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                <a href="../index.php" class="btn btn-ghost" style="margin-left:8px">Back</a>
            </form>
        </div>
    </main>
</div>
</body>
</html>
