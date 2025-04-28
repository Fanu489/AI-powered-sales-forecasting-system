<?php
// SalesData.php - Handles operations related to sales data

require_once(__DIR__ . '/db.php');  // Include the database connection

class SalesData {

    // Get all sales data from the database
    public static function getAllSalesData() {
        global $pdo;  // Use the global database connection

        try {
            $stmt = $pdo->prepare("SELECT * FROM sales_data ORDER BY product ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error fetching sales data: " . $e->getMessage());
        }
    }

    // Insert new sales data into the database
    public static function insertSalesData($product, $quantity, $price) {
        global $pdo;

        try {
            $stmt = $pdo->prepare("INSERT INTO sales_data (product, quantity, price) VALUES (?, ?, ?)");
            $stmt->execute([$product, $quantity, $price]);
        } catch (PDOException $e) {
            die("Error inserting sales data: " . $e->getMessage());
        }
    }

    // Fetch sales data by product name
    public static function getSalesDataByProduct($product) {
        global $pdo;

        try {
            $stmt = $pdo->prepare("SELECT * FROM sales_data WHERE product = ?");
            $stmt->execute([$product]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error fetching sales data by product: " . $e->getMessage());
        }
    }

    // Fetch sales data by ID
    public static function getSalesDataById($id) {
        global $pdo;

        try {
            $stmt = $pdo->prepare("SELECT * FROM sales_data WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error fetching sales data by ID: " . $e->getMessage());
        }
    }

    // Delete sales data by ID
    public static function deleteSalesData($id) {
        global $pdo;

        try {
            $stmt = $pdo->prepare("DELETE FROM sales_data WHERE id = ?");
            $stmt->execute([$id]);
        } catch (PDOException $e) {
            die("Error deleting sales data: " . $e->getMessage());
        }
    }

    // Update sales data (for example, if a sale's details need to be changed)
    public static function updateSalesData($id, $product, $quantity, $price) {
        global $pdo;

        try {
            $stmt = $pdo->prepare("UPDATE sales_data SET product = ?, quantity = ?, price = ? WHERE id = ?");
            $stmt->execute([$product, $quantity, $price, $id]);
        } catch (PDOException $e) {
            die("Error updating sales data: " . $e->getMessage());
        }
    }
}
