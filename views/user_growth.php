<div class="page-header">
    <h2><i class="fas fa-chart-line"></i> User Growth Report</h2>
    <p>New registrations per role per month</p>
</div>

<div id="growth-loading" class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading growth data...</div>
<div id="growth-content" class="card" style="display:none;">
    <h3><i class="fas fa-users"></i> Monthly Registrations by Role</h3>
    <div id="growth-table"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('controller.php?action=getUserGrowth')
        .then(r => r.json())
        .then(data => {
            renderGrowth(data);
            document.getElementById('growth-loading').style.display = 'none';
            document.getElementById('growth-content').style.display = 'block';
        });
});

function renderGrowth(data) {
    // Build month → role map
    const months = [];
    const map = {};
    data.forEach(row => {
        if (!map[row.month]) {
            map[row.month] = { seeker:0, employer:0, recruiter:0, admin:0 };
            months.push(row.month);
        }
        map[row.month][row.role] = +row.total;
    });

    if (months.length === 0) {
        document.getElementById('growth-table').innerHTML = '<p style="color:#64748b;">No data yet.</p>';
        return;
    }

    let html = '<div class="table-wrap"><table><thead><tr><th>Month</th><th>Seekers</th><th>Employers</th><th>Recruiters</th><th>Admins</th><th>Total</th></tr></thead><tbody>';
    months.forEach(m => {
        const r = map[m];
        const tot = r.seeker + r.employer + r.recruiter + r.admin;
        html += `<tr>
            <td style="font-family:'JetBrains Mono',monospace;">${m}</td>
            <td style="color:#3b82f6;">${r.seeker}</td>
            <td style="color:#10b981;">${r.employer}</td>
            <td style="color:#8b5cf6;">${r.recruiter}</td>
            <td style="color:#f59e0b;">${r.admin}</td>
            <td><strong>${tot}</strong></td>
        </tr>`;
    });
    html += '</tbody></table></div>';
    document.getElementById('growth-table').innerHTML = html;
}
</script>
