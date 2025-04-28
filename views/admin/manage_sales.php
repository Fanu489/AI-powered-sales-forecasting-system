<?php
// manage_sales.php

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

require_once('db.php'); // Make sure this file connects to the 'sales_forecasting' DB

$admin_username = $_SESSION['admin_username'] ?? 'Guest';

// Handle sale deletion
if (isset($_GET['delete_id'])) {
    $sale_id = $_GET['delete_id'];
    $stmt_delete = $pdo->prepare("DELETE FROM sales_data WHERE id = ?");
    $stmt_delete->execute([$sale_id]);
    $_SESSION['message'] = 'Sale deleted successfully.';
    header("Location: manage_sales.php");
    exit();
}

// Handle sale update
if (isset($_POST['update_sale'])) {
    $id = $_POST['sale_id'];
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $sale_date = $_POST['sale_date'];

    $stmt_update = $pdo->prepare("UPDATE sales_data SET product_name=?, quantity=?, price=?, sale_date=? WHERE id=?");
    $stmt_update->execute([$product_name, $quantity, $price, $sale_date, $id]);
    $_SESSION['message'] = 'Sale updated successfully.';
    header("Location: manage_sales.php");
    exit();
}

// Handle sale addition
if (isset($_POST['add_sale'])) {
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $sale_date = $_POST['sale_date'];

    $stmt_insert = $pdo->prepare("INSERT INTO sales_data (product_name, quantity, price, sale_date) VALUES (?, ?, ?, ?)");
    $stmt_insert->execute([$product_name, $quantity, $price, $sale_date]);
    $_SESSION['message'] = 'Sale added successfully.';
    header("Location: manage_sales.php");
    exit();
}

// Handle CSV upload
if (isset($_POST['upload_csv'])) {
    if ($_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
        fgetcsv($file); // Skip header
        while (($row = fgetcsv($file)) !== false) {
            $stmt = $pdo->prepare("INSERT INTO sales_data (product_name, quantity, price, sale_date) VALUES (?, ?, ?, ?)");
            $stmt->execute([$row[0], $row[1], $row[2], $row[3]]);
        }
        fclose($file);
        $_SESSION['message'] = 'CSV uploaded successfully.';
        header("Location: manage_sales.php");
        exit();
    }
}

// Handle search or filter
try {
    $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
    $sql = "SELECT * FROM sales_data";
    $params = [];

    if ($search_term !== '') {
        $sql .= " WHERE product_name LIKE ?";
        $params[] = "%$search_term%";
    }

    $sql .= " ORDER BY date DESC";

    $stmt_sales = $pdo->prepare($sql);
    $stmt_sales->execute($params);
    $sales = $stmt_sales->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error fetching sales data: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Sales</title>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { display: flex; margin: 0; font-family: Arial, sans-serif; }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            height: 100vh;
            position: fixed;
            padding-top: 20px;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
        }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { padding: 15px; text-align: center; }
        .sidebar ul li a {
            color: white; text-decoration: none; display: block; font-size: 18px;
        }
        .sidebar ul li a:hover { background:rgba(26, 137, 177, 0.92); }
        .sidebar h2 { text-align: center; margin-bottom: 20px; }
        .content {
            margin-left: 250px; padding: 20px; width: 100%;
        }
        .sales-table {
            width: 100%; border-collapse: collapse; margin-top: 20px;
        }
        .sales-table th, .sales-table td {
            padding: 10px; border: 1px solid #ddd; text-align: left;
        }
        .sales-table th { background-color: #f0f0f0; }
        .btn-delete {
            color: red; text-decoration: none; font-size: 14px;
        }
        .btn-delete:hover { text-decoration: underline; }
        .add-sale-form {
            margin-bottom: 20px; padding: 10px; background: #f9f9f9;
            border-radius: 5px; border: 1px solid #ddd;
        }
        .add-sale-form input, .add-sale-form button {
            padding: 10px; margin: 5px; font-size: 16px;
        }
        .success-message {
            background: #d4edda; color: #155724;
            padding: 10px; border-radius: 5px;
            margin-bottom: 10px; border: 1px solid #c3e6cb;
        }
        .footer {
    background-color: #333;
    color: white;
    text-align: center;
    padding: 15px 0;
    position: fixed;
    bottom: 0;
    left: 250px;
    width: calc(100% - 250px);
    font-size: 14px;
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
        <li><a href="manage_reports.php"><i class="fas fa-file-alt"></i>Manage Reports</a></li>
        <li><a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="content">
    <h1>Manage Sales</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="success-message"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <form method="GET" action="">
        <input type="text" name="search" placeholder="Search by product name..." value="<?= htmlspecialchars($search_term) ?>">
        <button type="submit">Search</button>
    </form>

    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="csv_file" required>
        <button type="submit" name="upload_csv">Upload CSV</button>
    </form>

    <form method="POST" class="add-sale-form">
        <h3>Add New Sale</h3>
        <input type="text" name="product_name" placeholder="Product Name" required>
        <input type="number" name="quantity" placeholder="Quantity" required>
        <input type="number" name="price" placeholder="Price" required>
        <input type="date" name="sale_date" required>
        <button type="submit" name="add_sale">Add Sale</button>
    </form>

    <table class="sales-table">
        <thead>
        <tr>
            <th>Product</th><th>Quantity</th><th>Price</th><th>Date</th><th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($sales as $sale): ?>
            <tr>
    <form method="POST">
        <td>
            <input type="text" name="product_name" value="<?= htmlspecialchars($sale['product_name'] ?? '') ?>">
        </td>
        <td>
            <input type="number" name="quantity" value="<?= htmlspecialchars($sale['quantity'] ?? '') ?>">
        </td>
        <td>
            <input type="number" name="price" value="<?= htmlspecialchars($sale['price'] ?? '') ?>">
        </td>
        <td>
            <input type="date" name="sale_date" value="<?= htmlspecialchars($sale['sale_date'] ?? '') ?>">
            <input type="hidden" name="sale_id" value="<?= htmlspecialchars($sale['id'] ?? '') ?>">
        </td>
        <td>
            <button name="update_sale">Update</button>
            <a class="btn-delete" href="?delete_id=<?= htmlspecialchars($sale['id'] ?? '') ?>" onclick="return confirm('Delete this sale?')">Delete</a>
        </td>
    </form>
</tr>

        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- ... your previous HTML ... -->

<!-- Footer -->
<footer class="footer">
    <p>&copy; <?= date("Y") ?> AI Sales Forecasting System. All rights reserved.</p>
</footer>

</body>
</html>

</body>
</html>
