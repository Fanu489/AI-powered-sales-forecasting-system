<?php
class Session {
    // Start a session if it hasn't been started already
    public static function start() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Set a session variable
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    // Get a session variable
    public static function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    // Check if a session variable exists
    public static function has($key) {
        return isset($_SESSION[$key]);
    }

    // Remove a session variable
    public static function remove($key) {
        if (self::has($key)) {
            unset($_SESSION[$key]);
        }
    }

    // Destroy the session
    public static function destroy() {
        session_unset();
        session_destroy();
    }

    // Check if the user is logged in
    public static function isLoggedIn() {
        return self::has('user_id');  // Adjust to your session key
    }

    // Set a flash message
    public static function setFlash($key, $message) {
        $_SESSION['flash'][$key] = $message;
    }

    // Get a flash message
    public static function getFlash($key) {
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);  // Remove it after displaying
            return $message;
        }
        return null;
    }
}
?>
