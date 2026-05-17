
function previewPic(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('pic-preview');
            if (img) {
                img.src = e.target.result;
                img.style.display = 'block';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}



function showTab(tab) {
    document.querySelectorAll('.auth-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    document.querySelectorAll('.auth-tab').forEach(t => {
        if (t.textContent.toLowerCase().includes(tab === 'login' ? 'login' : 'register')) {
            t.classList.add('active');
        }
    });
}



function searchSeekers() {
    const keyword  = document.getElementById('sk-keyword')?.value || '';
    const location = document.getElementById('sk-location')?.value || '';
    const exp      = document.getElementById('sk-exp')?.value || '';
    const salary   = document.getElementById('sk-salary')?.value || '';

    const resultsDiv = document.getElementById('seeker-results');
    if (!resultsDiv) return;

    resultsDiv.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Searching candidates...</div>';

    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const seekers = JSON.parse(this.responseText);
            renderSeekers(seekers);
        }
    };
    const params = `action=searchSeekers&keyword=${encodeURIComponent(keyword)}&location=${encodeURIComponent(location)}&exp=${encodeURIComponent(exp)}&salary=${encodeURIComponent(salary)}`;
    xhttp.open('GET', 'index.php?' + params, true);
    xhttp.send();
}

function clearSearch() {
    ['sk-keyword','sk-location','sk-salary'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    const sel = document.getElementById('sk-exp');
    if (sel) sel.value = '';
    document.getElementById('seeker-results').innerHTML = '<div class="empty-state"><i class="fas fa-search"></i><p>Use the filters above to search for candidates.</p></div>';
    const counter = document.getElementById('seeker-count');
    if (counter) counter.textContent = '';
}

function renderSeekers(seekers) {
    const counter = document.getElementById('seeker-count');
    if (counter) counter.textContent = '(' + seekers.length + ')';

    const div = document.getElementById('seeker-results');
    if (seekers.length === 0) {
        div.innerHTML = '<div class="empty-state"><i class="fas fa-user-slash"></i><p>No candidates found. Try different filters.</p></div>';
        return;
    }

    let html = '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">';
    seekers.forEach(s => {
        const skills = s.skills ? s.skills.split(',').slice(0,4).map(sk =>
            `<span style="background:rgba(59,130,246,0.15);color:#60a5fa;padding:2px 8px;border-radius:20px;font-size:11px;">${escHtml(sk.trim())}</span>`
        ).join('') : '';

        html += `<div style="background:#1c1f2b;border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:18px;transition:box-shadow 0.2s;" onmouseover="this.style.boxShadow='0 8px 30px rgba(0,0,0,0.4)'" onmouseout="this.style.boxShadow=''">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                <div style="width:44px;height:44px;border-radius:50%;background:#0b0c10;display:flex;align-items:center;justify-content:center;color:#64748b;font-size:18px;border:2px solid rgba(255,255,255,0.07);flex-shrink:0;">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <div style="font-weight:600;font-size:15px;">${escHtml(s.name)}</div>
                    <div style="color:#a855f7;font-size:12px;">${escHtml(s.headline || 'Job Seeker')}</div>
                </div>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:10px;">${skills}</div>
            <div style="font-size:12px;color:#64748b;display:flex;flex-direction:column;gap:3px;margin-bottom:12px;">
                ${s.preferredlocation ? `<span><i class="fas fa-map-marker-alt" style="width:14px;"></i> ${escHtml(s.preferredlocation)}</span>` : ''}
                <span><i class="fas fa-clock" style="width:14px;"></i> ${s.yearsexperience || 0} yrs exp</span>
                ${s.expectedsalary ? `<span><i class="fas fa-money-bill" style="width:14px;"></i> ৳${parseFloat(s.expectedsalary).toLocaleString()}</span>` : ''}
            </div>
            <a href="index.php?page=seeker_profile&seeker_id=${s.id}" style="display:inline-flex;align-items:center;gap:6px;background:#7c3aed;color:#fff;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                <i class="fas fa-user"></i> View & Reach Out
            </a>
        </div>`;
    });
    html += '</div>';
    div.innerHTML = html;
}


function toggleStatus(jobId, newStatus) {
    const badge = document.getElementById('status-badge-' + jobId);
    if (badge) badge.textContent = 'Updating...';

    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            if (res.success) {
                // Reload row area
                window.location.reload();
            }
        }
    };
    xhttp.open('GET', `index.php?action=toggleJobStatus&job_id=${jobId}&status=${newStatus}`, true);
    xhttp.send();
}


function updateAppStatus(appId, status, selectEl) {
    selectEl.disabled = true;

    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            selectEl.disabled = false;
            if (res.success) {
                selectEl.style.borderColor = '#10b981';
                setTimeout(() => { selectEl.style.borderColor = ''; }, 1500);
            } else {
                alert('Failed to update status. Please try again.');
            }
        }
    };
    xhttp.open('GET', `index.php?action=updateAppStatus&app_id=${appId}&status=${status}`, true);
    xhttp.send();
}


function escHtml(s) {
    if (!s) return '';
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}