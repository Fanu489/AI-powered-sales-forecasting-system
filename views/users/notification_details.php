<?php
session_start();
include(__DIR__ . '/../../db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    echo "Notification ID not provided.";
    exit();
}

$notificationId = $_GET['id'];
$userId = $_SESSION['user_id'];

// Fetch notification details
$query = "SELECT * FROM notifications WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $notificationId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$notification = $result->fetch_assoc();

if (!$notification) {
    echo "Notification not found or access denied.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notification Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light p-4">
    <div class="container">
        <h2><?php echo htmlspecialchars($notification['title']); ?></h2>
        <p><?php echo htmlspecialchars($notification['message']); ?></p>
        <p><small><?php echo date('F j, Y, g:i a', strtotime($notification['created_at'])); ?></small></p>
        <a href="notifications.php" class="btn btn-primary">Back to Notifications</a>
    </div>
</body>
</html>
