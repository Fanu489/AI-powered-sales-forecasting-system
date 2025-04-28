<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > 1800) {
    session_unset();
    session_destroy();
    header("Location: login.php?error=Session expired");
    exit();
}
$_SESSION['last_activity'] = time();

$conn = mysqli_connect('localhost', 'root', '', 'sales_forecasting');
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

function getCompanyName($user_id, $conn) {
    $query = "SELECT company_name FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $company_name);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        return $company_name ?: "Company";
    }
    return "Company";
}

function getTotalUploads($user_id, $conn) {
    $query = "SELECT COUNT(*) FROM sales_data WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $total);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        return $total;
    }
    return 0;
}

$company_name = getCompanyName($_SESSION['user_id'], $conn);
$total_uploads = getTotalUploads($_SESSION['user_id'], $conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - AI Sales Forecasting</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
            display: flex;
        }
        .sidebar {
            width: 260px;
            background-color: #1e293b;
            padding: 20px 0;
            height: 100vh;
            position: fixed;
        }
        .sidebar h2 {
            text-align: center;
            font-size: 1.5rem;
            color: #38bdf8;
            margin-bottom: 1rem;
        }
        .sidebar ul {
            list-style: none;
            padding: 0 10px;
        }
        .sidebar ul li a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .sidebar ul li a:hover {
            background-color: #334155;
            color: #38bdf8;
            transform: translateX(5px);
        }
        .sidebar i {
            margin-right: 10px;
        }
        .content {
            margin-left: 260px;
            padding: 30px;
            flex-grow: 1;
        }
        .dashboard-header h1 {
            font-size: 2rem;
            font-weight: bold;
            color: #38bdf8;
        }
        .dashboard-header p {
            font-size: 1rem;
            color: #94a3b8;
        }
        .stats-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 30px;
            justify-content: flex-start;
        }
        .stat-card {
            background: #1e293b;
            padding: 25px;
            border-radius: 12px;
            width: 250px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
            transition: transform 0.2s ease;
            text-align: center;
        }
        .stat-card:hover {
            transform: scale(1.03);
        }
        .stat-card i {
            font-size: 30px;
            color: #38bdf8;
            margin-bottom: 10px;
        }
        .stat-card h3 {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }
        .stat-card p {
            color: #cbd5e1;
            margin: 0;
        }
        .recent-activities {
            margin-top: 40px;
            background: #1e293b;
            padding: 20px;
            border-radius: 12px;
        }
        .recent-activities h4 {
            margin-bottom: 15px;
            color: #38bdf8;
        }
        .activity-list {
            padding-left: 0;
            list-style: none;
        }
        .activity-list li {
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .graph-placeholder {
            background: #1e293b;
            border-radius: 12px;
            margin-top: 40px;
            padding: 20px;
        }
        footer {
            text-align: center;
            margin-top: 40px;
            font-size: 0.9rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2><i class="bi bi-lightning-charge-fill"></i> AI Forecast</h2>
    <ul>
        <li><a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li><a href="upload.php"><i class="bi bi-cloud-upload"></i> Upload Data</a></li>
        <li><a href="report.php"><i class="bi bi-file-earmark-bar-graph"></i> Reports</a></li>
        <li><a href="analytics.php"><i class="bi bi-bar-chart"></i> Analytics</a></li>
        <li><a href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
        <li><a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
        <li><a href="notifications.php"><i class="bi bi-bell"></i> Notifications</a></li>
        <li><a href="help.php"><i class="bi bi-question-circle"></i> Help</a></li>
        <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
    </ul>
</div>

<div class="content">
    <div class="dashboard-header">
        <h1>Welcome, <?php echo htmlspecialchars($company_name); ?>!</h1>
        <p>Access smart insights and manage your forecasts efficiently.</p>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <i class="bi bi-graph-up-arrow"></i>
            <h3><?php echo $total_uploads; ?></h3>
            <p>Total Uploads</p>
        </div>
        <div class="stat-card">
            <i class="bi bi-bar-chart-line-fill"></i>
            <h3>+15%</h3>
            <p>Sales Growth</p>
        </div>
    </div>

    <div class="recent-activities">
        <h4>Recent Activities</h4>
        <ul class="activity-list">
            <li>New data uploaded</li>
            <li>Report generated</li>
        </ul>
    </div>

    <div class="graph-placeholder">
        <canvas id="salesChart"></canvas>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> AI Sales Forecasting System. All rights reserved.</p>
    </footer>
</div>

<script>
    const ctx = document.getElementById('salesChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Sales Forecast',
                data: [120, 190, 300, 500, 200, 300],
                borderColor: '#38bdf8',
                backgroundColor: 'rgba(56, 189, 248, 0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: {
                    display: true,
                    text: 'Monthly Forecast Overview',
                    color: '#f1f5f9',
                    font: {
                        size: 18
                    }
                }
            },
            scales: {
                x: { ticks: { color: '#f1f5f9' } },
                y: { ticks: { color: '#f1f5f9' } }
            }
        }
    });
</script>

</body>
</html>