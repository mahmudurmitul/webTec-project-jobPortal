<?php
require_once "../config.php";
require_once "../model.php";
require_once "../controller.php";
employerOnlyFrom();

$userid  = $_SESSION['userid'];
$message = $msgtype = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subjectid   = (int)$_POST['subjectid'];
    $description = trim($_POST['description']);
    if ($description) {
        if (submitComplaint($conn, $userid, $subjectid, $description)) {
            $message = "Complaint submitted successfully."; $msgtype = "success";
        } else {
            $message = "Submission failed. Please try again."; $msgtype = "error";
        }
    } else {
        $message = "Description is required."; $msgtype = "error";
    }
}

$complaints = getMyComplaints($conn, $userid);
$activePage = 'complaints'; $basePath = '..';
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Complaints</title></head>
<body>
<div class="app-layout">
    <?php include "sidebar.php"; ?>
    <main class="main-content">
        <div class="page-header">
            <h2><i class="fas fa-flag" style="color:var(--accent2);margin-right:8px"></i>Complaints</h2>
            <p>Report issues with recruiters or job seekers to the admin team.</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $msgtype ?>"><i class="fas fa-<?= $msgtype==='success'?'check':'circle-exclamation' ?>"></i><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Submit Form -->
        <div class="card">
            <h3><i class="fas fa-pen-to-square"></i> Submit a Complaint</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Subject User ID <span class="text-muted">(the recruiter or seeker you are reporting)</span></label>
                    <input type="number" name="subjectid" required placeholder="User ID of the person">
                </div>
                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" required rows="5" placeholder="Describe the issue in detail..."></textarea>
                </div>
                <button type="submit" class="btn btn-danger"><i class="fas fa-flag"></i> Submit Complaint</button>
            </form>
        </div>

        <!-- Past Complaints -->
        <div class="card">
            <h3><i class="fas fa-list-check"></i> My Complaints</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Against</th><th>Description</th><th>Status</th><th>Admin Note</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                    <?php $has = false; while ($c = $complaints->fetch_assoc()): $has = true; ?>
                    <tr>
                        <td><?= htmlspecialchars($c['subjectname'] ?? 'Unknown') ?></td>
                        <td style="max-width:260px"><?= htmlspecialchars(mb_substr($c['description'], 0, 80)) ?><?= strlen($c['description'])>80?'…':'' ?></td>
                        <td>
                            <span class="badge <?= $c['status']==='resolved'?'badge-interview':'badge-submitted' ?>">
                                <?= ucfirst($c['status']) ?>
                            </span>
                        </td>
                        <td class="text-muted" style="font-size:12px"><?= htmlspecialchars($c['adminnote'] ?? '—') ?></td>
                        <td class="text-muted" style="font-size:12px"><?= date('d M Y', strtotime($c['createdat'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if (!$has): ?>
                        <tr><td colspan="5" class="empty-state"><i class="fas fa-check-circle"></i><p>No complaints submitted yet.</p></td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
