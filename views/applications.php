<?php
$analytics = getRecruiterAnalytics($_SESSION['recruiter_id']);
$responseRate = $analytics['outreach_total'] > 0
    ? round(($analytics['outreach_responded'] / $analytics['outreach_total']) * 100, 1)
    : 0;
$placementRate = $analytics['apps_total'] > 0
    ? round(($analytics['placed'] / $analytics['apps_total']) * 100, 1)
    : 0;


$selectedClient = (int)($_GET['client'] ?? 0);
$clientReport   = [];
if ($selectedClient) {
    $clientReport = getClientReport($_SESSION['recruiter_id'], $selectedClient);
}
?>
<div class="page-header">
    <h2><i class="fas fa-chart-bar"></i> Analytics & Reports</h2>
    <p>Track your recruitment performance and generate client reports</p>
</div>


<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-paper-plane"></i></div>
        <div>
            <div class="stat-num"><?= $analytics['outreach_total'] ?></div>
            <div class="stat-label">Total Outreach Sent</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-reply"></i></div>
        <div>
            <div class="stat-num"><?= $responseRate ?>%</div>
            <div class="stat-label">Response Rate</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-file-alt"></i></div>
        <div>
            <div class="stat-num"><?= $analytics['apps_total'] ?></div>
            <div class="stat-label">Applications Managed</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="fas fa-trophy"></i></div>
        <div>
            <div class="stat-num"><?= $placementRate ?>%</div>
            <div class="stat-label">Placement Rate</div>
        </div>
    </div>
</div>


<div class="card" id="analytics-card">
    <h3><i class="fas fa-chart-donut"></i> Overview</h3>
    <div id="analytics-loading" class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
    <div id="analytics-chart" style="display:none;"></div>
</div>


<div class="card">
    <h3><i class="fas fa-file-chart-line"></i> Generate Client Report</h3>
    <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
        <input type="hidden" name="page" value="analytics">
        <div class="form-group" style="margin:0;min-width:240px;">
            <label>Select Client</label>
            <select name="client">
                <option value="">— Choose Client —</option>
                <?php foreach ($employers_list as $emp): ?>
                <option value="<?= $emp['id'] ?>" <?= $selectedClient == $emp['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($emp['companyname']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-chart-bar"></i> Generate Report</button>
    </form>

    <?php if ($selectedClient && !empty($clientReport)): ?>
    <div style="margin-top:24px;">
        <h4 style="font-family:'Syne',sans-serif;margin-bottom:16px;color:var(--accent2);">
            Report for: <?= htmlspecialchars($employers_list[array_search($selectedClient, array_column($employers_list, 'id'))]['companyname'] ?? '') ?>
        </h4>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Status</th>
                        <th>Total Apps</th>
                        <th>Submitted</th>
                        <th>Reviewed</th>
                        <th>Shortlisted</th>
                        <th>Interview</th>
                        <th>Rejected</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $totals = ['appcount'=>0,'submitted'=>0,'reviewed'=>0,'shortlisted'=>0,'interview'=>0,'rejected'=>0];
                foreach ($clientReport as $r):
                    foreach ($totals as $k => $_) $totals[$k] += $r[$k];
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($r['title']) ?></strong></td>
                    <td><span class="badge badge-<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
                    <td><?= $r['appcount'] ?></td>
                    <td><?= $r['submitted'] ?></td>
                    <td><?= $r['reviewed'] ?></td>
                    <td><?= $r['shortlisted'] ?></td>
                    <td><?= $r['interview'] ?></td>
                    <td><?= $r['rejected'] ?></td>
                </tr>
                <?php endforeach; ?>
                <tr style="background:rgba(124,58,237,0.1);font-weight:700;">
                    <td colspan="2">TOTALS</td>
                    <?php foreach (['appcount','submitted','reviewed','shortlisted','interview','rejected'] as $k): ?>
                    <td><?= $totals[$k] ?></td>
                    <?php endforeach; ?>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php elseif ($selectedClient): ?>
    <div class="empty-state" style="padding:30px;"><i class="fas fa-chart-bar"></i><p>No jobs posted for this client yet.</p></div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadAnalytics();
});

function loadAnalytics() {
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const d = JSON.parse(this.responseText);
            renderAnalyticsChart(d);
            document.getElementById('analytics-loading').style.display = 'none';
            document.getElementById('analytics-chart').style.display = 'block';
        }
    };
    xhttp.open('GET', 'index.php?action=getAnalytics', true);
    xhttp.send();
}

function renderAnalyticsChart(d) {
    const bars = [
        { label: 'Jobs Active', val: parseInt(d.jobs_active) || 0, color: '#10b981' },
        { label: 'Jobs Closed', val: parseInt(d.jobs_closed) || 0, color: '#ef4444' },
        { label: 'Clients',     val: parseInt(d.clients_total) || 0, color: '#3b82f6' },
        { label: 'Placed',      val: parseInt(d.placed) || 0, color: '#a855f7' },
        { label: 'Outreach',    val: parseInt(d.outreach_total) || 0, color: '#f59e0b' },
        { label: 'Apps Mgd',    val: parseInt(d.apps_total) || 0, color: '#64748b' },
    ];
    const max = Math.max(...bars.map(b => b.val), 1);

    let html = '<div style="display:flex;flex-direction:column;gap:14px;">';
    bars.forEach(b => {
        const pct = Math.round((b.val / max) * 100);
        html += `<div>
            <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                <span style="font-size:13px;color:#94a3b8;">${b.label}</span>
                <span style="font-size:13px;font-weight:700;color:${b.color};">${b.val}</span>
            </div>
            <div style="background:#1c1f2b;border-radius:6px;height:12px;overflow:hidden;">
                <div style="background:${b.color};height:100%;width:${pct}%;border-radius:6px;transition:width 0.8s ease;"></div>
            </div>
        </div>`;
    });
    html += '</div>';
    document.getElementById('analytics-chart').innerHTML = html;
}
</script>