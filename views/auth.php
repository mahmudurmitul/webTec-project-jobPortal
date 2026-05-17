<div class="auth-wrap">
    <div class="auth-box">
        <div class="auth-header">
            <h1><i class="fas fa-headset"></i> RecruiterHub Pro</h1>
            <p>The professional recruitment management platform</p>
        </div>

        <div class="auth-tabs">
            <div class="auth-tab active" onclick="showTab('login')">Login</div>
            <div class="auth-tab" onclick="showTab('register')">Register</div>
        </div>

        <!-- Login -->
        <div id="tab-login" class="auth-section active">
            <form method="POST">
                <div class="auth-cols">
                    <div>
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email Address</label>
                            <input type="email" name="email" placeholder="recruiter@agency.com" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Password</label>
                            <input type="password" name="password" placeholder="••••••••" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary" style="width:100%;">
                            <i class="fas fa-arrow-right"></i> Sign In
                        </button>
                    </div>
                    <div style="display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;padding:20px;background:rgba(124,58,237,0.08);border-radius:12px;border:1px solid rgba(124,58,237,0.2);">
                        <i class="fas fa-headset" style="font-size:48px;color:#a855f7;margin-bottom:16px;opacity:0.7;"></i>
                        <h3 style="font-family:'Syne',sans-serif;color:#e2e8f0;margin-bottom:8px;">Welcome Back</h3>
                        <p style="color:#64748b;font-size:13px;">Manage your clients, post jobs, find top talent, and track your placements — all in one place.</p>
                    </div>
                </div>
            </form>
        </div>

        <!-- Register -->
        <div id="tab-register" class="auth-section">
            <form method="POST">
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
                        <input type="email" name="email" placeholder="jane@talentbridge.com" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="tel" name="phone" placeholder="+88017xxxxxxxx" required>
                    </div>
                    <div class="form-group">
                        <label>Password * (min 6 chars)</label>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password *</label>
                        <input type="password" name="confirm" placeholder="••••••••" required>
                    </div>
                </div>
                <button type="submit" name="register" class="btn btn-primary" style="width:100%;">
                    <i class="fas fa-user-plus"></i> Create Recruiter Account
                </button>
                <p style="color:#64748b;font-size:12px;text-align:center;margin-top:12px;">
                    <i class="fas fa-info-circle"></i> Account requires admin verification before full access.
                </p>
            </form>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-error" style="margin-top:20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <?php foreach ($errors as $e): ?><div><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>