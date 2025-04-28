<?php
// AuthController.php

require_once(__DIR__ . '/../../includes/db.php');

require_once(__DIR__ . '/../../includes/User.php');

require_once(__DIR__ . '/../../includes/Session.php');


class AuthController {

    // Handle user login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Validate the user's credentials
            $user = User::getUserByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                // Set user session on successful login
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];

                // Redirect to user dashboard
                header('Location: dashboard.php');
                exit();
            } else {
                // Invalid credentials, show error message
                $_SESSION['login_error'] = 'Invalid email or password.';
                header('Location: login.php');
                exit();
            }
        }

        // Show login page
        include 'views/user/login.php';
    }

    // Handle user registration
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $passwordConfirm = $_POST['password_confirm'];

            // Check if passwords match
            if ($password !== $passwordConfirm) {
                $_SESSION['register_error'] = 'Passwords do not match.';
                header('Location: register.php');
                exit();
            }

            // Check if email already exists
            if (User::getUserByEmail($email)) {
                $_SESSION['register_error'] = 'Email is already registered.';
                header('Location: register.php');
                exit();
            }

            // Hash the password before storing it
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Create new user in the database
            $user = new User();
            $user->createUser($name, $email, $hashedPassword);

            // Redirect to login page after successful registration
            $_SESSION['register_success'] = 'Registration successful. Please log in.';
            header('Location: login.php');
            exit();
        }

        // Show registration page
        include 'views/user/register.php';
    }

    // Handle user logout
    public function logout() {
        // Destroy the user session
        session_unset();
        session_destroy();

        // Redirect to login page
        header('Location: login.php');
        exit();
    }

    // Check if the user is logged in (useful for protected routes)
    public function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }
}
