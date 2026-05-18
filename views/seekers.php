<?php
$search = $_GET['search'] ?? '';
$list = getSeekers($search);
?>
<div class="page-header">
    <h2><i class="fas fa-users"></i> Seeker Accounts</h2>
    <p>Search and manage job seeker accounts flagged for misuse</p>
</div>

<div class="search-bar">
    <form method="GET" style="display:contents;">
        <input type="hidden" name="page" value="seekers">
        <div class="form-group" style="flex:2;">
            <label>Search</label>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Name, email, skills...">
        </div>
        <div class="form-group" style="display:flex;align-items:flex-end;">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Search</button>
        </div>
    </form>
</div>

<div class="card">
    <h3><i class="fas fa-list"></i> Job Seekers (<?= count($list) ?>)</h3>
    <?php if (empty($list)): ?>
    <div class="empty-state"><i class="fas fa-users"></i><p>No seekers found.</p></div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Name</th><th>Email</th><th>Headline</th><th>Skills</th><th>Experience</th><th>Status</th><th>Joined</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($list as $u): ?>
            <tr>
                <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                <td class="text-muted"><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['headline'] ?? '—') ?></td>
                <td>
                    <?php
                    $skills = array_slice(array_map('trim', explode(',', $u['skills'] ?? '')), 0, 3);
                    foreach ($skills as $s): if ($s): ?>
                    <span style="background:rgba(59,130,246,0.12);color:#60a5fa;padding:1px 7px;border-radius:20px;font-size:10px;margin-right:2px;"><?= htmlspecialchars($s) ?></span>
                    <?php endif; endforeach; ?>
                </td>
                <td><?= $u['yearsexperience'] ?? '—' ?> yrs</td>
                <td>
                    <span class="badge <?= $u['isactive'] ? 'badge-active' : 'badge-suspended' ?>">
                        <?= $u['isactive'] ? 'Active' : 'Deactivated' ?>
                    </span>
                </td>
                <td class="text-muted"><?= date('d M Y', strtotime($u['createdat'])) ?></td>
                <td>
                    <?php if ($u['isactive']): ?>
                    <a href="?suspend_user=<?= $u['id'] ?>&back=seekers" class="btn btn-danger btn-xs" onclick="return confirm('Deactivate this seeker?')"><i class="fas fa-user-slash"></i> Deactivate</a>
                    <?php else: ?>
                    <a href="?reactivate_user=<?= $u['id'] ?>&back=seekers" class="btn btn-success btn-xs"><i class="fas fa-user-check"></i> Restore</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
