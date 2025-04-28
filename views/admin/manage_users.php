<?php
// manage_users.php

// Database connection
$conn = new mysqli("localhost", "root", "", "sales_forecasting");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create user
if (isset($_POST['create_user'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $conn->query("INSERT INTO users (name, email) VALUES ('$name', '$email')");
}

// Edit user
if (isset($_POST['edit_user'])) {
    $id = intval($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $conn->query("UPDATE users SET name='$name', email='$email' WHERE id=$id");
}

// Delete user
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM users WHERE id=$id");
}

// Fetch users from DB
$result = $conn->query("SELECT * FROM users ORDER BY id ASC");
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Inter', sans-serif;
        }
        body {
            display: flex;
            flex-direction: column;
            background-color: #f5f7fa;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            height: 100vh;
            position: fixed;
            padding-top: 20px;
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
            margin-left: 250px;
            padding: 30px;
            flex: 1;
        }
        header h1 {
            font-weight: 600;
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .btn {
            padding: 8px 15px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            border-radius: 4px;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group input {
            padding: 8px;
            width: 100%;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .edit-form {
            display: none;
            background-color: rgba(0, 0, 0, 0.5);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            justify-content: center;
            align-items: center;
        }
        .edit-form form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 300px;
        }
        .edit-form input[type="submit"] {
            background-color: #28a745;
            padding: 10px;
            color: white;
            border: none;
            cursor: pointer;
        }
        .edit-form input[type="submit"]:hover {
            background-color: #218838;
        }
        .close {
            background-color: #dc3545;
            border: none;
            color: white;
            padding: 5px 10px;
            cursor: pointer;
            margin-top: 10px;
        }
        .edit-btn {
            margin-right: 10px;
        }
        footer {
            background-color:rgba(44, 62, 80, 0.49);
            color: white;
            padding: 15px;
            text-align: center;
            margin-left: 250px;
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
    <div class="container">
        <header>
            <h1>Manage Users</h1>
        </header>

        <h3>Create New User</h3>
        <form method="POST">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <input type="submit" name="create_user" class="btn" value="Create User">
        </form>

        <h3>Existing Users</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <button class="btn edit-btn" onclick="document.getElementById('editForm<?= $user['id'] ?>').style.display='flex'">Edit</button>
                            <a href="?delete_id=<?= $user['id'] ?>" class="btn" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>

                            <div id="editForm<?= $user['id'] ?>" class="edit-form">
                                <form method="POST">
                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                    <div class="form-group">
                                        <label>Name:</label>
                                        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Email:</label>
                                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                                    </div>
                                    <input type="submit" name="edit_user" value="Update User">
                                    <button type="button" class="close" onclick="document.getElementById('editForm<?= $user['id'] ?>').style.display='none'">Close</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>

<footer>
    &copy; <?= date('Y') ?> AI Sales Forecasting System. All rights reserved.
</footer>

</body>
</html>
