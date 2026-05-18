<?php $settings = getAllSettings(); ?>
<div class="page-header">
    <h2><i class="fas fa-sliders"></i> Platform Settings</h2>
    <p>Configure platform-wide policies and defaults</p>
</div>

<div class="card" style="max-width:700px;">
    <h3><i class="fas fa-cog"></i> Policy Configuration</h3>
    <form method="POST">
        <div class="setting-row">
            <div>
                <div class="setting-label">Max Job Postings per Employer</div>
                <div class="setting-desc">Maximum number of active job postings an employer can have simultaneously</div>
            </div>
            <input type="number" name="max_jobs_per_employer"
                   value="<?= htmlspecialchars($settings['max_jobs_per_employer'] ?? '10') ?>"
                   min="1" max="100" style="width:100px;padding:8px;background:var(--bg3);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:14px;">
        </div>

        <div class="setting-row">
            <div>
                <div class="setting-label">Max Active Applications per Seeker</div>
                <div class="setting-desc">Maximum number of simultaneous active applications a seeker can hold</div>
            </div>
            <input type="number" name="max_apps_per_seeker"
                   value="<?= htmlspecialchars($settings['max_apps_per_seeker'] ?? '20') ?>"
                   min="1" max="100" style="width:100px;padding:8px;background:var(--bg3);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:14px;">
        </div>

        <div class="setting-row">
            <div>
                <div class="setting-label">Resume Visibility Default</div>
                <div class="setting-desc">Default visibility of seeker resumes to recruiters upon registration</div>
            </div>
            <select name="resume_visibility_default" style="width:150px;padding:8px;background:var(--bg3);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:14px;">
                <option value="public"  <?= ($settings['resume_visibility_default']??'public')==='public'?'selected':'' ?>>Public</option>
                <option value="private" <?= ($settings['resume_visibility_default']??'')==='private'?'selected':'' ?>>Private</option>
            </select>
        </div>

        <div style="margin-top:20px;">
            <button type="submit" name="save_settings" class="btn btn-primary"><i class="fas fa-save"></i> Save Settings</button>
        </div>
    </form>
</div>

<div class="card" style="max-width:700px;">
    <h3><i class="fas fa-info-circle"></i> Current Values</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Setting</th><th>Value</th><th>Last Updated</th></tr></thead>
            <tbody>
            <?php
            global $conn;
            ensureSettingsTable();
            $srows = mysqli_query($conn, "SELECT * FROM platform_settings ORDER BY `key`");
            if (mysqli_num_rows($srows) === 0):
            ?>
            <tr><td colspan="3" class="text-muted" style="text-align:center;">No settings saved yet. Using defaults.</td></tr>
            <?php else: while ($sr = mysqli_fetch_assoc($srows)): ?>
            <tr>
                <td><code style="background:var(--bg3);padding:2px 8px;border-radius:4px;font-size:12px;"><?= htmlspecialchars($sr['key']) ?></code></td>
                <td><strong><?= htmlspecialchars($sr['value']) ?></strong></td>
                <td class="text-muted"><?= date('d M Y H:i', strtotime($sr['updated_at'])) ?></td>
            </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>
