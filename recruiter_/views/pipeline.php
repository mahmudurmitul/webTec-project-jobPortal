<div class="page-header">
    <h2><i class="fas fa-stream"></i> Candidate Pipeline</h2>
    <p>Unified view of all active candidates across your clients</p>
</div>

<div id="pipeline-loading" class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading pipeline...</div>
<div id="pipeline-kanban" style="display:none;"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadPipeline();
});

function loadPipeline() {
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4) {
            if (this.status == 200) {
                try {
                    const data = JSON.parse(this.responseText);
                    if (data.error) throw new Error(data.error);
                    renderPipeline(data);
                    document.getElementById('pipeline-loading').style.display = 'none';
                    document.getElementById('pipeline-kanban').style.display = 'flex';
                } catch(e) {
                    document.getElementById('pipeline-loading').innerHTML =
                        '<div style="text-align:center;color:#ef4444;padding:40px;"><i class="fas fa-exclamation-circle" style="font-size:32px;margin-bottom:12px;display:block;"></i>Failed to load pipeline. Please refresh the page.</div>';
                }
            } else {
                document.getElementById('pipeline-loading').innerHTML =
                    '<div style="text-align:center;color:#ef4444;padding:40px;"><i class="fas fa-exclamation-circle" style="font-size:32px;margin-bottom:12px;display:block;"></i>Server error (' + this.status + '). Please refresh.</div>';
            }
        }
    };
    xhttp.open('GET', 'index.php?action=getPipeline', true);
    xhttp.send();
}

function renderPipeline(apps) {
    const stages = {
        submitted:   { label: 'Submitted',   color: '#3b82f6' },
        reviewed:    { label: 'Reviewed',     color: '#a855f7' },
        shortlisted: { label: 'Shortlisted',  color: '#f59e0b' },
        interview:   { label: 'Interview',    color: '#10b981' }
    };

    const grouped = {};
    Object.keys(stages).forEach(s => grouped[s] = []);
    apps.forEach(a => {
        if (grouped[a.status]) grouped[a.status].push(a);
    });

    let html = '<div class="kanban">';
    Object.entries(stages).forEach(([key, stage]) => {
        const cards = grouped[key];
        html += `<div class="kanban-col">
            <div class="kanban-col-header" style="color:${stage.color};">
                ${stage.label} <span style="background:rgba(255,255,255,0.08);padding:2px 8px;border-radius:20px;font-size:11px;margin-left:6px;">${cards.length}</span>
            </div>`;
        if (cards.length === 0) {
            html += `<div style="text-align:center;padding:20px;color:#64748b;font-size:12px;">Empty</div>`;
        }
        cards.forEach(c => {
            const isInterview = key === 'interview';
            html += `<div class="kanban-card" id="kcard-${c.id}">
                <div class="name">${escHtml(c.seekername)}</div>
                <div class="job">${escHtml(c.jobtitle)}</div>
                <div style="color:#64748b;font-size:11px;margin-top:4px;">${escHtml(c.companyname || '—')}</div>
                ${c.skills ? `<div style="margin-top:8px;">${c.skills.split(',').slice(0,2).map(s=>`<span style="background:rgba(59,130,246,0.15);color:#60a5fa;padding:2px 7px;border-radius:20px;font-size:10px;margin-right:3px;">${escHtml(s.trim())}</span>`).join('')}</div>` : ''}
                <div style="margin-top:10px;display:flex;gap:6px;flex-wrap:wrap;">
                    <a href="index.php?page=seeker_profile&seeker_id=${c.seekerid}" style="font-size:11px;color:#a855f7;text-decoration:none;">View Profile →</a>
                    ${isInterview ? `<button onclick="markHired(${c.id}, this)" style="background:#fbbf24;color:#000;border:none;border-radius:6px;padding:4px 10px;font-size:11px;font-weight:700;cursor:pointer;"><i class='fas fa-check-double'></i> Mark Hired</button>` : ''}
                </div>
            </div>`;
        });
        html += '</div>';
    });
    html += '</div>';

    document.getElementById('pipeline-kanban').innerHTML = html;
}

function escHtml(s) {
    if (!s) return '';
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>