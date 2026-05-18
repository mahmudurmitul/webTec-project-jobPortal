<?php
$statusF = $_GET['status'] ?? '';
$list = getComplaints($statusF);
?>
<div class="page-header">
    <h2><i class="fas fa-flag"></i> Complaints & Disputes</h2>
    <p>Review platform complaints, add resolution notes, and close tickets</p>
</div>

<div class="search-bar" style="padding:12px 16px;">
    <form method="GET" style="display:contents;">
        <input type="hidden" name="page" value="complaints">
        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="">All</option>
                <option value="open"     <?= $statusF==='open'?'selected':'' ?>>Open</option>
                <option value="resolved" <?= $statusF==='resolved'?'selected':'' ?>>Resolved</option>
            </select>
        </div>
        <div class="form-group" style="display:flex;align-items:flex-end;">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
        </div>
    </form>
</div>

<?php if (empty($list)): ?>
<div class="card">
    <div class="empty-state"><i class="fas fa-check-circle" style="color:var(--green);opacity:1;"></i><p>No complaints found. The platform is running clean!</p></div>
</div>
<?php else: ?>
<?php foreach ($list as $c): ?>
<div class="card" style="border-left:3px solid <?= $c['status']==='open'?'var(--red)':'var(--green)' ?>;">
    <div class="flex-between" style="margin-bottom:12px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <span class="badge badge-<?= $c['status'] ?>"><?= ucfirst($c['status']) ?></span>
            <span style="font-size:12px;color:var(--muted);">Complaint #<?= $c['id'] ?> — <?= date('d M Y H:i', strtotime($c['createdat'])) ?></span>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:14px;">
        <div style="font-size:13px;">
            <span class="text-muted">Submitted by:</span><br>
            <strong><?= htmlspecialchars($c['submittername']) ?></strong>
            <span style="color:var(--accent);font-size:11px;">[<?= ucfirst($c['submitterrole']) ?>]</span><br>
            <span class="text-muted"><?= htmlspecialchars($c['submitteremail']) ?></span>
        </div>
        <div style="font-size:13px;">
            <span class="text-muted">Subject (User #<?= $c['subjectid'] ?>):</span><br>
            <strong><?= htmlspecialchars($c['subjectname'] ?? 'Unknown / Deleted') ?></strong>
        </div>
    </div>

    <div style="background:var(--bg3);border-radius:8px;padding:14px;margin-bottom:14px;font-size:13px;line-height:1.7;color:var(--text);">
        <?= nl2br(htmlspecialchars($c['description'])) ?>
    </div>

    <?php if ($c['adminnote']): ?>
    <div style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);border-radius:8px;padding:12px;font-size:13px;color:#6ee7b7;margin-bottom:12px;">
        <i class="fas fa-check-circle"></i> <strong>Resolution:</strong> <?= nl2br(htmlspecialchars($c['adminnote'])) ?>
    </div>
    <?php endif; ?>

    <?php if ($c['status'] === 'open'): ?>
    <button class="btn btn-primary btn-sm" onclick="toggleResolve(<?= $c['id'] ?>)">
        <i class="fas fa-gavel"></i> Resolve Complaint
    </button>
    <div id="resolve-<?= $c['id'] ?>" class="resolve-box">
        <form method="POST">
            <input type="hidden" name="complaint_id" value="<?= $c['id'] ?>">
            <div class="form-group" style="margin-bottom:10px;">
                <label>Admin Resolution Note *</label>
                <textarea name="admin_note" rows="3" placeholder="Describe what action was taken..." required></textarea>
            </div>
            <button type="submit" name="resolve_complaint" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Mark Resolved</button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="toggleResolve(<?= $c['id'] ?>)">Cancel</button>
        </form>
    </div>
    <?php endif; ?>
</div>
<?php endforeach; ?>
<?php endif; ?>
