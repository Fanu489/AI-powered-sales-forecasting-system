<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Include the database connection and PHPMailer library
include('db.php');
include('autoload.php'); // Include PHPMailer if you're using Composer for autoloading

$email_error = "";
$email_sent = false;

// Check if the form is submitted
if (isset($_POST['submit'])) {
    $email = $_POST['email'];

    // Validate email
    if (empty($email)) {
        $email_error = "Please enter your email address.";
    } else {
        // Check if the email exists in the database
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // User found, generate a password reset token
            $token = bin2hex(random_bytes(50)); // Create a unique token
            $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); // Token expires in 1 hour

            // Save the token and expiry to the database
            $update_sql = "UPDATE users SET reset_token = '$token', token_expiry = '$expiry' WHERE email = '$email'";
            if ($conn->query($update_sql) === TRUE) {
                // Prepare the reset link
                $reset_link = "http://localhost/ai-powered-sales-forecasting-system/views/users/reset_password.php?token=$token";

                // Set up PHPMailer
                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Use your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'fanuelomondi489@gmail.com'; // Your email address
                $mail->Password = 'bdgl ktga wzbq iecc'; // Your email password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('fanuelomondi489@gmail.com', 'Sales Forecasting System');
                $mail->addAddress($email);
                $mail->Subject = 'Password Reset Request';
                $mail->Body = "Click the following link to reset your password: $reset_link";

                if ($mail->send()) {
                    $email_sent = true;
                } else {
                    $email_error = "Failed to send the password reset email. Please try again.";
                }
            } else {
                $email_error = "Error updating token: " . $conn->error;
            }
        } else {
            $email_error = "No account found with that email address.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        /* Basic Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 400px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
        }

        .success {
            color: green;
            text-align: center;
            margin: 20px 0;
        }

        .error {
            color: red;
            text-align: center;
            margin: 20px 0;
        }

        p {
            text-align: center;
            font-size: 14px;
        }

        a {
            color: #4CAF50;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>

        <?php if ($email_sent): ?>
            <p class="success">A password reset link has been sent to your email address. Please check your inbox.</p>
        <?php endif; ?>

        <?php if ($email_error): ?>
            <p class="error"><?= $email_error; ?></p>
        <?php endif; ?>

        <form action="forgot_password.php" method="POST">
            <div class="form-group">
                <label for="email">Enter Your Email Address:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit" name="submit">Send Reset Link</button>
        </form>

        <p>Remembered your password? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
