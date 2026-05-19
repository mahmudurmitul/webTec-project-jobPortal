<?php
$seekerId = (int)($_GET['seeker_id'] ?? 0);
$seeker = $seekerId ? getSeekerPublicProfile($seekerId) : null;
if (!$seeker): ?>
<div class="empty-state"><i class="fas fa-user-slash"></i><p>Seeker not found.</p></div>
<?php return; endif; ?>

<div class="page-header">
    <div class="flex-between">
        <div>
            <h2><i class="fas fa-user"></i> Candidate Profile</h2>
        </div>
        <a href="index.php?page=seekers" class="btn btn-ghost"><i class="fas fa-arrow-left"></i> Back to Search</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1.5fr;gap:24px;flex-wrap:wrap;">

    <!-- Profile Card -->
    <div>
        <div class="card">
            <div style="text-align:center;margin-bottom:20px;">
                <?php if ($seeker['profilepic']): ?>
                <img src="<?= htmlspecialchars($seeker['profilepic']) ?>" style="width:90px;height:90px;border-radius:50%;object-fit:cover;border:3px solid var(--accent);">
                <?php else: ?>
                <div style="width:90px;height:90px;border-radius:50%;background:var(--bg3);display:flex;align-items:center;justify-content:center;font-size:32px;color:var(--muted);margin:0 auto;border:3px solid var(--border);">
                    <i class="fas fa-user"></i>
                </div>
                <?php endif; ?>
                <h3 style="margin-top:12px;font-family:'Syne',sans-serif;"><?= htmlspecialchars($seeker['name']) ?></h3>
                <p style="color:var(--accent2);font-size:14px;"><?= htmlspecialchars($seeker['headline'] ?? '') ?></p>
            </div>

            <div style="display:flex;flex-direction:column;gap:12px;font-size:14px;">
                <div><span class="text-muted"><i class="fas fa-graduation-cap"></i> Education:</span> <?= htmlspecialchars($seeker['educationlevel'] ?? '—') ?></div>
                <div><span class="text-muted"><i class="fas fa-clock"></i> Experience:</span> <?= ($seeker['yearsexperience'] ?? 0) ?> years</div>
                <div><span class="text-muted"><i class="fas fa-map-marker-alt"></i> Location:</span> <?= htmlspecialchars($seeker['preferredlocation'] ?? '—') ?></div>
                <div><span class="text-muted"><i class="fas fa-money-bill-wave"></i> Expected:</span> ৳<?= number_format($seeker['expectedsalary'] ?? 0) ?></div>

                <?php if ($seeker['resumepath']): ?>
                <div>
                    <a href="<?= htmlspecialchars($seeker['resumepath']) ?>" target="_blank" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;">
                        <i class="fas fa-download"></i> Download Resume
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($seeker['skills']): ?>
        <div class="card">
            <h3><i class="fas fa-tags"></i> Skills</h3>
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                <?php foreach (array_map('trim', explode(',', $seeker['skills'])) as $skill): ?>
                <?php if ($skill): ?>
                <span style="background:rgba(124,58,237,0.15);color:var(--accent2);padding:4px 12px;border-radius:20px;font-size:12px;font-weight:500;"><?= htmlspecialchars($skill) ?></span>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Outreach Form -->
    <div>
        <?php if ($seeker['summary']): ?>
        <div class="card">
            <h3><i class="fas fa-align-left"></i> Professional Summary</h3>
            <p style="font-size:14px;line-height:1.8;color:var(--text);"><?= nl2br(htmlspecialchars($seeker['summary'])) ?></p>
        </div>
        <?php endif; ?>

        <div class="card">
            <h3><i class="fas fa-envelope-open-text"></i> Send Outreach</h3>
            <form method="POST">
                <input type="hidden" name="seeker_id" value="<?= $seeker['id'] ?>">
                <div class="form-group">
                    <label>Related Job (Optional)</label>
                    <select name="job_id">
                        <option value="">— General outreach —</option>
                        <?php
                        $myJobs = getRecruiterJobs($_SESSION['recruiter_id'], '', 'active');
                        foreach ($myJobs as $j): ?>
                        <option value="<?= $j['id'] ?>"><?= htmlspecialchars($j['title']) ?> @ <?= htmlspecialchars($j['clientname'] ?? '') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Message *</label>
                    <textarea name="message" rows="6" placeholder="Hi <?= htmlspecialchars($seeker['name']) ?>, I came across your profile and believe you'd be a great fit for an exciting opportunity we have..."  required></textarea>
                </div>
                <button type="submit" name="send_outreach" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Send Outreach</button>
            </form>
        </div>
    </div>
</div>