<div class="page-header">
    <h2><i class="fas fa-flag"></i> Submit Complaint</h2>
    <p>Report a seeker or employer to the platform administrator</p>
</div>

<div class="card" style="max-width:700px;">
    <h3><i class="fas fa-exclamation-triangle"></i> File a Complaint</h3>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label>Subject User ID * <span class="text-muted">(User ID of the person you're reporting)</span></label>
                <input type="number" name="subject_id" placeholder="e.g. 15" required min="1">
            </div>
        </div>
        <div class="form-group">
            <label>Description *</label>
            <textarea name="description" rows="6" placeholder="Describe the issue in detail: what happened, when, and any evidence you can provide..." required></textarea>
        </div>
        <div style="display:flex;gap:12px;">
            <button type="submit" name="submit_complaint" class="btn btn-danger"><i class="fas fa-flag"></i> Submit Complaint</button>
            <a href="?page=dashboard" class="btn btn-ghost"><i class="fas fa-times"></i> Cancel</a>
        </div>
    </form>
    <div class="alert alert-success" style="margin-top:20px;background:rgba(59,130,246,0.1);border-color:rgba(59,130,246,0.3);color:#93c5fd;">
        <i class="fas fa-info-circle"></i>
        All complaints are reviewed by the platform admin. You can expect a response within 48 hours.
    </div>
</div>