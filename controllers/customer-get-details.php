<?php

$config = require_once 'config.php';
$db = new dbConnection($config['database']);

if (!isset($_GET['id'])) {
    die('Customer ID not provided');
}

try {
    // Get customer details with user information
    $customer = $db->query("
        SELECT 
            c.customer_id, 
            c.company_name, 
            u.name, 
            u.email, 
            u.phone, 
            u.address, 
            u.city, 
            u.zipcode
        FROM customers c
        LEFT JOIN users u ON c.user_id = u.user_id
        WHERE c.customer_id = :customer_id",
        [':customer_id' => $_GET['id']]
    )->fetch();

    if (!$customer) {
        die('Customer not found');
    }

    // Get customer's work orders with technician details
    $work_orders = $db->query("
        SELECT 
            wo.*, 
            u.name AS tech_name
        FROM work_orders wo
        LEFT JOIN technicians t ON wo.tech_id = t.tech_id
        LEFT JOIN users u ON t.user_id = u.user_id
        WHERE wo.customer_id = :customer_id
        ORDER BY wo.created_at DESC",
        [':customer_id' => $_GET['id']]
    )->fetchAll();

    // Calculate statistics
    $stats = $db->query("
        SELECT 
            COUNT(*) AS total_orders,
            COUNT(CASE WHEN status = 'completed' THEN 1 END) AS completed_orders,
            COUNT(CASE WHEN status IN ('pending', 'assigned', 'in_progress') THEN 1 END) AS active_orders,
            AVG(CASE WHEN status = 'completed' THEN 
                TIMESTAMPDIFF(HOUR, created_at, completion_date) 
            END) AS avg_completion_time
        FROM work_orders
        WHERE customer_id = :customer_id",
        [':customer_id' => $_GET['id']]
    )->fetch();
    require_once 'views/partials/customers/detailsContent.php';
} catch (Exception $e) {
    error_log("Error in customer details: " . $e->getMessage());
    die('Error loading customer details');
}
?>

