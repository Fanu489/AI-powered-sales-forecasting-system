<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "sales_forecasting");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 1: Fetch distinct products securely
$product_query = "SELECT DISTINCT product_name FROM sales_data";
$product_result = $conn->query($product_query);
if ($product_result === false) {
    die("Error fetching product data: " . $conn->error);
}

$product_data = [];
$bar_labels = [];
$bar_totals = [];

function predict_sales($historical_sales) {
    // This is a placeholder for the prediction logic.
    // You can replace this with an actual ML model or algorithm.
    // Here, we assume a simple linear prediction: +10% for each future date.
    $predicted_sales = [];
    $last_quantity = end($historical_sales);

    for ($i = 0; $i < 5; $i++) { // Predict next 5 days.
        $predicted_sales[] = $last_quantity * 1.1; // Assuming 10% increase
        $last_quantity = $predicted_sales[$i];
    }

    return $predicted_sales;
}

while ($product = $product_result->fetch_assoc()) {
    $product_name = $product['product_name'];

    // Get total per day securely with prepared statement
    $stmt = $conn->prepare("SELECT date, SUM(quantity) as total_quantity FROM sales_data WHERE product_name = ? GROUP BY date ORDER BY date ASC");
    $stmt->bind_param("s", $product_name);
    $stmt->execute();
    $result = $stmt->get_result();

    $dates = [];
    $quantities = [];

    while ($row = $result->fetch_assoc()) {
        $dates[] = $row['date'];
        $quantities[] = $row['total_quantity'];
    }

    $predicted_sales = predict_sales($quantities); // Get predictions

    $product_data[] = [
        'product' => $product_name,
        'dates' => $dates,
        'quantities' => $quantities,
        'predictions' => $predicted_sales // Include predictions
    ];

    // For bar & pie
    $total_stmt = $conn->prepare("SELECT SUM(quantity) as total FROM sales_data WHERE product_name = ?");
    $total_stmt->bind_param("s", $product_name);
    $total_stmt->execute();
    $total_result = $total_stmt->get_result();
    $total_row = $total_result->fetch_assoc();
    
    $bar_labels[] = $product_name;
    $bar_totals[] = (int)$total_row['total'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product-wise Sales Predictions</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #001f3d;
            color: white;
        }
        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            background-color: #333;
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
            background-color:rgb(9, 136, 233);
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
        }
        .content {
            margin-left: 270px;
            padding: 20px;
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
    <h2 class="text-center mb-4">Analytics Per Product</h2>

    <!-- Bar Chart -->
    <div class="mb-5">
        <h4>Total Sales - Bar Chart</h4>
        <canvas id="barChart"></canvas>
    </div>

    <!-- Pie Chart -->
    <div class="mb-5">
        <h4>Product Sales Share - Pie Chart</h4>
        <canvas id="pieChart"></canvas>
    </div>

    <!-- Line Charts Per Product -->
    <?php foreach ($product_data as $index => $product): ?>
        <div class="mb-5">
            <h4><?= htmlspecialchars($product['product']) ?></h4>
            <canvas id="chart<?= $index ?>"></canvas>
        </div>
    <?php endforeach; ?>
</div>

<!-- Footer -->
<div class="footer">
    <p>&copy; 2025 Sales Forecasting System. All Rights Reserved.</p>
</div>

<script>
// --- Bar Chart ---
const barCtx = document.getElementById('barChart').getContext('2d');
new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($bar_labels) ?>,
        datasets: [{
            label: 'Total Sales Quantity',
            data: <?= json_encode($bar_totals) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// --- Pie Chart ---
const pieCtx = document.getElementById('pieChart').getContext('2d');
new Chart(pieCtx, {
    type: 'pie',
    data: {
        labels: <?= json_encode($bar_labels) ?>,
        datasets: [{
            label: 'Product Sales Share',
            data: <?= json_encode($bar_totals) ?>,
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', '#8BC34A', '#9C27B0', '#00BCD4'
            ],
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true
    }
});

// --- Line Charts Per Product ---
<?php foreach ($product_data as $index => $product): ?>
const ctx<?= $index ?> = document.getElementById('chart<?= $index ?>').getContext('2d');
new Chart(ctx<?= $index ?>, {
    type: 'line',
    data: {
        labels: <?= json_encode($product['dates']) ?>,
        datasets: [
            {
                label: 'Sales Quantity',
                data: <?= json_encode($product['quantities']) ?>,
                fill: true,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                tension: 0.4
            },
            {
                label: 'Predicted Sales',
                data: <?= json_encode($product['predictions']) ?>,
                fill: false,
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 2,
                tension: 0.4,
                borderDash: [5, 5], // Dotted line for prediction
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
<?php endforeach; ?>
</script>

</body>
</html>
