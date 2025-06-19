<?php
include('db.php'); // Database connection

$successMessage = "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collecting form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $uniqueId = $_POST['unique_id'];

    // Password validation
    if ($password !== $confirmPassword) {
        $errorMessage = "Passwords do not match!";
    } else {
        // Hash the password before saving to the database
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Get current timestamp for created_at and updated_at
        $timestamp = date('Y-m-d H:i:s');

        // Insert user into the database with created_at and updated_at
        $query = "INSERT INTO userstable (username, password, unique_id, created_at, updated_at) 
                  VALUES ('$username', '$hashedPassword', '$uniqueId', '$timestamp', '$timestamp')";
        if ($conn->query($query) === TRUE) {
            $successMessage = "Registration Successful!";
        } else {
            $errorMessage = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
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

        .form-group input[type="submit"], .form-group input[type="reset"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-group input[type="submit"]:hover, .form-group input[type="reset"]:hover {
            background-color: #45a049;
        }

        .success-message {
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
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
    <h2>Signup Form</h2>
    
    <!-- Display success message if the registration is successful -->
    <?php if ($successMessage): ?>
        <div class="success-message"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <!-- Display error message if the registration fails -->
    <?php if ($errorMessage): ?>
        <div class="error-message"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <!-- Form to capture user data -->
    <form action="signup.php" method="POST" id="signupForm">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required oninput="validateUsername()">
            <div id="username-message"></div>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <div class="password-container">
                <input type="password" id="password" name="password" required oninput="validatePassword()">
                <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">👁️</button>
            </div>
            <div id="password-message"></div>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required oninput="validateConfirmPassword()">
            <div id="confirm-password-message"></div>
        </div>

        <div class="form-group">
            <label for="unique_id">Unique ID</label>
            <input type="text" id="unique_id" name="unique_id" required oninput="validateUniqueId()">
            <div id="unique-id-message"></div>
        </div>

        <div class="form-group">
            <input type="submit" value="Submit" id="submit-button">
        </div>

        <div class="form-group">
            <input type="reset" value="Back" onclick="window.location.href='index.php'; return false;">
        </div>
    </form>
</div>

<script>
    // Password visibility toggle
    function togglePasswordVisibility() {
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('confirm_password');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            confirmPasswordField.type = 'text';
        } else {
            passwordField.type = 'password';
            confirmPasswordField.type = 'password';
        }
    }

    // Real-time validation functions
    function validateUsername() {
        const username = document.getElementById('username').value;
        const usernameMessage = document.getElementById('username-message');
        const usernameRegex = /^[A-Z][A-Za-z0-9]*$/;  // Starts with a capital letter and followed by letters or numbers
        
        if (!usernameRegex.test(username)) {
            usernameMessage.innerHTML = "Username must start with a capital letter!";
            usernameMessage.style.color = "red";
        } else {
            usernameMessage.innerHTML = "";
        }
    }

    function validatePassword() {
        const password = document.getElementById('password').value;
        const passwordMessage = document.getElementById('password-message');
        const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/; // Password must include letters, numbers, and special chars and at least 8 letters

        if (!passwordRegex.test(password)) {
            passwordMessage.innerHTML = "Password must contain at least one letter, one number, and one special character and at least 8 letters!";
            passwordMessage.style.color = "red";
        } else {
            passwordMessage.innerHTML = "";
        }
    }

    function validateConfirmPassword() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const confirmPasswordMessage = document.getElementById('confirm-password-message');

        if (confirmPassword !== password) {
            confirmPasswordMessage.innerHTML = "Passwords do not match!";
            confirmPasswordMessage.style.color = "red";
        } else {
            confirmPasswordMessage.innerHTML = "";
        }
    }

    function validateUniqueId() {
        const uniqueId = document.getElementById('unique_id').value;
        const uniqueIdMessage = document.getElementById('unique-id-message');
        const uniqueIdRegex = /^[A-Za-z0-9_]+$/;  // Regex to allow letters, numbers, and underscores

        if (!uniqueIdRegex.test(uniqueId)) {
            uniqueIdMessage.innerHTML = "Unique ID must contain only letters, numbers, and underscores!";
            uniqueIdMessage.style.color = "red";
        } else {
            uniqueIdMessage.innerHTML = "";
        }
    }
</script>

</body>
</html>
