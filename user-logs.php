<?php
require_once 'includes/auth_check.php';
require_once 'includes/db.php';

$admin_id = $_SESSION['user_id'];

// 1. Double-check they are actually an admin
$role_stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$role_stmt->bind_param("i", $admin_id);
$role_stmt->execute();
$role_result = $role_stmt->get_result();
$current_user = $role_result->fetch_assoc();
$role_stmt->close();

if ($current_user['role'] !== 'admin') {
    header("Location: profile.php");
    exit();
}

// 2. Get the target user's ID from the URL (e.g., user-logs.php?id=5)
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin.php"); // Send them back if no ID is provided
    exit();
}

$target_user_id = $_GET['id'];

// 3. Fetch the target user's basic info for the header
$user_stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$user_stmt->bind_param("i", $target_user_id);
$user_stmt->execute();
$target_user = $user_stmt->get_result()->fetch_assoc();
$user_stmt->close();

if (!$target_user) {
    echo "User not found.";
    exit();
}

// 4. Fetch the target user's entire login history
$log_stmt = $conn->prepare("SELECT ip_address, login_time FROM login_logs WHERE user_id = ? ORDER BY login_time DESC");
$log_stmt->bind_param("i", $target_user_id);
$log_stmt->execute();
$logs_result = $log_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Logs | Auth System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/zephyr/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="admin.php"><i class="bi bi-shield-shaded me-2"></i>Admin Panel</a>
        <div class="d-flex">
            <a href="admin.php" class="btn btn-light btn-sm me-2">Back to Dashboard</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Log Out</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Activity Logs</h5>
                    <span class="badge bg-light text-dark">User ID: #<?php echo htmlspecialchars($target_user_id); ?></span>
                </div>
                
                <div class="card-body bg-light border-bottom">
                    <h6 class="mb-1 text-muted">Auditing Account:</h6>
                    <h5 class="mb-0 fw-bold"><?php echo htmlspecialchars($target_user['name']); ?> <small class="text-muted fw-normal">(<?php echo htmlspecialchars($target_user['email']); ?>)</small></h5>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
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
                                            <td class="ps-4"><?php echo date('F j, Y, g:i a', strtotime($log['login_time'])); ?></td>
                                            <td><span class="text-muted font-monospace"><?php echo htmlspecialchars($log['ip_address']); ?></span></td>
                                            <td><span class="badge bg-success">Success</span></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">No login activity recorded for this user yet.</td>
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