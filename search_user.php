<?php
include('db.php');

$uniqueId = $_GET['unique_id'];

if (empty($uniqueId)) {
    echo json_encode(['success' => false]);
    exit();
}

$query = "SELECT id AS user_id, username FROM userstable WHERE unique_id = '$uniqueId'";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode(['success' => true, 'user_id' => $user['user_id'], 'username' => $user['username']]);
} else {
    echo json_encode(['success' => false]);
}
?>
