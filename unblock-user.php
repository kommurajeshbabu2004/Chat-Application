<?php
session_start();
include 'db_connect.php'; // Ensure this connects to your database

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $blocked_user_id = $_POST['blocked_user_id'];

    if ($user_id && $blocked_user_id) {
        $sql = "DELETE FROM blocked_users WHERE blocker_id = ? AND blocked_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $blocked_user_id);

        if ($stmt->execute()) {
            echo "User unblocked successfully.";
        } else {
            echo "Error unblocking user.";
        }
    } else {
        echo "Invalid user data.";
    }
} else {
    echo "Invalid request method.";
}
?>
