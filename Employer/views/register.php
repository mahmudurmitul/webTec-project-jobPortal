<?php
require_once __DIR__ . "/../config.php";

if (isset($_SESSION['userid']) && $_SESSION['role'] === 'employer') {
    header("Location: index.php"); exit();
}

$error = $success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name        = trim($_POST['name']);
    $email       = trim($_POST['email']);
    $phone       = trim($_POST['phone']);
    $password    = trim($_POST['password']);
    $confirm     = trim($_POST['confirm']);
    $companyname = trim($_POST['companyname']);
    $industry    = trim($_POST['industry']);

    if (!$name || !$email || !$password || !$companyname) {
        $error = "Name, email, password, and company name are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Check email uniqueness
        $chk = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $chk->bind_param("s", $email);
        $chk->execute();
        if ($chk->get_result()->num_rows > 0) {
            $error = "An account with this email already exists.";
        } else {
            $hash = md5($password);
            // Insert user (isactive=0 until admin approves, isverified=0)
            $stmt = $conn->prepare("INSERT INTO users (name, email, passwordhash, phone, role, isactive, isverified, createdat) VALUES (?, ?, ?, ?, 'employer', 0, 0, NOW())");
            $stmt->bind_param("ssss", $name, $email, $hash, $phone);
            if ($stmt->execute()) {
                $userid = $conn->insert_id;
                // Create initial employer profile
                $ep = $conn->prepare("INSERT INTO employerprofiles (userid, companyname, industry, companysize, description, website, address, logopath) VALUES (?, ?, ?, '', '', '', '', '')");
                $ep->bind_param("iss", $userid, $companyname, $industry);
                $ep->execute();
                $success = "Registration successful! Your account is pending admin verification. You will be able to log in once approved.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employer Registration</title>
     <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-box" style="max-width:520px">
        <div class="auth-header">
            <h1><i class="fas fa-briefcase"></i> HireDesk</h1>
            <p>Employer Portal — Create your company account</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><i class="fas fa-circle-exclamation"></i><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><i class="fas fa-check"></i><?= htmlspecialchars($success) ?></div>
            <p style="text-align:center;margin-top:12px"><a href="login.php" style="color:var(--accent2)">Back to Login</a></p>
        <?php else: ?>

        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Password * <span class="text-muted">(min 6 chars)</span></label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="confirm" required>
                </div>
            </div>

            <hr style="border-color:var(--border);margin:16px 0">
            <p style="font-size:13px;color:var(--text-muted);margin-bottom:12px"><i class="fas fa-building"></i> Company Details</p>

            <div class="form-row">
                <div class="form-group">
                    <label>Company Name *</label>
                    <input type="text" name="companyname" required value="<?= htmlspecialchars($_POST['companyname'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Industry</label>
                    <input type="text" name="industry" value="<?= htmlspecialchars($_POST['industry'] ?? '') ?>" placeholder="e.g. Technology">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px;">
                <i class="fas fa-user-plus"></i> Register Company
            </button>
        </form>

        <p style="text-align:center;margin-top:16px;font-size:13px;color:var(--text-muted)">
            Already have an account? <a href="login.php" style="color:var(--accent2)">Sign in</a>
        </p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>