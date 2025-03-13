<?php

$config = require_once 'config.php';
$db = new dbConnection($config['database']);

define("ROOT", dirname(__DIR__));
define("VIEWS", ROOT . "/../views/");

// Make sure user is logged in and has customer role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header('Location: /login');
    exit;
}

$userId = $_SESSION['user_id'];

// Get customer_id from users table
$customer = $db->query(
    "SELECT c.customer_id, u.city FROM customers c JOIN users u ON c.user_id = u.user_id WHERE u.user_id = :user_id",
    [$userId]
)->fetch();

if (!$customer) {
    // Handle error - customer not found
    header('Location: /login');
    exit;
}

$customerId = $customer['customer_id'];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $preferredTechnician = $_POST['preferred_technician'] ?? null;
    $preferredDayTime = $_POST['preferred_day_time'] ?? null;
    $preferredMethod = $_POST['contact_method'] ?? null;
    
    // Check if preferences already exist
    $existingPrefs = $db->query(
        "SELECT id FROM customer_preferences WHERE customer_id = :customer_id",
        [$customerId]
    )->fetch();
    
    if ($existingPrefs) {
        // Update existing preferences
        $db->query(
            "UPDATE customer_preferences 
            SET preferred_technician = :tech, preferred_time_of_day = :timeOfDay ,preferred_contact_method = :method 
            WHERE customer_id = :customer_id",
            [
                'tech' => $preferredTechnician,
                'timeOfDay' => $preferredDayTime,
                'method' => $preferredMethod,
                'customer_id' => $customerId
            ]
        );
    } else {
        // Insert new preferences
        $db->query(
            "INSERT INTO customer_preferences (customer_id, preferred_technician, preferred_time_of_day, preferred_contact_method) 
            VALUES (:customer_id, :tech, :timeOfDay, :method)",
            [
                'customer_id' => $customerId,
                'tech' => $preferredTechnician,
                'timeOfDay' => $preferredDayTime,
                'method' => $preferredMethod
            ]
        );
    }
    
    // Redirect to preferences page with success message
    header('Location: /user/preferences');
    exit;
}

// Get current preferences
$preferences = $db->query(
    "SELECT * FROM customer_preferences WHERE customer_id = :customer_id",
    [$customerId]
)->fetch();



// Prepare city filter with wildcard
$cityFilter = '%' . $customer['city'] . '%';

// Get list of technicians for dropdown
$technicians = $db->query(
    "SELECT t.tech_id, u.name 
    FROM technicians t 
    JOIN users u ON t.user_id = u.user_id 
    WHERE t.status = 'available' AND u.city LIKE ? 
    ORDER BY u.name ASC", 
    [$cityFilter]
)->fetchAll();




require_once 'views/customers/profile-preferences.view.php';