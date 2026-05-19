<?php
$analytics      = getRecruiterAnalytics($_SESSION['recruiter_id']);
$placementPerClient = getPlacementPerClient($_SESSION['recruiter_id']);

$responseRate = $analytics['outreach_total'] > 0
    ? round(($analytics['outreach_responded'] / $analytics['outreach_total']) * 100, 1) : 0;
$placementRate = $analytics['apps_total'] > 0
    ? round(($analytics['placed'] / $analytics['apps_total']) * 100, 1) : 0;
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
>>>>>>> 0e5b1b3773d675329dd7049dc83f42cfc694ce5f

>>>>>>> 43a4b345e51f77b9bede491f9ae3f1139cea9d11

// Client report — use recruiter's own client list
$selectedClient = (int)($_GET['client'] ?? 0);
$clientReport   = [];
if ($selectedClient) {
    $clientReport = getClientReport($_SESSION['recruiter_id'], $selectedClient);
}
?>
<div class="page-header">
    <h2><i class="fas fa-chart-bar"></i> Analytics & Reports</h2>
    <p>Track your recruitment performance and generate per-client reports</p>
</div>

<!-- KPI Cards -->
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
            <div class="stat-label">Overall Placement Rate</div>
        </div>
    </div>
</div>

<<<<<<< HEAD
<!-- Overview Bar Chart (AJAX) -->
=======

<<<<<<< HEAD
=======
>>>>>>> 43a4b345e51f77b9bede491f9ae3f1139cea9d11
>>>>>>> 0e5b1b3773d675329dd7049dc83f42cfc694ce5f
<div class="card">
    <h3><i class="fas fa-chart-bar"></i> Activity Overview</h3>
    <div id="analytics-loading" class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
    <div id="analytics-chart" style="display:none;"></div>
</div>

<<<<<<< HEAD
<div class="card">
    <h3><i class="fas fa-building"></i> Placement Success Rate per Client</h3>
    <?php if (empty($placementPerClient)): ?>
    <div class="empty-state"><i class="fas fa-chart-bar"></i><p>No client data yet.</p></div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Total Applications</th>
                    <th>Placed (Interview)</th>
                    <th>Rejected</th>
                    <th>Success Rate</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($placementPerClient as $c):
                $rate = $c['total_apps'] > 0
                    ? round(($c['placed'] / $c['total_apps']) * 100, 1) : 0;
            ?>
            <tr>
                <td><strong><?= htmlspecialchars($c['clientname']) ?></strong></td>
                <td><?= $c['total_apps'] ?></td>
                <td style="color:var(--green);font-weight:600;"><?= $c['placed'] ?></td>
                <td style="color:var(--red);"><?= $c['rejected'] ?></td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="flex:1;background:var(--bg3);border-radius:4px;height:8px;min-width:80px;">
                            <div style="background:var(--green);height:100%;border-radius:4px;width:<?= $rate ?>%;"></div>
                        </div>
                        <span style="color:var(--green);font-weight:700;font-size:13px;"><?= $rate ?>%</span>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>


<div class="card">
=======
<!-- Placement Success Rate per Client -->
<div class="card">
    <h3><i class="fas fa-building"></i> Placement Success Rate per Client</h3>
    <?php if (empty($placementPerClient)): ?>
    <div class="empty-state"><i class="fas fa-chart-bar"></i><p>No client data yet.</p></div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Total Applications</th>
                    <th>Placed (Interview)</th>
                    <th>Rejected</th>
                    <th>Success Rate</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($placementPerClient as $c):
                $rate = $c['total_apps'] > 0
                    ? round(($c['placed'] / $c['total_apps']) * 100, 1) : 0;
            ?>
            <tr>
                <td><strong><?= htmlspecialchars($c['clientname']) ?></strong></td>
                <td><?= $c['total_apps'] ?></td>
                <td style="color:var(--green);font-weight:600;"><?= $c['placed'] ?></td>
                <td style="color:var(--red);"><?= $c['rejected'] ?></td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="flex:1;background:var(--bg3);border-radius:4px;height:8px;min-width:80px;">
                            <div style="background:var(--green);height:100%;border-radius:4px;width:<?= $rate ?>%;"></div>
                        </div>
                        <span style="color:var(--green);font-weight:700;font-size:13px;"><?= $rate ?>%</span>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<<<<<<< HEAD
<!-- Generate Client Report -->
=======

>>>>>>> 43a4b345e51f77b9bede491f9ae3f1139cea9d11
<div class="card">
>>>>>>> 0e5b1b3773d675329dd7049dc83f42cfc694ce5f
    <h3><i class="fas fa-file-alt"></i> Generate Client Report</h3>
    <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
        <input type="hidden" name="page" value="analytics">
        <div class="form-group" style="margin:0;min-width:260px;">
            <label>Select Your Client</label>
            <select name="client">
                <option value="">— Choose Client —</option>
                <?php foreach ($clients_list as $cl):
                    // Only show clients that are linked to a registered employer
                    if (!$cl['employerid']) continue;
                ?>
                <option value="<?= $cl['employerid'] ?>"
                        <?= $selectedClient == $cl['employerid'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cl['companynameoverride']) ?>
                </option>
                <?php endforeach; ?>
                <?php
                // Also show employers the recruiter has posted jobs for but may not have a client record
                $jobEmployers = [];
                foreach ($placementPerClient as $pc) {
                    if ($pc['employerid'] && !in_array($pc['employerid'], array_column($clients_list, 'employerid'))) {
                        $jobEmployers[] = $pc;
                    }
                }
                foreach ($jobEmployers as $je): ?>
                <option value="<?= $je['employerid'] ?>"
                        <?= $selectedClient == $je['employerid'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($je['clientname']) ?> (via jobs)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-chart-bar"></i> Generate Report</button>
    </form>

    <?php if ($selectedClient && !empty($clientReport)): ?>
    <?php
<<<<<<< HEAD
  
=======
<<<<<<< HEAD
    // Find client name
=======
  
>>>>>>> 43a4b345e51f77b9bede491f9ae3f1139cea9d11
>>>>>>> 0e5b1b3773d675329dd7049dc83f42cfc694ce5f
    $clientName = '';
    foreach ($clients_list as $cl) {
        if ($cl['employerid'] == $selectedClient) { $clientName = $cl['companynameoverride']; break; }
    }
    if (!$clientName) {
        foreach ($placementPerClient as $pc) {
            if ($pc['employerid'] == $selectedClient) { $clientName = $pc['clientname']; break; }
        }
    }
    ?>
    <div style="margin-top:24px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
            <h4 style="font-family:'Syne',sans-serif;color:var(--accent2);font-size:16px;">
                Report: <?= htmlspecialchars($clientName) ?>
            </h4>
            <button class="btn btn-ghost btn-sm" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
        </div>
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
                    foreach (array_keys($totals) as $k) $totals[$k] += (int)$r[$k];
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($r['title']) ?></strong></td>
                    <td><span class="badge badge-<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
                    <td><?= $r['appcount'] ?></td>
                    <td><?= $r['submitted'] ?></td>
                    <td><?= $r['reviewed'] ?></td>
                    <td><?= $r['shortlisted'] ?></td>
                    <td style="color:var(--green);font-weight:600;"><?= $r['interview'] ?></td>
                    <td style="color:var(--red);"><?= $r['rejected'] ?></td>
                </tr>
                <?php endforeach; ?>
                <tr style="background:rgba(124,58,237,0.1);font-weight:700;border-top:2px solid var(--accent);">
                    <td colspan="2" style="color:var(--accent2);">TOTALS</td>
                    <td><?= $totals['appcount'] ?></td>
                    <td><?= $totals['submitted'] ?></td>
                    <td><?= $totals['reviewed'] ?></td>
                    <td><?= $totals['shortlisted'] ?></td>
                    <td style="color:var(--green);"><?= $totals['interview'] ?></td>
                    <td style="color:var(--red);"><?= $totals['rejected'] ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php elseif ($selectedClient): ?>
    <div class="empty-state" style="padding:30px;">
        <i class="fas fa-chart-bar"></i>
        <p>No jobs posted for this client yet.</p>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', loadAnalytics);

function loadAnalytics() {
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            try {
                const d = JSON.parse(this.responseText);
                renderAnalyticsChart(d);
                document.getElementById('analytics-loading').style.display = 'none';
                document.getElementById('analytics-chart').style.display = 'block';
            } catch(e) {
                document.getElementById('analytics-loading').innerHTML =
                    '<p style="color:#64748b;text-align:center;">Could not load chart data.</p>';
            }
        }
    };
    xhttp.open('GET', 'index.php?action=getAnalytics', true);
    xhttp.send();
}

function renderAnalyticsChart(d) {
    const bars = [
        { label: 'Active Jobs',       val: parseInt(d.jobs_active)    || 0, color: '#10b981' },
        { label: 'Closed Jobs',       val: parseInt(d.jobs_closed)    || 0, color: '#ef4444' },
        { label: 'Clients',           val: parseInt(d.clients_total)  || 0, color: '#3b82f6' },
        { label: 'Interviews/Placed', val: parseInt(d.placed)         || 0, color: '#a855f7' },
        { label: 'Outreach Sent',     val: parseInt(d.outreach_total) || 0, color: '#f59e0b' },
        { label: 'Applications Mgd', val: parseInt(d.apps_total)     || 0, color: '#64748b' },
    ];
    const max = Math.max(...bars.map(b => b.val), 1);
    let html = '<div style="display:flex;flex-direction:column;gap:14px;">';
    bars.forEach(b => {
        const pct = Math.round(b.val / max * 100);
        html += `<div>
            <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                <span style="font-size:13px;color:#94a3b8;">${b.label}</span>
                <span style="font-size:13px;font-weight:700;color:${b.color};">${b.val}</span>
            </div>
            <div style="background:#1c1f2b;border-radius:6px;height:10px;overflow:hidden;">
                <div style="background:${b.color};height:100%;width:${pct}%;border-radius:6px;transition:width 0.8s ease;"></div>
            </div>
        </div>`;
    });
    html += '</div>';
    document.getElementById('analytics-chart').innerHTML = html;
}
</script>