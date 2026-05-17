<?php $placements = getPlacementHistory($_SESSION['recruiter_id']); ?>
<div class="page-header">
    <h2><i class="fas fa-trophy"></i> Placement History</h2>
    <p>Candidates who reached the interview stage through your recruitment</p>
</div>

<div class="card">
    <h3><i class="fas fa-star"></i> Successful Placements (<?= count($placements) ?>)</h3>
    <?php if (empty($placements)): ?>
    <div class="empty-state">
        <i class="fas fa-trophy"></i>
        <p>No placements yet. Keep working your pipeline!</p>
    </div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Candidate</th>
                    <th>Headline</th>
                    <th>Job</th>
                    <th>Company</th>
                    <th>Date Placed</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($placements as $p): ?>
            <tr>
                <td><strong><?= htmlspecialchars($p['seekername']) ?></strong></td>
                <td><span class="text-muted"><?= htmlspecialchars($p['headline'] ?? '—') ?></span></td>
                <td><?= htmlspecialchars($p['jobtitle']) ?></td>
                <td><?= htmlspecialchars($p['companyname'] ?? '—') ?></td>
                <td><?= date('d M Y', strtotime($p['appliedat'])) ?></td>
                <td>
                    <a href="?page=seeker_profile&seeker_id=<?= $p['seekerid'] ?>" class="btn btn-ghost btn-xs">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>