<?php
// Start the session to check for the VIP pass
session_start();

// If the user_id session variable doesn't exist, they aren't logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>