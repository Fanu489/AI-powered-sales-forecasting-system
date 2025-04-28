<?php
session_start();
require_once('../../config.php');

$errors = [];

// Show logout message
if (isset($_SESSION['message'])) {
    echo "<p style='color: green; font-weight: bold;'>" . $_SESSION['message'] . "</p>";
    unset($_SESSION['message']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($username) || empty($password)) {
        $errors[] = "Both username and password are required.";
    }

    if (empty($errors)) {
        $query = "SELECT id, name, password FROM admins WHERE username = ?";
        if ($stmt = mysqli_prepare($conn, $query)) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $admin_id, $admin_name, $hashed_password);
                mysqli_stmt_fetch($stmt);

                if (password_verify($password, $hashed_password)) {
                    // Set session variables after successful login
                    $_SESSION['admin_id'] = $admin_id;
                    $_SESSION['admin_name'] = $admin_name;
                    $_SESSION['admin_username'] = $username;

                    // Redirect to dashboard
                    header("Location: admin_dashboard.php"); // Redirect to the admin dashboard page
                    exit();
                } else {
                    $errors[] = "Invalid password.";
                }
            } else {
                $errors[] = "Admin not found.";
            }

            mysqli_stmt_close($stmt);
        } else {
            $errors[] = "Database query error.";
        }
    }
}
?>

<!-- HTML FORM for Login -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color:rgb(11, 15, 19);
            font-family: Arial, sans-serif;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            padding: 40px;
        }
        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .alert-danger p {
            margin: 0;
            padding: 5px 0;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 10px 20px;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .text-center a {
            color: #007bff;
            font-size: 1rem;
            text-decoration: none;
        }
        .text-center a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 400px;">
        <div class="card-body">
            <h3 class="card-title text-center mb-4">Admin Login</h3>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <p class="text-center mt-3"><a href="admin_register.php">Create an admin account</a></p>
        </div>
    </div>
</div>

</body>
</html>
