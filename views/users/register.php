<?php
$company_name = $email = $password = $business_type = $products = "";
$errors = [];

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'sales_forecasting');
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/../../vendor/autoload.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $business_type = mysqli_real_escape_string($conn, $_POST['business_type']);
    $products = mysqli_real_escape_string($conn, $_POST['products']);

    // Check if email already exists
    $email_check_query = "SELECT email FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $email_check_query);

    if (mysqli_num_rows($result) > 0) {
        $error_message = "This email is already registered. Please use another email.";
    } else {
        if (strlen($password) < 8) {
            $error_message = "Password must be at least 8 characters long!";
        } else {
            $verification_token = bin2hex(random_bytes(16));
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO users (company_name, email, password, business_type, products, verification_token) 
                      VALUES ('$company_name', '$email', '$hashed_password', '$business_type', '$products', '$verification_token')";

            if (mysqli_query($conn, $query)) {
                $verification_link = "http://localhost/ai%20powered%20sales%20forecasting%20system/views/users/verify.php?token=$verification_token";
                $subject = "Verify Your Email - Sales Forecasting System";
                $message = "Hello $company_name,\n\nPlease verify your email by clicking the link below:\n\n$verification_link";

                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'fanuelomondi489@gmail.com'; // your Gmail
                    $mail->Password = 'bdgl ktga wzbq iecc'; // your app password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('no-reply@salesforecasting.com', 'Sales Forecasting System');
                    $mail->addAddress($email);

                    $mail->isHTML(false);
                    $mail->Subject = $subject;
                    $mail->Body = $message;

                    $mail->send();
                    echo "<div class='alert alert-success text-center'>Registration successful! Check your email to verify your account.</div>";
                } catch (Exception $e) {
                    echo "<div class='alert alert-danger text-center'>Error sending verification email. Please try again later.</div>";
                }
            } else {
                $error_message = "Error during registration. Please try again.";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Company Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #002147;
            color: white;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            flex-direction: column;
        }
        .card {
            background-color: #ffffff;
            color: #333;
            border-radius: 10px;
            margin-top: auto;
            margin-bottom: auto;
            padding: 2rem;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
        }
        .form-label {
            font-weight: 600;
        }
        .form-control:focus {
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
            border-color: #007bff;
        }
        .btn-primary {
            background-color: #004080;
            border: none;
        }
        .btn-primary:hover {
            background-color: #003366;
        }
        .alert {
            margin-top: 1rem;
        }
        footer {
            text-align: center;
            margin-top: 30px;
            color: #ccc;
            font-size: 0.9em;
        }
    </style>
</head>
<body>

<div class="card">
    <h2 class="text-center mb-3"><i class="bi bi-building"></i> Company Registration</h2>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST" action="" novalidate>
        <div class="mb-3">
            <label for="company_name" class="form-label"><i class="bi bi-pencil"></i> Company Name</label>
            <input type="text" class="form-control" name="company_name" id="company_name" required value="<?= htmlspecialchars($company_name) ?>">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label"><i class="bi bi-envelope"></i> Email address</label>
            <input type="email" class="form-control" name="email" id="email" required value="<?= htmlspecialchars($email) ?>">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label"><i class="bi bi-lock-fill"></i> Password</label>
            <input type="password" class="form-control" name="password" id="password" required>
            <small class="form-text text-muted">Use at least 8 characters with a mix of uppercase letters and numbers.</small>
        </div>

        <div class="mb-3">
            <label for="business_type" class="form-label"><i class="bi bi-briefcase"></i> Business Type</label>
            <select class="form-select" name="business_type" id="business_type" required>
                <option value="" disabled <?= empty($business_type) ? 'selected' : '' ?>>Select Business Type</option>
                <?php
                $options = ["Retail", "Manufacturing", "Services", "E-commerce", "Hospitality", "Technology", "Other"];
                foreach ($options as $opt) {
                    $selected = ($business_type === $opt) ? 'selected' : '';
                    echo "<option value='$opt' $selected>$opt</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="products" class="form-label"><i class="bi bi-bag"></i> Products</label>
            <textarea class="form-control" name="products" id="products" rows="3" required><?= htmlspecialchars($products) ?></textarea>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" value="1" id="terms" required>
            <label class="form-check-label" for="terms">
                I agree to the <a href="terms_and_conditions.php" class="text-decoration-underline">terms and conditions</a>
            </label>
        </div>

        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-person-plus"></i> Register</button>

        <div class="text-center mt-3">
            Already have an account? <a href="/AI%20POWERED%20SALES%20FORECASTING%20SYSTEM/views/users/Login.php">Login here</a>
        </div>
    </form>
</div>

<footer class="text-center mt-4 text-muted small">
    &copy; <?= date("Y") ?> Sales Forecasting System. All rights reserved.
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (() => {
        'use strict'
        const forms = document.querySelectorAll('form')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
</body>
</html>
