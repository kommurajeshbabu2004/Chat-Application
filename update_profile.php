<?php
session_start();
include 'db.php'; // Database connection

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    $_SESSION['message'] = "Unauthorized access!";
    $_SESSION['message_type'] = "error";
    header("Location: profile.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_username = $_POST['username'];
    $new_password = $_POST['password'];

    // Validate username
    if (!preg_match('/^[A-Z][A-Za-z0-9]*$/', $new_username)) {
        $_SESSION['message'] = "Username must start with a capital letter.";
        $_SESSION['message_type'] = "error";
        header("Location: profile.php");
        exit();
    }

    // Validate password if changed
    if (!empty($new_password) && !preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $new_password)) {
        $_SESSION['message'] = "Password must be at least 8 characters with letters, numbers, and special characters.";
        $_SESSION['message_type'] = "error";
        header("Location: profile.php");
        exit();
    }

    // Hash the password if it's changed
    $password_sql = "";
    $password_param = [];
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $password_sql = ", password=?";
        $password_param = [$hashed_password];
    }

    // Update query
    $sql = "UPDATE userstable SET username=? $password_sql, updated_at=NOW() WHERE id=?";
    $stmt = $conn->prepare($sql);

    if (!empty($new_password)) {
        $stmt->bind_param("ssi", $new_username, $hashed_password, $user_id);
    } else {
        $stmt->bind_param("si", $new_username, $user_id);
    }
    

    if ($stmt->execute()) {
        $_SESSION['message'] = "Profile updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Failed to update profile. Try again!";
        $_SESSION['message_type'] = "error";
    }

    header("Location: profile.php");
    exit();
}
?>
