<?php
// ProfileController.php

require_once(__DIR__ . '/../../includes/db.php');  // Database connection
require_once(__DIR__ . '/../../includes/User.php'); // User model


class ProfileController {

    // Show the user's profile page
    public function index() {
        // Check if the user is authenticated
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit();
        }

        // Get the user details from the database
        $userId = $_SESSION['user_id'];
        $user = User::getUserById($userId);

        // Render the profile page
        include 'views/user/profile.php';
    }

    // Update user profile
    public function update() {
        // Check if the user is authenticated
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit();
        }

        // Check if form is submitted to update the profile
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get the posted data
            $userId = $_SESSION['user_id'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            // Validate password confirmation
            if ($password !== $confirmPassword) {
                $_SESSION['error'] = 'Passwords do not match.';
                header('Location: profile.php');
                exit();
            }

            // Update user details
            $updateSuccess = User::updateUserProfile($userId, $username, $email, $password);

            if ($updateSuccess) {
                $_SESSION['success'] = 'Profile updated successfully.';
            } else {
                $_SESSION['error'] = 'Failed to update profile.';
            }

            // Redirect to the profile page
            header('Location: profile.php');
            exit();
        }
    }

    // Check if the user is authenticated
    private function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }

    // Change password functionality
    public function changePassword() {
        // Check if the user is authenticated
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit();
        }

        // Check if form is submitted to change password
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $currentPassword = $_POST['current_password'];
            $newPassword = $_POST['new_password'];
            $confirmNewPassword = $_POST['confirm_new_password'];

            // Validate current password
            $user = User::getUserById($userId);
            if (!password_verify($currentPassword, $user['password'])) {
                $_SESSION['error'] = 'Current password is incorrect.';
                header('Location: change_password.php');
                exit();
            }

            // Validate new password confirmation
            if ($newPassword !== $confirmNewPassword) {
                $_SESSION['error'] = 'New passwords do not match.';
                header('Location: change_password.php');
                exit();
            }

            // Update password in the database
            $updatePasswordSuccess = User::updatePassword($userId, password_hash($newPassword, PASSWORD_DEFAULT));

            if ($updatePasswordSuccess) {
                $_SESSION['success'] = 'Password updated successfully.';
            } else {
                $_SESSION['error'] = 'Failed to update password.';
            }

            // Redirect to the profile page
            header('Location: profile.php');
            exit();
        }
    }
}
