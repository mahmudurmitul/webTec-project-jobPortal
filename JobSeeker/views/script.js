// Enhanced Professional JS
function buildTable(jobs) {
    if (jobs.length === 0) {
        return '<div style="text-align:center;padding:60px;color:#b0b0b0;"><i class="fas fa-inbox" style="font-size:64px;margin-bottom:20px;opacity:0.5;"></i><h3>No jobs found</h3><p>Try adjusting your search filters</p></div>';
    }
    
    let table = `
        <table>
            <thead>
                <tr>
                    <th><i class="fas fa-briefcase"></i> Job Title</th>
                    <th><i class="fas fa-tags"></i> Category</th>
                    <th><i class="fas fa-map-marker-alt"></i> Location</th>
                    <th><i class="fas fa-dollar-sign"></i> Salary</th>
                    <th><i class="fas fa-clock"></i> Type</th>
                    <th><i class="fas fa-user-tie"></i> Experience</th>
                    <th><i class="fas fa-calendar-alt"></i> Deadline</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    jobs.forEach(job => {
        table += `
            <tr>
                <td style="font-weight:600;">${job.title}</td>
                <td><span class="badge">${job.catname}</span></td>
                <td><i class="fas fa-map-marker-alt" style="color:#00d4ff;"></i> ${job.location}</td>
                <td class="salary">৳${parseFloat(job.salarymin).toLocaleString()} - ৳${parseFloat(job.salarymax).toLocaleString()}</td>
                <td><span class="type-badge type-${job.jobtype}">${job.jobtype.replace('-', ' ').toUpperCase()}</span></td>
                <td>${job.experiencelevel}</td>
                <td>${new Date(job.deadline).toLocaleDateString('en-GB')}</td>
                <td>
                    <button class="apply-btn" onclick="apply(${job.id}, '${job.title}')">
                        <i class="fas fa-paper-plane"></i> Apply
                    </button>
                </td>
            </tr>
        `;
    });
    
    table += '</tbody></table>';
    document.getElementById('job-count').textContent = `(${jobs.length} jobs)`;
    return table;
}

// Load Jobs with Loading State
function loadJobs() {
    document.getElementById('jobs').innerHTML = '<div style="text-align:center;padding:40px;"><i class="fas fa-spinner fa-spin" style="font-size:48px;color:#00d4ff;"></i><p>Loading jobs...</p></div>';
    
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const jobs = JSON.parse(this.responseText);
            document.getElementById('jobs').innerHTML = buildTable(jobs);
        }
    }
    xhttp.open('GET', '../controller.php?action=getJobs', true);
    xhttp.send();
}

// Filter with Animation
function filterJobs() {
    const cat = document.getElementById('category').value;
    const loc = document.getElementById('location').value;
    const typ = document.getElementById('type').value;
    
    document.getElementById('jobs').innerHTML = '<div style="text-align:center;padding:40px;"><i class="fas fa-search fa-beat-fade" style="font-size:48px;color:#00d4ff;"></i><p>Searching jobs...</p></div>';
    
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const jobs = JSON.parse(this.responseText);
            document.getElementById('jobs').innerHTML = buildTable(jobs);
        }
    }
    xhttp.open('GET', `../controller.php?action=filterJobs&category=${cat}&location=${encodeURIComponent(loc)}&type=${typ}`, true);
    xhttp.send();
}

function apply(jobId, jobTitle) {
    if (confirm(`Apply to "${jobTitle}"?\n\nYou'll need to add cover letter & resume`)) {
        // Simulate application
        const btn = event.target.closest('button');
        btn.innerHTML = '<i class="fas fa-check"></i> Applied!';
        btn.style.background = '#10b981';
        btn.disabled = true;
        
        setTimeout(() => {
            alert(`✅ Application submitted for "${jobTitle}"\nStatus: Submitted\nCheck your dashboard soon!`);
        }, 500);
    }
}

// Auto load on dashboard
if (document.getElementById('jobs')) {
    window.addEventListener('load', loadJobs);
}