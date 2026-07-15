<?php
// Start the session to remember the user
session_start();
require_once 'includes/db.php';

$error_msg = "";

// Check if the login form was submitted
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 1. Find the user by their email
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // 2. Check if the user exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // 3. Verify the encrypted password
        // 3. Verify the encrypted password
        if (password_verify($password, $user['password'])) {
            // Success! Create session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            
            // --- NEW: Record the login activity ---
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $log_stmt = $conn->prepare("INSERT INTO login_logs (user_id, ip_address) VALUES (?, ?)");
            $log_stmt->bind_param("is", $user['id'], $ip_address);
            $log_stmt->execute();
            $log_stmt->close();
            // --------------------------------------

            // Redirect to the profile page
            header("Location: profile.php");
            exit();
        } else {
            $error_msg = "Incorrect password. Please try again.";
        }
    } else {
        $error_msg = "No account found with that email.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | MANIMĀRAN STUDIOS 8</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/zephyr/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/theme.css">
</head>
<body class="theme-shell">

<div class="login-page auth-page d-flex flex-column">
    <div class="container flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="auth-layout auth-shell row g-4 align-items-stretch">
            <div class="col-lg-5 d-flex">
                <div class="auth-hero w-100 p-4 p-md-5 d-flex align-items-end">
                    <div class="auth-hero-panel">
                        <img src="assets/Manimaran-Studios-logo.png" alt="MANIMĀRAN STUDIOS 8 logo" class="brand-logo brand-logo-auth mb-4">
                        <div class="auth-kicker mb-2">Thriall</div>
                        <h1 class="auth-title mb-3">MANIMĀRAN STUDIOS 8</h1>
                        <p class="auth-subtitle mb-0">Conquer Your Strategy</p>
                        <div class="auth-hero-list">
                            <div class="auth-hero-item"><i class="bi bi-shield-lock-fill"></i><span>Secure access with password or Google sign-in</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 d-flex">
                <div class="card auth-card auth-form-shell p-4 p-md-5 w-100">
                    <h3 class="text-center mb-4">Log In</h3>
                    
                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary w-100">Log In</button>
                        <div class="d-flex align-items-center my-3">
                            <hr class="flex-grow-1">
                            <span class="mx-2 text-muted">OR</span>
                            <hr class="flex-grow-1">
                        </div>

                        <a href="google-login.php" class="btn btn-outline-dark w-100">
                            <i class="bi bi-google text-danger me-2"></i>Sign in with Google
                        </a>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p class="mb-2"><a href="forgot-password.php" class="text-decoration-none text-muted"><small>Forgot your password?</small></a></p>
                        <p class="mb-0">Don't have an account? <a href="register.php" class="text-decoration-none">Register here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (!empty($error_msg)): ?>
<script>
    Swal.fire({
        icon: 'error',
        title: 'Login Failed',
        text: '<?php echo addslashes($error_msg); ?>',
        confirmButtonColor: '#d33'
    });
</script>
<?php endif; ?>

</body>
</html>