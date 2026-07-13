<?php
session_start();
require_once 'vendor/autoload.php';

// 1. Initialize the Google Client
$client = new Google\Client();

// ⚠️ PASTE YOUR KEYS HERE ⚠️
$client->setClientId('358581470371-6sahbg7febsu5i00ac13elkb7kerrvsm.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/auth-system/google-callback.php');

// 2. Request access to their email and basic profile info
$client->addScope("email");
$client->addScope("profile");

// 3. Generate the Google login URL and instantly redirect the user there
$login_url = $client->createAuthUrl();
header("Location: " . $login_url);
exit();
?>