<div class="page-header">
    <h2><i class="fas fa-chart-bar"></i> Platform Analytics</h2>
    <p>Insights across all users, jobs, applications, and activity</p>
</div>

<div id="analytics-loading" class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading analytics...</div>
<div id="analytics-content" style="display:none;">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;">

        <div class="card" id="card-cat">
            <h3><i class="fas fa-tags"></i> Jobs per Category</h3>
            <div id="chart-cat"></div>
        </div>

        <div class="card" id="card-types">
            <h3><i class="fas fa-clock"></i> Job Types Distribution</h3>
            <div id="chart-types"></div>
        </div>

        <div class="card" id="card-employers">
            <h3><i class="fas fa-building"></i> Top Employers by Applications</h3>
            <div id="chart-employers"></div>
        </div>

        <div class="card" id="card-recruiters">
            <h3><i class="fas fa-headset"></i> Most Active Recruiters</h3>
            <div id="chart-recruiters"></div>
        </div>

        <div class="card" id="card-locations">
            <h3><i class="fas fa-map-marker-alt"></i> Popular Locations</h3>
            <div id="chart-locations"></div>
        </div>

        <div class="card" id="card-apps">
            <h3><i class="fas fa-chart-line"></i> Applications Over Time</h3>
            <div id="chart-apps"></div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', loadAnalytics);

function loadAnalytics() {
    fetch('controller.php?action=getAnalytics')
        .then(r => r.json())
        .then(data => {
            renderBarChart('chart-cat',       data.jobsPerCategory,   'name',         'total',         'var(--accent)');
            renderBarChart('chart-types',     data.popularJobTypes,   'jobtype',      'total',         'var(--purple)');
            renderBarChart('chart-employers', data.topEmployers,      'companyname',  'total_apps',    'var(--green)');
            renderBarChart('chart-recruiters',data.activeRecruiters,  'agencyname',   'outreach_count','var(--blue)');
            renderBarChart('chart-locations', data.popularLocations,  'location',     'total',         'var(--accent)');
            renderAppsOverTime('chart-apps',  data.appsOverTime);
            document.getElementById('analytics-loading').style.display = 'none';
            document.getElementById('analytics-content').style.display = 'block';
        });
}

function renderBarChart(containerId, data, labelKey, valKey, color) {
    const el = document.getElementById(containerId);
    if (!data || data.length === 0) {
        el.innerHTML = '<p style="color:#64748b;font-size:13px;">No data available.</p>';
        return;
    }
    const max = Math.max(...data.map(d => +d[valKey]), 1);
    let html = '';
    data.forEach(item => {
        const val = +item[valKey];
        const pct = Math.round(val/max*100);
        html += `<div class="bar-row">
            <div class="bar-label">
                <span>${esc(item[labelKey])}</span>
                <span style="color:${color};font-weight:700;">${val}</span>
            </div>
            <div class="bar-track">
                <div class="bar-fill" style="width:${pct}%;background:${color};"></div>
            </div>
        </div>`;
    });
    el.innerHTML = html;
}

function renderAppsOverTime(containerId, data) {
    const el = document.getElementById(containerId);
    if (!data || data.length === 0) {
        el.innerHTML = '<p style="color:#64748b;font-size:13px;">No data available.</p>';
        return;
    }
    const max = Math.max(...data.map(d => +d.total), 1);
    let html = '<div style="display:flex;align-items:flex-end;gap:6px;height:120px;padding:0 4px;">';
    data.forEach(item => {
        const pct = Math.round(+item.total/max*100);
        html += `<div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;">
            <span style="font-size:10px;color:#64748b;">${item.total}</span>
            <div style="width:100%;background:var(--green);border-radius:3px 3px 0 0;height:${pct}%;min-height:4px;transition:height 0.5s;"></div>
            <span style="font-size:9px;color:#64748b;writing-mode:vertical-rl;transform:rotate(180deg);">${item.month}</span>
        </div>`;
    });
    html += '</div>';
    el.innerHTML = html;
}

function esc(s) {
    return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
</script>
