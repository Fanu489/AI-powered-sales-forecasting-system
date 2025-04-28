<?php
// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to a specific URL
function redirect($url) {
    header("Location: $url");
    exit;
}

// Check if admin is logged in
function checkAdminLogin() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        redirect('../../admin_login.php'); // Adjust if your login path is different
    }
}

// Flash messages
function setFlash($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

function getFlash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

// Sanitize user input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
