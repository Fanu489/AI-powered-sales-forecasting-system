<?php
// ReporterController.php

require_once(__DIR__ . '/../../includes/db.php');  // Database connection
require_once(__DIR__ . '/../../includes/User.php'); // User model
require_once(__DIR__ . '/../../includes/SalesData.php');  // Correct path to SalesData.php
 // Sales Data model
require_once(__DIR__ . '/../../includes/Report.php'); // Report model

class ReporterController {

    // Show the list of generated reports
    public function index() {
        // Check if the user is authenticated
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit();
        }

        // Get all reports from the database
        $reports = Report::getAllReports();

        // Render the reports page
        include 'views/admin/manage_reports.php';
    }

    // Generate a new sales report
    public function generateReport() {
        // Check if the user is authenticated
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit();
        }

        // Check if form is submitted to generate a report
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get the filter parameters for the report
            $startDate = $_POST['start_date'];
            $endDate = $_POST['end_date'];
            $product = $_POST['product'];

            // Validate the date range
            if ($startDate > $endDate) {
                $_SESSION['error'] = 'Start date cannot be later than end date.';
                header('Location: generate_report.php');
                exit();
            }

            // Fetch sales data for the selected product and date range
            $salesData = SalesData::getSalesDataByDateRange($startDate, $endDate, $product);

            if (empty($salesData)) {
                $_SESSION['error'] = 'No sales data found for the given period and product.';
                header('Location: generate_report.php');
                exit();
            }

            // Generate the report
            $report = Report::createReport($salesData, $startDate, $endDate, $product);

            if ($report) {
                $_SESSION['success'] = 'Report generated successfully.';
                header('Location: manage_reports.php');
                exit();
            } else {
                $_SESSION['error'] = 'Failed to generate report.';
                header('Location: generate_report.php');
                exit();
            }
        }
    }

    // View a specific report
    public function viewReport($reportId) {
        // Check if the user is authenticated
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit();
        }

        // Get the report from the database
        $report = Report::getReportById($reportId);

        // Render the report view
        include 'views/admin/view_report.php';
    }

    // Delete a specific report
    public function deleteReport($reportId) {
        // Check if the user is authenticated
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit();
        }

        // Delete the report
        $deleteSuccess = Report::deleteReportById($reportId);

        if ($deleteSuccess) {
            $_SESSION['success'] = 'Report deleted successfully.';
        } else {
            $_SESSION['error'] = 'Failed to delete the report.';
        }

        // Redirect to the manage reports page
        header('Location: manage_reports.php');
        exit();
    }

    // Check if the user is authenticated
    private function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }
}
