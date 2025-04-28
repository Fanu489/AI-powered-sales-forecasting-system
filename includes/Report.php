<?php
// Report.php - Handles report generation logic (e.g., sales forecast)

require_once(__DIR__ . '/db.php');  // Include database connection

class Report {

    // Fetch all reports from the database
    public static function getAllReports() {
        global $pdo;  // Use the global database connection

        try {
            $stmt = $pdo->prepare("SELECT * FROM reports ORDER BY created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error fetching reports: " . $e->getMessage());
        }
    }

    // Generate a sales forecast report based on sales data
    public static function generateSalesForecast() {
        global $pdo;

        try {
            // Example logic for sales forecast
            // This could be a complex algorithm that uses historical data to predict future sales
            $stmt = $pdo->prepare("SELECT product, SUM(quantity) AS total_quantity, SUM(price) AS total_sales FROM sales_data GROUP BY product");
            $stmt->execute();

            // Fetch the results and generate the report
            $salesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Create a report (in this case, just an example summary)
            $report = [
                'title' => 'Sales Forecast Report',
                'generated_at' => date('Y-m-d H:i:s'),
                'sales_data' => $salesData
            ];

            // Insert the report into the database
            $reportStmt = $pdo->prepare("INSERT INTO reports (title, content, created_at) VALUES (?, ?, ?)");
            $reportStmt->execute([$report['title'], json_encode($report['sales_data']), $report['generated_at']]);

            return $report;  // Return the generated report
        } catch (PDOException $e) {
            die("Error generating sales forecast report: " . $e->getMessage());
        }
    }

    // Fetch a specific report by ID
    public static function getReportById($reportId) {
        global $pdo;

        try {
            $stmt = $pdo->prepare("SELECT * FROM reports WHERE id = ?");
            $stmt->execute([$reportId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error fetching report by ID: " . $e->getMessage());
        }
    }
}
