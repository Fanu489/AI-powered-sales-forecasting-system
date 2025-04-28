
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            color: #333;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            margin-top: 50px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #007bff;
        }
        footer {
            background-color: #343a40;
            color: #ffffff;
            padding: 10px 0;
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Terms and Conditions</h2>
    <hr>

    <h4>Introduction</h4>
    <p>Welcome to our website! These are the terms and conditions governing your access to and use of this website. By using this website, you agree to comply with and be bound by the following terms and conditions. Please read them carefully.</p>

    <h4>Acceptance of Terms</h4>
    <p>By accessing or using this website, you agree to be bound by these terms. If you do not agree to these terms, please do not use this website.</p>

    <h4>Account Registration</h4>
    <p>To access certain features of the website, you may need to register for an account. You agree to provide accurate, current, and complete information during registration and to update such information as necessary.</p>

    <h4>Privacy Policy</h4>
    <p>Your use of this website is also governed by our Privacy Policy, which can be accessed <a href="privacy_policy.php">here</a>.</p>

    <h4>Use of Content</h4>
    <p>All content provided on this website is the property of the website owner or its affiliates, and is protected by copyright and other intellectual property laws. You may not use the content without the express written permission of the website owner.</p>

    <h4>Prohibited Activities</h4>
    <p>You agree not to engage in any activity that violates the integrity of the website, including but not limited to hacking, spamming, or distributing harmful software.</p>

    <h4>Limitation of Liability</h4>
    <p>We are not liable for any damages or losses resulting from your use of this website, including but not limited to direct, indirect, incidental, or consequential damages.</p>

    <h4>Termination</h4>
    <p>We reserve the right to suspend or terminate your account and access to the website at our discretion, for any reason and without prior notice.</p>

    <h4>Governing Law</h4>
    <p>These terms and conditions are governed by and construed in accordance with the laws of [Your Country], and any disputes will be handled in the courts of [Your City/Region].</p>

    <h4>Contact Information</h4>
    <p>If you have any questions about these terms, please contact us at [your email].</p>

    <div class="form-check">
        <input class="form-check-input" type="checkbox" value="" id="acceptTerms">
        <label class="form-check-label" for="acceptTerms">
            I have read and agree to the Terms and Conditions.
        </label>
    </div>

    <button class="btn btn-primary mt-3" id="continueButton" disabled>Continue</button>
</div>

<footer>
    <p>&copy; 2025 AI Sales Forecasting System. All rights reserved.</p>
</footer>

<script>
    // Enable/Disable the continue button based on checkbox state
    document.getElementById("acceptTerms").addEventListener("change", function () {
        document.getElementById("continueButton").disabled = !this.checked;
    });

    // Redirect to registration page
    document.getElementById("continueButton").addEventListener("click", function() {
        window.location.href = 'register.php'; // or 'login.php' depending on your flow
    });
</script>

</body>
</html>
