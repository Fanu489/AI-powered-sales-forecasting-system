
<?php
    $host = 'localhost';
    $username = 'root';
    $password = '';  // Or your actual password
    $dbname = 'sales_forecasting';  // Or your actual database name

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>
