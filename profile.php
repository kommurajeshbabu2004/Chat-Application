<?php
session_start();
include 'db.php'; // Database connection

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    die("Unauthorized access!");
}

// Fetch user details
$sql = "SELECT * FROM userstable WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found!");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
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
            overflow-y: auto;
            max-height: 90vh;
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
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .form-group input:disabled {
            background-color: #e9ecef;
        }

        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #45a049;
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
            padding: 0 10px;
        }

        .error-message {
            color: red;
            font-weight: bold;
        }

        .success-message {
            color: green;
            font-weight: bold;
        }

        #update-btn {
            margin-top: 10px;
            display: none;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>My Profile</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="<?php echo $_SESSION['message_type'] === 'success' ? 'success-message' : 'error-message'; ?>">
            <?php echo $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
    <?php endif; ?>


    <!-- Success or Error Messages -->
    <div id="message-container"></div>

    <form action="update_profile.php" method="POST" onsubmit="return validateForm()">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" 
                   value="<?php echo htmlspecialchars($user['username']); ?>" disabled oninput="validateUsername()">
            <div id="username-message" class="error-message"></div>
        </div>

        <div class="form-group">
            <label for="password">New Password</label>
            <div class="password-container">
                <input type="password" id="password" name="password" placeholder="Enter new password" disabled oninput="validatePassword()">
                <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">👁️</button>
            </div>
            <div id="password-message" class="error-message"></div>
        </div>

        <div class="form-group">
            <label for="unique_id">Unique ID</label>
            <input type="text" id="unique_id" value="<?php echo htmlspecialchars($user['unique_id']); ?>" disabled>
        </div>

        <div class="form-group">
            <label for="created_at">Created At</label>
            <input type="text" id="created_at" value="<?php echo htmlspecialchars($user['created_at']); ?>" disabled>
        </div>

        <div class="form-group">
            <label for="updated_at">Updated At</label>
            <input type="text" id="updated_at" value="<?php echo htmlspecialchars($user['updated_at']); ?>" disabled>
        </div>

        <div class="form-group">
            <button type="button" onclick="enableEdit()">Edit</button>
            <button type="submit" id="update-btn">Update</button>
        </div>

        <div class="form-group">
            <button type="button" onclick="window.location.href='chatwindow.php'">Back to Chat</button>
        </div>
    </form>
</div>

<script>
    function enableEdit() {
        document.getElementById('username').removeAttribute('disabled');
        document.getElementById('password').removeAttribute('disabled');
        document.getElementById('update-btn').style.display = 'block';
    }

    function togglePasswordVisibility() {
        const passwordField = document.getElementById('password');
        passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
    }

    function validateUsername() {
        const username = document.getElementById('username').value;
        const usernameMessage = document.getElementById('username-message');
        const usernameRegex = /^[A-Z][A-Za-z0-9]*$/; // Starts with capital letter, then letters/numbers

        if (!usernameRegex.test(username)) {
            usernameMessage.innerText = "Username must start with a capital letter!";
        } else {
            usernameMessage.innerText = "";
        }
    }

    function validatePassword() {
        const password = document.getElementById('password').value;
        const passwordMessage = document.getElementById('password-message');
        const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

        if (!passwordRegex.test(password)) {
            passwordMessage.innerText = "Password must be at least 8 characters long and include a letter, number, and special character!";
        } else {
            passwordMessage.innerText = "";
        }
    }

    function validateForm() {
        validateUsername();
        validatePassword();

        const usernameMessage = document.getElementById('username-message').innerText;
        const passwordMessage = document.getElementById('password-message').innerText;

        if (usernameMessage || passwordMessage) {
            return false;
        }
        return true;
    }
</script>

</body>
</html>
