<?php
session_start();
require_once 'includes/db.php';
require_once 'vendor/autoload.php';
require_once 'config.php'; // Add this line!

$client = new Google\Client();

// ⚠️ PASTE YOUR EXACT SAME KEYS HERE ⚠️
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri('http://localhost/auth-system/google-callback.php');

// 1. Check if Google sent back an authorization code
if (isset($_GET['code'])) {
    
    // 2. Exchange the code for a valid access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    // 3. Get the user's profile data from Google
    $google_oauth = new Google\Service\Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    
    $google_id = $google_account_info->id;
    $email = $google_account_info->email;
    $name = $google_account_info->name;

    // 4. Check if this user already exists in our database
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // --- USER EXISTS: Log them in ---
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        
        // Update their google_id just in case they previously registered manually
        $update = $conn->prepare("UPDATE users SET google_id = ? WHERE id = ?");
        $update->bind_param("si", $google_id, $user['id']);
        $update->execute();
        $update->close();

    } else {
        // --- NEW USER: Register them silently ---
        $insert = $conn->prepare("INSERT INTO users (name, email, google_id) VALUES (?, ?, ?)");
        $insert->bind_param("sss", $name, $email, $google_id);
        $insert->execute();
        
        // Log them in immediately after creating the account
        $_SESSION['user_id'] = $insert->insert_id;
        $_SESSION['name'] = $name;
        $insert->close();
    }
    $stmt->close();

    // 5. Add to the User Activity Log (So it shows up on their profile!)
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $log_stmt = $conn->prepare("INSERT INTO login_logs (user_id, ip_address) VALUES (?, ?)");
    $log_stmt->bind_param("is", $_SESSION['user_id'], $ip_address);
    $log_stmt->execute();
    $log_stmt->close();

    // 6. Redirect to the profile dashboard
    header("Location: profile.php");
    exit();
} else {
    // If someone tries to access this page directly without logging in
    header("Location: login.php");
    exit();
}
?>