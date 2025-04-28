<?php
// manage_reports.php

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Redirect to login if not logged in
    header("Location: admin_login.php");
    exit();
}

// Admin user info
$admin_username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Guest';

// Include DB connection
require_once('../../includes/db.php');

// Search functionality
$search = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : '%';

// Pagination functionality
$per_page = 10; // Number of reports per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

// Sorting functionality
$allowed_sort_columns = ['report_name', 'created_at']; // Correct column name
$sort_by = (isset($_GET['sort_by']) && in_array($_GET['sort_by'], $allowed_sort_columns)) ? $_GET['sort_by'] : 'created_at';
$order = isset($_GET['order']) && $_GET['order'] == 'desc' ? 'desc' : 'asc';

// Fetch reports from the database with search, pagination, and sorting
$stmt_reports = $pdo->prepare("SELECT * FROM reports WHERE report_name LIKE ? ORDER BY $sort_by $order LIMIT $per_page OFFSET $offset");
$stmt_reports->execute([$search]);
$reports = $stmt_reports->fetchAll(PDO::FETCH_ASSOC);

// Count total reports for pagination
$stmt_total = $pdo->prepare("SELECT COUNT(*) FROM reports WHERE report_name LIKE ?");
$stmt_total->execute([$search]);
$total_reports = $stmt_total->fetchColumn();
$total_pages = ceil($total_reports / $per_page);

// Handle report deletion
if (isset($_GET['delete_id'])) {
    $report_id = $_GET['delete_id'];

    // Delete the report from the database
    $stmt_delete = $pdo->prepare("DELETE FROM reports WHERE report_id = ?");
    $stmt_delete->execute([$report_id]);

    // Set a session message for confirmation
    $_SESSION['message'] = 'Report deleted successfully.';
    header("Location: manage_reports.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reports</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Styling for the page */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
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

        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar ul li {
            padding: 15px;
            text-align: center;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
            font-size: 18px;
        }

        .sidebar ul li a:hover {
            background-color:rgb(25, 218, 202);
        }

        .content {
            margin-left: 250px; /* Account for the sidebar width */
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .reports-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .reports-table th, .reports-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .reports-table th {
            background-color: #f0f0f0;
        }

        .btn-delete {
            color: red;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-delete:hover {
            text-decoration: underline;
        }

        .btn-print {
            color: green;
            text-decoration: none;
            margin: 0 5px;
        }

        .btn-print:hover {
            text-decoration: underline;
        }

        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination .page-link {
            padding: 8px 12px;
            margin: 0 5px;
            text-decoration: none;
            background-color: #ddd;
            color: black;
            border-radius: 5px;
        }

        .pagination .page-link:hover {
            background-color: #555;
            color: white;
        }

        .success-message {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 16px;
            border-radius: 5px;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            background-color: #333;
            color: white;
            width: 100%;
            position: relative;
            bottom: 0;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2 style="text-align: center; color: #fff;">Admin Panel</h2>
        <ul>
            <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a></li>
            <li><a href="manage_sales.php"><i class="fas fa-chart-line"></i> Manage Sales Data</a></li>
            <li><a href="manage_reports.php"><i class="fas fa-file-alt"></i> Manage Reports</a></li>
            <li><a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <header>
            <h1>Manage Reports</h1>
        </header>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="success-message">
                <?php echo $_SESSION['message']; ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <form method="GET" action="manage_reports.php">
            <input type="text" name="search" placeholder="Search reports" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
            <button type="submit">Search</button>
        </form>

        <main>
            <table class="reports-table">
                <thead>
                    <tr>
                        <th><a href="?sort_by=report_name&order=<?php echo $order == 'asc' ? 'desc' : 'asc'; ?>">Report Name</a></th>
                        <th><a href="?sort_by=created_at&order=<?php echo $order == 'asc' ? 'desc' : 'asc'; ?>">Generated On</a></th>
                        <th>Report Type</th>
                        <th>Report Summary</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($reports) > 0): ?>
                        <?php foreach ($reports as $report): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($report['report_name']); ?></td>
                                <td><?php echo date("F j, Y, g:i a", strtotime($report['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($report['report_type']); ?></td>
                                <td><?php echo htmlspecialchars($report['report_summary']); ?></td>
                                <td>
                                    <a href="view_report.php?id=<?php echo $report['report_id']; ?>" target="_blank" class="btn-view">View</a> | 
                                    <a href="view_report.php?id=<?php echo $report['report_id']; ?>&print=1" target="_blank" class="btn-print">Print</a> |
                                    <a href="?delete_id=<?php echo $report['report_id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this report?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No reports found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="page-link"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        </main>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> AI-Powered Sales Forecasting System. All rights reserved.</p>
    </footer>
</body>
</html>
