<div class="page-header">
    <h2><i class="fas fa-th-large"></i> Dashboard</h2>
    <p>Platform overview — <?= date('l, d F Y') ?></p>
</div>


<div id="stats-loading" class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading stats...</div>
<div id="stats-grid" class="stats-grid" style="display:none;"></div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;flex-wrap:wrap;">

    <div class="card">
        <h3><i class="fas fa-users"></i> Users by Role</h3>
        <div id="role-breakdown">
            <?php
            $stats = getDashboardStats();
            $roles = ['seeker'=>['label'=>'Job Seekers','icon'=>'fa-user','color'=>'var(--blue)'],
                      'employer'=>['label'=>'Employers','icon'=>'fa-building','color'=>'var(--green)'],
                      'recruiter'=>['label'=>'Recruiters','icon'=>'fa-headset','color'=>'var(--purple)'],
                      'admin'=>['label'=>'Admins','icon'=>'fa-shield-halved','color'=>'var(--accent)']];
            $total = array_sum($stats['users_by_role']);
            foreach ($roles as $key => $r):
                $count = $stats['users_by_role'][$key] ?? 0;
                $pct   = $total > 0 ? round($count/$total*100) : 0;
            ?>
            <div class="bar-row">
                <div class="bar-label">
                    <span><i class="fas <?= $r['icon'] ?>" style="color:<?= $r['color'] ?>;margin-right:6px;"></i><?= $r['label'] ?></span>
                    <span style="color:<?= $r['color'] ?>;font-weight:700;"><?= $count ?></span>
                </div>
                <div class="bar-track">
                    <div class="bar-fill" style="width:<?= $pct ?>%;background:<?= $r['color'] ?>;"></div>
                </div>
            </div>
            <?php endforeach; ?>
            <p class="text-muted" style="margin-top:12px;">Total registered users: <strong style="color:var(--text);"><?= $total ?></strong></p>
        </div>
    </div>

    <div class="card">
        <h3><i class="fas fa-bell"></i> Pending Actions</h3>
        <?php
        $pend_emp = mysqli_fetch_assoc(mysqli_query($GLOBALS['conn'],"SELECT COUNT(*) as c FROM users WHERE role='employer' AND isverified=0 AND isactive=1"))['c'];
        $pend_rec = mysqli_fetch_assoc(mysqli_query($GLOBALS['conn'],"SELECT COUNT(*) as c FROM users WHERE role='recruiter' AND isverified=0 AND isactive=1"))['c'];
        $open_cmp = mysqli_fetch_assoc(mysqli_query($GLOBALS['conn'],"SELECT COUNT(*) as c FROM complaints WHERE status='open'"))['c'];
        ?>
        <div style="display:flex;flex-direction:column;gap:12px;">
            <a href="?page=employers&verified=0" style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;background:var(--bg3);border-radius:8px;text-decoration:none;border:1px solid var(--border);transition:border-color 0.2s;" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--border)'">
                <span style="color:var(--text);font-size:13px;"><i class="fas fa-building" style="color:var(--green);margin-right:8px;"></i>Employer Verifications</span>
                <span style="background:<?= $pend_emp>0?'var(--accent)':'var(--bg4)' ?>;color:<?= $pend_emp>0?'#000':'var(--muted)' ?>;padding:2px 10px;border-radius:20px;font-size:12px;font-weight:700;"><?= $pend_emp ?></span>
            </a>
            <a href="?page=recruiters&verified=0" style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;background:var(--bg3);border-radius:8px;text-decoration:none;border:1px solid var(--border);transition:border-color 0.2s;" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--border)'">
                <span style="color:var(--text);font-size:13px;"><i class="fas fa-headset" style="color:var(--purple);margin-right:8px;"></i>Recruiter Verifications</span>
                <span style="background:<?= $pend_rec>0?'var(--accent)':'var(--bg4)' ?>;color:<?= $pend_rec>0?'#000':'var(--muted)' ?>;padding:2px 10px;border-radius:20px;font-size:12px;font-weight:700;"><?= $pend_rec ?></span>
            </a>
            <a href="?page=complaints&status=open" style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;background:var(--bg3);border-radius:8px;text-decoration:none;border:1px solid var(--border);transition:border-color 0.2s;" onmouseover="this.style.borderColor='var(--red)'" onmouseout="this.style.borderColor='var(--border)'">
                <span style="color:var(--text);font-size:13px;"><i class="fas fa-flag" style="color:var(--red);margin-right:8px;"></i>Open Complaints</span>
                <span style="background:<?= $open_cmp>0?'var(--red)':'var(--bg4)' ?>;color:#fff;padding:2px 10px;border-radius:20px;font-size:12px;font-weight:700;"><?= $open_cmp ?></span>
            </a>
        </div>
    </div>
</div>


<div class="card">
    <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="?page=categories" class="btn btn-ghost btn-sm"><i class="fas fa-tags"></i> Manage Categories</a>
        <a href="?page=jobs"       class="btn btn-ghost btn-sm"><i class="fas fa-briefcase"></i> Review Jobs</a>
        <a href="?page=announcements" class="btn btn-primary btn-sm"><i class="fas fa-bullhorn"></i> Post Announcement</a>
        <a href="?page=report"     class="btn btn-ghost btn-sm"><i class="fas fa-file-export"></i> Monthly Report</a>
        <a href="?page=analytics"  class="btn btn-ghost btn-sm"><i class="fas fa-chart-bar"></i> Analytics</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('controller.php?action=getDashboardStats')
        .then(r => r.json())
        .then(data => {
            const items = [
                { icon: 'fa-users',       label: 'Total Users',       val: Object.values(data.users_by_role).reduce((a,b)=>a+ +b,0), cls: 'si-blue' },
                { icon: 'fa-briefcase',   label: 'Active Jobs',        val: data.active_jobs,           cls: 'si-green' },
                { icon: 'fa-file-alt',    label: 'Applications Today', val: data.apps_today,            cls: 'si-amber' },
                { icon: 'fa-clock',       label: 'Pending Verif.',     val: data.pending_verification,  cls: 'si-purple' },
                { icon: 'fa-flag',        label: 'Open Complaints',    val: data.open_complaints,       cls: 'si-red' },
            ];
            let html = '';
            items.forEach(i => {
                html += `<div class="stat-card">
                    <div class="stat-icon ${i.cls}"><i class="fas ${i.icon}"></i></div>
                    <div><div class="stat-num">${i.val}</div><div class="stat-label">${i.label}</div></div>
                </div>`;
            });
            document.getElementById('stats-grid').innerHTML = html;
            document.getElementById('stats-loading').style.display = 'none';
            document.getElementById('stats-grid').style.display = 'grid';
        });
});
</script>
