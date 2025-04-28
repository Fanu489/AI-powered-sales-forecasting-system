<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// DB connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "sales_forecasting";
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$alertType = "";
$uploadSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["sales_file"])) {
    $file = $_FILES["sales_file"];
    $fileTmpName = $file["tmp_name"];
    $fileName = $file["name"];
    $fileType = $file["type"];

    if (($handle = fopen($fileTmpName, "r")) !== FALSE) {
        fgetcsv($handle); // skip header
        $rowsInserted = 0;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (count($data) < 4) continue;

            $date = $data[0];
            $product_name = $data[1];
            $quantity = (int)$data[2];
            $revenue = (int)$data[3];

            $stmt = $conn->prepare("INSERT INTO sales_data (date, product_name, quantity, revenue) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssii", $date, $product_name, $quantity, $revenue);
            
            if ($stmt->execute()) {
                $rowsInserted++;
            }
        }

        fclose($handle);

        $message = "$rowsInserted records uploaded successfully!";
        $alertType = "success";
        $uploadSuccess = $rowsInserted > 0;

        // ✅ Send notification to the user
        if ($uploadSuccess) {
            $notifTitle = "File Upload Successful";
            $notifMessage = "Your file '$fileName' has been uploaded successfully with $rowsInserted records.";
            $notifStatus = "unread";
            $userId = $_SESSION['user_id'];

            $notifStmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, status) VALUES (?, ?, ?, ?)");
            $notifStmt->bind_param("isss", $userId, $notifTitle, $notifMessage, $notifStatus);
            $notifStmt->execute();
        }

    } else {
        $message = "Error opening the file.";
        $alertType = "danger";
    }
}

$conn->close();
?>


<!-- HTML STARTS -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Sales Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f7fc;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 220px;
            height: 100vh;
            background-color: #0d1b33;
            padding-top: 20px;
            color: #ffffff;
        }

        .sidebar h2 {
            text-align: center;
            font-size: 22px;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding-left: 0;
        }

        .sidebar ul li {
            padding: 10px 20px;
        }

        .sidebar ul li a {
            color: #ffffff;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .sidebar ul li a i {
            margin-right: 10px;
        }

        .sidebar ul li a:hover {
            background-color: blue;
            border-radius: 5px;
        }

        .main-content {
            margin-left: 220px;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .upload-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 220px;
            width: calc(100% - 220px);
            background-color: #1e2a47;
            color: #ffffff;
            text-align: center;
            padding: 10px 0;
            font-size: 13px;
        }
    </style>
</head>
<body>

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

<div class="main-content">
    <div class="upload-container">
        <h2 class="text-center mb-4">Upload Sales Data (CSV)</h2>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $alertType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form id="uploadForm" action="upload.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="sales_file" class="form-label">Select CSV File:</label>
                <input type="file" class="form-control" name="sales_file" required>
            </div>
            <button id="uploadBtn" type="submit" class="btn btn-primary w-100">Upload</button>

            <div id="uploadSpinner" class="text-center mt-3" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Uploading...</span>
                </div>
                <p class="mt-2">Uploading... Please wait</p>
            </div>
        </form>

        <?php if ($uploadSuccess): ?>
            <div class="text-center mt-4">
                <a href="report.php" class="btn btn-success w-100">Generate Report</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>&copy; <?php echo date("Y"); ?> AI Sales Forecasting System. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById("uploadForm").addEventListener("submit", function () {
        document.getElementById("uploadSpinner").style.display = "block";
        document.getElementById("uploadBtn").disabled = true;
    });
</script>
</body>
</html>
