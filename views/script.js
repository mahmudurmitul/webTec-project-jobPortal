
function toggleFeatured(jobId, newVal) {
    const btn = document.getElementById('feat-btn-' + jobId);
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; }

    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            if (res.success && btn) {
                if (newVal == 1) {
                    btn.className = 'btn btn-xs btn-primary';
                    btn.innerHTML = '<i class="fas fa-star"></i> Featured';
                    btn.setAttribute('onclick', `toggleFeatured(${jobId}, 0)`);
                    btn.title = 'Remove featured';
                } else {
                    btn.className = 'btn btn-xs btn-ghost';
                    btn.innerHTML = '<i class="fas fa-star"></i> Feature';
                    btn.setAttribute('onclick', `toggleFeatured(${jobId}, 1)`);
                    btn.title = 'Mark as featured';
                }
                btn.disabled = false;
            }
        }
    };
    xhttp.open('GET', `controller.php?action=toggleFeatured&job_id=${jobId}&val=${newVal}`, true);
    xhttp.send();
}


function toggleUser(uid, active) {
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            if (res.success) window.location.reload();
        }
    };
    xhttp.open('GET', `controller.php?action=toggleUser&uid=${uid}&active=${active}`, true);
    xhttp.send();
}


function toggleResolve(id) {
    const box = document.getElementById('resolve-' + id);
    if (box) box.classList.toggle('open');
}


document.addEventListener('DOMContentLoaded', function() {
    // Auto-close success alerts after 4 seconds
    const alert = document.querySelector('.alert-success');
    if (alert) {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    }
});
