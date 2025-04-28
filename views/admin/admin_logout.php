<?php
session_start(); // Start session

// Optional: Set logout message before destroying session
$_SESSION['message'] = "You have been logged out successfully.";

// Optional: Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_unset();
session_destroy();

// Redirect to login page
header("Location: admin_login.php");
exit();
?>
