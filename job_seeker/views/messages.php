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
    <style>
        .msg-list { display: flex; flex-direction: column; gap: 14px; }

        .msg-item {
            background: var(--bg3);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px 22px;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            position: relative;
        }

        .msg-item.unread {
            border-left: 4px solid var(--accent2);
            background: rgba(168,85,247,0.05);
        }

        .msg-item:hover { border-color: rgba(168,85,247,0.4); }

        .msg-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .msg-sender {
            font-weight: 600;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .msg-sender .role-tag {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
            background: rgba(124,58,237,0.2);
            color: var(--accent2);
            text-transform: uppercase;
        }

        .msg-time {
            font-size: 12px;
            color: var(--muted);
        }

        .msg-body {
            color: #c0c0c0;
            font-size: 14px;
            line-height: 1.7;
            display: none;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--border);
            white-space: pre-wrap;
        }

        .msg-body.open { display: block; }

        .msg-preview {
            color: #888;
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 600px;
        }

        .unread-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: var(--accent2);
            display: inline-block;
            flex-shrink: 0;
        }

        .msg-toggle-icon {
            color: var(--muted);
            font-size: 12px;
            transition: transform 0.2s;
        }

        .msg-toggle-icon.open { transform: rotate(180deg); }
    </style>
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
            Messages from Admin and system notifications. (<?= count($messages) ?> total)
        </p>

        <?php if (empty($messages)): ?>
        <div class="empty-state">
            <i class="far fa-envelope-open"></i>
            <h3>No messages yet</h3>
            <p>Messages from Admin will appear here.</p>
        </div>

        <?php else: ?>
        <div class="msg-list">
            <?php foreach ($messages as $msg):
                $is_unread = !$msg['isread'];
                $sent_at   = date('d M Y, h:i A', strtotime($msg['sentat']));
                $preview   = mb_strimwidth(strip_tags($msg['body']), 0, 100, '…');
            ?>
            <div class="msg-item <?= $is_unread ? 'unread' : '' ?>"
                 id="msg-<?= $msg['id'] ?>"
                 onclick="toggleMsg(this, <?= $msg['id'] ?>, <?= $is_unread ? 'true' : 'false' ?>)">

                <div class="msg-header">
                    <div class="msg-sender">
                        <?php if ($is_unread): ?>
                        <span class="unread-dot" id="dot-<?= $msg['id'] ?>"></span>
                        <?php endif; ?>
                        <i class="fas fa-user-shield" style="color:var(--accent2);"></i>
                        <?= htmlspecialchars($msg['sender_name']) ?>
                        <span class="role-tag"><?= htmlspecialchars($msg['sender_role']) ?></span>
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
    const body  = document.getElementById('body-' + id);
    const prev  = document.getElementById('prev-' + id);
    const icon  = document.getElementById('icon-' + id);
    const dot   = document.getElementById('dot-' + id);

    const isOpen = body.classList.contains('open');

    if (isOpen) {
        body.classList.remove('open');
        icon.classList.remove('open');
        prev.style.display = '';
    } else {
        body.classList.add('open');
        icon.classList.add('open');
        prev.style.display = 'none';

        // Mark as read
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