/* LOAD JOBS */
function loadJobs() {

    let jobs = document.getElementById("jobs");
    jobs.innerHTML = "Loading...";

    let xhttp = new XMLHttpRequest();

    xhttp.onload = function () {
        let data = JSON.parse(this.responseText);

        let table = "<table border='1' width='100%'>";
        table += "<tr><th>Title</th><th>Company</th><th>Category</th><th>Location</th><th>Salary</th><th>Type</th><th>Experience</th><th>Deadline</th><th>Action</th></tr>";

        for (let i = 0; i < data.length; i++) {
            table += "<tr>";
            table += "<td>" + data[i].title + "</td>";
            table += "<td>" + data[i].company_name + "</td>";
            table += "<td>" + data[i].catname + "</td>";
            table += "<td>" + data[i].location + "</td>";
            table += "<td>" + data[i].salary_min + " - " + data[i].salary_max + "</td>";
            table += "<td>" + data[i].job_type + "</td>";
            table += "<td>" + data[i].experiencelevel + "</td>";
            table += "<td>" + data[i].deadline + "</td>";
            table += "<td>";
            table += "<button onclick='saveJob(" + data[i].id + ")'>Save</button> ";
            table += "<a href='views/job.php?id=" + data[i].id + "'>View</a>";
            table += "</td>";
            table += "</tr>";
        }

        table += "</table>";
        jobs.innerHTML = table;
    };

    xhttp.open("GET", "controllers/JobController.php?action=getJobs", true);
    xhttp.send();
}


/* FILTER JOBS */
function filterJobs() {

    let category = document.getElementById("category").value;
    let location = document.getElementById("location").value;
    let type     = document.getElementById("type").value;
    let exp      = document.getElementById("exp").value;
    let sal_min  = document.getElementById("sal_min").value;
    let sal_max  = document.getElementById("sal_max").value;
    let keyword  = document.getElementById("keyword").value;

    let jobs = document.getElementById("jobs");
    jobs.innerHTML = "Searching...";

    let xhttp = new XMLHttpRequest();

    xhttp.onload = function () {
        let data = JSON.parse(this.responseText);

        let table = "<table border='1' width='100%'>";
        table += "<tr><th>Title</th><th>Company</th><th>Category</th><th>Location</th><th>Salary</th><th>Type</th><th>Experience</th><th>Deadline</th><th>Action</th></tr>";

        for (let i = 0; i < data.length; i++) {
            table += "<tr>";
            table += "<td>" + data[i].title + "</td>";
            table += "<td>" + data[i].company_name + "</td>";
            table += "<td>" + data[i].catname + "</td>";
            table += "<td>" + data[i].location + "</td>";
            table += "<td>" + data[i].salary_min + " - " + data[i].salary_max + "</td>";
            table += "<td>" + data[i].job_type + "</td>";
            table += "<td>" + data[i].experiencelevel + "</td>";
            table += "<td>" + data[i].deadline + "</td>";
            table += "<td>";
            table += "<button onclick='saveJob(" + data[i].id + ")'>Save</button> ";
            table += "<a href='views/job.php?id=" + data[i].id + "'>View</a>";
            table += "</td>";
            table += "</tr>";
        }

        table += "</table>";
        jobs.innerHTML = table;
    };

    xhttp.open(
        "GET",
        "controllers/JobController.php?action=filterJobs" +
        "&category=" + category +
        "&location=" + location +
        "&type=" + type +
        "&exp=" + exp +
        "&sal_min=" + sal_min +
        "&sal_max=" + sal_max +
        "&keyword=" + keyword,
        true
    );

    xhttp.send();
}


/* RESET FILTERS */
function resetFilters() {
    document.getElementById("category").value = "";
    document.getElementById("location").value = "";
    document.getElementById("type").value = "";
    document.getElementById("exp").value = "";
    document.getElementById("sal_min").value = "";
    document.getElementById("sal_max").value = "";
    document.getElementById("keyword").value = "";
    loadJobs();
}


/* SAVE JOB (from index page) */
function saveJob(id) {
    let xhttp = new XMLHttpRequest();
    xhttp.onload = function () {
        let data = JSON.parse(this.responseText);
        if (data.error) { alert(data.error); return; }
        alert(data.saved ? "Job saved!" : "Job removed from saved.");
    };
    xhttp.open("GET", "controllers/JobController.php?action=toggleSave&job_id=" + id, true);
    xhttp.send();
}


/* WITHDRAW APPLICATION */
function withdrawApp(btn, id) {
    if (!confirm("Withdraw this application?")) return;

    let xhttp = new XMLHttpRequest();
    xhttp.onload = function () {
        let data = JSON.parse(this.responseText);
        if (data.success) {
            const row = btn.closest("tr");
            if (row) {
                row.style.transition = "opacity 0.3s";
                row.style.opacity = "0";
                setTimeout(() => row.remove(), 300);
            }
        } else {
            alert("Could not withdraw application.");
        }
    };
    xhttp.open("GET", "../controllers/JobController.php?action=withdraw&app_id=" + id, true);
    xhttp.send();
}


/* AUTO LOAD */
window.onload = function () {
    if (document.getElementById("jobs")) {
        loadJobs();
    }
};
