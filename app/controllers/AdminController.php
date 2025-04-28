<?php
// AdminController.php

require_once(__DIR__ . '/../../includes/db.php');  // Database connection
require_once(__DIR__ . '/../../includes/User.php'); // User model
require_once(__DIR__ . '/../../includes/SalesData.php');  // Correct path to SalesData.php
 // Sales Data model
require_once(__DIR__ . '/../../includes/Report.php'); // Report model
require_once(__DIR__ . '/../../includes/Settings.php'); // Settings model for site configuration

class AdminController {

    // Check if admin is logged in
    public function isAdmin() {
        if (!isset($_SESSION['admin_id'])) {
            header('Location: login.php');
            exit();
        }
    }

    // Show Admin Dashboard
    public function showDashboard() {
        $this->isAdmin();
        
        // Fetch sales data and reports
        $salesData = SalesData::getAllSalesData();
        $reports = Report::getAllReports();

        include '../../views/admin/admin_dashboard.php'; // Include the admin dashboard view
    }

    // Manage Users (view, add, edit, delete)
    public function manageUsers() {
        $this->isAdmin();

        // Fetch users from the database
        $users = User::getAllUsers();

        // Include the manage users view
        include '../../views/admin/manage_users.php';
    }

    // Create or Edit User
    public function createUser() {
        $this->isAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            // Create a new user in the database
            $user = new User();
            $user->createUser($name, $email, $password);

            header('Location: manage_users.php');
        }

        // Include user creation form
        include '../../views/admin/create_user.php';
    }

    // Delete User
    public function deleteUser($userId) {
        $this->isAdmin();

        // Delete the user from the database
        $user = new User();
        $user->deleteUser($userId);

        header('Location: manage_users.php');
    }

    // Manage Sales Data (view, add, delete)
    public function manageSales() {
        $this->isAdmin();

        // Fetch all sales data
        $salesData = SalesData::getAllSalesData();

        include '../../views/admin/manage_sales.php';
    }

    // Upload Sales Data (CSV file handling)
    public function uploadSalesData() {
        $this->isAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['sales_file'])) {
            $file = $_FILES['sales_file']['tmp_name'];

            // Process the uploaded CSV file
            if (($handle = fopen($file, 'r')) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    $product = $data[0];
                    $quantity = $data[1];
                    $price = $data[2];

                    // Insert into database (you should create a SalesData model to handle this)
                    SalesData::insertSalesData($product, $quantity, $price);
                }
                fclose($handle);
            }

            header('Location: manage_sales.php');
        }

        // Include the upload sales form
        include '../../views/admin/upload_sales.php';
    }

    // Generate Report (sales forecast, etc.)
    public function generateReport() {
        $this->isAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Process the report generation logic
            $report = new Report();
            $report->generateSalesForecast();

            // Redirect to the report view page
            header('Location: manage_reports.php');
        }

        // Include report generation page
        include '../../views/admin/generate_report.php';
    }

    // Manage Site Settings
    public function manageSettings() {
        $this->isAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $site_name = $_POST['site_name'];
            $admin_email = $_POST['admin_email'];

            // Update site settings in the database
            $this->updateSiteSettings($site_name, $admin_email);

            header('Location: site_settings.php');
        }

        // Include site settings form
        include '../../views/admin/site_settings.php';
    }

    // Update Site Settings
    private function updateSiteSettings($site_name, $admin_email) {
        // You can implement the database update logic for site settings here
        // Example: Settings::updateSiteName($site_name);
        // Example: Settings::updateAdminEmail($admin_email);

        $settings = new Settings();
        $settings->updateSiteName($site_name);
        $settings->updateAdminEmail($admin_email);
    }
}