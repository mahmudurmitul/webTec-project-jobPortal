<?php $p = $recruiterProfile; ?>
<div class="page-header">
    <h2><i class="fas fa-id-card"></i> My Profile</h2>
    <p>Manage your agency profile and contact details</p>
</div>

<div class="card" style="max-width:800px;">
    <h3><i class="fas fa-edit"></i> Edit Profile</h3>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label>Agency / Recruiter Name *</label>
                <input type="text" name="agency_name" value="<?= htmlspecialchars($p['agencyname'] ?? '') ?>" placeholder="TalentBridge Recruitment" required>
            </div>
            <div class="form-group">
                <label>Specialization</label>
                <input type="text" name="specialization" value="<?= htmlspecialchars($p['specialization'] ?? '') ?>" placeholder="IT, Finance, Healthcare...">
            </div>
            <div class="form-group">
                <label>Website</label>
                <input type="url" name="website" value="<?= htmlspecialchars($p['website'] ?? '') ?>" placeholder="https://yoursite.com">
            </div>
            <div class="form-group">
                <label>Profile Picture</label>
                <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
                    <img id="pic-preview"
                         src="<?= $p && $p['profilepic'] ? htmlspecialchars($p['profilepic']) : '' ?>"
                         alt=""
                         style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:2px solid var(--accent);display:<?= $p && $p['profilepic'] ? 'block' : 'none' ?>;">
                    <div>
                        <input type="file" name="profilepic" accept="image/*" id="pic-input"
                               onchange="previewPic(this)">
                        <?php if ($p && $p['profilepic']): ?>
                        <small style="color:var(--muted);display:block;margin-top:4px;">
                            <i class="fas fa-check-circle" style="color:var(--green);"></i> Photo uploaded. Choose new file to replace.
                        </small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>Description / Bio</label>
            <textarea name="description" rows="5" placeholder="Describe your agency, expertise, and what makes you unique..."><?= htmlspecialchars($p['description'] ?? '') ?></textarea>
        </div>
        <div style="display:flex;gap:12px;">
            <button type="submit" name="update_profile" class="btn btn-primary"><i class="fas fa-save"></i> Save Profile</button>
            <a href="?page=dashboard" class="btn btn-ghost"><i class="fas fa-times"></i> Cancel</a>
        </div>
    </form>
</div>

<?php if ($p): ?>

    
<div class="card" style="max-width:800px;">
    <h3><i class="fas fa-eye"></i> Profile Preview</h3>
    <div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap;">
        <?php if ($p['profilepic']): ?>
        <img src="<?= htmlspecialchars($p['profilepic']) ?>"
             alt="Profile Photo"
             style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--accent);flex-shrink:0;"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
        <div style="width:80px;height:80px;border-radius:50%;background:var(--bg3);display:none;align-items:center;justify-content:center;font-size:28px;color:var(--muted);border:3px solid var(--border);flex-shrink:0;">
            <i class="fas fa-user-tie"></i>
        </div>
        <?php else: ?>
        <div style="width:80px;height:80px;border-radius:50%;background:var(--bg3);display:flex;align-items:center;justify-content:center;font-size:28px;color:var(--muted);border:3px solid var(--border);flex-shrink:0;">
            <i class="fas fa-user-tie"></i>
        </div>
        <?php endif; ?>
        <div>
            <h3 style="font-family:'Syne',sans-serif;font-size:20px;margin-bottom:4px;"><?= htmlspecialchars($p['agencyname']) ?></h3>
            <p style="color:var(--accent2);font-size:14px;margin-bottom:6px;"><?= htmlspecialchars($p['specialization'] ?? 'Recruitment Agency') ?></p>
            <p style="color:var(--muted);font-size:13px;"><?= htmlspecialchars($p['name']) ?> — <?= htmlspecialchars($p['email']) ?></p>
            <?php if ($p['website']): ?><p><a href="<?= htmlspecialchars($p['website']) ?>" target="_blank" style="color:var(--blue);font-size:13px;"><i class="fas fa-globe"></i> <?= htmlspecialchars($p['website']) ?></a></p><?php endif; ?>
            <?php if ($p['description']): ?><p style="color:var(--text);font-size:14px;margin-top:10px;line-height:1.7;"><?= nl2br(htmlspecialchars($p['description'])) ?></p><?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>