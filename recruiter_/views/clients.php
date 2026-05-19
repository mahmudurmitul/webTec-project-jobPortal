<div class="page-header">
    <h2><i class="fas fa-building"></i> Client Companies</h2>
    <p>Manage the companies you recruit for</p>
</div>



<div class="card">
    <h3><i class="fas fa-plus-circle"></i> Add Client</h3>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label>Company Name *</label>
                <input type="text" name="company_name_override" placeholder="e.g. DataSoft Ltd." required>
            </div>
            <div class="form-group">
                <label>Link to Registered Employer (Optional)</label>
                <select name="employer_id">
                    <option value="">— Not registered on platform —</option>
                    <?php foreach ($employers_list as $emp): ?>
                    <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['companyname'] . ' (' . $emp['name'] . ')') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" name="add_client" class="btn btn-primary"><i class="fas fa-plus"></i> Add Client</button>
    </form>
</div>



<div class="card">
    <h3><i class="fas fa-list"></i> Your Clients (<?= count($clients_list) ?>)</h3>
    <?php if (empty($clients_list)): ?>
    <div class="empty-state">
        <i class="fas fa-building"></i>
        <p>No clients yet. Add your first client above.</p>
    </div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Company Name</th>
                    <th>Registered Employer</th>
                    <th>Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($clients_list as $i => $client): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><strong><?= htmlspecialchars($client['companynameoverride']) ?></strong></td>
                <td>
                    <?php if ($client['regcompany']): ?>
                    <span style="color:var(--green);">
                        <i class="fas fa-link"></i> <?= htmlspecialchars($client['regcompany']) ?>
                    </span>
                    <?php else: ?>
                    <span class="text-muted"><i class="fas fa-unlink"></i> Standalone</span>
                    <?php endif; ?>
                </td>
                <td><?= date('d M Y', strtotime($client['addedat'])) ?></td>
                <td>
                    <a href="?page=analytics&client=<?= $client['employerid'] ?? '' ?>" class="btn btn-ghost btn-xs"><i class="fas fa-chart-bar"></i> Report</a>
                    <a href="?delete_client=<?= $client['id'] ?>" class="btn btn-danger btn-xs" onclick="return confirm('Remove this client?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>