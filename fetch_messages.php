<?php
session_start();
include('db.php');

// Set correct timezone
date_default_timezone_set('Asia/Kolkata'); 

if (!isset($_GET['receiver_id']) || !is_numeric($_GET['receiver_id'])) {
    echo "<p class='error-message'>No user selected.</p>";
    exit();
}

$receiverId = intval($_GET['receiver_id']);
$senderId = $_SESSION['user_id'];

// Set MySQL timezone
$conn->query("SET time_zone = '+04:30'");

// Fetch messages with converted time
$query = "SELECT *, CONVERT_TZ(timestamp, '+00:00', '+05:30') AS local_timestamp 
          FROM messagestable 
          WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
          AND NOT (sender_id = ? AND is_deleted_sender = 1)
          ORDER BY timestamp ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("iiiii", $senderId, $receiverId, $receiverId, $senderId, $senderId);
$stmt->execute();
$result = $stmt->get_result();

$lastDate = ""; 

echo '<style>
    .messages { height: 400px; overflow-y: auto; border-bottom: 1px solid #ddd; padding: 10px; }
    .date-separator { text-align: center; margin: 10px 0; font-size: 12px; color: gray; font-weight: bold; }
    .message-container { display: flex; align-items: center; margin: 5px 0; width: 100%; }
    .sent { justify-content: flex-end; }
    .received { justify-content: flex-start; }
    .message { padding: 10px 15px; border-radius: 18px; max-width: 60%; font-size: 14px; line-height: 1.4; word-wrap: break-word; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2); display: inline-block; position: relative; }
    .sent .message { background: #4CAF50; color: white; }
    .received .message { background: #EAEAEA; color: black; }
    .time { font-size: 12px; color: rgba(255, 255, 255, 0.7); margin-left: 10px; display: inline-block; }
    .received .time { color: rgba(0, 0, 0, 0.6); }
</style>';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $isSender = ($row['sender_id'] == $senderId);
        $messageDate = date('d M Y', strtotime($row['local_timestamp'])); // Use converted timestamp
        $time = date('h:i A', strtotime($row['local_timestamp'])); 
        $class = $isSender ? 'sent' : 'received';

        if ($messageDate != $lastDate) {
            $displayDate = (date('d M Y') == $messageDate) ? 'Today' : 
                           ((date('d M Y', strtotime('yesterday')) == $messageDate) ? 'Yesterday' : $messageDate);
            echo "<div class='date-separator'>$displayDate</div>";
            $lastDate = $messageDate;
        }

        echo '<div class="message-container ' . $class . '">
                <div class="message">' . htmlspecialchars($row['message']) . 
                '<span class="time">' . $time . '</span>
                </div>
              </div>';
    }
} else {
    echo "<p class='error-message'>No messages yet. Start chatting!</p>";
}

$stmt->close();
?>
