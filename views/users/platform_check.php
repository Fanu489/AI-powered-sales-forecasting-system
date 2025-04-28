<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}

// Get the current user-agent
$user_agent = $_SERVER['HTTP_USER_AGENT'];

// Define the list of supported platforms (e.g., browsers)
$supported_browsers = ['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera'];

// Check if the user is using a supported browser
$is_supported_browser = false;
foreach ($supported_browsers as $browser) {
    if (strpos($user_agent, $browser) !== false) {
        $is_supported_browser = true;
        break;
    }
}

// If the browser is not supported, show a message and stop further execution
if (!$is_supported_browser) {
    echo "<div style='color: red; text-align: center;'>Your browser is not supported. Please use Chrome, Firefox, Safari, Edge, or Opera.</div>";
    exit();
}

// Optional: Additional checks can be added, such as checking for mobile vs. desktop platforms
$is_mobile = preg_match('/(android|iphone|ipad)/i', $user_agent);

// Redirect to a different page based on the platform (optional)
if ($is_mobile) {
    // Redirect to mobile-friendly page (optional)
    header("Location: mobile_dashboard.php");
    exit();
}

// If the browser is supported and user is logged in, allow access to the platform
// You can include the actual content of the page below, for example:

echo "<h1>Welcome to the platform!</h1>";
echo "<p>Your platform is fully functional. Enjoy your visit.</p>";
?>
