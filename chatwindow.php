<?php
session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch logged-in user's unique ID
$query = "SELECT unique_id FROM userstable WHERE username = '$username'";
$result = $conn->query($query);
$user = $result->fetch_assoc();
$unique_id = $user['unique_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="winstyle.css">
    <style>
        /* WhatsApp-like styling for the chat and search interface */
        .chat-container {
            display: flex;
            height: 90vh;
        }

        .sidebar {
            width: 300px;
            background-color: #f0f0f0;
            padding: 20px;
            box-sizing: border-box;
            overflow-y: auto;
        }

        .chat-box {
            flex-grow: 1;
            background-color: #ffffff;
            padding: 20px;
            box-sizing: border-box;
            display: none;
        }

        .sidebar h2 {
            font-size: 20px;
            color: #4CAF50;
        }

        #searchUser {
            width: 80%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            font-size: 16px;
            outline: none;
            transition: all 0.3s ease-in-out;
            background-color: #f9f9f9;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Hover effect */
        #searchUser:hover {
            border-color: #999;
            background-color: #fff;
        }

        /* Focus effect */
        #searchUser:focus {
            border-color: #4CAF50;
            background-color: #fff;
            box-shadow: 0 0 8px rgba(76, 175, 80, 0.5);
        }

        /* Placeholder styling */
        #searchUser::placeholder {
            color: #aaa;
            font-style: italic;
        }

        /* Smooth typing effect */
        #searchUser:active {
            transition: none;
        }


        #userList {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
        }

        .user {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 5px;
            background-color: #f9f9f9;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .user:hover {
            background-color: #f1f1f1;
        }

        .user .username {
            font-weight: bold;
        }

        .user .status {
            font-size: 12px;
            color: gray;
        }

        .messages {
            height: 400px;
            overflow-y: auto;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .input-area {
            display: flex;
            align-items: center;
        }

        .input-area input[type="text"] {
            width: 85%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
        }

        .input-area button {
            padding: 15px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 20px;
            margin-left: 10px;
            cursor: pointer;
        }

        .input-area button:hover {
            background-color: #45a049;
        }

        .error-message {
            color: red;
            font-weight: bold;
        }

        h2 {
            font-size: 18px;
            margin-bottom: 15px;
        }

        /* Removed green background from sent messages */
        .sent .message {
            background: #4CAF50 /* Neutral Gray background */
            color: black;
            text-align: left;
        }

        .received .message {
            background: #EAEAEA; /* Light Gray */
            color: black;
        }
        .sent {
           background: none !important; /* Remove any applied background */
        }
        .sent .message {
            background: #4CAF50 !important; /* Ensure neutral gray background */
        }
        button {
            background-color: #4CAF50; /* Green */
            color: white;
            font-size: 16px;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        /* Hover Effect */
        button:hover {
            background-color: #45a049; /* Darker green */
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        /* Active Click Effect */
        button:active {
            transform: scale(0.98);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        /* Ripple Effect on Click */
        button::after {
            content: "";
            position: absolute;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.3);
            display: block;
            border-radius: 50%;
            transform: scale(0);
            opacity: 0;
            transition: transform 0.5s, opacity 0.6s;
        }

        button:active::after {
            transform: scale(3);
            opacity: 1;
        }

        /* User List & Active Chats Styling */
        #userList, #activeChats {
            background: #ffffff;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
            transition: all 0.3s ease-in-out;
            max-height: 300px;
            overflow-y: auto;
        }

        /* Section Headings */
        h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
            padding-left: 5px;
            border-left: 4px solid #4CAF50;
            transition: all 0.3s ease-in-out;
        }

        /* Hover Effect on Containers */
        #userList:hover, #activeChats:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        /* User List Items */
        .user-item, .chat-item {
            background: #f8f8f8;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease-in-out;
            cursor: pointer;
            border: 1px solid #ddd;
        }

        /* Hover Effect for Users/Chats */
        .user-item:hover, .chat-item:hover {
            background: #4CAF50;
            color: white;
            transform: scale(1.05);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        /* Scrollbar Styling */
        #userList::-webkit-scrollbar, #activeChats::-webkit-scrollbar {
            width: 6px;
        }

        #userList::-webkit-scrollbar-thumb, #activeChats::-webkit-scrollbar-thumb {
            background: #4CAF50;
            border-radius: 6px;
        }

        #userList::-webkit-scrollbar-track, #activeChats::-webkit-scrollbar-track {
            background: #ddd;
        }

        /* Active chat styling */
        .chat-item {
            background: #f8f8f8;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease-in-out;
            cursor: pointer;
            border: 1px solid #ddd;
            font-weight: normal;
            position: relative;
        }

        .chat-item:hover {
            background: #4CAF50;
            color: white;
            transform: scale(1.05);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        /* Highlight the active chat */
        .chat-item.active {
            background: #4CAF50;
            color: white;
            font-weight: bold;
            border: 2px solid #388E3C;
        }

        /* Indicator for active chat */
        .chat-item.active::after {
            content: "•";
            position: absolute;
            right: 10px;
            font-size: 20px;
            color: white;
        }

        /* Message Input Bar */
        #message {
            width: 80%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 20px;
            font-size: 16px;
            transition: all 0.3s ease-in-out;
        }

        /* Glowing Effect on Focus */
        #message:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 8px rgba(76, 175, 80, 0.8);
            outline: none;
        }

        /* Send Button */
        #sendBtn {
            background: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 20px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            margin-left: 10px;
        }

        /* Send Button Hover Effect */
        #sendBtn:hover {
            background: #388E3C;
        }

        /* Glowing Effect when Typing */
        #message:focus + #sendBtn {
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.8);
        }

        /* Sending Effect */
        #sendBtn.sending {
            background: #ff9800;
            cursor: not-allowed;
        }

        .small {
            font-size: 12px;
            color: gray;
            margin-left: 5px;
            display: block;
            text-align: right;
        }


        /* Menu Container */
        .menu-container {
            position: relative;
            display: inline-block;
        }

        /* Menu Button (☰) */
        .menu-btn {
            background: #007bff; /* Visible Blue Background */
            border: 2px solid #0056b3; /* Border for Visibility */
            font-size: 28px; /* Slightly Larger */
            cursor: pointer;
            padding: 10px 15px;
            border-radius: 8px; /* Rounded Corners */
            color: white; /* White ☰ Symbol */
            transition: background 0.3s ease, transform 0.1s ease;
            outline: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Hover and Active Effects */
        .menu-btn:hover {
            background-color: rgba(0, 0, 0, 0.1);
        }

        .menu-btn:active {
            transform: scale(0.95);
        }

        /* Active State (when dropdown is open) */
        .menu-container.active .menu-btn {
            background-color: rgba(0, 0, 0, 0.2);
        }

        /* Keyboard Focus Styles */
        .menu-btn:focus-visible {
            outline: 2px solid #007bff;
        }

        /* Dropdown Menu */
        .menu-dropdown {
            display: none;
            position: absolute;
            right: 0;
            background: white;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            overflow: hidden;
            width: 200px; /* Slightly wider for better readability */
            z-index: 100;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        /* Show Dropdown */
        .menu-container.active .menu-dropdown {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        /* Dropdown Button Styles */
        .menu-dropdown button {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 12px 15px; /* Better spacing */
            border: none;
            background: none;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.2s ease;
            color: #333;
            text-align: left;
        }

        /* Hover Effect */
        .menu-dropdown button:hover {
            background: #f0f0f0;
        }

        /* Active Effect */
        .menu-dropdown button:active {
            background: #e0e0e0;
        }

        /* Keyboard Navigation Support */
        .menu-dropdown button:focus-visible {
            outline: 2px solid #007bff;
        }

        .logout-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            transition: 0.3s ease-in-out;
        }

        .logout-btn:hover {
            background: #c0392b;
        }


    </style>
</head>
<body>
    <div class="chat-container">
        <div class="sidebar">
            <h2>Welcome, <?php echo $username; ?></h2>
            <input type="text" id="searchUser" placeholder="Enter Unique ID">
            <button onclick="searchUser()">Search</button>
            <div id="userList"></div> <!-- User search results will appear here -->
            <h3>Active Chats</h3>
            <div id="activeChats"></div> <!-- Active chats list will appear here -->
        </div>

        <div class="chat-box" id="chatBox">
            <h2>Chat</h2>
            <div class="messages" id="messages"></div>
            <div class="input-area">
                <input type="text" id="message" placeholder="Type a message...">
                <button onclick="sendMessage()">Send</button>
            </div>
        </div>

        <!-- Menu Button -->
        <div class="menu-container">
            <button class="menu-btn" onclick="toggleMenu()">☰</button>
            <div id="menuDropdown" class="menu-dropdown">
                <ul>
                    <li><a href ="profile.php"><button class="viewProfileBtn">👤 View Profile</button></a></li>
                    <li><button class="clearChatBtn">🗑️ Clear Chat</button></li>
                    <li><button class="archiveChatBtn">📂 Archive Chat</button></li>
                    <li><button class="blockUserBtn">🚫 Block User</button></li>
                    <li><button class="unblockUserBtn">⚠️ Unblock User</button></li>
                    <li><button class="logout-btn" onclick="logout()">🚪 Logout</button></li>
                </ul>
            </div>
        </div>

    </div>

    <script>
        let receiverId = null;
        const activeChats = {}; // Store active chats by receiver ID

        // Function to search user by unique ID
        function searchUser() {
            const uniqueId = document.getElementById('searchUser').value.trim();

            if (uniqueId.length < 4) {
                document.getElementById('userList').innerHTML = "<p>Enter at least 4 characters to search.</p>";
                return;
            }

            document.getElementById('userList').innerHTML = "<p>Searching...</p>";

            fetch('search_user.php?unique_id=' + uniqueId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Display the user's name below the search bar
                        const userName = data.username;
                        const userId = data.user_id;

                        // Check if this user is already in active chats
                        if (!activeChats[userId]) {
                            const userElement = document.createElement('div');
                            userElement.classList.add('user');
                            userElement.innerHTML = `<span class="username">${userName}</span><span class="status">Online</span>`;
                            userElement.onclick = () => startChat(userId, userName);
                            document.getElementById('userList').innerHTML = ''; // Clear previous search results
                            document.getElementById('userList').appendChild(userElement);
                        } else {
                            document.getElementById('userList').innerHTML += "<p>User is already in your active chats.</p>";
                        }
                    } else {
                        document.getElementById('userList').innerHTML = "<p>No user found with that unique ID.</p>";
                    }
                })
                .catch(error => {
                    console.error('Error searching user:', error);
                    document.getElementById('userList').innerHTML = "<p>Error occurred while searching.</p>";
                });
        }

        // Function to open chat with the selected user
        function startChat(userId, userName) {
            receiverId = userId;
            activeChats[userId] = userName; // Add user to active chats
            document.getElementById('chatBox').style.display = 'block'; // Show the chat box
            document.getElementById('messages').innerHTML = ''; // Clear previous messages
            fetchMessages(); // Load chat messages

            // Change chat window title
            document.getElementById('chatBox').querySelector('h2').textContent = `Chat with ${userName}`;

            // Update active chats in the sidebar
            updateActiveChats(userId);
        }

        function updateActiveChats(activeUserId) {
            const activeChatsContainer = document.getElementById('activeChats');
            activeChatsContainer.innerHTML = ''; // Clear existing chats

            for (const userId in activeChats) {
                const userName = activeChats[userId];
                const userElement = document.createElement('div');
                userElement.classList.add('chat-item');
                userElement.innerHTML = `<span class="username">${userName}</span>`;

                if (userId == activeUserId) {
                    userElement.classList.add('active'); // Highlight active chat
                }

                userElement.onclick = () => startChat(userId, userName);
                activeChatsContainer.appendChild(userElement);
            }
        }


        // Function to fetch messages for the current chat
        function fetchMessages() {
            if (!receiverId) return;

            fetch('fetch_messages.php?receiver_id=' + receiverId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('messages').innerHTML = data;
                    document.getElementById('messages').scrollTop = document.getElementById('messages').scrollHeight; // Auto-scroll
                })
                .catch(error => console.error('Error fetching messages:', error));
        }

        // Function to send a message
        function sendMessage() {
            const message = document.getElementById('message').value.trim();

            if (message === "" || !receiverId) return;

            fetch('send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `receiver_id=${receiverId}&message=${encodeURIComponent(message)}`
            })
            .then(response => response.text())
            .then(() => {
                document.getElementById('message').value = ''; // Clear the message input
                fetchMessages(); // Refresh chat
            })
            .catch(error => console.error('Error sending message:', error));
        }



        /* MENU BUTTON CODES*/
        document.addEventListener("DOMContentLoaded", function () {
            const menuBtn = document.querySelector(".menu-btn");
            const menuContainer = document.querySelector(".menu-container");
            const menuDropdown = document.getElementById("menuDropdown");

            // Toggle Menu Visibility
            menuBtn.addEventListener("click", function (event) {
                event.stopPropagation();
                menuContainer.classList.toggle("active");
            });

            // Close menu when clicking outside
            document.addEventListener("click", function (event) {
                if (!event.target.closest(".menu-container")) {
                    menuContainer.classList.remove("active");
                }
            });

            // View Profile Function
            function viewProfile() {
                window.location.href="profile.php";
                // Redirect or fetch user profile data
            }

            // Clear Chat Function
             // Function to clear chat
            async function clearChat() {
                try {
                    if (!receiverId) {
                        alert("Error: No chat partner selected.");
                        return;
                    }

                    const confirmation = confirm("Are you sure you want to clear the chat?");
                    if (!confirmation) return;

                    // Send request to clear chat messages
                    const response = await fetch("clear_chat.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: new URLSearchParams({
                            sender_id: "<?php echo $_SESSION['user_id']; ?>", // Logged-in user's ID
                            receiver_id: receiverId // ID of the person they're chatting with
                        })
                    });

                    const result = await response.text();
                    alert(result);

                    // If successful, clear chat UI
                    if (result.toLowerCase().includes("success")) {
                        document.getElementById('messages').innerHTML = '';
                    }
                } catch (error) {
                    console.error("Error clearing chat:", error);
                    alert("Failed to clear chat. Please try again.");
                }
            }

            // Archive Chat Function
            function archiveChat() {
                alert("Chat archived! (Implement archive logic here)");
            }

            // Block User Function
            function blockUser() {
                if (confirm("Are you sure you want to block this user?")) {
                    fetch("block_user.php", { method: "POST" })
                        .then(response => response.text())
                        .then(data => alert(data))
                        .catch(error => console.error("Error blocking user:", error));
                }
            }

            // Unblock User Function (Replaces Report User)
            function unblockUser() {
                if (confirm("Are you sure you want to unblock this user?")) {
                    fetch("unblock_user.php", { method: "POST" })
                        .then(response => response.text())
                        .then(data => alert(data))
                        .catch(error => console.error("Error unblocking user:", error));
                }
            }

            // Assign functions to buttons by class selectors (because IDs aren't defined in the HTML provided)
            document.querySelector(".viewProfileBtn").addEventListener("click", viewProfile);
            document.querySelector(".clearChatBtn").addEventListener("click", clearChat);
            document.querySelector(".muteChatBtn").addEventListener("click", muteChat);
            document.querySelector(".archiveChatBtn").addEventListener("click", archiveChat);
            document.querySelector(".blockUserBtn").addEventListener("click", blockUser);
            document.querySelector(".unblockUserBtn").addEventListener("click", unblockUser);
        });




        function logout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "login.php"; // Redirect to logout page
            }
        }


        // Auto-refresh the chat every 2 seconds
        setInterval(fetchMessages, 10000);
    </script>
</body>
</html>
