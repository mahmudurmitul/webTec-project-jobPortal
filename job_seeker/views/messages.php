<?php
require_once __DIR__ . "/../controllers/JobController.php";

if (!isset($_SESSION['seeker_id'])) {
    header("Location: ../index.php");
    exit();
}

$messages = getSeekerMessages($_SESSION['seeker_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Messages – JobPortal</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="container">

    <div class="header">
        <div class="logo">
            <h1><i class="fas fa-briefcase"></i> JobPortal Pro</h1>
        </div>
    </div>

    <div class="nav-links">
        <a href="../index.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        <a href="profile.php"><i class="fas fa-user-edit"></i> Edit Profile</a>
        <a href="applications.php"><i class="fas fa-file-alt"></i> My Applications</a>
        <a href="saved.php"><i class="fas fa-bookmark"></i> Saved Jobs</a>
    </div>

    <div class="card">
        <h3 style="margin-bottom:6px;">
            <i class="fas fa-envelope" style="color:#00d4ff;"></i> My Messages
        </h3>
        
<p style="color:#666;font-size:13px;margin-bottom:24px;">
    Messages from Employers and Recruiters. (<?= count($messages) ?> total)
</p>

        <?php if (empty($messages)): ?>
        <div class="empty-state">
            <i class="far fa-envelope-open"></i>
            <h3>No messages yet</h3>
            <p>Messages from Employers and Recruiters will appear here.</p>
        </div>

        <?php else: ?>
        <div class="msg-list">
            <?php foreach ($messages as $msg):
                $is_unread  = !$msg['isread'];
                $sent_at    = date('d M Y, h:i A', strtotime($msg['sentat']));
                $preview    = mb_strimwidth(strip_tags($msg['body']), 0, 100, '…');
                $role       = $msg['sender_role']; // 'employer' or 'recruiter'
                $role_icon  = ($role === 'recruiter') ? 'fa-user-tie' : 'fa-building';
            ?>
            <div class="msg-item <?= $is_unread ? 'unread' : '' ?>"
                 id="msg-<?= $msg['id'] ?>"
                 onclick="toggleMsg(this, <?= $msg['id'] ?>, <?= $is_unread ? 'true' : 'false' ?>)">

                <div class="msg-header">
                    <div class="msg-sender">
                        <?php if ($is_unread): ?>
                        <span class="unread-dot" id="dot-<?= $msg['id'] ?>"></span>
                        <?php endif; ?>
                        <i class="fas <?= $role_icon ?>" style="color:var(--accent2);"></i>
                        <?= htmlspecialchars($msg['sender_name']) ?>
                        <span class="role-tag <?= htmlspecialchars($role) ?>"><?= ucfirst(htmlspecialchars($role)) ?></span>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <span class="msg-time"><i class="far fa-clock"></i> <?= $sent_at ?></span>
                        <i class="fas fa-chevron-down msg-toggle-icon" id="icon-<?= $msg['id'] ?>"></i>
                    </div>
                </div>

                <div class="msg-preview" id="prev-<?= $msg['id'] ?>"><?= htmlspecialchars($preview) ?></div>
                <div class="msg-body" id="body-<?= $msg['id'] ?>"><?= htmlspecialchars($msg['body']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

</div>

<script>
function toggleMsg(el, id, isUnread) {
    const body = document.getElementById('body-' + id);
    const prev = document.getElementById('prev-' + id);
    const icon = document.getElementById('icon-' + id);
    const dot  = document.getElementById('dot-' + id);
    const isOpen = body.classList.contains('open');

    if (isOpen) {
        body.classList.remove('open');
        icon.classList.remove('open');
        prev.style.display = '';
    } else {
        body.classList.add('open');
        icon.classList.add('open');
        prev.style.display = 'none';
        if (isUnread) {
            el.classList.remove('unread');
            el.style.borderLeft = '';
            if (dot) dot.remove();
            fetch(`../controllers/JobController.php?action=markRead&msg_id=${id}`);
        }
    }
}
</script>
</body>
</html>