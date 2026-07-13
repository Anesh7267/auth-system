<?php
require_once 'includes/auth_check.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];
$success_msg = "";
$error_msg = "";

// 1. THE ULTIMATE BOUNCER: Check if the logged-in user is actually an admin
$role_stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$role_stmt->bind_param("i", $user_id);
$role_stmt->execute();
$role_result = $role_stmt->get_result();
$current_user = $role_result->fetch_assoc();
$role_stmt->close();

if ($current_user['role'] !== 'admin') {
    // If they aren't an admin, kick them back to their profile silently
    header("Location: profile.php");
    exit();
}

// 2. Handle User Deletion
if (isset($_POST['delete_user'])) {
    $target_id = $_POST['target_id'];
    
    // Prevent the admin from accidentally deleting themselves
    if ($target_id == $user_id) {
        $error_msg = "You cannot delete your own admin account.";
    } else {
        $del_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $del_stmt->bind_param("i", $target_id);
        if ($del_stmt->execute()) {
            $success_msg = "User successfully deleted. Their activity logs were also removed.";
        } else {
            $error_msg = "Failed to delete user.";
        }
        $del_stmt->close();
    }
}

// 3. Fetch ALL users for the dashboard table
$users_stmt = $conn->prepare("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
$users_stmt->execute();
$all_users = $users_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Auth System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/zephyr/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="admin.php"><i class="bi bi-shield-shaded me-2"></i>Admin Panel</a>
        <div class="d-flex">
            <a href="profile.php" class="btn btn-light btn-sm me-2">My Profile</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Log Out</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>User Management</h5>
                    <span class="badge bg-primary rounded-pill">Total Users: <?php echo $all_users->num_rows; ?></span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $all_users->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-muted">#<?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td>
                                            <?php if ($row['role'] === 'admin'): ?>
                                                <span class="badge bg-danger">Admin</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">User</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($row['created_at'])); ?></td>
                                        <td class="text-end pe-4">
                                            <a href="user-logs.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-info me-1">
                                                <i class="bi bi-clock-history"></i> Logs
                                            </a>

                                            <?php if ($row['id'] != $user_id): ?>
                                                <form action="admin.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                                    <input type="hidden" name="target_id" value="<?php echo $row['id']; ?>">
                                                    <button type="submit" name="delete_user" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash-fill"></i> Delete
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-outline-secondary disabled">Current</button>
                                            <?php endif; ?> 
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (!empty($success_msg)): ?>
<script>
    Swal.fire({ icon: 'success', title: 'Deleted', text: '<?php echo addslashes($success_msg); ?>', confirmButtonColor: '#3085d6' });
</script>
<?php endif; ?>

<?php if (!empty($error_msg)): ?>
<script>
    Swal.fire({ icon: 'error', title: 'Action Denied', text: '<?php echo addslashes($error_msg); ?>', confirmButtonColor: '#d33' });
</script>
<?php endif; ?>

</body>
</html>