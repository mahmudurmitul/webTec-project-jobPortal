<?php
require_once "config.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Matching current shared DB sample password system
    $passwordhash = md5($password);

    $stmt = $conn->prepare("
        SELECT id, name, email, role, isactive, isverified
        FROM users
        WHERE email = ? AND passwordhash = ? AND role = 'employer'
    ");

    $stmt->bind_param("ss", $email, $passwordhash);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if ($user["isactive"] != 1) {
            $error = "Your account is inactive. Please contact admin.";
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
<body>

<div class="container">
    <div class="card" style="max-width:500px; margin:80px auto;">
        <h2>Employer Login</h2>

        <?php if (!empty($error)): ?>
            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="employer@test.com">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="password">
            </div>

            <button type="submit">Login</button>
        </form>
    </div>
</div>

</body>
</html>