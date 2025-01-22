<?php
// Database configuration
$servername = "localhost:4000"; // Default for XAMPP
$username = "root";        // Default MySQL username for XAMPP
$password = "";            // Leave blank for no password
$dbname = "e_commerce";    // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
