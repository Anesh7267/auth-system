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
    <title>Admin Dashboard | MANIMĀRAN STUDIOS 8</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/zephyr/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="assets/Manimaran-Studios-logo.png">
    <link rel="stylesheet" href="css/theme.css">
</head>
<body class="theme-shell">

<div class="admin-shell container-fluid px-0">
    <div class="row g-0 min-vh-100">
        <aside class="col-lg-3 col-xl-2 admin-sidebar p-4 p-lg-4 d-flex flex-column">
            <a class="admin-sidebar-brand mb-4" href="admin.php">
                <img src="assets/Manimaran-Studios-logo.png" alt="MANIMĀRAN STUDIOS 8 logo" class="brand-logo brand-logo-nav">
                <div>
                    <div class="auth-kicker mb-1">Thriall</div>
                    <div class="fw-semibold">MANIMĀRAN STUDIOS 8</div>
                </div>
            </a>

            <div class="admin-sidebar-card mb-4">
                <div class="small text-uppercase fw-semibold mb-2" style="letter-spacing: 0.08em;">Admin Panel</div>
                <div class="admin-sidebar-copy small">Monitor users, inspect logs, and manage access from the branded dashboard.</div>
            </div>

            <nav class="d-grid gap-2">
                <a class="admin-nav-link active" href="admin.php"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
                <a class="admin-nav-link" href="profile.php"><i class="bi bi-person-circle"></i><span>My Profile</span></a>
                <a class="admin-nav-link" href="user-logs.php?id=<?php echo $user_id; ?>"><i class="bi bi-clock-history"></i><span>My Logs</span></a>
                <a class="admin-nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i><span>Log Out</span></a>
            </nav>

            <div class="mt-auto pt-4 small text-muted">
                Logged in as admin<br>
                <?php echo htmlspecialchars($_SESSION['name'] ?? 'Administrator'); ?>
            </div>
        </aside>

        <main class="col-lg-9 col-xl-10 admin-main p-3 p-lg-4">
            <div class="admin-topbar mb-4">
                <div>
                    <h1 class="hero-title mb-2 text-dark">User management dashboard</h1>
                    <p class="hero-copy text-muted mb-0">View every user, review activity, and manage access.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="profile.php" class="btn btn-light btn-sm"><i class="bi bi-person-circle me-2"></i>My Profile</a>
                    <a href="logout.php" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right me-2"></i>Log Out</a>
                </div>
            </div>

            <div class="dashboard-hero p-4 p-md-5 mb-4">
                <div class="position-relative" style="z-index: 1;">
                    <div class="hero-kicker mb-2">USER BASE</div>
                    <h2 class="hero-title mb-3">Total users in the system</h2>
                    <p class="hero-copy mb-4">A quick look at the current user base.</p>
                    <span class="metric-badge"><i class="bi bi-people-fill"></i><?php echo $all_users->num_rows; ?> total users</span>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card surface-card">
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
        </main>
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