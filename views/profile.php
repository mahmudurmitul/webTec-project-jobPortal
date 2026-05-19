<?php $p = $recruiterProfile; ?>
<div class="page-header">
    <h2><i class="fas fa-id-card"></i> My Profile</h2>
    <p>Manage your agency profile and contact details</p>
</div>

<div class="card" style="max-width:820px;">
    <h3><i class="fas fa-edit"></i> Edit Profile</h3>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label>Agency / Recruiter Name *</label>
                <input type="text" name="agency_name"
                       value="<?= htmlspecialchars($p['agencyname'] ?? '') ?>"
                       placeholder="TalentBridge Recruitment" required>
            </div>
            <div class="form-group">
                <label>Specialization</label>
                <input type="text" name="specialization"
                       value="<?= htmlspecialchars($p['specialization'] ?? '') ?>"
                       placeholder="IT, Finance, Healthcare...">
            </div>
            <div class="form-group">
                <label>Website</label>
                <input type="url" name="website"
                       value="<?= htmlspecialchars($p['website'] ?? '') ?>"
                       placeholder="https://yoursite.com">
            </div>
            <div class="form-group">
                <label>Contact Phone</label>
                <input type="tel" name="phone"
                       value="<?= htmlspecialchars($p['phone'] ?? '') ?>"
                       placeholder="+88017xxxxxxxx">
            </div>
        </div>
        <div class="form-group">
            <label>Description / Bio</label>
            <textarea name="description" rows="5"
                      placeholder="Describe your agency, expertise, and what makes you unique..."><?= htmlspecialchars($p['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Profile Picture</label>
            <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                <img id="pic-preview"
                     src="<?= $p && $p['profilepic'] ? htmlspecialchars($p['profilepic']) : '' ?>"
                     alt=""
                     style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:2px solid var(--accent);display:<?= $p && $p['profilepic'] ? 'block' : 'none' ?>;"
                     onerror="this.style.display='none';">
                <div>
                    <input type="file" name="profilepic" accept="image/*" id="pic-input"
                           onchange="previewPic(this)">
                    <small style="color:var(--muted);display:block;margin-top:5px;">
                        <?php if ($p && $p['profilepic']): ?>
                        <i class="fas fa-check-circle" style="color:var(--green);"></i> Photo on file. Choose new file to replace.
                        <?php else: ?>
                        Accepted: JPG, PNG, WebP (max recommended 2 MB)
                        <?php endif; ?>
                    </small>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:12px;margin-top:8px;">
            <button type="submit" name="update_profile" class="btn btn-primary"><i class="fas fa-save"></i> Save Profile</button>
            <a href="?page=dashboard" class="btn btn-ghost"><i class="fas fa-times"></i> Cancel</a>
        </div>
    </form>
</div>

<?php if ($p && $p['agencyname']): ?>
<div class="card" style="max-width:820px;">
    <h3><i class="fas fa-eye"></i> Profile Preview</h3>
    <div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap;">
        <?php if ($p['profilepic']): ?>
        <img src="<?= htmlspecialchars($p['profilepic']) ?>"
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
        <div style="flex:1;">
            <h3 style="font-family:'Syne',sans-serif;font-size:20px;margin-bottom:4px;">
                <?= htmlspecialchars($p['agencyname']) ?>
                <?php if ($p['isverified']): ?>
                <span style="font-size:12px;background:rgba(16,185,129,0.15);color:var(--green);padding:2px 10px;border-radius:20px;margin-left:8px;">&#10003; Verified</span>
                <?php else: ?>
                <span style="font-size:12px;background:rgba(245,158,11,0.15);color:var(--accent2);padding:2px 10px;border-radius:20px;margin-left:8px;">Pending Verification</span>
                <?php endif; ?>
            </h3>
            <?php if ($p['specialization']): ?>
            <p style="color:var(--accent2);font-size:14px;margin-bottom:8px;"><?= htmlspecialchars($p['specialization']) ?></p>
            <?php endif; ?>
            <div style="display:flex;flex-wrap:wrap;gap:16px;font-size:13px;color:var(--muted);margin-bottom:10px;">
                <span><i class="fas fa-user"></i> <?= htmlspecialchars($p['name']) ?></span>
                <span><i class="fas fa-envelope"></i> <?= htmlspecialchars($p['email']) ?></span>
                <?php if ($p['phone']): ?>
                <span><i class="fas fa-phone"></i> <?= htmlspecialchars($p['phone']) ?></span>
                <?php endif; ?>
                <?php if ($p['website']): ?>
                <span><i class="fas fa-globe"></i> <a href="<?= htmlspecialchars($p['website']) ?>" target="_blank" style="color:var(--blue);"><?= htmlspecialchars($p['website']) ?></a></span>
                <?php endif; ?>
            </div>
            <?php if ($p['description']): ?>
            <p style="color:var(--text);font-size:14px;line-height:1.8;"><?= nl2br(htmlspecialchars($p['description'])) ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>