<?php

$config = require_once 'config.php';
$db = new dbConnection($config['database']);

define("ROOT", dirname(__DIR__));
define("VIEWS", ROOT . "/../views/");

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$userId = $_SESSION['user_id'];
$errors = [];
$success = false;

// Get current user information to pre-fill the form
$currentUser = $db->query("SELECT * FROM users WHERE user_id = :user_id", [$userId])->fetch();

// Check if the user is a customer
if ($currentUser['role'] !== 'customer') {
    header('Location: /dashboard');
    exit;
}

// Get customer information
$customerInfo = $db->query("SELECT * FROM customers WHERE user_id = :user_id", [$userId])->fetch();
if (!$customerInfo) {
    // Handle the case where customer record doesn't exist
    $errors[] = 'Customer record not found. Please contact support.';
}

// Determine if the customer is a company (has company name) or resident
$isCompany = !empty($customerInfo['company_name']);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $fullName = trim($_POST['update_FullName'] ?? '');
    $email = trim($_POST['update_EmailAddress'] ?? '');
    $phone = trim($_POST['update_PhoneNumber'] ?? '');
    $address = trim($_POST['update_Address'] ?? '');
    $city = trim($_POST['update_City'] ?? '');
    $zipCode = trim($_POST['update_zipCode'] ?? '');
    $password = $_POST['update_Password'] ?? '';
    $companyName = $isCompany ? trim($_POST['update_CompanyName'] ?? '') : null;

    // Validate form data
    if (empty($fullName)) {
        $errors[] = 'Full name is required';
    }

    if (!empty($email)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        // Check if email is already taken by another user
        $emailExists = $db->query("SELECT user_id FROM users WHERE email = :email AND user_id != :user_id", [
            'email' => $email,
            'user_id' => $userId
        ])->rowCount();
        
        if ($emailExists > 0) {
            $errors[] = 'Email address is already in use';
        }
    } else {
        $errors[] = 'Email address is required';
    }

    // Validate phone format (optional)
    if (!empty($phone) && !preg_match('/^[\d\s\(\)\-\+\.]{7,20}$/', $phone)) {
        $errors[] = 'Please enter a valid phone number';
    }

    // Only validate if password field is not empty (user wants to change password)
    if (!empty($password) && strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long';
    }

    // Validate if company name is provided for company accounts
    if ($isCompany && empty($companyName)) {
        $errors[] = 'Company name is required for business accounts';
    }

    // If no errors, update user information
    if (empty($errors)) {
        try {
            
            // Prepare update data for users table
            $updateData = [
                'name' => $fullName,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'city' => $city,
                'zipcode' => $zipCode,
                'user_id' => $userId
            ];
            
            // Only update password if provided
            if (!empty($password)) {
                $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
            
            // Build the update query dynamically
            $updateFields = [];
            foreach (array_keys($updateData) as $field) {
                if ($field !== 'user_id') {
                    $updateFields[] = "$field = :$field";
                }
            }
            
            if (!empty($updateFields)) {
                $updateQuery = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE user_id = :user_id";
                $db->query($updateQuery, $updateData);
            }
            
            // If company, update company name
            if ($isCompany && !empty($companyName)) {
                $db->query("UPDATE customers SET company_name = :company_name WHERE customer_id = :customer_id", [
                    'company_name' => $companyName,
                    'customer_id' => $customerInfo['customer_id']
                ]);
            }
            
            $success = true;
            
            // Refresh current user data after update
            $currentUser = $db->query("SELECT * FROM users WHERE user_id = :user_id", [$userId])->fetch();
            $customerInfo = $db->query("SELECT * FROM customers WHERE user_id = :user_id", [$userId])->fetch();
            
            // Refresh the session data
            $_SESSION['name'] = $fullName;
            $_SESSION['email'] = $email;
            
        } catch (Exception $e) {
            $errors[] = 'An error occurred while updating your information. Please try again.';
            // Log the error for administrators
            error_log('Account update error: ' . $e->getMessage());
        }
    }
}

require_once 'views/customers/profile.view.php';