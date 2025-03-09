<?php

$config = require_once 'config.php';
$db = new dbConnection($config['database']);


// Ensure user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$userId = $_SESSION['user_id'];

// Get customer_id from the current user
$customer_id = $db->query("SELECT customer_id FROM customers WHERE user_id = :user_id", [$userId])->fetch()['customer_id'];

// Get request body
$data = json_decode(file_get_contents('php://input'), true);

// Validate order ID
if (!isset($data['order_id']) || !is_numeric($data['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid work order ID']);
    exit;
}

$order_id = $data['order_id'];

// Check if the work order belongs to this customer and is in a cancellable state
$workOrder = $db->query("
    SELECT status
    FROM work_orders 
    WHERE order_id = :order_id AND customer_id = :customer_id
", [$order_id, $customer_id])->fetch();

if (!$workOrder) {
    echo json_encode(['success' => false, 'message' => 'Work order not found']);
    exit;
}

// Check if the order is already completed or cancelled
if ($workOrder['status'] === 'completed' || $workOrder['status'] === 'cancelled') {
    echo json_encode(['success' => false, 'message' => 'This work order cannot be cancelled']);
    exit;
}

// Update work order status to cancelled
$result = $db->query("
    UPDATE work_orders 
    SET status = 'cancelled' 
    WHERE order_id = :order_id AND customer_id = :customer_id
", [$order_id, $customer_id]);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Work order cancelled successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to cancel work order']);
}