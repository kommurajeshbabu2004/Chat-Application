<?php
session_start(); // Start the session
include('db.php'); // Include database connection

$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use prepared statement to prevent SQL injection
    $query = "SELECT * FROM userstable WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);  // "s" means string type
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Valid credentials
            $_SESSION['username'] = $username; // Set session variable
            $_SESSION['user_id'] = $user['id']; // Store user_id in session
            header("Location: chatwindow.php"); // Redirect to chat page
            exit();
        } else {
            $errorMessage = "Invalid password!";
        }
    } else {
        $errorMessage = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }

        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        h2 {
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input[type="text"], .form-group input[type="password"] {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .form-group input[type="submit"], .form-group input[type="button"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-group input[type="submit"]:hover, .form-group input[type="button"]:hover {
            background-color: #45a049;
        }

        .error-message {
            color: red;
            font-weight: bold;
        }

        .password-container {
            display: flex;
            align-items: center;
        }

        .password-container input {
            width: calc(100% - 40px);
        }

        .toggle-password {
            background: none;
            border: none;
            color: #555;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Login</h2>

    <!-- Display error message if login fails -->
    <?php if ($errorMessage): ?>
        <div class="error-message"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <!-- Login form -->
    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <div class="password-container">
                <input type="password" id="password" name="password" required>
                <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">👁️</button>
            </div>
        </div>

        <div class="form-group">
            <input type="submit" value="Submit">
        </div>

        <div class="form-group">
            <input type="button" value="Back" onclick="window.location.href='signup.php'; return false;">
        </div>
    </form>
</div>

<script>
    // Password visibility toggle
    function togglePasswordVisibility() {
        const passwordField = document.getElementById('password');
        const passwordFieldType = passwordField.type;
        
        if (passwordFieldType === 'password') {
            passwordField.type = 'text';
        } else {
            passwordField.type = 'password';
        }
    }
</script>

</body>
</html>
