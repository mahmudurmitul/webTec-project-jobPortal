<?php
require_once "../config.php";

employerOnlyFrom();

$userid = $_SESSION['userid'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subjectid = $_POST['subjectid'];
    $description = trim($_POST['description']);

    if ($description != "") {
        $stmt = $conn->prepare("
            INSERT INTO complaints
            (submitterid, subjectid, description, status, createdat)
            VALUES (?, ?, ?, 'open', NOW())
        ");

        $stmt->bind_param("iis", $userid, $subjectid, $description);

        if ($stmt->execute()) {
            $message = "Complaint submitted successfully.";
        } else {
            $message = "Complaint submission failed.";
        }
    } else {
        $message = "Description is required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Complaint</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <div class="header">
        <h1>Submit Complaint</h1>
        <div class="nav">
            <a href="../index.php">Dashboard</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="card">
        <h2>Complaint Form</h2>

        <?php if ($message != ""): ?>
            <div class="success-msg"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Subject ID</label>
                <input type="number" name="subjectid" required>
            </div>

            <div class="form-group">
                <label>Complaint Description</label>
                <textarea name="description" required></textarea>
            </div>

            <button type="submit">Submit Complaint</button>
        </form>
    </div>

</div>

</body>
</html>