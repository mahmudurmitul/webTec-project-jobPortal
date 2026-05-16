function toggleJobStatus(jobId) {
    if (!confirm("Do you want to change this job status?")) {
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "api/toggle_job_status.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);

            if (response.success) {
                const statusElement = document.getElementById("status-" + jobId);
                statusElement.innerText =
                    response.newstatus.charAt(0).toUpperCase() + response.newstatus.slice(1);
                statusElement.className = "status-" + response.newstatus;
            } else {
                alert(response.message);
            }
        }
    };

    xhr.send("jobid=" + jobId);
}