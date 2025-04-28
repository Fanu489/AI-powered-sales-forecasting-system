<?php
// SalesController.php

require_once 'db.php'; // Database connection
require_once 'SalesData.php'; // Sales data model
require_once 'Report.php'; // Report model

class SalesController {

    // Display sales data upload page
    public function uploadPage() {
        // Check if the user is authenticated
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit();
        }

        // Render the upload page
        include 'views/user/upload.php';
    }

    // Handle the upload of sales data
    public function uploadSalesData() {
        // Check if the user is authenticated
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit();
        }

        // Handle the uploaded file
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['sales_file'])) {
            $file = $_FILES['sales_file'];
            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileType = $file['type'];
            $fileError = $file['error'];

            // Validate file type and size
            if ($fileError === 0) {
                if ($fileType !== 'text/csv') {
                    $_SESSION['error'] = 'Please upload a CSV file.';
                    header('Location: upload.php');
                    exit();
                }

                // Move the uploaded file to the /uploads/sales_data directory
                $uploadPath = 'uploads/sales_data/' . basename($fileName);
                if (move_uploaded_file($fileTmpName, $uploadPath)) {
                    // Parse the CSV file
                    $salesData = $this->parseCSV($uploadPath);

                    // Insert sales data into the database
                    if ($salesData) {
                        $inserted = SalesData::insertSalesData($salesData);
                        if ($inserted) {
                            $_SESSION['success'] = 'Sales data uploaded successfully!';
                            header('Location: report.php');
                            exit();
                        } else {
                            $_SESSION['error'] = 'Failed to insert sales data into the database.';
                            header('Location: upload.php');
                            exit();
                        }
                    } else {
                        $_SESSION['error'] = 'Invalid CSV format.';
                        header('Location: upload.php');
                        exit();
                    }
                } else {
                    $_SESSION['error'] = 'Failed to upload file.';
                    header('Location: upload.php');
                    exit();
                }
            } else {
                $_SESSION['error'] = 'An error occurred while uploading the file.';
                header('Location: upload.php');
                exit();
            }
        }
    }

    // Display all uploaded sales data
    public function viewSalesData() {
        // Check if the user is authenticated
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit();
        }

        // Get all sales data
        $salesData = SalesData::getAllSalesData();

        // Render the sales data page
        include 'views/admin/manage_sales.php';
    }

    // Delete a specific sales record
    public function deleteSalesRecord($recordId) {
        // Check if the user is authenticated
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit();
        }

        // Delete the sales record
        $deleted = SalesData::deleteSalesDataById($recordId);

        if ($deleted) {
            $_SESSION['success'] = 'Sales record deleted successfully.';
        } else {
            $_SESSION['error'] = 'Failed to delete sales record.';
        }

        // Redirect to the sales data management page
        header('Location: manage_sales.php');
        exit();
    }

    // Parse the uploaded CSV file
    private function parseCSV($filePath) {
        $salesData = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            $header = fgetcsv($handle); // Read the header row
            while (($row = fgetcsv($handle)) !== false) {
                $salesData[] = [
                    'date' => $row[0],
                    'product' => $row[1],
                    'quantity' => $row[2],
                    'price' => $row[3],
                    'total' => $row[4]
                ];
            }
            fclose($handle);
        }
        return $salesData;
    }

    // Generate a sales report
    public function generateReport() {
        // Check if the user is authenticated
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit();
        }

        // Get all sales data
        $salesData = SalesData::getAllSalesData();

        // Generate a report
        $report = new Report();
        $report->generate($salesData);

        // Save the report to the server
        $reportPath = 'storage/reports/sales_report_' . date('Y-m-d') . '.pdf';
        $report->save($reportPath);

        // Redirect to the report page
        $_SESSION['success'] = 'Sales report generated successfully!';
        header('Location: report.php');
        exit();
    }

    // Check if the user is authenticated
    private function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }
}
