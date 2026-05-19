function toggleJobStatus(jobId) {
    if (!confirm("Toggle this job's status?")) return;

    fetch("api/toggle_job_status.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "jobid=" + jobId
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const el = document.getElementById("status-" + jobId);
            const s  = data.newstatus;
            el.textContent = s.charAt(0).toUpperCase() + s.slice(1);
            el.className   = "badge badge-" + s;
        } else {
            alert(data.message);
        }
    });
}

function updateApplicationStatus(applicationId, status) {
    fetch("../api/update_application_status.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "applicationid=" + applicationId + "&status=" + status
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) alert(data.message);
    });
}
