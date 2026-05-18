<?php
$cats = getCategories();
$editCat = null;
if (isset($_GET['edit_cat'])) {
    $editCat = getCategoryById((int)$_GET['edit_cat']);
}
?>
<div class="page-header">
    <h2><i class="fas fa-tags"></i> Job Categories</h2>
    <p>Manage job category taxonomy — deletion blocked if active jobs reference the category</p>
</div>

<div style="display:grid;grid-template-columns:1.2fr 2fr;gap:22px;">

   
    <div>
        <div class="card">
            <h3><i class="fas fa-<?= $editCat ? 'edit' : 'plus' ?>"></i> <?= $editCat ? 'Edit Category' : 'Add Category' ?></h3>
            <form method="POST">
                <?php if ($editCat): ?>
                <input type="hidden" name="cat_id" value="<?= $editCat['id'] ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label>Category Name *</label>
                    <input type="text" name="cat_name" value="<?= htmlspecialchars($editCat['name'] ?? '') ?>" placeholder="e.g. Information Technology" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="cat_desc" rows="3" placeholder="Brief description..."><?= htmlspecialchars($editCat['description'] ?? '') ?></textarea>
                </div>
                <div style="display:flex;gap:8px;">
                    <?php if ($editCat): ?>
                    <button type="submit" name="update_category" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Update</button>
                    <a href="?page=categories" class="btn btn-ghost btn-sm"><i class="fas fa-times"></i> Cancel</a>
                    <?php else: ?>
                    <button type="submit" name="add_category" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Category</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

   
    <div class="card">
        <h3><i class="fas fa-list"></i> All Categories (<?= count($cats) ?>)</h3>
        <?php if (empty($cats)): ?>
        <div class="empty-state"><i class="fas fa-tags"></i><p>No categories yet.</p></div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead><tr><th>#</th><th>Name</th><th>Description</th><th>Active Jobs</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($cats as $i => $cat): ?>
                <tr>
                    <td class="text-muted"><?= $i+1 ?></td>
                    <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                    <td class="text-muted"><?= htmlspecialchars(mb_strimwidth($cat['description'] ?? '', 0, 50, '...')) ?></td>
                    <td>
                        <span style="color:<?= $cat['jobcount']>0?'var(--green)':'var(--muted)' ?>;font-weight:600;"><?= $cat['jobcount'] ?></span>
                    </td>
                    <td style="white-space:nowrap;">
                        <a href="?page=categories&edit_cat=<?= $cat['id'] ?>" class="btn btn-ghost btn-xs"><i class="fas fa-edit"></i> Edit</a>
                        <?php if ($cat['jobcount'] == 0): ?>
                        <a href="?delete_category=<?= $cat['id'] ?>" class="btn btn-danger btn-xs" onclick="return confirm('Delete this category?')"><i class="fas fa-trash"></i></a>
                        <?php else: ?>
                        <span class="btn btn-ghost btn-xs" style="opacity:0.35;cursor:not-allowed;" title="Has active jobs"><i class="fas fa-lock"></i></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
