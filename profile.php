<?php
require_once 'includes/auth_check.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];

// Fetch the logged-in user's data
$stmt = $conn->prepare("SELECT name, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch the user's 5 most recent logins
$log_stmt = $conn->prepare("SELECT ip_address, login_time FROM login_logs WHERE user_id = ? ORDER BY login_time DESC LIMIT 5");
$log_stmt->bind_param("i", $user_id);
$log_stmt->execute();
$logs_result = $log_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Auth System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/zephyr/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="profile.php"><i class="bi bi-shield-lock me-2"></i>Auth System</a>
        <div class="d-flex">
            <a href="logout.php" class="btn btn-danger btn-sm">Log Out</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>User Profile</h5>
                </div>
                <div class="card-body">
                    <h4 class="card-title mb-4">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h4>
                    <table class="table table-sm table-borderless">
                        <tbody>
                            <tr>
                                <th class="text-muted" style="width: 40%">Full Name:</th>
                                <td><span class="badge bg-light text-dark fs-6"><?php echo htmlspecialchars($user['name']); ?></span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Email Address:</th>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Account Created:</th>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="d-grid gap-2 mt-4">
                        <a href="edit-profile.php" class="btn btn-outline-primary"><i class="bi bi-pencil-square me-2"></i>Edit Profile</a>
                        <a href="change-password.php" class="btn btn-outline-secondary"><i class="bi bi-key me-2"></i>Change Password</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Login Activity</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Date & Time</th>
                                    <th>IP Address</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($logs_result->num_rows > 0): ?>
                                    <?php while ($log = $logs_result->fetch_assoc()): ?>
                                        <tr>
                                            <td class="ps-4"><?php echo date('M j, Y g:i A', strtotime($log['login_time'])); ?></td>
                                            <td><span class="text-muted"><?php echo htmlspecialchars($log['ip_address']); ?></span></td>
                                            <td><span class="badge bg-success">Success</span></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">No recent activity found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>