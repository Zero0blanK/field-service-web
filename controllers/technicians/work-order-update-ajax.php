<?php

$config = require_once 'config.php';
$db = new dbConnection($config['database']);

// Check if request is AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    header('HTTP/1.1 403 Forbidden');
    exit('Direct access not allowed.');
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'technician') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$order_id = $_POST['order_id'] ?? 0;
$new_status = $_POST['status'] ?? '';

if (!$order_id || !in_array($new_status, ['in_progress', 'completed'])) {
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}

$userId = $_SESSION['user_id'];

// Get technician ID
$tech_id = $db->query("SELECT tech_id FROM technicians WHERE user_id = :user_id", [$userId])->fetch()['tech_id'];

// Check if the work order belongs to this technician
$order = $db->query("
    SELECT status FROM work_orders 
    WHERE order_id = :order_id AND tech_id = :tech_id
", [
    'order_id' => $order_id,
    'tech_id' => $tech_id
])->fetch();

if (!$order) {
    echo json_encode(['error' => 'Work order not found']);
    exit;
}

// Validate status transition
$current_status = $order['status'];

if ($new_status === 'in_progress' && $current_status !== 'assigned') {
    echo json_encode(['error' => 'Invalid status transition. Order must be assigned to start working.']);
    exit;
}

if ($new_status === 'completed' && $current_status !== 'in_progress') {
    echo json_encode(['error' => 'Invalid status transition. Order must be in progress to complete.']);
    exit;
}

// Update the work order status
$update_data = ['status' => $new_status];

// If marking as completed, also set completion date
if ($new_status === 'completed') {
    $update_data['completion_date'] = date('Y-m-d H:i:s');
}

$sql = "UPDATE work_orders SET ";
$update_parts = [];
$params = [];

foreach ($update_data as $key => $value) {
    $update_parts[] = "$key = :$key";
    $params[$key] = $value;
}

$sql .= implode(', ', $update_parts);
$sql .= " WHERE order_id = :order_id AND tech_id = :tech_id";

$params['order_id'] = $order_id;
$params['tech_id'] = $tech_id;

$db->query($sql, $params);

echo json_encode(['success' => true, 'message' => 'Work order updated successfully']);