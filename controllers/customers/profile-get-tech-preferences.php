<?php
// File: api/technicians.php

$config = require_once '../config.php';
$db = new dbConnection($config['database']);

header('Content-Type: application/json');

// Ensure user is authenticated
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get all active technicians
$technicians = $db->query(
    "SELECT t.tech_id, u.name 
    FROM technicians t 
    JOIN users u ON t.user_id = u.user_id 
    WHERE t.status = 'active'
    ORDER BY u.name ASC"
)->fetchAll();

echo json_encode($technicians);

// File: api/customer/preferences.php

$config = require_once '../../config.php';
$db = new dbConnection($config['database']);

header('Content-Type: application/json');

// Ensure user is authenticated
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];

// Get customer_id
$customer = $db->query(
    "SELECT c.customer_id FROM customers c JOIN users u ON c.user_id = u.user_id WHERE u.user_id = :user_id",
    [$userId]
)->fetch();

if (!$customer) {
    http_response_code(404);
    echo json_encode(['error' => 'Customer not found']);
    exit;
}

$customerId = $customer['customer_id'];

// Get customer preferences
$preferences = $db->query(
    "SELECT * FROM customer_preferences WHERE customer_id = :customer_id",
    [$customerId]
)->fetch();

echo json_encode($preferences ?: null);