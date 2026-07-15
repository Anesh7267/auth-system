<?php
// Include the database connection file
require_once 'includes/db.php';

$success_msg = "";
$error_msg = "";

// Check if the form was submitted
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Check if passwords match
    if ($password !== $confirm_password) {
        $error_msg = "Passwords do not match!";
    } else {
        // 2. Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 3. Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        // 4. Execute the query
        try {
            if ($stmt->execute()) {
                $success_msg = "Registration successful! You can now log in.";
            } else {
                $error_msg = "Something went wrong. Please try again.";
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $error_msg = "An account with this email already exists.";
            } else {
                $error_msg = "Database error: " . $e->getMessage();
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | MANIMĀRAN STUDIOS 8</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/zephyr/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/theme.css">
</head>
<body class="theme-shell">

<div class="register-page auth-page d-flex flex-column">
    <div class="container flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="auth-layout auth-shell row g-4 align-items-stretch">
            <div class="col-lg-5 d-flex">
                <div class="auth-hero w-100 p-4 p-md-5 d-flex align-items-end">
                    <div class="auth-hero-panel">
                        <img src="assets/Manimaran-Studios-logo.png" alt="MANIMĀRAN STUDIOS 8 logo" class="brand-logo brand-logo-auth mb-4">
                        <div class="auth-kicker mb-2">Thriall</div>
                        <h1 class="auth-title mb-3">MANIMĀRAN STUDIOS 8</h1>
                        <p class="auth-subtitle mb-0">Create your account to get started with the company portal and manage your profile securely.</p>
                        <div class="auth-hero-list">
                            <div class="auth-hero-item"><i class="bi bi-person-check-fill"></i><span>Register once and access your own workspace</span></div>
                            <div class="auth-hero-item"><i class="bi bi-shield-lock-fill"></i><span>Passwords are securely hashed and protected</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 d-flex">
                <div class="card auth-card auth-form-shell p-4 p-md-5 w-100">
                    <h3 class="text-center mb-4">Create Account</h3>
                    
                    <form action="register.php" method="POST">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-shield-lock-fill"></i></span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                        
                        <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p class="mb-0">Already have an account? <a href="login.php" class="text-decoration-none">Log in here</a></p>
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
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '<?php echo addslashes($success_msg); ?>',
        confirmButtonColor: '#3085d6'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect them to login page after they click OK
            window.location.href = 'login.php';
        }
    });
</script>
<?php endif; ?>

<?php if (!empty($error_msg)): ?>
<script>
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '<?php echo addslashes($error_msg); ?>',
        confirmButtonColor: '#d33'
    });
</script>
<?php endif; ?>

</body>
</html>