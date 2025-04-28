<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include(__DIR__ . '/../../db_connect.php');

$userId = $_SESSION['user_id'];

// Fetch all notifications
$query = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);

// Mark all as read
$updateQuery = "UPDATE notifications SET status = 'read' WHERE user_id = ? AND status = 'unread'";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->bind_param("i", $userId);
$updateStmt->execute();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications - AI Sales Forecasting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #0d1b33;
            color: white;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: rgba(134, 131, 131, 0.57);
            padding-top: 20px;
            position: fixed;
            overflow-y: auto;
        }
        .sidebar h2 {
            text-align: center;
            color: white;
        }
        .sidebar ul {
            padding: 0;
            list-style: none;
        }
        .sidebar ul li a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            transition: 0.3s;
        }
        .sidebar ul li a:hover {
            background-color: #007bff;
        }
        .content {
            margin-left: 270px;
            padding: 20px;
            flex-grow: 1;
        }
        .notification {
            background-color: #1e2a47;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .notification.unread {
            background-color: #007bff;
        }
        .notification .title {
            font-weight: bold;
        }
        .notification .message {
            font-size: 14px;
        }
        .notification .timestamp {
            font-size: 12px;
            color: #ccc;
        }
        .notification-btn {
            color: white;
            text-decoration: none;
            background-color: #007bff;
            padding: 5px 15px;
            border-radius: 5px;
        }
        .notification-btn:hover {
            background-color: #0056b3;
        }
        footer {
            text-align: center;
            padding: 8px 0;
            background-color: #1e2a47;
            color: white;
            border-radius: 10px;
            margin-top: auto;
        }
        .badge {
            font-size: 12px;
            padding: 4px 8px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Menu</h2>
    <ul>
        <li><a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li><a href="upload.php"><i class="bi bi-cloud-upload"></i> Upload Data</a></li>
        <li><a href="report.php"><i class="bi bi-file-earmark-bar-graph"></i> Reports</a></li>
        <li><a href="analytics.php"><i class="bi bi-bar-chart"></i> Analytics</a></li>
        <li><a href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
        <li><a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
        <li>
            <a href="notifications.php">
                <span><i class="bi bi-bell"></i> Notifications</span>
                <span id="notifCount" class="badge bg-danger">
                    <?php
                    $countQuery = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND status = 'unread'";
                    $countStmt = $conn->prepare($countQuery);
                    $countStmt->bind_param("i", $userId);
                    $countStmt->execute();
                    $countResult = $countStmt->get_result();
                    $unread = $countResult->fetch_assoc();
                    echo $unread['unread_count'] > 0 ? $unread['unread_count'] : '';
                    ?>
                </span>
            </a>
        </li>
        <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="content">
    <h2>Notifications</h2>

    <!-- Display Notifications -->
    <?php if (count($notifications) > 0): ?>
        <?php foreach ($notifications as $notification): ?>
            <div class="notification <?php echo $notification['status'] == 'unread' ? 'unread' : ''; ?>">
                <div class="title"><?php echo htmlspecialchars($notification['title']); ?></div>
                <div class="message"><?php echo htmlspecialchars($notification['message']); ?></div>
                <div class="timestamp"><?php echo date('F j, Y, g:i a', strtotime($notification['created_at'])); ?></div>
                <a href="notification_details.php?id=<?php echo $notification['id']; ?>" class="notification-btn">View Details</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No new notifications.</p>
    <?php endif; ?>
</div>

<!-- Footer -->
<footer>
    <p>&copy; 2025 AI Sales Forecasting. All rights reserved.</p>
</footer>

<!-- JS -->
<script>
    function fetchUnreadCount() {
        fetch('fetch_unread_count.php')
            .then(res => res.text())
            .then(count => {
                const badge = document.getElementById('notifCount');
                badge.textContent = count > 0 ? count : '';
            });
    }

    // Poll every 10 seconds
    setInterval(fetchUnreadCount, 10000);
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
