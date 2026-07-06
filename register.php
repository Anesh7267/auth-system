<?php
// Include the database connection file
require_once 'includes/db.php';

$success_msg = "";
$error_msg = "";

// Check if the form was submitted
if (isset($_POST['register'])) {
    // Grab the data from the form
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

        // 3. Prepare the SQL statement to prevent SQL Injection
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        // 4. Execute the query and check for success
        try {
            if ($stmt->execute()) {
                $success_msg = "Registration successful! You can now log in.";
            } else {
                $error_msg = "Something went wrong. Please try again.";
            }
        } catch (mysqli_sql_exception $e) {
            // If the email is already in the database, it throws an error (Code 1062)
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
    <title>Register | Auth System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
        <h3 class="text-center mb-4">Create Account</h3>
        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
        </form>
        
        <div class="text-center mt-3">
            <p class="mb-0">Already have an account? <a href="login.php" class="text-decoration-none">Log in here</a></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>