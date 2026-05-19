function searchTalent() {
    let kw = document.getElementById('seekerSearch').value;
    let resultsDiv = document.getElementById('seekerResults');

    if(kw.length < 2) {
        resultsDiv.innerHTML = "<p>Type at least 2 characters...</p>";
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('GET', `controller.php?action=searchSeekers&keyword=${encodeURIComponent(kw)}`, true);
    
    xhr.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200) {
            const seekers = JSON.parse(this.responseText);
            let html = '<table style="width:100%"><thead><tr><th>Name</th><th>Headline</th><th>Skills</th><th>Action</th></tr></thead><tbody>';
            
            if(seekers.length === 0) {
                html = "<p>No matching candidates found.</p>";
            } else {
                seekers.forEach(s => {
                    html += `<tr>
                        <td>${s.name}</td>
                        <td>${s.headline}</td>
                        <td><span class="badge">${s.skills}</span></td>
                        <td><button class="apply-btn" onclick="alert('Outreach sent to ${s.name}!')">Reach Out</button></td>
                    </tr>`;
                });
                html += '</tbody></table>';
            }
            resultsDiv.innerHTML = html;
        }
    };
    xhr.send();
}