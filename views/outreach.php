<?php $outreachList = getOutreachByRecruiter($_SESSION['recruiter_id']); ?>
<div class="page-header">
    <h2><i class="fas fa-envelope-open-text"></i> Outreach Messages</h2>
    <p>Send messages directly to seekers about job opportunities — they appear in the seeker's inbox</p>
</div>

<!-- Stats row -->
<?php
$total     = count($outreachList);
$read      = count(array_filter($outreachList, fn($o) => $o['status'] === 'read'));
$responded = count(array_filter($outreachList, fn($o) => $o['status'] === 'responded'));
?>
<div class="stats-grid" style="margin-bottom:22px;">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-paper-plane"></i></div>
        <div><div class="stat-num"><?= $total ?></div><div class="stat-label">Sent</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-envelope-open"></i></div>
        <div><div class="stat-num"><?= $read ?></div><div class="stat-label">Read</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-reply"></i></div>
        <div><div class="stat-num"><?= $responded ?></div><div class="stat-label">Responded</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="fas fa-percentage"></i></div>
        <div>
            <div class="stat-num"><?= $total > 0 ? round($responded/$total*100) : 0 ?>%</div>
            <div class="stat-label">Response Rate</div>
        </div>
    </div>
</div>

<!-- Quick Compose -->
<div class="card">
    <h3><i class="fas fa-pen"></i> Send New Outreach</h3>
    <p style="color:var(--muted);font-size:13px;margin-bottom:16px;">
        Find a candidate first, then send them an outreach message.
        Messages appear directly in the seeker's inbox.
    </p>
    <a href="index.php?page=seekers" class="btn btn-primary">
        <i class="fas fa-search"></i> Find Candidates to Message
    </a>
</div>

<!-- Sent Outreach List -->
<div class="card">
    <h3><i class="fas fa-list"></i> Sent Messages (<?= $total ?>)</h3>
    <?php if (empty($outreachList)): ?>
    <div class="empty-state">
        <i class="fas fa-envelope"></i>
        <p>No outreach sent yet.</p>
        <a href="index.php?page=seekers" class="btn btn-primary btn-sm" style="margin-top:12px;">
            <i class="fas fa-search"></i> Find Seekers
        </a>
    </div>
    <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:12px;">
    <?php foreach ($outreachList as $o): ?>
    <div style="background:var(--bg3);border:1px solid var(--border);border-radius:10px;padding:16px;
                border-left:3px solid <?= $o['status']==='responded'?'var(--green)':($o['status']==='read'?'var(--blue)':'var(--border)') ?>;">
        <div class="flex-between" style="margin-bottom:8px;">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <strong style="font-size:14px;"><?= htmlspecialchars($o['seekername']) ?></strong>
                <span style="color:var(--muted);font-size:12px;"><?= htmlspecialchars($o['seekeremail']) ?></span>
                <span class="badge badge-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span>
            </div>
            <span style="color:var(--muted);font-size:11px;">
                <?= date('d M Y, h:i A', strtotime($o['sentat'])) ?>
            </span>
        </div>
        <?php if ($o['jobtitle']): ?>
        <div style="margin-bottom:8px;">
            <span style="background:rgba(124,58,237,0.15);color:var(--accent2);padding:2px 10px;border-radius:20px;font-size:11px;">
                <i class="fas fa-briefcase"></i> <?= htmlspecialchars($o['jobtitle']) ?>
            </span>
        </div>
        <?php endif; ?>
        <p style="font-size:13px;color:var(--text);line-height:1.7;margin:0;">
            <?= nl2br(htmlspecialchars($o['message'])) ?>
        </p>
        <div style="margin-top:10px;">
            <a href="index.php?page=seeker_profile&seeker_id=<?= $o['seekerid'] ?>"
               class="btn btn-ghost btn-xs">
                <i class="fas fa-user"></i> View Profile
            </a>
            <a href="index.php?page=seeker_profile&seeker_id=<?= $o['seekerid'] ?>"
               class="btn btn-primary btn-xs" style="margin-left:6px;">
                <i class="fas fa-paper-plane"></i> Send Follow-up
            </a>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>