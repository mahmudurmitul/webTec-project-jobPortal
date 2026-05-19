<div class="auth-wrap">
    <div class="auth-box">
        <div class="auth-header">
            <h1><i class="fas fa-headset"></i> RecruiterHub Pro</h1>
            <p>Professional recruitment management platform</p>
        </div>

        <?php
        $activeTab = 'login';
        if (!empty($errors)) $activeTab = isset($_POST['register']) ? 'register' : 'login';
        if (isset($_GET['msg']) && $_GET['msg'] === 'registered') $activeTab = 'login';
        ?>

        <div class="auth-tabs">
            <div class="auth-tab <?= $activeTab==='login'?'active':'' ?>"    onclick="showTab('login')">Sign In</div>
            <div class="auth-tab <?= $activeTab==='register'?'active':'' ?>" onclick="showTab('register')">Register</div>
        </div>

       
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'registered'): ?>
        <div class="alert alert-success" style="margin-bottom:16px;">
            <i class="fas fa-check-circle"></i>
            Account created successfully! Your account is <strong>pending admin verification</strong>.
            You will be able to log in once an admin approves your account.
        </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-error" style="margin-bottom:16px;">
            <?php foreach ($errors as $e): ?>
            <div><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

       
        <div id="tab-login" class="auth-section <?= $activeTab==='login'?'active':'' ?>">
            <form method="POST" action="index.php">
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" name="email" placeholder="recruiter@agency.com" required autofocus>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary" style="width:100%;justify-content:center;">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>
            <p style="margin-top:14px;text-align:center;font-size:12px;color:var(--muted);">
                <i class="fas fa-info-circle"></i>
                New accounts require admin verification before login is permitted.
            </p>
        </div>

    
        <div id="tab-register" class="auth-section <?= $activeTab==='register'?'active':'' ?>">
            <form method="POST" action="index.php">
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="name" placeholder="Jane Smith" required>
                    </div>
                    <div class="form-group">
                        <label>Agency / Recruiter Name *</label>
                        <input type="text" name="agency_name" placeholder="TalentBridge Recruitment" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" placeholder="jane@agency.com" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="tel" name="phone" placeholder="+88017xxxxxxxx" required>
                    </div>
                    <div class="form-group">
                        <label>Password * <span style="color:var(--muted);font-size:11px;">(min 6 characters)</span></label>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password *</label>
                        <input type="password" name="confirm" placeholder="••••••••" required>
                    </div>
                </div>
                <button type="submit" name="register" class="btn btn-primary" style="width:100%;justify-content:center;">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
                <p style="margin-top:12px;text-align:center;font-size:12px;color:var(--muted);">
                    <i class="fas fa-shield-alt"></i>
                    Your account will be reviewed by an admin before you can log in.
                </p>
            </form>
        </div>
    </div>
</div>