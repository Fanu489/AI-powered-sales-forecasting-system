<?php
// Database configuration
$host = "localhost";      // or 127.0.0.1
$db_user = "root";        // default user for XAMPP/WAMP
$db_password = "";        // leave empty if no password is set
$db_name = "sales_forecasting"; // your database name

// Create PDO connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
