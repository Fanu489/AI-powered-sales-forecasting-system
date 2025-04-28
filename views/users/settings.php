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
$user = mysqli_fetch_assoc($result);

// Update profile details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = mysqli_real_escape_string($conn, $_POST['name']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_password = mysqli_real_escape_string($conn, $_POST['password']);
    $profile_picture = $_FILES['profile_picture']['name'];

    // Handle password update
    if (!empty($new_password)) {
        $new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET name='$new_name', email='$new_email', password='$new_password' WHERE id='$user_id'";
    } else {
        $update_query = "UPDATE users SET name='$new_name', email='$new_email' WHERE id='$user_id'";
    }

    // Handle profile picture upload
    if (isset($profile_picture) && $profile_picture != "") {
        $target_dir = "uploads/profile_pics/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);

        // Check if the file is an image
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['message'] = "File is not an image.";
            header("Location: settings.php");
            exit();
        }

        // Check file size (limit to 2MB)
        if ($_FILES["profile_picture"]["size"] > 2000000) {
            $_SESSION['message'] = "Sorry, your file is too large. Max allowed size is 2MB.";
            header("Location: settings.php");
            exit();
        }

        // Allow only certain file formats
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            $_SESSION['message'] = "Sorry, only JPG, JPEG, PNG, and GIF files are allowed.";
            header("Location: settings.php");
            exit();
        }

        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // Update profile picture in the database
            $update_picture_query = "UPDATE users SET profile_picture='$target_file' WHERE id='$user_id'";
            mysqli_query($conn, $update_picture_query);
        } else {
            $_SESSION['message'] = "Sorry, there was an error uploading your file.";
            header("Location: settings.php");
            exit();
        }
    }

    if (mysqli_query($conn, $update_query)) {
        $_SESSION['message'] = "Profile updated successfully!";
    } else {
        $_SESSION['message'] = "Error updating profile: " . mysqli_error($conn);
    }
    header("Location: settings.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - AI Sales Forecasting</title>

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

        .form-container input[type="text"], .form-container input[type="email"], .form-container input[type="password"], .form-container input[type="file"] {
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
                <li class="breadcrumb-item active" aria-current="page">Settings</li>
            </ol>
        </nav>

        <!-- Profile Update Form -->
        <div class="form-container">
            <h2>Update Profile</h2>

            <?php
            if (isset($_SESSION['message'])) {
                echo "<div class='alert alert-info'>" . $_SESSION['message'] . "</div>";
                unset($_SESSION['message']);
            }
            ?>

            <!-- Display Profile Picture -->
            <div class="text-center">
                <?php if (!empty($user['profile_picture'])): ?>
                    <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-img">
                <?php else: ?>
                    <img src="uploads/profile_pics/default.png" alt="Default Profile Picture" class="profile-img">
                <?php endif; ?>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">New Password (Leave blank to keep current password)</label>
                    <input type="password" id="password" name="password" placeholder="Enter new password">
                </div>

                <div class="form-group">
                    <label for="profile_picture">Profile Picture</label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                </div>

                <button type="submit">Update Profile</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; <?php echo date("Y"); ?> AI Sales Forecasting System. All rights reserved.</p>
    </footer>

</body>
</html>
