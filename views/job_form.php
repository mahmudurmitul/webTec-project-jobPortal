<?php
$editJob = null;
$editId  = (int)($_GET['edit'] ?? 0);
if ($editId) {
    $editJob = getJobById($editId, $_SESSION['recruiter_id']);
}
?>
<div class="page-header">
    <h2><i class="fas fa-<?= $editJob ? 'edit' : 'plus-circle' ?>"></i> <?= $editJob ? 'Edit Job' : 'Post New Job' ?></h2>
    <p><?= $editJob ? 'Update job posting details' : 'Create a new job posting for a client' ?></p>
</div>

<div class="card" style="max-width:900px;">
    <form method="POST">
        <?php if ($editJob): ?>
        <input type="hidden" name="job_id" value="<?= $editJob['id'] ?>">
        <input type="hidden" name="client_employer_id" value="<?= $editJob['employerid'] ?>">
        <?php endif; ?>

        <div class="form-row">
            <?php if (!$editJob): ?>
            <div class="form-group">
                <label>Client Company *</label>
                <select name="client_employer_id" required>
                    <option value="">— Select Client —</option>
                    <?php foreach ($clients_list as $c): ?>
                    <?php if ($c['employerid']): ?>
                    <option value="<?= $c['employerid'] ?>"><?= htmlspecialchars($c['companynameoverride']) ?></option>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    <?php
                    // also offer registered employers directly
                    foreach ($employers_list as $emp):
                    ?>
                    <option value="<?= $emp['id'] ?>">[Direct] <?= htmlspecialchars($emp['companyname']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Job Title *</label>
                <input type="text" name="title" value="<?= htmlspecialchars($editJob['title'] ?? '') ?>" placeholder="e.g. Senior PHP Developer" required>
            </div>

            <div class="form-group">
                <label>Category *</label>
                <select name="category_id" required>
                    <option value="">— Select Category —</option>
                    <?php foreach ($categories_list as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($editJob['categoryid'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Location *</label>
                <input type="text" name="location" value="<?= htmlspecialchars($editJob['location'] ?? '') ?>" placeholder="Dhaka, Bangladesh" required>
            </div>

            <div class="form-group">
                <label>Job Type *</label>
                <select name="job_type" required>
                    <option value="">— Select —</option>
                    <?php foreach (['full-time','part-time','remote','contract'] as $jt): ?>
                    <option value="<?= $jt ?>" <?= ($editJob['jobtype'] ?? '') === $jt ? 'selected' : '' ?>>
                        <?= ucwords(str_replace('-',' ',$jt)) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Experience Level *</label>
                <select name="exp_level" required>
                    <option value="">— Select —</option>
                    <?php foreach (['entry','mid','senior'] as $el): ?>
                    <option value="<?= $el ?>" <?= ($editJob['experiencelevel'] ?? '') === $el ? 'selected' : '' ?>>
                        <?= ucfirst($el) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Min Salary (৳)</label>
                <input type="number" name="salary_min" value="<?= $editJob['salarymin'] ?? '' ?>" placeholder="30000" min="0">
            </div>

            <div class="form-group">
                <label>Max Salary (৳)</label>
                <input type="number" name="salary_max" value="<?= $editJob['salarymax'] ?? '' ?>" placeholder="60000" min="0">
            </div>

            <div class="form-group">
                <label>Application Deadline *</label>
                <input type="date" name="deadline" value="<?= $editJob['deadline'] ?? '' ?>" min="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="draft" <?= ($editJob['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Save as Draft</option>
                    <option value="active" <?= ($editJob['status'] ?? '') === 'active' ? 'selected' : '' ?>>Publish Active</option>
                    <option value="closed" <?= ($editJob['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Closed</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Job Description</label>
            <textarea name="description" rows="5" placeholder="Detailed description of the role, responsibilities, company culture..."><?= htmlspecialchars($editJob['description'] ?? '') ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Requirements</label>
                <textarea name="requirements" rows="4" placeholder="Required skills, experience, qualifications..."><?= htmlspecialchars($editJob['requirements'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>Benefits</label>
                <textarea name="benefits" rows="4" placeholder="Salary, perks, insurance, leave policy..."><?= htmlspecialchars($editJob['benefits'] ?? '') ?></textarea>
            </div>
        </div>

        <div style="display:flex;gap:12px;margin-top:8px;">
            <button type="submit" name="save_job" class="btn btn-primary">
                <i class="fas fa-<?= $editJob ? 'save' : 'plus' ?>"></i> <?= $editJob ? 'Update Job' : 'Post Job' ?>
            </button>
            <a href="?page=jobs" class="btn btn-ghost"><i class="fas fa-times"></i> Cancel</a>
        </div>
    </form>
</div>