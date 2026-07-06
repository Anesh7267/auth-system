<?php
// Database configuration
$host = "localhost";
$username = "root"; // Default XAMPP username
$password = "";     // Default XAMPP password is empty
$dbname = "auth-system"; // The database we created in phpMyAdmin

// Create a connection using MySQLi
$conn = new mysqli($host, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>