<?php
// Start session
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Destroy all session variables to log out the user
    session_unset();

    // Destroy the session
    session_destroy();

    // Redirect to login page with a success message
    $_SESSION['message'] = "You have been logged out successfully!";
    header("Location: login.php");
    exit();
} else {
    // If no session exists, redirect to login page
    header("Location: login.php");
    exit();
}
?>
