<?php
session_start();
include_once('db.php'); // Include your PDO connection

$admin_name = "Admin";

// Count total users
$user_query = "SELECT COUNT(*) AS total_users FROM users";
$user_stmt = $pdo->prepare($user_query);
$user_stmt->execute();
$row_users = $user_stmt->fetch(PDO::FETCH_ASSOC);
$num_users = $row_users['total_users'];

// Count total sales
$sales_query = "SELECT COUNT(*) AS total_sales FROM sales_data";
$sales_stmt = $pdo->prepare($sales_query);
$sales_stmt->execute();
$row_sales = $sales_stmt->fetch(PDO::FETCH_ASSOC);
$num_sales = $row_sales['total_sales'];

// Count total reports (Adjust the query to match your reports table if necessary)
$report_query = "SELECT COUNT(*) AS total_reports FROM sales_data"; // Modify if needed
$report_stmt = $pdo->prepare($report_query);
$report_stmt->execute();
$row_reports = $report_stmt->fetch(PDO::FETCH_ASSOC);
$num_reports = $row_reports['total_reports'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: row;
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fa;
        }

        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            height: 100vh;
            position: fixed;
            padding-top: 20px;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            padding: 15px 20px;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            font-size: 16px;
        }

        .sidebar ul li a i {
            margin-right: 10px;
        }

        .sidebar ul li a:hover {
            background-color: rgb(7, 197, 231);
            border-radius: 5px;
        }

        .content {
            display: flex;
            flex-direction: column;
            flex: 1;
            margin-left: 250px;
            padding: 30px;
            min-height: 100vh;
            box-sizing: border-box;
        }

        header h1 {
            font-weight: 600;
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
        }

        .dashboard-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
        }

        .summary-box {
            background-color: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
        }

        .summary-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        .summary-box h3 {
            margin: 0;
            font-size: 18px;
            color: #666;
        }

        .summary-box p {
            font-size: 24px;
            font-weight: bold;
            margin-top: 10px;
            color: #4CAF50;
        }

        .summary-box .badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: crimson;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        main {
            flex: 1;
        }

        footer {
            text-align: center;
            padding: 15px;
            color: whitesmoke;
            font-size: 14px;
            background-color: rgba(44, 62, 80, 0.66);
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .content {
                margin-left: 200px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a></li>
            <li><a href="manage_sales.php"><i class="fas fa-chart-line"></i> Manage Sales</a></li>
            <li><a href="manage_reports.php"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($admin_name); ?> 👋</h1>
        </header>

        <main>
            <div class="dashboard-summary">
                <div class="summary-box">
                    <h3>Total Users</h3>
                    <p><?php echo $num_users; ?></p>
                    <span class="badge"><?php echo $num_users; ?></span>
                </div>
                <div class="summary-box">
                    <h3>Total Sales Records</h3>
                    <p><?php echo $num_sales; ?></p>
                    <span class="badge"><?php echo $num_sales; ?></span>
                </div>
                <div class="summary-box">
                    <h3>Total Reports</h3>
                    <p><?php echo $num_reports; ?></p>
                    <span class="badge"><?php echo $num_reports; ?></span>
                </div>
            </div>
        </main>

        <footer>
            &copy; <?php echo date("Y"); ?> AI-Powered Sales Forecasting System. All rights reserved.
        </footer>
    </div>

</body>
</html>
