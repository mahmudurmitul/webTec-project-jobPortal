<?php
$sent     = getMessagesByRecruiter($_SESSION['recruiter_id']);
$received = getReceivedMessages($_SESSION['recruiter_id']);
$tab      = $_GET['tab'] ?? 'received';
?>
<div class="page-header">
    <h2><i class="fas fa-comments"></i> Messages</h2>
    <p>Send and receive direct messages. Status updates automatically when the recipient reads your message.</p>
</div>

<!-- Compose -->
<div class="card">
    <h3><i class="fas fa-pen"></i> Compose Message</h3>
    <form method="POST" action="index.php">
        <div class="form-row">
            <div class="form-group">
                <label>Recipient User ID <span class="text-muted">(Enter the seeker or employer's user ID)</span></label>
                <input type="number" name="recipient_id"
                       value="<?= htmlspecialchars($_POST['recipient_id'] ?? '') ?>"
                       placeholder="e.g. 5" required min="1">
            </div>
        </div>
        <div class="form-group">
            <label>Message *</label>
            <textarea name="body" rows="4"
                      placeholder="Write your message..."
                      required><?= htmlspecialchars($_POST['body'] ?? '') ?></textarea>
        </div>
        <button type="submit" name="send_message" class="btn btn-primary">
            <i class="fas fa-paper-plane"></i> Send Message
        </button>
    </form>
</div>

<!-- Tabs -->
<div style="display:flex;gap:4px;background:var(--bg3);border-radius:10px;padding:4px;margin-bottom:20px;width:fit-content;">
    <a href="index.php?page=messages&tab=received"
       class="btn btn-sm <?= $tab==='received'?'btn-primary':'btn-ghost' ?>" style="border-radius:7px;">
        <i class="fas fa-inbox"></i> Received
        <?php $unread = count(array_filter($received, fn($m)=>!$m['isread'])); ?>
        <?php if ($unread > 0): ?>
        <span style="background:#ef4444;color:#fff;border-radius:20px;padding:0 6px;font-size:10px;margin-left:4px;"><?= $unread ?></span>
        <?php endif; ?>
    </a>
    <a href="index.php?page=messages&tab=sent"
       class="btn btn-sm <?= $tab==='sent'?'btn-primary':'btn-ghost' ?>" style="border-radius:7px;">
        <i class="fas fa-paper-plane"></i> Sent
        <span style="background:rgba(255,255,255,0.1);border-radius:20px;padding:0 6px;font-size:10px;margin-left:4px;"><?= count($sent) ?></span>
    </a>
</div>

<!-- RECEIVED -->
<?php if ($tab === 'received'): ?>
<div class="card">
    <h3><i class="fas fa-inbox"></i> Received Messages (<?= count($received) ?>)</h3>
    <?php if (empty($received)): ?>
    <div class="empty-state"><i class="fas fa-inbox"></i><p>No messages received yet.</p></div>
    <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:10px;">
    <?php foreach ($received as $m): ?>
    <div style="background:var(--bg3);border:1px solid var(--border);border-radius:10px;padding:16px;
                <?= !$m['isread'] ? 'border-left:3px solid var(--accent);' : '' ?>">
        <div class="flex-between" style="margin-bottom:8px;">
            <div>
                <strong><?= htmlspecialchars($m['sendername']) ?></strong>
                <span style="color:var(--muted);font-size:11px;margin-left:6px;">[<?= ucfirst($m['senderrole']) ?>]</span>
                <?php if (!$m['isread']): ?>
                <span class="badge badge-submitted" style="margin-left:6px;">New</span>
                <?php endif; ?>
            </div>
            <span style="color:var(--muted);font-size:11px;"><?= date('d M Y, h:i A', strtotime($m['sentat'])) ?></span>
        </div>
        <p style="font-size:13px;line-height:1.7;"><?= nl2br(htmlspecialchars($m['body'])) ?></p>
        <?php if (!$m['isread']): ?>
        <a href="index.php?mark_read=<?= $m['id'] ?>&page=messages&tab=received"
           class="btn btn-ghost btn-xs" style="margin-top:8px;">
            <i class="fas fa-check"></i> Mark as Read
        </a>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- SENT -->
<?php else: ?>
<div class="card">
    <h3><i class="fas fa-paper-plane"></i> Sent Messages (<?= count($sent) ?>)</h3>
    <?php if (empty($sent)): ?>
    <div class="empty-state"><i class="fas fa-paper-plane"></i><p>No direct messages sent yet.</p></div>
    <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:10px;">
    <?php foreach ($sent as $m): ?>
    <div style="background:var(--bg3);border:1px solid var(--border);border-radius:10px;padding:16px;
                border-left:3px solid <?= $m['isread'] ? 'var(--green)' : 'var(--border)' ?>;">
        <div class="flex-between" style="margin-bottom:8px;">
            <div style="display:flex;align-items:center;gap:8px;">
                <strong>To: <?= htmlspecialchars($m['recipientname']) ?></strong>
                <span style="color:var(--muted);font-size:11px;"><?= htmlspecialchars($m['recipientemail']) ?></span>
                <?php if ($m['isread']): ?>
                <span class="badge badge-active"><i class="fas fa-check-double"></i> Seen</span>
                <?php else: ?>
                <span class="badge badge-draft"><i class="fas fa-clock"></i> Not seen yet</span>
                <?php endif; ?>
            </div>
            <span style="color:var(--muted);font-size:11px;"><?= date('d M Y, h:i A', strtotime($m['sentat'])) ?></span>
        </div>
        <p style="font-size:13px;line-height:1.7;color:var(--text);"><?= nl2br(htmlspecialchars($m['body'])) ?></p>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>