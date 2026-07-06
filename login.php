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
        if (password_verify($password, $user['password'])) {
            // Success! Create session variables (the VIP pass)
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            
            // Redirect to the profile page
            header("Location: profile.php");
            exit();
        } else {
            $error_msg = "Incorrect password.";
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
    <title>Login | Auth System</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
        <h3 class="text-center mb-4">Log In</h3>
        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        <!-- Form submits via POST to the same page -->
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <button type="submit" name="login" class="btn btn-primary w-100">Log In</button>
        </form>
        
        <div class="text-center mt-3">
            <p class="mb-0">Don't have an account? <a href="register.php" class="text-decoration-none">Register here</a></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>