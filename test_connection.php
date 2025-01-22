<?php
$servername = "localhost:4000";
$username = "root";
$password = ""; // No password based on your phpMyAdmin setup
$dbname = "e_commerce"; // Replace with your actual database name

// Attempt connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Database connection successful!";
?>
