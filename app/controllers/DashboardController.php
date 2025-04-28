<?php
// DashboardController.php
require_once(__DIR__ . '/../../includes/db.php');  // Database connection
require_once(__DIR__ . '/../../includes/User.php'); // User model
require_once(__DIR__ . '/../../includes/SalesData.php');  // Correct path to SalesData.php
 // Sales Data model
require_once(__DIR__ . '/../../includes/Report.php'); // Report model
require_once(__DIR__ . '/../../includes/Settings.php'); // Settings model for site configuration


class DashboardController {

    // Show user dashboard
    public function index() {
        // Check if the user is logged in
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit();
        }

        // Get user data
        $userId = $_SESSION['user_id'];
        $user = User::getUserById($userId);

        // Get sales data for the dashboard (e.g., total sales, top products, etc.)
        $salesData = SalesData::getSalesDataForUser($userId);

        // Get generated reports (e.g., sales reports)
        $reports = Report::getReportsByUser($userId);

        // Render the dashboard view
        include 'views/user/dashboard.php';
    }

    // Check if the user is authenticated
    public function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }

    // Show user profile page
    public function profile() {
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $user = User::getUserById($userId);
        
        // Render profile page with user details
        include 'views/user/profile.php';
    }

    // Upload sales data
    public function uploadSalesData() {
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit();
        }

        // Check if a file is uploaded
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['sales_file'])) {
            // Get file details
            $file = $_FILES['sales_file'];

            // Validate file type (only allow CSV)
            if ($file['type'] !== 'text/csv') {
                $_SESSION['upload_error'] = 'Invalid file type. Only CSV files are allowed.';
                header('Location: upload.php');
                exit();
            }

            // Save the file to the server (in the /uploads directory)
            $targetDir = 'uploads/sales_data/';
            $fileName = basename($file['name']);
            $targetFilePath = $targetDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
                // Parse and process the CSV file data (insert into database)
                $this->processSalesData($targetFilePath);

                $_SESSION['upload_success'] = 'Sales data uploaded successfully.';
                header('Location: dashboard.php');
                exit();
            } else {
                $_SESSION['upload_error'] = 'Failed to upload the file.';
                header('Location: upload.php');
                exit();
            }
        }

        // Show the upload sales data page
        include 'views/user/upload.php';
    }

    // Process the uploaded sales data CSV
    private function processSalesData($filePath) {
        // Open the file and read its content
        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                // Assuming the CSV contains [product_id, product_name, quantity_sold, sale_amount]
                $productId = $data[0];
                $productName = $data[1];
                $quantitySold = $data[2];
                $saleAmount = $data[3];

                // Insert sales data into the database (using the SalesData model)
                SalesData::insertSalesData($productId, $productName, $quantitySold, $saleAmount);
            }
            fclose($handle);
        }
    }

    // Generate a sales report
    public function generateReport() {
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $report = Report::generateSalesReport($userId);

        // Show the generated report
        include 'views/user/report.php';
    }

    // Logout the user
    public function logout() {
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit();
    }
}
