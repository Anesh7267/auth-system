<?php
require_once 'includes/db.php';

$success_msg = "";
$error_msg = "";
$valid_token = false;
$user_id = null;

// 1. Grab the token from the URL (e.g., reset-password.php?token=abc123xyz)
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // 2. Check if the token exists AND hasn't expired
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expires > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $valid_token = true;
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
    }
    $stmt->close();
}

// 3. Handle the form submission for the new password
if (isset($_POST['reset_password']) && $valid_token) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error_msg = "Passwords do not match.";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update the password AND wipe the token so the link can't be used again
        $update_stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
        $update_stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($update_stmt->execute()) {
            $success_msg = "Your password has been successfully reset! You can now log in.";
            $valid_token = false; // Hide the form after success
        } else {
            $error_msg = "Something went wrong. Please try again.";
        }
        $update_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | MANIMĀRAN STUDIOS 8</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/zephyr/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/theme.css">
</head>
<body class="theme-shell">

<div class="auth-page d-flex flex-column">
    <div class="container flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="auth-brand-block w-100 d-flex flex-column align-items-center">
            <div class="text-center mb-4 mb-md-5">
                <img src="assets/Manimaran-Studios-logo.png" alt="MANIMĀRAN STUDIOS 8 logo" class="brand-logo brand-logo-auth mb-3">
                <div class="auth-kicker mb-2">Thriall</div>
                <h1 class="auth-title mb-3">MANIMĀRAN STUDIOS 8</h1>
                <p class="auth-subtitle mx-auto mb-0">Set a new password to regain access to your account.</p>
            </div>

            <div class="card auth-card p-4 p-md-5">
        <h3 class="text-center mb-4">Set New Password</h3>
        
        <?php if (!$valid_token && empty($success_msg)): ?>
            <div class="alert alert-danger text-center">
                <i class="bi bi-exclamation-triangle-fill fs-4 d-block mb-2"></i>
                This password reset link is invalid or has expired. Please request a new one.
            </div>
            <a href="forgot-password.php" class="btn btn-primary w-100">Request New Link</a>
        <?php elseif ($valid_token): ?>
            <form action="reset-password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-shield-lock-fill"></i></span>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                
                <button type="submit" name="reset_password" class="btn btn-primary w-100">Reset Password</button>
            </form>
        <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (!empty($success_msg)): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Password Updated!',
        text: '<?php echo addslashes($success_msg); ?>',
        confirmButtonColor: '#3085d6'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'login.php';
        }
    });
</script>
<?php endif; ?>

<?php if (!empty($error_msg)): ?>
<script>
    Swal.fire({ icon: 'error', title: 'Oops...', text: '<?php echo addslashes($error_msg); ?>', confirmButtonColor: '#d33' });
</script>
<?php endif; ?>

</body>
</html>