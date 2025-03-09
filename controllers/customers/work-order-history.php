<?php

$config = require_once 'config.php';
$db = new dbConnection($config['database']);

define("ROOT", dirname(__DIR__));
define("VIEWS", ROOT . "/../views/");

// Ensure user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: /login');
    exit;
}

$userId = $_SESSION['user_id'];

// Get customer_id from the current user
$customerData = $db->query("SELECT customer_id FROM customers WHERE user_id = :user_id", [':user_id' => $userId])->fetch();
$customer_id = $customerData['customer_id'] ?? null;

if (!$customer_id) {
    die("Customer record not found.");
}

// Allowed sorting fields to prevent SQL injection
$allowedOrderBy = ['created_at', 'priority', 'status', 'scheduled_date'];
$allowedSortBy = ['ASC', 'DESC'];

$orderBy = isset($_GET['orderBy']) && in_array($_GET['orderBy'], $allowedOrderBy) ? $_GET['orderBy'] : 'created_at';
$sortBy = isset($_GET['sortBy']) && in_array($_GET['sortBy'], $allowedSortBy) ? $_GET['sortBy'] : 'DESC';
$filterBy = isset($_GET['filterBy']) ? $_GET['filterBy'] : '';

// Pagination variables
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;
$offset = ($page - 1) * $records_per_page;

// Base query
$query = "
    SELECT 
        wo.order_id, 
        wo.title, 
        wo.description, 
        wo.priority, 
        wo.status, 
        wo.location, 
        wo.scheduled_date, 
        wo.scheduled_time, 
        wo.created_at, 
        wo.completion_date,
        t.tech_id,
        u.name AS technician_name,
        u.phone AS technician_phone
    FROM 
        work_orders wo
    LEFT JOIN 
        technicians t ON wo.tech_id = t.tech_id
    LEFT JOIN 
        users u ON t.user_id = u.user_id
    WHERE 
        wo.customer_id = :customer_id
";

// Add filter if set
$params = [':customer_id' => $customer_id];
if (!empty($filterBy)) {
    $query .= " AND wo.status = :status";
    $params[':status'] = $filterBy;
}

// Order results safely
$query .= " ORDER BY $orderBy $sortBy";

// Get total records for pagination
$countQuery = "SELECT COUNT(*) as total FROM work_orders WHERE customer_id = :customer_id";
if (!empty($filterBy)) {
    $countQuery .= " AND status = :status";
}

$total_records = $db->query($countQuery, $params)->fetch()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Add limit for pagination
$query .= " LIMIT $offset, $records_per_page";

// Execute the query
$work_orders = $db->query($query, $params)->fetchAll();


// Include the view
require_once VIEWS . 'customers/work-order-history.view.php';
