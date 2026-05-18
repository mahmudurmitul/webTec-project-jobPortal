<?php
require_once __DIR__ . "/../controllers/JobController.php";

if (!isset($_SESSION['seeker_id'])) {
    header("Location: /webtech/webTec-project-jobPortal/job_seeker/index.php");
    exit();
}

$messages = getSeekerMessages($_SESSION['seeker_id']);
$outreach  = getRecruiterOutreach($_SESSION['seeker_id']);
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

<div class="app-layout">

    <aside class="sidebar">
        <div class="sidebar-logo">
            <i class="fas fa-briefcase"></i>
            <span>JobPortal Pro</span>
        </div>
        <div class="sidebar-user">
            <?php if (!empty($user_profile['profilepic'])): ?>
                <img src="<?= htmlspecialchars($user_profile['profilepic']) ?>" class="sidebar-avatar" alt="Profile">
            <?php else: ?>
                <div class="sidebar-avatar placeholder"><i class="fas fa-user"></i></div>
            <?php endif; ?>
            <div class="sidebar-user-info">
                <strong><?= htmlspecialchars($_SESSION['seeker_name']) ?></strong>
                <span><?= !empty($user_profile['headline']) ? htmlspecialchars(mb_strimwidth($user_profile['headline'],0,28,'…')) : 'Job Seeker' ?></span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="/webtech/webTec-project-jobPortal/job_seeker/index.php"><i class="fas fa-briefcase"></i> Job Listings</a>
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/profile.php"><i class="fas fa-user-edit"></i> Edit Profile</a>
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/applications.php"><i class="fas fa-file-alt"></i> My Applications</a>
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/saved.php"><i class="fas fa-bookmark"></i> Saved Jobs <span class="nav-badge"><?= $saved_count ?></span></a>
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/messages.php" class="active">
                <i class="fas fa-envelope"></i> Messages
                <?php if ($unread_msgs > 0): ?>
                <span class="nav-badge red"><?= $unread_msgs ?></span>
                <?php endif; ?>
            </a>
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/alerts.php"><i class="fas fa-bell"></i> Job Alerts</a>
            <a href="/webtech/webTec-project-jobPortal/job_seeker/views/complaint.php"><i class="fas fa-flag"></i> Report Issue</a>
        </nav>
        <div class="sidebar-footer">
            <a href="/webtech/webTec-project-jobPortal/job_seeker/controllers/JobController.php?logout=1" class="sidebar-logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </aside>

    <main class="main-content">

        <div class="page-heading">
            <h2><i class="fas fa-envelope"></i> Messages</h2>
            <p>Inbox from employers and recruiter outreach</p>
        </div>

        <!-- ── Employer/Recruiter Messages ── -->
        <div class="card">
            <h3 style="margin-bottom:6px;">
                <i class="fas fa-building" style="color:#a855f7;"></i> Employer Messages
            </h3>
            <p style="color:#666;font-size:13px;margin-bottom:20px;">
                Direct messages from employers and recruiters. (<?= count($messages) ?> total)
            </p>

            <?php if (empty($messages)): ?>
            <div class="empty-state">
                <i class="far fa-envelope-open"></i>
                <h3>No messages yet</h3>
                <p>Messages from employers will appear here.</p>
            </div>
            <?php else: ?>
            <div class="msg-list">
                <?php foreach ($messages as $msg):
                    $is_unread = !$msg['isread'];
                    $sent_at   = date('d M Y, h:i A', strtotime($msg['sentat']));
                    $preview   = mb_strimwidth(strip_tags($msg['body']), 0, 100, '…');
                    $role      = $msg['sender_role'];
                    $role_icon = ($role === 'recruiter') ? 'fa-user-tie' : 'fa-building';
                ?>
                <div class="msg-item <?= $is_unread ? 'unread' : '' ?>"
                     id="msg-<?= $msg['id'] ?>"
                     onclick="toggleMsg(this, <?= $msg['id'] ?>, <?= $is_unread ? 'true' : 'false' ?>)">
                    <div class="msg-header">
                        <div class="msg-sender">
                            <?php if ($is_unread): ?>
                            <span class="unread-dot" id="dot-<?= $msg['id'] ?>"></span>
                            <?php endif; ?>
                            <i class="fas <?= $role_icon ?>" style="color:#a855f7;"></i>
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

        <!-- ── Recruiter Outreach ── -->
        <div class="card">
            <h3 style="margin-bottom:6px;">
                <i class="fas fa-user-tie" style="color:#a855f7;"></i> Recruiter Outreach
            </h3>
            <p style="color:#666;font-size:13px;margin-bottom:20px;">
                Job opportunities sent directly by recruiters. (<?= count($outreach) ?> total)
            </p>

            <?php if (empty($outreach)): ?>
            <div class="empty-state">
                <i class="fas fa-user-tie"></i>
                <h3>No outreach yet</h3>
                <p>Recruiters can reach out to you about specific opportunities here.</p>
            </div>
            <?php else: ?>
            <div class="msg-list">
                <?php foreach ($outreach as $o):
                    $is_unread = ($o['status'] === 'sent');
                    $sent_at   = date('d M Y, h:i A', strtotime($o['sentat']));
                    $preview   = mb_strimwidth(strip_tags($o['message']), 0, 100, '…');
                    $agency    = $o['agencyname'] ? $o['agencyname'] : $o['recruiter_name'];
                ?>
                <div class="msg-item <?= $is_unread ? 'unread' : '' ?>"
                     id="omsg-<?= $o['id'] ?>"
                     onclick="toggleOutreach(this, <?= $o['id'] ?>, '<?= $o['status'] ?>')">
                    <div class="msg-header">
                        <div class="msg-sender">
                            <?php if ($is_unread): ?>
                            <span class="unread-dot" id="odot-<?= $o['id'] ?>"></span>
                            <?php endif; ?>
                            <i class="fas fa-user-tie" style="color:#a855f7;"></i>
                            <?= htmlspecialchars($o['recruiter_name']) ?>
                            <span class="role-tag recruiter">Recruiter</span>
                            <?php if ($o['agencyname']): ?>
                            <span style="font-size:12px;color:#666;">· <?= htmlspecialchars($o['agencyname']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <span id="ostatus-<?= $o['id'] ?>" class="status-badge status-<?= $o['status'] === 'sent' ? 'submitted' : ($o['status'] === 'read' ? 'reviewed' : 'shortlisted') ?>">
                                <?= ucfirst($o['status']) ?>
                            </span>
                            <span class="msg-time"><i class="far fa-clock"></i> <?= $sent_at ?></span>
                            <i class="fas fa-chevron-down msg-toggle-icon" id="oicon-<?= $o['id'] ?>"></i>
                        </div>
                    </div>

                    <?php if ($o['job_title']): ?>
                    <div style="font-size:12px;color:#a855f7;margin-bottom:6px;">
                        <i class="fas fa-briefcase"></i>
                        Re: <?= htmlspecialchars($o['job_title']) ?>
                        <?= $o['job_location'] ? '· ' . htmlspecialchars($o['job_location']) : '' ?>
                    </div>
                    <?php endif; ?>

                    <div class="msg-preview" id="oprev-<?= $o['id'] ?>"><?= htmlspecialchars($preview) ?></div>
                    <div class="msg-body" id="obody-<?= $o['id'] ?>">
                        <?= htmlspecialchars($o['message']) ?>
                        <?php if ($o['status'] !== 'responded'): ?>
                        <div style="margin-top:14px;">
                            <button class="btn btn-sm" style="font-size:13px;padding:9px 18px;"
                                onclick="event.stopPropagation(); respondOutreach(this, <?= $o['id'] ?>)">
                                <i class="fas fa-reply"></i> Mark as Responded
                            </button>
                        </div>
                        <?php else: ?>
                        <div style="margin-top:10px;font-size:13px;color:#10b981;">
                            <i class="fas fa-check-circle"></i> You have responded to this outreach.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

    </main>
</div>

<script src="script.js"></script>
</body>
</html>