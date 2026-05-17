<?php $messagesList = getMessages($_SESSION['recruiter_id']); ?>
<div class="page-header">
    <h2><i class="fas fa-comments"></i> Messages</h2>
    <p>In-platform messages from employers and applicants</p>
</div>


<div class="card">
    <h3><i class="fas fa-pen"></i> Send Message</h3>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label>Recipient User ID</label>
                <input type="number" name="recipient_id" placeholder="Enter user ID" required>
            </div>
            <div class="form-group">
                <label>Related Application ID (Optional)</label>
                <input type="number" name="app_id" placeholder="Leave blank if general">
            </div>
        </div>
        <div class="form-group">
            <label>Message</label>
            <textarea name="body" rows="4" placeholder="Write your message..." required></textarea>
        </div>
        <button type="submit" name="send_message" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Send</button>
    </form>
</div>



<div class="card">
    <h3><i class="fas fa-inbox"></i> Inbox (<?= count($messagesList) ?>)</h3>
    <?php if (empty($messagesList)): ?>
    <div class="empty-state"><i class="fas fa-inbox"></i><p>No messages yet.</p></div>
    <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:12px;">
    <?php foreach ($messagesList as $m): ?>
    <div style="background:var(--bg3);border:1px solid var(--border);border-radius:12px;padding:18px;<?= !$m['isread'] ? 'border-left:3px solid var(--accent);' : '' ?>">
        <div class="flex-between" style="margin-bottom:8px;">
            <div>
                <strong><?= htmlspecialchars($m['sendername']) ?></strong>
                <span style="color:var(--muted);font-size:12px;margin-left:8px;">[<?= ucfirst($m['senderrole']) ?>]</span>
                <?php if (!$m['isread']): ?><span class="badge badge-submitted" style="margin-left:8px;">New</span><?php endif; ?>
            </div>
            <span style="color:var(--muted);font-size:12px;"><?= date('d M Y H:i', strtotime($m['sentat'])) ?></span>
        </div>
        <p style="font-size:14px;color:var(--text);line-height:1.7;"><?= nl2br(htmlspecialchars($m['body'])) ?></p>
        <?php if (!$m['isread']): ?>
        <div style="margin-top:10px;">
            <a href="?mark_read=<?= $m['id'] ?>" class="btn btn-ghost btn-sm"><i class="fas fa-check"></i> Mark as Read</a>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>