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

if (!$order_id) {
    echo json_encode(['error' => 'Invalid order ID']);
    exit;
}

$userId = $_SESSION['user_id'];

// Get technician ID
$tech_id = $db->query("SELECT tech_id FROM technicians WHERE user_id = :user_id", [$userId])->fetch()['tech_id'];

// Fetch work order details
$order = $db->query("
    SELECT wo.*, c.company_name, u.name AS customer_name, u.email AS customer_email, 
          u.phone AS customer_phone, u.address, u.city, u.zipcode
    FROM work_orders wo
    JOIN customers c ON wo.customer_id = c.customer_id
    JOIN users u ON c.user_id = u.user_id
    WHERE wo.order_id = :order_id AND wo.tech_id = :tech_id
", [
    'order_id' => $order_id,
    'tech_id' => $tech_id
])->fetch();

if (!$order) {
    echo json_encode(['error' => 'Work order not found']);
    exit;
}

echo json_encode(['success' => true, 'order' => $order]);