<?php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in first.";
    exit();
}

// Get logged-in user's ID and the receiver's ID from the request
$senderId = $_SESSION['user_id'];
$receiverId = $_POST['receiver_id'];
$message = $_POST['message'];
$timestamp = date('Y-m-d H:i:s'); // Store current date-time

// Sanitize the message input to avoid security issues (e.g., XSS attacks)
$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

// Validate that both sender and receiver IDs are provided
if (empty($senderId) || empty($receiverId) || empty($message)) {
    echo "Error: Missing required fields.";
    exit();
}

// Insert the message into the database
$query = "INSERT INTO messagestable (sender_id, receiver_id, message, timestamp) VALUES ('$senderId', '$receiverId', '$message','$timestamp')";

if ($conn->query($query) === TRUE) {
    echo "Message sent successfully.";
} else {
    echo "Error sending message: " . $conn->error;
}
?>
