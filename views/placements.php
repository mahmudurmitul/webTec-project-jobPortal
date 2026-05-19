<?php $placements = getPlacementHistory($_SESSION['recruiter_id']); ?>
<div class="page-header">
    <h2><i class="fas fa-trophy"></i> Placement History</h2>
    <p>All candidates you have successfully hired through your recruitment</p>
</div>

<<<<<<< HEAD

=======
<<<<<<< HEAD
<!-- How to hire info box -->
=======

>>>>>>> 43a4b345e51f77b9bede491f9ae3f1139cea9d11
>>>>>>> 0e5b1b3773d675329dd7049dc83f42cfc694ce5f
<div style="background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.25);border-left:4px solid #fbbf24;border-radius:10px;padding:14px 18px;margin-bottom:22px;display:flex;gap:12px;align-items:flex-start;">
    <i class="fas fa-info-circle" style="color:#fbbf24;margin-top:2px;font-size:16px;flex-shrink:0;"></i>
    <div style="font-size:13px;color:#e2e8f0;">
        <strong style="color:#fbbf24;">How to hire a candidate:</strong>
        Go to <a href="?page=applications" style="color:#a855f7;">Applications</a> or the
        <a href="?page=pipeline" style="color:#a855f7;">Pipeline</a>, find a candidate at
        <strong>Interview</strong> stage, then click the
        <span style="background:#fbbf24;color:#000;padding:1px 8px;border-radius:4px;font-size:11px;font-weight:700;">✓✓ Mark Hired</span>
        button. They will appear here immediately.
    </div>
</div>

<div class="card">
    <div class="flex-between" style="margin-bottom:18px;">
        <h3><i class="fas fa-star" style="color:#fbbf24;"></i> Hired Candidates (<?= count($placements) ?>)</h3>
        <?php if (!empty($placements)): ?>
        <span style="color:var(--muted);font-size:13px;">Total placements made through your recruitment</span>
        <?php endif; ?>
    </div>

    <?php if (empty($placements)): ?>
    <div class="empty-state">
        <i class="fas fa-trophy" style="color:#fbbf24;opacity:0.4;"></i>
        <p style="margin-bottom:12px;">No placements yet.</p>
        <p style="font-size:13px;">
            Move candidates through the pipeline to <strong>Interview</strong> stage,<br>
            then use the <strong style="color:#fbbf24;">Mark Hired</strong> button.
        </p>
        <div style="margin-top:16px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
            <a href="?page=pipeline" class="btn btn-primary btn-sm"><i class="fas fa-stream"></i> Open Pipeline</a>
            <a href="?page=applications" class="btn btn-ghost btn-sm"><i class="fas fa-file-alt"></i> View Applications</a>
        </div>
    </div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Candidate</th>
                    <th>Headline</th>
                    <th>Skills</th>
                    <th>Experience</th>
                    <th>Job Placed For</th>
                    <th>Company</th>
                    <th>Hired On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($placements as $p): ?>
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:34px;height:34px;border-radius:50%;background:rgba(251,191,36,0.15);display:flex;align-items:center;justify-content:center;color:#fbbf24;font-size:14px;flex-shrink:0;">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div>
                            <strong><?= htmlspecialchars($p['seekername']) ?></strong><br>
                            <span style="color:var(--muted);font-size:11px;"><?= htmlspecialchars($p['seekeremail']) ?></span>
                        </div>
                    </div>
                </td>
                <td class="text-muted"><?= htmlspecialchars($p['headline'] ?? '—') ?></td>
                <td>
                    <?php
                    $skills = array_slice(array_map('trim', explode(',', $p['skills'] ?? '')), 0, 3);
                    foreach ($skills as $s): if ($s): ?>
                    <span style="background:rgba(59,130,246,0.12);color:#60a5fa;padding:2px 8px;border-radius:20px;font-size:10px;margin-right:2px;"><?= htmlspecialchars($s) ?></span>
                    <?php endif; endforeach; ?>
                </td>
                <td><?= ($p['yearsexperience'] ?? 0) ?> yrs</td>
                <td><strong><?= htmlspecialchars($p['jobtitle']) ?></strong></td>
                <td><?= htmlspecialchars($p['companyname'] ?? '—') ?></td>
                <td>
                    <span class="badge badge-hired"><i class="fas fa-star"></i> Hired</span><br>
                    <span style="color:var(--muted);font-size:11px;"><?= date('d M Y', strtotime($p['appliedat'])) ?></span>
                </td>
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

<<<<<<< HEAD
   
=======
<<<<<<< HEAD
    <!-- Summary stats -->
=======
   
>>>>>>> 43a4b345e51f77b9bede491f9ae3f1139cea9d11
>>>>>>> 0e5b1b3773d675329dd7049dc83f42cfc694ce5f
    <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:24px;flex-wrap:wrap;">
        <?php
        $companies = array_unique(array_filter(array_column($placements, 'companyname')));
        $jobs      = array_unique(array_filter(array_column($placements, 'jobtitle')));
        ?>
        <div style="font-size:13px;color:var(--muted);">
            <span style="color:var(--text);font-weight:700;font-size:20px;"><?= count($placements) ?></span> total hires
        </div>
        <div style="font-size:13px;color:var(--muted);">
            <span style="color:var(--text);font-weight:700;font-size:20px;"><?= count($companies) ?></span> client companies
        </div>
        <div style="font-size:13px;color:var(--muted);">
            <span style="color:var(--text);font-weight:700;font-size:20px;"><?= count($jobs) ?></span> distinct roles filled
        </div>
    </div>
    <?php endif; ?>
</div>