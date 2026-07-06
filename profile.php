<?php
// 1. Call the bouncer (this must be the very first thing)
require_once 'includes/auth_check.php';
// 2. Connect to the database
require_once 'includes/db.php';

// Fetch the logged-in user's data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Auth System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="profile.php">Auth System</a>
        <div class="d-flex">
            <a href="logout.php" class="btn btn-danger btn-sm">Log Out</a>
        </div>
    </div>
</nav>

<!-- Profile Content -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">User Profile</h4>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h5>
                    <hr>
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th style="width: 30%">Full Name:</th>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                            </tr>
                            <tr>
                                <th>Email Address:</th>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                            </tr>
                            <tr>
                                <th>Account Created:</th>
                                <td><?php echo date('F j, Y', strtotime($user['created_at'])); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="d-flex gap-2 mt-4">
                        <a href="edit-profile.php" class="btn btn-outline-primary">Edit Profile</a>
                        <a href="change-password.php" class="btn btn-outline-secondary">Change Password</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>