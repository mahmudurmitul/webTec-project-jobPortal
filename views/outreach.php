<?php $outreachList = getOutreachByRecruiter($_SESSION['recruiter_id']); ?>
<div class="page-header">
    <h2><i class="fas fa-envelope-open-text"></i> Outreach Messages</h2>
    <p>Track all messages sent to candidates</p>
</div>

<div class="card">
    <h3><i class="fas fa-paper-plane"></i> Sent Outreach (<?= count($outreachList) ?>)</h3>
    <?php if (empty($outreachList)): ?>
    <div class="empty-state">
        <i class="fas fa-envelope"></i>
        <p>No outreach sent yet. <a href="?page=seekers" style="color:var(--accent2);">Find candidates →</a></p>
    </div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Candidate</th>
                    <th>Email</th>
                    <th>Job</th>
                    <th>Message Preview</th>
                    <th>Status</th>
                    <th>Sent</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($outreachList as $o): ?>
            <tr>
                <td><strong><?= htmlspecialchars($o['seekername']) ?></strong></td>
                <td class="text-muted" style="font-size:12px;"><?= htmlspecialchars($o['seekeremail']) ?></td>
                <td><?= htmlspecialchars($o['jobtitle'] ?? '—') ?></td>
                <td>
                    <span style="color:var(--muted);font-size:13px;">
                        <?= htmlspecialchars(mb_strimwidth($o['message'], 0, 60, '...')) ?>
                    </span>
                </td>
                <td><span class="badge badge-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
                <td><?= date('d M Y', strtotime($o['sentat'])) ?></td>
                <td>
                    <a href="?page=seeker_profile&seeker_id=<?= $o['seekerid'] ?>" class="btn btn-ghost btn-xs">
                        <i class="fas fa-user"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>