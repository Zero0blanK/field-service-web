<?php

$config = require_once 'config.php';
$db = new dbConnection($config['database']);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    try {

        $email = $_POST['email'];
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = "Both fields are required";
            header("Location: /login");
            exit();
        }

        // Fetch user from database using PDO
        $user = $db->query(
            "SELECT 
                user_id, 
                name, 
                email, 
                password, 
                role 
             FROM 
                users 
             WHERE 
                email = :email",
            [':email' => $email]
        )->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            switch ($user['role']) {
                case 'admin':
                    header("Location: /dashboard");
                    break;
                case 'technician':
                    header("Location: /employee/technician");
                    break;
                case 'customer':
                    header("Location: /user/home");
                    break;
                default:
                    header("Location: /dashboard");
            }
            exit();
        } else {
            $_SESSION['error'] = "Invalid email or password";
            header("Location: /login");
            exit();
        }

    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        $_SESSION['error'] = "An error occurred during login";
        header("Location: /login");
        exit();
    }
}

// If not POST request, show the login form
require_once 'views/login.view.php';