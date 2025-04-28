<?php
// Verify.php

// Get the token from the URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Database connection
    $conn = mysqli_connect('localhost', 'root', '', 'sales_forecasting');
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Prepare and bind statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE verification_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    $message = ''; // To store the message to be displayed
    $showSpinner = true; // To show the loader spinner during verification

    if (mysqli_num_rows($result) > 0) {
        // Token found, update the user to 'verified'
        $user = mysqli_fetch_assoc($result);
        $update_query = "UPDATE users SET verification_token = NULL WHERE id = " . $user['id'];
        if (mysqli_query($conn, $update_query)) {
            $showSpinner = false; // Hide the spinner once the process is completed
            $message = "<div class='alert alert-success text-center'>Your email has been verified! You can now log in.</div>";
            $message .= "<a href='http://localhost/ai%20powered%20sales%20forecasting%20system/views/users/login.php' class='btn btn-primary btn-lg d-block mx-auto mt-3' id='goToLogin'>Go to Login Page</a>";
        } else {
            $showSpinner = false; // Hide the spinner on error
            $message = "<div class='alert alert-danger text-center'>Error updating the verification status: " . mysqli_error($conn) . "</div>";
        }
    } else {
        $showSpinner = false; // Hide the spinner if the token is invalid
        $message = "<div class='alert alert-danger text-center'>Invalid verification token or the token has expired.</div>";
    }

    mysqli_close($conn);
} else {
    $message = "<div class='alert alert-danger text-center'>No verification token provided.</div>";
    $showSpinner = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Verification</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #001f3d; /* Dark blue background */
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            flex-direction: column;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }
        .alert {
            margin-bottom: 1rem;
        }
        .btn-primary {
            background-color: #004080;
            border: none;
        }
        .btn-primary:hover {
            background-color: #003366;
        }
        footer {
            text-align: center;
            padding: 20px 0;
            background-color: rgb(3, 26, 49);
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
            box-shadow: black;
            border-radius: 5px;
        }
        /* Hide spinner when the process is finished */
        .spinner-container {
            display: <?= $showSpinner ? 'block' : 'none'; ?>;
        }
    </style>
</head>
<body>

<div class="container text-center">
    <!-- Loader Spinner -->
    <div class="spinner-container">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Verifying...</p>
    </div>
    
    <!-- Verification message will appear here after the process is done -->
    <?php echo $message; // Display the verification message dynamically ?>
</div>

<footer class="text-center mt-4 text-muted small">
    &copy; <?= date("Y") ?> Sales Forecasting System. All rights reserved.
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript for automatic redirection after button click -->
<script>
    document.getElementById('goToLogin')?.addEventListener('click', function() {
        window.location.href = 'views/users/login.php';
    });
</script>

</body>
</html>
