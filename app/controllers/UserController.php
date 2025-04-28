<?php
// UserController.php

require_once 'db.php'; // Database connection
require_once 'User.php'; // User model

class UserController {

    // Display the registration page
    public function registerPage() {
        // Check if the user is already logged in
        if ($this->isAuthenticated()) {
            header('Location: dashboard.php');
            exit();
        }

        // Render the registration page
        include 'views/user/register.php';
    }

    // Handle user registration
    public function register() {
        // Check if the user is already logged in
        if ($this->isAuthenticated()) {
            header('Location: dashboard.php');
            exit();
        }

        // Collect data from the registration form
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $confirmPassword = trim($_POST['confirm_password']);

            // Validate the inputs
            if ($password !== $confirmPassword) {
                $_SESSION['error'] = 'Passwords do not match.';
                header('Location: register.php');
                exit();
            }

            if (strlen($password) < 6) {
                $_SESSION['error'] = 'Password must be at least 6 characters long.';
                header('Location: register.php');
                exit();
            }

            // Check if the username or email already exists
            if (User::isUsernameTaken($username)) {
                $_SESSION['error'] = 'Username already taken.';
                header('Location: register.php');
                exit();
            }

            if (User::isEmailTaken($email)) {
                $_SESSION['error'] = 'Email already in use.';
                header('Location: register.php');
                exit();
            }

            // Create the user in the database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $userId = User::createUser($username, $email, $hashedPassword);

            if ($userId) {
                $_SESSION['success'] = 'Registration successful!';
                header('Location: login.php');
                exit();
            } else {
                $_SESSION['error'] = 'Registration failed. Please try again.';
                header('Location: register.php');
                exit();
            }
        }
    }

    // Display the login page
    public function loginPage() {
        // Check if the user is already logged in
        if ($this->isAuthenticated()) {
            header('Location: dashboard.php');
            exit();
        }

        // Render the login page
        include 'views/user/login.php';
    }

    // Handle user login
    public function login() {
        // Check if the user is already logged in
        if ($this->isAuthenticated()) {
            header('Location: dashboard.php');
            exit();
        }

        // Collect login data
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            // Validate the inputs
            if (empty($email) || empty($password)) {
                $_SESSION['error'] = 'Please fill in all fields.';
                header('Location: login.php');
                exit();
            }

            // Check if the email exists in the database
            $user = User::getUserByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['success'] = 'Login successful!';
                header('Location: dashboard.php');
                exit();
            } else {
                $_SESSION['error'] = 'Invalid email or password.';
                header('Location: login.php');
                exit();
            }
        }
    }

    // Handle user logout
    public function logout() {
        // Destroy the session to log out the user
        session_unset();
        session_destroy();

        // Redirect to the login page
        header('Location: login.php');
        exit();
    }

    // Display the user profile page
    public function profile() {
        // Check if the user is authenticated
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit();
        }

        // Render the profile page
        include 'views/user/profile.php';
    }

    // Update user profile
    public function updateProfile() {
        // Check if the user is authenticated
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit();
        }

        // Collect data from the profile update form
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $newPassword = trim($_POST['new_password']);
            $confirmNewPassword = trim($_POST['confirm_new_password']);

            // Validate the inputs
            if (!empty($newPassword) && $newPassword !== $confirmNewPassword) {
                $_SESSION['error'] = 'New passwords do not match.';
                header('Location: profile.php');
                exit();
            }

            // Check if the username or email already exists
            if (User::isUsernameTaken($username, $_SESSION['user_id'])) {
                $_SESSION['error'] = 'Username already taken.';
                header('Location: profile.php');
                exit();
            }

            if (User::isEmailTaken($email, $_SESSION['user_id'])) {
                $_SESSION['error'] = 'Email already in use.';
                header('Location: profile.php');
                exit();
            }

            // Update user information in the database
            $updateData = [
                'username' => $username,
                'email' => $email,
            ];

            // Update password if provided
            if (!empty($newPassword)) {
                $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            $updated = User::updateUserProfile($_SESSION['user_id'], $updateData);

            if ($updated) {
                $_SESSION['success'] = 'Profile updated successfully!';
                header('Location: profile.php');
                exit();
            } else {
                $_SESSION['error'] = 'Failed to update profile.';
                header('Location: profile.php');
                exit();
            }
        }
    }

    // Check if the user is authenticated
    private function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }
}
