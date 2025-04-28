<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'sales_forecasting');
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if ($result) {
    $user = mysqli_fetch_assoc($result);
} else {
    // Handle the error if the query fails
    $_SESSION['message'] = "Error fetching user data: " . mysqli_error($conn);
    header("Location: dashboard.php");
    exit();
}

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $allowed_types = ['image/jpeg', 'image/png'];
    $file_type = $_FILES['profile_picture']['type'];
    $file_name = $_FILES['profile_picture']['name'];
    $file_tmp_name = $_FILES['profile_picture']['tmp_name'];
    $upload_dir = 'uploads/profile_pics/';
    $file_path = $upload_dir . basename($file_name);

    if (!in_array($file_type, $allowed_types)) {
        $_SESSION['message'] = "Invalid file type. Only JPG and PNG are allowed.";
    } elseif ($_FILES['profile_picture']['size'] > 2000000) {
        $_SESSION['message'] = "File size exceeds the maximum limit of 2MB.";
    } else {
        if (move_uploaded_file($file_tmp_name, $file_path)) {
            $update_query = "UPDATE users SET profile_picture = '$file_path' WHERE id = '$user_id'";
            if (mysqli_query($conn, $update_query)) {
                $_SESSION['message'] = "Profile picture updated successfully!";
                header("Location: profile.php");
                exit();
            } else {
                $_SESSION['message'] = "Error updating profile picture: " . mysqli_error($conn);
            }
        } else {
            $_SESSION['message'] = "Error uploading file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - AI Sales Forecasting</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
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

        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background-color: #1e2a47;
            border-radius: 10px;
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-container .form-group {
            margin-bottom: 15px;
        }

        .form-container input[type="text"], .form-container input[type="email"], .form-container input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-container button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            color: white;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-container button:hover {
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

        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }

        .alert {
            text-align: center;
            margin-bottom: 20px;
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
            <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profile</li>
            </ol>
        </nav>

        <!-- Profile Details -->
        <div class="form-container">
            <h2>Your Profile</h2>

            <!-- Display any messages -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-info"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
            <?php endif; ?>

            <!-- Display Profile Picture -->
            <div class="text-center">
                <?php if (!empty($user['profile_picture'])): ?>
                    <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-img">
                <?php else: ?>
                    <img src="uploads/profile_pics/default.png" alt="Default Profile Picture" class="profile-img">
                <?php endif; ?>
            </div>

            <!-- Profile Details -->
            <div class="form-group">
                <label>Name</label>
                <p><?php echo isset($user['name']) ? htmlspecialchars($user['name']) : 'Name not available'; ?></p>
            </div>

            <div class="form-group">
                <label>Email</label>
                <p><?php echo isset($user['email']) ? htmlspecialchars($user['email']) : 'Email not available'; ?></p>
            </div>

            <div class="form-group">
                <label>Profile Picture</label>
                <p><?php echo !empty($user['profile_picture']) ? "Uploaded" : "Not uploaded"; ?></p>
            </div>

            <!-- Profile Picture Upload Form -->
            <form action="profile.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="profile_picture">Upload New Profile Picture</label>
                    <input type="file" name="profile_picture" id="profile_picture" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>

            <a href="settings.php" class="btn btn-primary btn-block mt-3">Edit Profile</a>
        </div>

    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 AI Sales Forecasting. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
