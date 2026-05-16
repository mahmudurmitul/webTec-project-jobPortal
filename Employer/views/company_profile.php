<?php
require_once "../config.php";
require_once "../model.php";
require_once "../controller.php";

employerOnlyFrom();

$userid = $_SESSION['userid'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (saveEmployerProfile($conn, $userid)) {
        $message = "Company profile saved successfully.";
    } else {
        $message = "Failed to save company profile.";
    }
}

$profile = getEmployerProfile($conn, $userid);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Company Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <div class="header">
        <h1>Company Profile</h1>
        <div class="nav">
            <a href="../index.php">Dashboard</a>
            <a href="create_job.php">Create Job</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="card">
        <h2>Manage Company Profile</h2>

        <?php if ($message != ""): ?>
            <div class="success-msg"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="form-group">
                <label>Company Name</label>
                <input type="text" name="companyname" required
                       value="<?php echo htmlspecialchars($profile['companyname'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Industry</label>
                <input type="text" name="industry"
                       value="<?php echo htmlspecialchars($profile['industry'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Company Size</label>
                <select name="companysize">
                    <option value="1-10">1-10</option>
                    <option value="11-50">11-50</option>
                    <option value="51-200">51-200</option>
                    <option value="200+">200+</option>
                </select>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description"><?php echo htmlspecialchars($profile['description'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>Website</label>
                <input type="text" name="website"
                       value="<?php echo htmlspecialchars($profile['website'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea name="address"><?php echo htmlspecialchars($profile['address'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>Company Logo</label>
                <input type="file" name="logo" accept="image/*">
            </div>

            <button type="submit">Save Profile</button>
        </form>
    </div>

</div>

</body>
</html>