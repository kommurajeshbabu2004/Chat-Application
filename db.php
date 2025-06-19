<?php
// Database configuration
$host = "localhost";
$username = "root"; // Default XAMPP username
$password = "";     // Default XAMPP password (usually empty)
$dbname = "chat_application"; // Replace with your database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
