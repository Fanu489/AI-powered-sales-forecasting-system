<?php
// User.php - Handles user operations like creation, fetching, updating, and deletion

require_once(__DIR__ . '/db.php');  // Include the database connection

class User {

    // Get all users from the database
    public static function getAllUsers() {
        global $pdo;  // Use the global database connection

        try {
            $stmt = $pdo->prepare("SELECT * FROM users");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error fetching users: " . $e->getMessage());
        }
    }

    // Get a single user by ID
    public static function getUserById($userId) {
        global $pdo;

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error fetching user: " . $e->getMessage());
        }
    }

    // Create a new user
    public function createUser($name, $email, $password) {
        global $pdo;

        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $password]);
        } catch (PDOException $e) {
            die("Error creating user: " . $e->getMessage());
        }
    }

    // Update user details (name, email)
    public function updateUser($userId, $name, $email) {
        global $pdo;

        try {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, $userId]);
        } catch (PDOException $e) {
            die("Error updating user: " . $e->getMessage());
        }
    }

    // Delete a user by ID
    public function deleteUser($userId) {
        global $pdo;

        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
        } catch (PDOException $e) {
            die("Error deleting user: " . $e->getMessage());
        }
    }

    // Check if a user exists by email
    public static function userExists($email) {
        global $pdo;

        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            die("Error checking user existence: " . $e->getMessage());
        }
    }

    // Validate user credentials for login
    public static function validateLogin($email, $password) {
        global $pdo;

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if password matches the hashed password in the database
            if ($user && password_verify($password, $user['password'])) {
                return $user;  // Return user data if login is successful
            } else {
                return false;  // Return false if invalid credentials
            }
        } catch (PDOException $e) {
            die("Error validating login: " . $e->getMessage());
        }
    }
}
