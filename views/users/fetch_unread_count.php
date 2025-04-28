<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo 0;
    exit;
}

include(__DIR__ . '/../../db_connect.php');
$userId = $_SESSION['user_id'];

$query = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND status = 'unread'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo $data['unread_count'];
?>
