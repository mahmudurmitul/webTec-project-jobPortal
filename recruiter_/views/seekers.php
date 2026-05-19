<div class="page-header">
    <h2><i class="fas fa-search"></i> Find Candidates</h2>
    <p>Search the seeker database and reach out to top talent</p>
</div>


<div class="card">
    <h3><i class="fas fa-filter"></i> Search Filters</h3>
    <div class="form-row">
        <div class="form-group">
            <label>Keyword (Skills / Headline)</label>
            <input type="text" id="sk-keyword" placeholder="PHP, React, Marketing...">
        </div>
        <div class="form-group">
            <label>Preferred Location</label>
            <input type="text" id="sk-location" placeholder="Dhaka, Chittagong...">
        </div>
        <div class="form-group">
            <label>Min. Years Experience</label>
            <select id="sk-exp">
                <option value="">Any</option>
                <option value="1">1+</option>
                <option value="2">2+</option>
                <option value="3">3+</option>
                <option value="5">5+</option>
                <option value="10">10+</option>
            </select>
        </div>
        <div class="form-group">
            <label>Max Expected Salary (৳)</label>
            <input type="number" id="sk-salary" placeholder="80000">
        </div>
    </div>
    <div style="display:flex;gap:10px;">
        <button class="btn btn-primary" onclick="searchSeekers()"><i class="fas fa-search"></i> Search Candidates</button>
        <button class="btn btn-ghost" onclick="clearSearch()"><i class="fas fa-redo"></i> Clear</button>
    </div>
</div>


<div class="card">
    <div class="flex-between" style="margin-bottom:16px;">
        <h3><i class="fas fa-users"></i> Results <span id="seeker-count" style="color:var(--accent2);"></span></h3>
    </div>
    <div id="seeker-results">
        <div class="empty-state">
            <i class="fas fa-search"></i>
            <p>Use the filters above to search for candidates.</p>
        </div>
    </div>
</div>