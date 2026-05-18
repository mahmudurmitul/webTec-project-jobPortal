<?php $announcements = getAnnouncements(); ?>
<div class="page-header">
    <h2><i class="fas fa-bullhorn"></i> Platform Announcements</h2>
    <p>Broadcast messages to all platform users</p>
</div>

<div class="card" style="max-width:750px;">
    <h3><i class="fas fa-pen"></i> Post New Announcement</h3>
    <form method="POST">
        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="ann_title" placeholder="e.g. System Maintenance Notice" required>
        </div>
        <div class="form-group">
            <label>Message Body *</label>
            <textarea name="ann_body" rows="5" placeholder="Write your platform-wide announcement here..." required></textarea>
        </div>
        <button type="submit" name="post_announcement" class="btn btn-primary"><i class="fas fa-bullhorn"></i> Post Announcement</button>
    </form>
</div>

<div class="card">
    <h3><i class="fas fa-list"></i> Posted Announcements (<?= count($announcements) ?>)</h3>
    <?php if (empty($announcements)): ?>
    <div class="empty-state"><i class="fas fa-bullhorn"></i><p>No announcements posted yet.</p></div>
    <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:14px;">
    <?php foreach ($announcements as $a): ?>
    <div style="background:var(--bg3);border:1px solid var(--border);border-radius:10px;padding:18px;border-left:3px solid var(--accent);">
        <div class="flex-between" style="margin-bottom:8px;">
            <h4 style="font-size:15px;color:var(--accent);"><?= htmlspecialchars($a['title']) ?></h4>
            <div style="display:flex;align-items:center;gap:10px;">
                <span class="text-muted"><?= date('d M Y H:i', strtotime($a['createdat'])) ?></span>
                <a href="?delete_announcement=<?= $a['id'] ?>" class="btn btn-danger btn-xs" onclick="return confirm('Delete this announcement?')"><i class="fas fa-trash"></i></a>
            </div>
        </div>
        <p style="font-size:13px;color:var(--text);line-height:1.7;"><?= nl2br(htmlspecialchars($a['body'])) ?></p>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
