<?php
// Database connection details
$host = 'localhost';          // Database host (usually localhost)
$dbname = 'sales_forecasting'; // Replace with your database name
$username = 'root'; // Replace with your database username
$password = ''; // Replace with your database password

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection errors
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Optional: Set the character set to UTF-8
$pdo->exec("SET NAMES 'utf8'");

// You can also use MySQLi instead of PDO if you prefer.
// $conn = new mysqli($host, $username, $password, $dbname);

// Check the connection (for MySQLi)
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

?>
