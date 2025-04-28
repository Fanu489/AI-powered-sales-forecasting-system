<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'sales_forecasting');
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle the login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $remember = isset($_POST['remember']); // Check if "Remember Me" is checked

    // Fetch user data from the database
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];  // Store role (user/admin) in session

            // If "Remember Me" is checked, set a cookie for 30 days
            if ($remember) {
                setcookie('user_email', $email, time() + (30 * 24 * 60 * 60), '/');  // 30 days
                setcookie('user_password', $_POST['password'], time() + (30 * 24 * 60 * 60), '/');
            }

            // Redirect to the correct dashboard based on user role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");  // Redirect to admin dashboard
            } else {
                header("Location: dashboard.php");  // Redirect to user dashboard
            }
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "No user found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AI Sales Forecasting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #001f3d;
            color: white;
            font-family: 'Arial', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .login-container {
            max-width: 450px;
            padding: 40px;
            background-color: #1e2a47;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            width: 100%;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #f1f1f1;
        }

        .login-container h5 {
            text-align: center;
            margin-bottom: 30px;
            color: #3498db;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            background-color: #2c3e50;
            border: 1px solid #ccc;
            color: white;
        }

        .form-control:focus {
            border-color: #3498db;
            background-color: #34495e;
            box-shadow: 0 0 5px #3498db;
        }

        .btn-primary {
            background-color: #3498db;
            border: none;
            font-size: 16px;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .alert {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        .register-link {
            color: #f1f1f1;
        }

        .register-link:hover {
            color: #3498db;
        }

        .forgot-password-link {
            color: #f1f1f1;
        }

        .forgot-password-link:hover {
            color: #3498db;
        }

        .footer {
            position: absolute;
            bottom: 10px;
            width: 100%;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Login</h2>
        <h5>to the AI-Powered Sales Forecasting</h5>

        <?php if (isset($error_message)): ?>
            <div class="alert"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required class="form-control" placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required class="form-control" placeholder="Enter your password">
            </div>
            <div class="form-check">
                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                <label class="form-check-label" for="remember">Remember Me</label>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Login</button>
        </form>

        <p class="text-center mt-3">
            Don't have an account? <a href="register.php" class="register-link">Register here</a>
        </p>
        <p class="text-center mt-3">
            <a href="forgot_password.php" class="forgot-password-link">Forgot Password?</a>
        </p>
    </div>

    <div class="footer">
        &copy; <?= date("Y") ?> AI Sales Forecasting. All rights reserved.
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
