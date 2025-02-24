<?php
// get_work_order_details.php
require_once 'config.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Work Order ID not provided']);
    exit;
}

$order_id = sanitize($_GET['id']);

// Get work order details
$work_order = $conn->query("
    SELECT wo.*, 
           c.company_name, 
           u_c.name AS customer_name, 
           u_c.phone AS customer_phone, 
           u_t.name AS tech_name, 
           t.status AS tech_status
    FROM work_orders wo
    LEFT JOIN customers c ON wo.customer_id = c.customer_id
    LEFT JOIN users u_c ON c.user_id = u_c.user_id -- Get customer details
    LEFT JOIN technicians t ON wo.tech_id = t.tech_id
    LEFT JOIN users u_t ON t.user_id = u_t.user_id -- Get technician details
    WHERE wo.order_id = '$order_id'
");

if (!$work_order) {
    http_response_code(500);
    echo json_encode(['error' => 'Error fetching work order details: ' . $conn->error]);
    exit;
}

$work_order = $work_order->fetch_assoc();

if (!$work_order) {
    http_response_code(404);
    echo json_encode(['error' => 'Work Order not found']);
    exit;
}

echo json_encode($work_order);
?>
