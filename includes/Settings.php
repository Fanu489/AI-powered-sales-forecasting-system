<?php
// Settings.php - Handles site configuration settings

require_once(__DIR__ . '/db.php');  // Include the database connection

class Settings {

    // Get all site settings from the database
    public static function getSiteSettings() {
        global $pdo;  // Use the global database connection

        try {
            $stmt = $pdo->prepare("SELECT * FROM site_settings LIMIT 1");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error fetching site settings: " . $e->getMessage());
        }
    }

    // Update site name
    public static function updateSiteName($site_name) {
        global $pdo;

        try {
            $stmt = $pdo->prepare("UPDATE site_settings SET site_name = ? WHERE id = 1");
            $stmt->execute([$site_name]);
        } catch (PDOException $e) {
            die("Error updating site name: " . $e->getMessage());
        }
    }

    // Update admin email
    public static function updateAdminEmail($admin_email) {
        global $pdo;

        try {
            $stmt = $pdo->prepare("UPDATE site_settings SET admin_email = ? WHERE id = 1");
            $stmt->execute([$admin_email]);
        } catch (PDOException $e) {
            die("Error updating admin email: " . $e->getMessage());
        }
    }

    // Add or update other site settings (you can expand this function)
    public static function updateOtherSettings($key, $value) {
        global $pdo;

        try {
            $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) 
                                   VALUES (?, ?) 
                                   ON DUPLICATE KEY UPDATE setting_value = ?");
            $stmt->execute([$key, $value, $value]);
        } catch (PDOException $e) {
            die("Error updating other settings: " . $e->getMessage());
        }
    }
}
