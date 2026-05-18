<div class="login-wrap">
    <div class="login-box">
        <div class="login-logo">
            <i class="fas fa-shield-halved"></i>
            <h1>Admin Portal</h1>
            <p>JobPortal Platform Administration</p>
        </div>

        <form method="POST">
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Administrator Email</label>
                <input type="email" name="email" placeholder="admin@jobportal.com" required autofocus>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;">
                <i class="fas fa-sign-in-alt"></i> Sign In to Dashboard
            </button>
        </form>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-error" style="margin-top:16px;">
            <?php foreach ($errors as $e): ?>
            <div><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <p style="text-align:center;color:var(--muted);font-size:11px;margin-top:20px;">
            <i class="fas fa-lock"></i> Restricted access — authorised personnel only
        </p>
    </div>
</div>
