<?php
require_once __DIR__ . "/../controllers/JobController.php";

if (!isset($_SESSION['seeker_id'])) {
    header("Location: ../index.php");
    exit();
}

$applications = getSeekerApplications($_SESSION['seeker_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Applications – JobPortal</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="container">

    <div class="header">
        <div class="logo">
            <h1><i class="fas fa-briefcase"></i> JobPortal Pro</h1>
        </div>
    </div>

    <div class="nav-links">
        <a href="../index.php"><i class="fas fa-arrow-left"></i> Back to Jobs</a>
        <a href="profile.php"><i class="fas fa-user-edit"></i> Edit Profile</a>
        <a href="saved.php"><i class="fas fa-bookmark"></i> Saved Jobs</a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'applied'): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> Your application has been submitted successfully!
    </div>
    <?php endif; ?>

    <div class="card">
        <h3 style="margin-bottom:6px;">
            <i class="fas fa-file-alt" style="color:#00d4ff;"></i> My Applications
        </h3>
        <p style="color:#666;font-size:13px;margin-bottom:20px;">
            Track all your job applications and their current status.
        </p>

        <?php if (empty($applications)): ?>
        <div class="empty-state">
            <i class="fas fa-file-alt"></i>
            <h3>No applications yet</h3>
            <p>Browse jobs and apply to positions you're interested in.</p>
            <a href="../index.php" class="btn" style="margin-top:16px;display:inline-block;">
                <i class="fas fa-search"></i> Browse Jobs
            </a>
        </div>

        <?php else: ?>
        <?php
        $counts = [];
        foreach ($applications as $a) {
            $s = $a['status'];
            $counts[$s] = ($counts[$s] ?? 0) + 1;
        }
        ?>
        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:22px;">
            <?php
            $statColors = [
                'submitted'   => '#3498db',
                'reviewed'    => '#e67e22',
                'shortlisted' => '#2ecc71',
                'interview'   => '#9b59b6',
                'rejected'    => '#e74c3c',
                'withdrawn'   => '#95a5a6',
            ];
            foreach ($statColors as $s => $c):
                if (!isset($counts[$s])) continue;
            ?>
            <div style="background:rgba(35,35,35,0.9);border:1px solid rgba(255,255,255,0.07);border-radius:10px;padding:12px 18px;text-align:center;min-width:100px;">
                <div style="font-size:22px;font-weight:700;color:<?= $c ?>;"><?= $counts[$s] ?></div>
                <div style="font-size:12px;color:#888;text-transform:capitalize;"><?= $s ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="jobs-table">
            <table>
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Company</th>
                        <th>Location</th>
                        <th>Type</th>
                        <th>Applied On</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($applications as $app):
                    $company  = $app['company_name'] ?? $app['employer_name'];
                    $applied  = date('d M Y', strtotime($app['applied_at']));
                    $deadline = date('d M Y', strtotime($app['deadline']));
                    $status   = $app['status'];
                ?>
                <tr>
                    <td style="font-weight:600;">
                        <a href="job.php?id=<?= $app['job_id'] ?>" style="color:#e0e0e0;text-decoration:none;">
                            <?= htmlspecialchars($app['title']) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($company) ?></td>
                    <td><?= htmlspecialchars($app['location']) ?></td>
                    <td>
                        <span class="type-badge type-<?= htmlspecialchars($app['job_type']) ?>">
                            <?= htmlspecialchars(str_replace('-',' ',strtoupper($app['job_type']))) ?>
                        </span>
                    </td>
                    <td><?= $applied ?></td>
                    <td style="color:<?= strtotime($app['deadline']) < time() ? '#e74c3c' : '#ccc' ?>">
                        <?= $deadline ?>
                    </td>
                    <td class="status-cell">
                        <span class="status-badge status-<?= $status ?>">
                            <?= ucfirst($status) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($status === 'submitted'): ?>
                        <button class="btn btn-danger btn-sm"
                            onclick="withdrawApp(this, <?= $app['id'] ?>)">
                            <i class="fas fa-undo"></i> Withdraw
                        </button>
                        <?php else: ?>
                        <span style="color:#555;font-size:13px;">—</span>
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

<script src="script.js"></script>
</body>
</html>
