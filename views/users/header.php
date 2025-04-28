<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



// Check if the user is logged in (simple session check)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include necessary stylesheets and scripts
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AI Sales Forecasting - Help</title>

    <!-- Bootstrap CSS (you can change this path as needed) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Icons for Bootstrap (to use icons like bi-gear, bi-person-circle, etc.) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS (Optional for your styles) -->
    <link href="assets/css/style.css" rel="stylesheet">

    <!-- Include jQuery (if needed for other JavaScript functionality) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- JavaScript for Voice Recognition (can be moved to the end of the page for optimization) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webkit-speech-recognition/1.0.0/webkit-speech-recognition.min.js"></script>
    
    <!-- Additional Scripts -->
    <script src="assets/js/script.js"></script>  <!-- Optional custom JS file for any interactive behavior -->
</head>
<body>
