<?php
// Create a database and tables if they don't already exist

$servername = "localhost";
$username = "root";
$password = "";
$conn = new mysqli($servername, $username, $password);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS chat_application";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully.<br>";
} else {
    echo "Error creating database: " . $conn->error;
}

// Use the chat_application database
$conn->select_db('chat_application');

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS userstable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    unique_id VARCHAR(50) NOT NULL UNIQUE,  
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'userstable' created successfully.<br>";
} else {
    echo "Error creating table 'userstable': " . $conn->error;
}

// Create messages table
$sql = "CREATE TABLE IF NOT EXISTS messagestable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_deleted_sender TINYINT(1) DEFAULT 0,
    is_archived_sender TINYINT(1) DEFAULT 0,
    file_path VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (sender_id) REFERENCES userstable(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES userstable(id) ON DELETE CASCADE
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'messagestable' created successfully.<br>";
} else {
    echo "Error creating table 'messagestable': " . $conn->error;
}

// Create archived chats table
$sql = "CREATE TABLE IF NOT EXISTS archived_chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    chat_id INT NOT NULL,
    archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES userstable(id) ON DELETE CASCADE,
    FOREIGN KEY (chat_id) REFERENCES messagestable(id) ON DELETE CASCADE
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'archived_chats' created successfully.<br>";
} else {
    echo "Error creating table 'archived_chats': " . $conn->error;
}

// Create groups table
$sql = "CREATE TABLE IF NOT EXISTS groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_name VARCHAR(255) NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES userstable(id) ON DELETE CASCADE
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'groups' created successfully.<br>";
} else {
    echo "Error creating table 'groups': " . $conn->error;
}

// Create group members table
$sql = "CREATE TABLE IF NOT EXISTS group_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    user_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES userstable(id) ON DELETE CASCADE
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'group_members' created successfully.<br>";
} else {
    echo "Error creating table 'group_members': " . $conn->error;
}

// Create group messages table
$sql = "CREATE TABLE IF NOT EXISTS group_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    file_path VARCHAR(255) DEFAULT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES userstable(id) ON DELETE CASCADE
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'group_messages' created successfully.<br>";
} else {
    echo "Error creating table 'group_messages': " . $conn->error;
}

// Close the connection
$conn->close();
?>
