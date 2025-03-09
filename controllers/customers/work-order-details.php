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

// Validate order ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid work order ID']);
    exit;
}

$order_id = $_GET['id'];

// Get work order details
$order = $db->query("
    SELECT * 
    FROM work_orders 
    WHERE order_id = :order_id AND customer_id = :customer_id
", ['order_id' => $order_id, 'customer_id' => $customer_id])->fetch();

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Work order not found']);
    exit;
}

// Get technician details if assigned
$technician = null;
if (!empty($order['tech_id'])) {
    $technician = $db->query("
        SELECT u.name, u.phone, u.email
        FROM technicians t
        JOIN users u ON t.user_id = u.user_id
        WHERE t.tech_id = :tech_id
    ", ['tech_id' => $order['tech_id']])->fetch();
}

// Return the data as JSON
echo json_encode([
    'success' => true,
    'order' => $order,
    'technician' => $technician
]);