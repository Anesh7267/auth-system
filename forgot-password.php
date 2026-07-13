<?php
session_start();
require_once 'includes/db.php';

// Import PHPMailer classes from the vendor folder you just downloaded
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

$success_msg = "";
$error_msg = "";

if (isset($_POST['reset_request'])) {
    $email = trim($_POST['email']);

    // 1. Check if the email exists in your database
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // 2. Generate a secure, random 32-character token
        $token = bin2hex(random_bytes(32));

        // 3 & 4. Save the token and let MySQL calculate the exact expiration time!
        $update_stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
        $update_stmt->bind_param("ss", $token, $email);
        $update_stmt->execute();
        $update_stmt->close();

        // 5. Send the Email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'aneshjag@gmail.com';     // <-- CHANGE THIS
            $mail->Password   = 'osyg kfef yonc xnlb'; // <-- CHANGE THIS (No spaces)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('aneshjag@gmail.com', 'Auth System Security'); // <-- CHANGE THIS
            $mail->addAddress($email, $user['name']);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            
            // The link that will be sent to their email
            $reset_link = "http://localhost/auth-system/reset-password.php?token=" . $token;
            
            $mail->Body = "
                <h3>Hello {$user['name']},</h3>
                <p>We received a request to reset your password. Click the link below to set a new one:</p>
                <p><a href='{$reset_link}'>{$reset_link}</a></p>
                <p><i>Note: This link will expire in 1 hour. If you did not request this, please ignore this email.</i></p>
            ";

            $mail->send();
            $success_msg = "If that email is registered, a password reset link has been sent.";
        } catch (Exception $e) {
            $error_msg = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        // Security feature: We show the exact same success message even if the email doesn't exist.
        // This prevents hackers from using this form to guess which emails are registered!
        $success_msg = "If that email is registered, a password reset link has been sent.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Auth System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/zephyr/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
        <h3 class="text-center mb-2">Forgot Password</h3>
        <p class="text-center text-muted mb-4"><small>Enter your email and we'll send you a reset link.</small></p>
        
        <form action="forgot-password.php" method="POST">
            <div class="mb-4">
                <label for="email" class="form-label">Email address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
            </div>
            
            <button type="submit" name="reset_request" class="btn btn-primary w-100 mb-3">Send Reset Link</button>
            <a href="login.php" class="btn btn-light w-100">Back to Login</a>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (!empty($success_msg)): ?>
<script>
    Swal.fire({ icon: 'success', title: 'Email Sent', text: '<?php echo addslashes($success_msg); ?>', confirmButtonColor: '#3085d6' });
</script>
<?php endif; ?>

<?php if (!empty($error_msg)): ?>
<script>
    Swal.fire({ icon: 'error', title: 'Error', text: '<?php echo addslashes($error_msg); ?>', confirmButtonColor: '#d33' });
</script>
<?php endif; ?>

</body>
</html>