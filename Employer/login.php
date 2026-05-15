<?php
require_once "config.php";
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email=trim($_POST["email"]);
    $password=trim($_POST["password"]);
   $stmt = $conn->prepare("
   SELECT id, name, email, passwordhash, role, isactive ,isverified
   FROM users
   WHERE email = ? AND role = 'employer'
   ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (!password_verify($password, $user["passwordhash"])) {
            $error = "Invalid employer email or password.";
        } elseif ($user["isactive"] != 1) {
            $error = "Your account is inactive. Please contact admin.";
        } elseif ($user["isverified"] != 1) {
            $error = "Your account is not verified. Please wait for verification.";
        } else {
            $_SESSION["userid"] = $user["id"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role"];
            header("Location: index.php");
            exit();
        }
    } else {
       $error = "Invalid employer email or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employer Login</title>
    <link rel="stylesheet" href="views/style.css">
</head>
<div class="container">
   <div class="card" style="max-width:500px; margin:80px auto;">
        <h2>Employer Login</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required placeholder="employer@test.com">
                </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required placeholder="password">
        </div>
        <button type="submit" form="login">Login</button>

        </form>
    </div>
</div>
</body>
</html>