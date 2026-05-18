<?php
$search  = $_GET['search']  ?? '';
$verified = isset($_GET['verified']) && $_GET['verified'] !== '' ? $_GET['verified'] : '';
$list = getEmployers($search, $verified);
?>
<div class="page-header">
    <h2><i class="fas fa-building"></i> Employer Accounts</h2>
    <p>Manage employer registrations and verification status</p>
</div>

<div class="search-bar">
    <form method="GET" style="display:contents;">
        <input type="hidden" name="page" value="employers">
        <div class="form-group">
            <label>Search</label>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Name, email, company...">
        </div>
        <div class="form-group">
            <label>Verification</label>
            <select name="verified">
                <option value="">All</option>
                <option value="0" <?= $verified==='0'?'selected':'' ?>>Pending</option>
                <option value="1" <?= $verified==='1'?'selected':'' ?>>Verified</option>
            </select>
        </div>
        <div class="form-group" style="display:flex;align-items:flex-end;">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Filter</button>
        </div>
    </form>
</div>

<div class="card">
    <h3><i class="fas fa-list"></i> Employers (<?= count($list) ?>)</h3>
    <?php if (empty($list)): ?>
    <div class="empty-state"><i class="fas fa-building"></i><p>No employers found.</p></div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Company</th><th>User</th><th>Email</th><th>Industry</th><th>Status</th><th>Verified</th><th>Joined</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($list as $u): ?>
            <tr>
                <td><strong><?= htmlspecialchars($u['companyname'] ?? '—') ?></strong></td>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td class="text-muted"><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['industry'] ?? '—') ?></td>
                <td>
                    <span class="badge <?= $u['isactive'] ? 'badge-active' : 'badge-suspended' ?>">
                        <?= $u['isactive'] ? 'Active' : 'Suspended' ?>
                    </span>
                </td>
                <td>
                    <span class="badge <?= $u['isverified'] ? 'badge-verified' : 'badge-pending' ?>">
                        <?= $u['isverified'] ? 'Verified' : 'Pending' ?>
                    </span>
                </td>
                <td class="text-muted"><?= date('d M Y', strtotime($u['createdat'])) ?></td>
                <td style="white-space:nowrap;">
                    <?php if (!$u['isverified'] && $u['isactive']): ?>
                    <a href="?approve_employer=<?= $u['id'] ?>" class="btn btn-success btn-xs" onclick="return confirm('Approve this employer?')"><i class="fas fa-check"></i> Approve</a>
                    <a href="?reject_employer=<?= $u['id'] ?>"  class="btn btn-danger btn-xs"  onclick="return confirm('Reject this employer?')"><i class="fas fa-times"></i> Reject</a>
                    <?php elseif ($u['isactive']): ?>
                    <a href="?suspend_user=<?= $u['id'] ?>&back=employers" class="btn btn-danger btn-xs" onclick="return confirm('Suspend this account?')"><i class="fas fa-ban"></i> Suspend</a>
                    <?php else: ?>
                    <a href="?reactivate_user=<?= $u['id'] ?>&back=employers" class="btn btn-success btn-xs" onclick="return confirm('Reactivate this account?')"><i class="fas fa-undo"></i> Reactivate</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
