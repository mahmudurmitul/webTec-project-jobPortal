<?php
require_once "../config.php";
require_once "../model.php";
require_once "../controller.php";
employerOnlyFrom();

$employerid = $_SESSION['userid'];
$msgsent    = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $recipientid   = (int)$_POST['recipientid'];
    $applicationid = (int)($_POST['applicationid'] ?? 0) ?: null;
    $body          = trim($_POST['body']);
    if ($body && $recipientid) {
        sendMessage($conn, $employerid, $recipientid, $applicationid, $body);
        $msgsent = "Message sent!";
    }
}

$messages   = getMessages($conn, $employerid);
$activePage = 'messages'; $basePath = '..';
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Messages</title></head>
<body>
<div class="app-layout">
    <?php include "sidebar.php"; ?>
    <main class="main-content">
        <div class="page-header">
            <h2><i class="fas fa-envelope" style="color:var(--accent2);margin-right:8px"></i>Messages</h2>
            <p>Send and review in-platform messages to applicants.</p>
        </div>

        <?php if ($msgsent): ?>
            <div class="alert alert-success"><i class="fas fa-check"></i><?= $msgsent ?></div>
        <?php endif; ?>

        <!-- Send Message -->
        <div class="card">
            <h3><i class="fas fa-paper-plane"></i> Send a Message</h3>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Recipient User ID</label>
                        <input type="number" name="recipientid" required placeholder="Seeker user ID">
                    </div>
                    <div class="form-group">
                        <label>Application ID (optional)</label>
                        <input type="number" name="applicationid" placeholder="Link to an application">
                    </div>
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="body" required placeholder="Interview invitation, next steps..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Send</button>
            </form>
        </div>

        <!-- Message History -->
        <div class="card">
            <h3><i class="fas fa-inbox"></i> Message History</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>From</th><th>To</th><th>Job</th><th>Message</th><th>Sent</th></tr>
                    </thead>
                    <tbody>
                    <?php $has = false; while ($m = $messages->fetch_assoc()): $has = true;
                        $isSender = $m['senderid'] == $employerid; ?>
                    <tr>
                        <td><?= htmlspecialchars($m['sendername']) ?> <?= $isSender ? '<span class="badge badge-reviewed" style="font-size:9px">You</span>' : '' ?></td>
                        <td><?= htmlspecialchars($m['recipientname']) ?></td>
                        <td class="text-muted" style="font-size:12px"><?= htmlspecialchars($m['jobtitle'] ?? '—') ?></td>
                        <td style="max-width:300px"><?= htmlspecialchars(mb_substr($m['body'], 0, 80)) ?><?= strlen($m['body'])>80?'…':'' ?></td>
                        <td class="text-muted" style="font-size:12px"><?= date('d M Y, H:i', strtotime($m['sentat'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if (!$has): ?>
                        <tr><td colspan="5" class="empty-state"><i class="fas fa-envelope-open"></i><p>No messages yet.</p></td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
