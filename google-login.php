<?php
session_start();
require_once 'vendor/autoload.php';
require_once 'config.php'; // Add this line!

// 1. Initialize the Google Client
$client = new Google\Client();

// ⚠️ PASTE YOUR KEYS HERE ⚠️
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri('http://localhost/auth-system/google-callback.php');

// 2. Request access to their email and basic profile info
$client->addScope("email");
$client->addScope("profile");

// 3. Generate the Google login URL and instantly redirect the user there
$login_url = $client->createAuthUrl();
header("Location: " . $login_url);
exit();
?>