<?php
// 1. Protect the page
require_once 'includes/auth_check.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];
$success_msg = "";
$error_msg = "";

// 2. Handle the form submission to update data
if (isset($_POST['update_profile'])) {
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);

    if (empty($new_name) || empty($new_email)) {
        $error_msg = "Name and email cannot be empty.";
    } else {
        // Prepare the UPDATE SQL statement
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $new_name, $new_email, $user_id);

        try {
            if ($stmt->execute()) {
                $success_msg = "Profile updated successfully!";
                // Update the session variable in case we use it in the navbar
                $_SESSION['name'] = $new_name; 
            } else {
                $error_msg = "Something went wrong updating your profile.";
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $error_msg = "That email is already registered to another account.";
            } else {
                $error_msg = "Database error: " . $e->getMessage();
            }
        }
        $stmt->close();
    }
}

// 3. Fetch the latest user data to pre-fill the form (done AFTER any updates)
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
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
    <title>Edit Profile | MANIMĀRAN STUDIOS 8</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/zephyr/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/theme.css">
</head>
<body class="theme-shell">

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-semibold d-flex align-items-center gap-2" href="profile.php">
            <img src="assets/Manimaran-Studios-logo.png" alt="MANIMĀRAN STUDIOS 8 logo" class="brand-logo brand-logo-nav">
            <span>MANIMĀRAN STUDIOS 8</span>
        </a>
        <div class="d-flex">
            <a href="profile.php" class="btn btn-light btn-sm me-2">Back to Profile</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Log Out</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="dashboard-hero p-4 p-md-5 mb-4">
        <div class="position-relative" style="z-index: 1;">
            <div class="hero-kicker mb-2">Profile settings</div>
            <h1 class="hero-title mb-3">Update your account details</h1>
            <p class="hero-copy mb-0">Keep your name and email up to date so your account stays accurate and easy to recover.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card surface-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edit Profile</h5>
                </div>
                <div class="card-body">
                    
                    <?php if (!empty($success_msg)): ?>
                        <div class="alert alert-success"><?php echo $success_msg; ?></div>
                    <?php endif; ?>

                    <?php if (!empty($error_msg)): ?>
                        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
                    <?php endif; ?>

                    <form action="edit-profile.php" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                            <a href="profile.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>