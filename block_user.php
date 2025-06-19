<?php
session_start();
include 'db.php'; // Ensure this connects to your database

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id']; // The logged-in user
    $blocked_user_id = $_POST['blocked_user_id']; // The user to be blocked

    if ($user_id && $blocked_user_id) {
        // Check if the user is already blocked
        $check_sql = "SELECT * FROM blocked_users WHERE blocker_id = ? AND blocked_id = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ii", $user_id, $blocked_user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // Insert into blocked_users table
            $sql = "INSERT INTO blocked_users (blocker_id, blocked_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $user_id, $blocked_user_id);

            if ($stmt->execute()) {
                echo "User blocked successfully.";
            } else {
                echo "Error blocking user.";
            }
        } else {
            echo "User is already blocked.";
        }
    } else {
        echo "Invalid user data.";
    }
} else {
    echo "Invalid request method.";
}
?>
