<?php
$editJob = null;
$editId  = (int)($_GET['edit'] ?? 0);
if ($editId) $editJob = getJobById($editId, $_SESSION['recruiter_id']);

// Only clients linked to a registered employer can post jobs
$linkedClients = array_filter($clients_list, fn($c) => !empty($c['employerid']));
?>
<div class="page-header">
    <h2><i class="fas fa-<?= $editJob ? 'edit' : 'plus-circle' ?>"></i> <?= $editJob ? 'Edit Job' : 'Post New Job' ?></h2>
    <p><?= $editJob ? 'Update the job posting details below' : 'Create a new job posting on behalf of a client' ?></p>
</div>

<?php if (empty($linkedClients) && !$editJob): ?>
<div class="alert alert-error">
    <i class="fas fa-exclamation-triangle"></i>
    You have no clients linked to a registered employer account.
    <a href="index.php?page=clients" style="color:#fca5a5;text-decoration:underline;">Add a client</a>
    and link them to a registered employer before posting a job.
</div>
<?php endif; ?>

<div class="card" style="max-width:920px;">
    <form method="POST" action="index.php">
        <?php if ($editJob): ?>
        <input type="hidden" name="job_id" value="<?= $editJob['id'] ?>">
        <?php endif; ?>

        <div class="form-row">

<<<<<<< HEAD
        
=======
            <!-- Client selection — shows CLIENT name from recruiterclients table -->
>>>>>>> 0e5b1b3773d675329dd7049dc83f42cfc694ce5f
            <?php if (!$editJob): ?>
            <div class="form-group">
                <label>Client Company * <span style="color:var(--muted);font-size:11px;">(posting on behalf of)</span></label>
                <select name="client_id" required>
                    <option value="">— Select Your Client —</option>
                    <?php foreach ($linkedClients as $c): ?>
                    <option value="<?= $c['id'] ?>">
                        <?= htmlspecialchars($c['companynameoverride']) ?>
                        <?php if ($c['regcompany'] && $c['regcompany'] !== $c['companynameoverride']): ?>
                        (<?= htmlspecialchars($c['regcompany']) ?>)
                        <?php endif; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <small style="color:var(--muted);">
                    Don't see your client? <a href="index.php?page=clients" style="color:var(--accent2);">Manage clients →</a>
                </small>
            </div>
            <?php else: ?>
            <div class="form-group">
                <label>Client</label>
                <input type="text" value="<?= htmlspecialchars($editJob['clientname']) ?>" disabled
                       style="opacity:0.6;cursor:not-allowed;">
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Job Title *</label>
                <input type="text" name="title"
                       value="<?= htmlspecialchars($editJob['title'] ?? '') ?>"
                       placeholder="e.g. Senior PHP Developer" required>
            </div>

            <div class="form-group">
                <label>Category *</label>
                <select name="category_id" required>
                    <option value="">— Select Category —</option>
                    <?php foreach ($categories_list as $cat): ?>
                    <option value="<?= $cat['id'] ?>"
                            <?= ($editJob['categoryid'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Location *</label>
                <input type="text" name="location"
                       value="<?= htmlspecialchars($editJob['location'] ?? '') ?>"
                       placeholder="Dhaka, Bangladesh" required>
            </div>

            <div class="form-group">
                <label>Job Type *</label>
                <select name="job_type" required>
                    <option value="">— Select —</option>
                    <?php foreach (['full-time'=>'Full Time','part-time'=>'Part Time','remote'=>'Remote','contract'=>'Contract'] as $val => $label): ?>
                    <option value="<?= $val ?>"
                            <?= ($editJob['jobtype'] ?? '') === $val ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Experience Level *</label>
                <select name="exp_level" required>
                    <option value="">— Select —</option>
                    <?php foreach (['entry'=>'Entry Level','mid'=>'Mid Level','senior'=>'Senior Level'] as $val => $label): ?>
                    <option value="<?= $val ?>"
                            <?= ($editJob['experiencelevel'] ?? '') === $val ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Min Salary (৳)</label>
                <input type="number" name="salary_min"
                       value="<?= $editJob['salarymin'] ?? '' ?>"
                       placeholder="30000" min="0">
            </div>

            <div class="form-group">
                <label>Max Salary (৳)</label>
                <input type="number" name="salary_max"
                       value="<?= $editJob['salarymax'] ?? '' ?>"
                       placeholder="60000" min="0">
            </div>

            <div class="form-group">
                <label>Application Deadline *</label>
                <input type="date" name="deadline"
                       value="<?= $editJob['deadline'] ?? '' ?>"
                       min="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
                <label>Publish Status</label>
                <select name="status">
                    <option value="draft"   <?= ($editJob['status'] ?? 'draft')==='draft'  ?'selected':'' ?>>Save as Draft</option>
                    <option value="active"  <?= ($editJob['status'] ?? '')==='active'       ?'selected':'' ?>>Publish (Active)</option>
                    <option value="closed"  <?= ($editJob['status'] ?? '')==='closed'       ?'selected':'' ?>>Closed</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Job Description</label>
            <textarea name="description" rows="5"
                      placeholder="Detailed role description, responsibilities, company culture..."><?= htmlspecialchars($editJob['description'] ?? '') ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Requirements</label>
                <textarea name="requirements" rows="4"
                          placeholder="Required skills, experience, qualifications..."><?= htmlspecialchars($editJob['requirements'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>Benefits</label>
                <textarea name="benefits" rows="4"
                          placeholder="Salary, perks, insurance, leave policy..."><?= htmlspecialchars($editJob['benefits'] ?? '') ?></textarea>
            </div>
        </div>

        <div style="display:flex;gap:12px;margin-top:10px;">
            <button type="submit" name="save_job" class="btn btn-primary">
                <i class="fas fa-<?= $editJob ? 'save' : 'paper-plane' ?>"></i>
                <?= $editJob ? 'Update Job' : 'Post Job' ?>
            </button>
            <a href="index.php?page=jobs" class="btn btn-ghost"><i class="fas fa-times"></i> Cancel</a>
        </div>
    </form>
</div>