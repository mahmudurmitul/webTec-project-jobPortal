<?php
require_once __DIR__ . "/config.php";

if (isset($_SESSION['userid']) && ($_SESSION['role'] ?? '') === 'employer') {
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $hash     = md5($password);

    $sql = "
        SELECT id, name, email, role, isactive, isverified
        FROM users
        WHERE email = ?
          AND passwordhash = ?
          AND role = 'employer'
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("SQL Prepare Error: " . $conn->error);
    }

    $stmt->bind_param("ss", $email, $hash);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if ((int)$user["isactive"] !== 1) {
            $error = "Your account is inactive or suspended. Please contact admin.";
        } elseif ((int)$user["isverified"] !== 1) {
            $error = "Your account is pending admin verification. Please wait for approval.";
        } else {
            $_SESSION["userid"] = $user["id"];
            $_SESSION["name"]   = $user["name"];
            $_SESSION["email"]  = $user["email"];
            $_SESSION["role"]   = $user["role"];

            header("Location: index.php");
            exit();
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employer Login</title>
    <link rel="stylesheet" href="views/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-box">
        <div class="auth-header">
            <h1><i class="fas fa-briefcase"></i> HireDesk</h1>
            <p>Employer Portal — Sign in to your account</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-circle-exclamation"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="employer@test.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px;">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>

        <p style="text-align:center;margin-top:16px;font-size:13px;color:var(--text-muted)">
            New employer?
            <a href="views/register.php" style="color:var(--accent2)">Register your company</a>
        </p>
    </div>
</div>
</body>
</html>