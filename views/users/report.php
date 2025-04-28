<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// DB connection
$conn = new mysqli("localhost", "root", "", "sales_forecasting");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Filtering Logic
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$start_date = isset($_GET['start_date']) ? $conn->real_escape_string($_GET['start_date']) : '';
$end_date = isset($_GET['end_date']) ? $conn->real_escape_string($_GET['end_date']) : '';

$query = "SELECT * FROM sales_data WHERE 1";

if (!empty($search)) {
    $query .= " AND product_name LIKE '%$search%'";
}
if (!empty($start_date) && !empty($end_date)) {
    $query .= " AND date BETWEEN '$start_date' AND '$end_date'";
}

$query .= " ORDER BY date DESC";
$result = $conn->query($query);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
      html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    background-color: #001f3d;
    color: white;
}

        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            background-color: rgba(134, 131, 131, 0.57);
            padding-top: 20px;
        }
        .sidebar h2 {
            text-align: center;
            color: white;
        }
        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }
        .sidebar ul li a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: white;
            font-size: 16px;
        }
        .sidebar ul li a:hover {
            background-color: #575757;
        }
        .footer {
            
          
            width: 100%;
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            border-radius: 10px;
        }
        .content {
    flex: 1;
    margin-left: 270px;
    padding: 20px;
    padding-bottom: 60px; /* Extra space for footer */
}
        table { background-color: white; color: black; }
        h2 { margin-bottom: 30px; }
        .filter-form {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: black;
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
        <li><a href="notifications.php"><i class="bi bi-bell"></i> Notifications</a></li>
        <li><a href="help.php"><i class="bi bi-question-circle"></i> Help</a></li>
        <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="content">
    <div class="container">
        <h2 class="text-center text-white">Sales Report</h2>

        <!-- Search & Filter Form -->
        <form method="GET" class="filter-form row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by product" value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-3">
                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>">
            </div>
            <div class="col-md-3">
                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Filter</button>
            </div>
        </form>

        <!-- Table -->
        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered table-striped mt-4">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($row['date']) ?></td>
                            <td><?= htmlspecialchars($row['product_name']) ?></td>
                            <td><?= $row['quantity'] ?></td>
                            <td>Ksh <?= number_format($row['revenue']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning mt-4">No results found for your filter. Try adjusting your search or date range.</div>
        <?php endif; ?>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <p>&copy; 2025 Sales Forecasting System. All Rights Reserved.</p>
</div>

</body>
</html>
