<?php

$config = require_once 'config.php';
$db = new dbConnection($config['database']);

if (!isset($_GET['id'])) {
    die('Work Order ID not provided');
}

try {
    $work_order = $db->query(
        "SELECT wo.*, 
            c.company_name, 
            u_c.name AS customer_name, 
            u_c.phone AS customer_phone, 
            u_t.name AS tech_name, 
            t.status AS tech_status
        FROM 
            work_orders wo
        LEFT JOIN customers c 
            ON wo.customer_id = c.customer_id
        LEFT JOIN users u_c 
            ON c.user_id = u_c.user_id -- Get customer details
        LEFT JOIN technicians t 
            ON wo.tech_id = t.tech_id
        LEFT JOIN users u_t 
            ON t.user_id = u_t.user_id -- Get technician details
        WHERE 
            wo.order_id = :order_id
        ",[':order_id' => $_GET['id']]
    )->fetch();

    if (!$work_order) {
        die('Error fetching work orders: ' . $conn->error);
    }

    echo json_encode($work_order);

} catch (Exception $e) {
    error_log("Error in Work Order get details: " . $e->getMessage());
    die('Error loading Work Order get details');
}

?>