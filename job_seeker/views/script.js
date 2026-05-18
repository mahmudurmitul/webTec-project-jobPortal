function showErr(id, msg) {
    var el = document.getElementById(id);
    if (el) { el.textContent = msg; el.style.display = msg ? 'block' : 'none'; }
}

function clearErrs() {
    var els = document.querySelectorAll('.field-error');
    for (var i = 0; i < els.length; i++) {
        els[i].textContent = '';
        els[i].style.display = 'none';
    }
}

function validateLogin() {
    clearErrs();
    var ok = true;
    var email = document.getElementById('loginEmail');
    var pass  = document.getElementById('loginPass');
    if (!email || !pass) return true;

    if (!email.value.trim()) {
        showErr('loginEmailErr', 'Email is required.'); ok = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
        showErr('loginEmailErr', 'Enter a valid email address.'); ok = false;
    }
    if (!pass.value) {
        showErr('loginPassErr', 'Password is required.'); ok = false;
    }
    return ok;
}

function validateRegister() {
    clearErrs();
    var ok = true;
    var name    = document.getElementById('regName');
    var email   = document.getElementById('regEmail');
    var phone   = document.getElementById('regPhone');
    var pass    = document.getElementById('regPass');
    var confirm = document.getElementById('regConfirm');
    if (!name) return true;

    if (!name.value.trim()) {
        showErr('regNameErr', 'Full name is required.'); ok = false;
    }
    if (!email.value.trim()) {
        showErr('regEmailErr', 'Email is required.'); ok = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
        showErr('regEmailErr', 'Enter a valid email address.'); ok = false;
    }
    if (!phone.value.trim()) {
        showErr('regPhoneErr', 'Phone number is required.'); ok = false;
    } else if (!/^\+?[0-9]{7,15}$/.test(phone.value.trim())) {
        showErr('regPhoneErr', 'Enter a valid phone number.'); ok = false;
    }
    if (!pass.value) {
        showErr('regPassErr', 'Password is required.'); ok = false;
    } else if (pass.value.length < 6) {
        showErr('regPassErr', 'Password must be at least 6 characters.'); ok = false;
    }
    if (pass.value && confirm.value !== pass.value) {
        showErr('regConfirmErr', 'Passwords do not match.'); ok = false;
    }
    return ok;
}

function validateAlert() {
    var kw  = document.getElementById('alert_keyword');
    var cat = document.getElementById('alert_cat');
    var loc = document.getElementById('alert_location');
    var typ = document.getElementById('alert_type');
    if (!kw) return true;
    var errEl = document.getElementById('alertErr');
    if (!kw.value.trim() && (!cat || !cat.value) && !loc.value.trim() && (!typ || !typ.value)) {
        if (errEl) errEl.textContent = 'Please fill at least one field.';
        return false;
    }
    if (errEl) errEl.textContent = '';
    return true;
}

function validateComplaint() {
    var desc = document.getElementById('comp_description');
    var subj = document.getElementById('comp_subject');
    var ok = true;
    if (subj && !subj.value) {
        showErr('compSubjErr', 'Please select a job/employer.'); ok = false;
    } else { showErr('compSubjErr', ''); }
    if (desc && desc.value.trim().length < 20) {
        showErr('compDescErr', 'Description must be at least 20 characters.'); ok = false;
    } else { showErr('compDescErr', ''); }
    return ok;
}


// ── Detect base path automatically so AJAX works from any page depth
var BASE = '/webtech/webTec-project-jobPortal/job_seeker';


function typeBadge(type) {
    var map = {
        'full-time':  'type-full-time',
        'part-time':  'type-part-time',
        'remote':     'type-remote',
        'contract':   'type-contract'
    };
    var cls = map[type] || '';
    return '<span class="type-badge ' + cls + '">' + type.replace('-', ' ').toUpperCase() + '</span>';
}

function featBadge(f) {
    return f ? '<span class="featured-badge">FEATURED</span>' : '';
}

function fmtSalary(min, max) {
    return '৳' + Number(min).toLocaleString() + ' – ৳' + Number(max).toLocaleString();
}

function buildJobsTable(data) {
    if (!data.length) {
        return '<div class="empty-state"><i class="fas fa-search"></i><h3>No jobs found</h3><p>Try adjusting your filters.</p></div>';
    }
    var h = '<table><thead><tr>' +
        '<th>Title</th><th>Company</th><th>Category</th>' +
        '<th>Location</th><th>Salary</th><th>Type</th>' +
        '<th>Experience</th><th>Deadline</th><th>Action</th>' +
        '</tr></thead><tbody>';

    for (var i = 0; i < data.length; i++) {
        var d = data[i];
        var company = d.company_name || d.employer_name || '—';
        h += '<tr>';
        h += '<td style="font-weight:600;">' +
             '<a href="' + BASE + '/views/job.php?id=' + d.id + '" style="color:#e2e8f0;text-decoration:none;">' +
             escHtml(d.title) + '</a>' + featBadge(d.is_featured) + '</td>';
        h += '<td>' + escHtml(company) + '</td>';
        h += '<td><span class="badge">' + escHtml(d.catname) + '</span></td>';
        h += '<td>' + escHtml(d.location) + '</td>';
        h += '<td class="salary">' + fmtSalary(d.salary_min, d.salary_max) + '</td>';
        h += '<td>' + typeBadge(d.job_type) + '</td>';
        h += '<td style="text-transform:capitalize;">' + escHtml(d.experiencelevel) + '</td>';
        h += '<td>' + escHtml(d.deadline) + '</td>';
        h += '<td style="white-space:nowrap;">' +
             '<button class="btn-save-inline" onclick="toggleSave(this,' + d.id + ')"><i class="far fa-bookmark"></i></button> ' +
             '<a href="' + BASE + '/views/job.php?id=' + d.id + '" class="apply-btn" style="padding:7px 14px;font-size:12px;">View</a>' +
             '</td>';
        h += '</tr>';
    }
    h += '</tbody></table>';
    return h;
}

function escHtml(str) {
    if (str == null) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}


function loadJobs() {
    var jobsEl = document.getElementById('jobs');
    if (!jobsEl) return;
    jobsEl.innerHTML = '<div style="text-align:center;padding:40px;color:#555;"><i class="fas fa-circle-notch fa-spin" style="font-size:28px;color:#a855f7;"></i></div>';

    var xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        var data = JSON.parse(this.responseText);
        var countEl = document.getElementById('job-count');
        if (countEl) countEl.textContent = '(' + data.length + ')';
        jobsEl.innerHTML = buildJobsTable(data);
    };
    xhttp.open('GET', BASE + '/controllers/JobController.php?action=getJobs', true);
    xhttp.send();
}


function filterJobs() {
    var jobsEl = document.getElementById('jobs');
    if (!jobsEl) return;
    jobsEl.innerHTML = '<div style="text-align:center;padding:40px;color:#555;"><i class="fas fa-circle-notch fa-spin" style="font-size:28px;color:#a855f7;"></i></div>';

    var category = document.getElementById('category').value;
    var location = document.getElementById('location').value;
    var type     = document.getElementById('type').value;
    var exp      = document.getElementById('exp').value;
    var sal_min  = document.getElementById('sal_min').value;
    var sal_max  = document.getElementById('sal_max').value;
    var keyword  = document.getElementById('keyword').value;

    var url = BASE + '/controllers/JobController.php?action=filterJobs' +
              '&category=' + encodeURIComponent(category) +
              '&location='  + encodeURIComponent(location) +
              '&type='      + encodeURIComponent(type) +
              '&exp='       + encodeURIComponent(exp) +
              '&sal_min='   + encodeURIComponent(sal_min) +
              '&sal_max='   + encodeURIComponent(sal_max) +
              '&keyword='   + encodeURIComponent(keyword);

    var xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        var data = JSON.parse(this.responseText);
        var countEl = document.getElementById('job-count');
        if (countEl) countEl.textContent = '(' + data.length + ')';
        jobsEl.innerHTML = buildJobsTable(data);
    };
    xhttp.open('GET', url, true);
    xhttp.send();
}


function resetFilters() {
    var ids = ['category','location','type','exp','sal_min','sal_max','keyword'];
    for (var i = 0; i < ids.length; i++) {
        var el = document.getElementById(ids[i]);
        if (el) el.value = '';
    }
    loadJobs();
}


function toggleSave(btn, jobId) {
    var xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        var data = JSON.parse(this.responseText);
        if (data.error) { alert(data.error); return; }
        var icon = btn.querySelector('i');
        if (data.saved) {
            icon.className = 'fas fa-bookmark';
            btn.title = 'Remove bookmark';
        } else {
            icon.className = 'far fa-bookmark';
            btn.title = 'Save job';
        }
        var badge = document.getElementById('saved-sidebar-badge');
        if (badge) {
            var cur = parseInt(badge.textContent) || 0;
            badge.textContent = data.saved ? cur + 1 : Math.max(0, cur - 1);
        }
    };
    xhttp.open('GET', BASE + '/controllers/JobController.php?action=toggleSave&job_id=' + jobId, true);
    xhttp.send();
}


function withdrawApp(btn, id) {
    if (!confirm('Withdraw this application?')) return;

    var xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        var data = JSON.parse(this.responseText);
        if (data.success) {
            var row = btn.closest('tr');
            if (row) {
                row.style.transition = 'opacity 0.3s';
                row.style.opacity = '0';
                setTimeout(function() { row.remove(); }, 300);
            }
        } else {
            alert('Could not withdraw application.');
        }
    };
    xhttp.open('GET', BASE + '/controllers/JobController.php?action=withdraw&app_id=' + id, true);
    xhttp.send();
}


function removeSaved(btn, jobId) {
    var xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        var data = JSON.parse(this.responseText);
        if (!data.saved) {
            var row = document.getElementById('saved-row-' + jobId);
            if (row) {
                row.style.transition = 'opacity 0.3s';
                row.style.opacity = '0';
                setTimeout(function() { row.remove(); }, 300);
            }
        }
    };
    xhttp.open('GET', BASE + '/controllers/JobController.php?action=toggleSave&job_id=' + jobId, true);
    xhttp.send();
}


function doToggleSave(btn, jobId) {
    var xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        var data = JSON.parse(this.responseText);
        if (data.error) { alert(data.error); return; }
        var icon = btn.querySelector('i');
        var textNode = btn.childNodes[btn.childNodes.length - 1];
        if (data.saved) {
            btn.classList.add('saved');
            icon.className = 'fas fa-bookmark';
            if (textNode) textNode.textContent = ' Saved';
            btn.title = 'Remove bookmark';
        } else {
            btn.classList.remove('saved');
            icon.className = 'far fa-bookmark';
            if (textNode) textNode.textContent = ' Save';
            btn.title = 'Save job';
        }
    };
    xhttp.open('GET', BASE + '/controllers/JobController.php?action=toggleSave&job_id=' + jobId, true);
    xhttp.send();
}


function toggleMsg(el, id, isUnread) {
    var body = document.getElementById('body-' + id);
    var prev = document.getElementById('prev-' + id);
    var icon = document.getElementById('icon-' + id);
    var dot  = document.getElementById('dot-'  + id);
    if (!body) return;
    var isOpen = body.classList.contains('open');

    if (isOpen) {
        body.classList.remove('open');
        if (icon) icon.classList.remove('open');
        if (prev) prev.style.display = '';
    } else {
        body.classList.add('open');
        if (icon) icon.classList.add('open');
        if (prev) prev.style.display = 'none';
        if (isUnread) {
            el.classList.remove('unread');
            if (dot) dot.remove();
            var xhttp = new XMLHttpRequest();
            xhttp.open('GET', BASE + '/controllers/JobController.php?action=markRead&msg_id=' + id, true);
            xhttp.send();
        }
    }
}


function toggleOutreach(el, id, status) {
    var body = document.getElementById('obody-' + id);
    var prev = document.getElementById('oprev-' + id);
    var icon = document.getElementById('oicon-' + id);
    var dot  = document.getElementById('odot-'  + id);
    if (!body) return;
    var isOpen = body.classList.contains('open');

    if (isOpen) {
        body.classList.remove('open');
        if (icon) icon.classList.remove('open');
        if (prev) prev.style.display = '';
    } else {
        body.classList.add('open');
        if (icon) icon.classList.add('open');
        if (prev) prev.style.display = 'none';
        if (status === 'sent') {
            el.classList.remove('unread');
            if (dot) dot.remove();
            var xhttp = new XMLHttpRequest();
            xhttp.open('GET', BASE + '/controllers/JobController.php?action=markOutreachRead&outreach_id=' + id, true);
            xhttp.send();
        }
    }
}

function respondOutreach(btn, id) {
    if (!confirm('Mark this outreach as responded?')) return;
    var xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        var data = JSON.parse(this.responseText);
        if (data.success) {
            btn.textContent = 'Responded';
            btn.disabled = true;
            btn.style.opacity = '0.6';
            var statusEl = document.getElementById('ostatus-' + id);
            if (statusEl) { statusEl.textContent = 'responded'; statusEl.className = 'status-badge status-shortlisted'; }
        }
    };
    xhttp.open('GET', BASE + '/controllers/JobController.php?action=respondOutreach&outreach_id=' + id, true);
    xhttp.send();
}

function deleteAlert(btn, id) {
    if (!confirm('Delete this job alert?')) return;
    var xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        var data = JSON.parse(this.responseText);
        if (data.success) {
            var row = document.getElementById('alert-row-' + id);
            if (row) {
                row.style.transition = 'opacity 0.3s';
                row.style.opacity = '0';
                setTimeout(function() { row.remove(); }, 300);
            }
        } else {
            alert('Could not delete alert.');
        }
    };
    xhttp.open('GET', BASE + '/controllers/JobController.php?action=deleteAlert&alert_id=' + id, true);
    xhttp.send();
}


window.onload = function() {
    if (document.getElementById('jobs')) {
        loadJobs();
    }
};