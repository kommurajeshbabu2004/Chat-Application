<?php
session_start();
include "db.php"; // Ensure your database connection file is correctly included

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo "Error: User not logged in.";
        exit;
    }

    $sender_id = $_SESSION['user_id']; // Ensure sender_id is the logged-in user
    $receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;

    if (empty($sender_id) || empty($receiver_id)) {
        echo "Error: Invalid parameters.";
        exit;
    }

    // Check if messages exist before updating
    $checkQuery = "SELECT COUNT(*) FROM messagestable WHERE sender_id = ? AND receiver_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ii", $sender_id, $receiver_id);
    $checkStmt->execute();
    $checkStmt->bind_result($messageCount);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($messageCount == 0) {
        echo "Error: No messages found between sender and receiver.";
        exit;
    }

    // Update messages to mark them as deleted for the sender
    $query = "UPDATE messagestable SET is_deleted_sender = 1 WHERE sender_id = ? AND receiver_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $sender_id, $receiver_id);

    if ($stmt->execute()) {
        echo "Success: Chat cleared.";
    } else {
        echo "Error: Could not clear chat. MySQL Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Error: Invalid request.";
}
?>
