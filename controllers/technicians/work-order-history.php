<?php

$config = require_once 'config.php';
$db = new dbConnection($config['database']);

define("ROOT", dirname(__DIR__));
define("VIEWS", ROOT . "/../views/");

// $tech_id = $_SESSION['tech_id'];
$tech_id = 2;

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;
$offset = ($page - 1) * $records_per_page;

// Fetch work order history with filtering options
$filter_status = $_GET['status'] ?? null;
$filter_start_date = $_GET['start_date'] ?? null;
$filter_end_date = $_GET['end_date'] ?? null;

// Base query
$query = "
    SELECT wo.*, c.company_name, u.name AS customer_name 
    FROM work_orders wo
    JOIN customers c ON wo.customer_id = c.customer_id
    JOIN users u ON c.user_id = u.user_id
    WHERE wo.tech_id = :tech_id
";

// Add filters
$conditions = [];
$params = [':tech_id' => $tech_id];

if ($filter_status) {
    $conditions[] = "wo.status = :status";
    $params[':status'] = $filter_status;
}

if ($filter_start_date) {
    $conditions[] = "wo.completion_date >= :start_date";
    $params[':start_date'] = $filter_start_date;
}

if ($filter_end_date) {
    $conditions[] = "wo.completion_date <= :end_date";
    $params[':end_date'] = $filter_end_date;
}

// Combine conditions
if (!empty($conditions)) {
    $query .= " AND " . implode(" AND ", $conditions);
}

// Pagination
$query .= " ORDER BY wo.completion_date DESC LIMIT $records_per_page OFFSET $offset";

// Execute the query
$work_orders = $db->query($query, $params)->fetchAll();

// Count Query
$count_query = str_replace(
    "wo.*, c.company_name, u.name AS customer_name", 
    "COUNT(*) as total", 
    $query
);
$count_query = preg_replace("/ORDER BY .+/", "", $count_query);

$total_records = $db->query($count_query, $params)->fetch()['total'];
$total_pages = ceil($total_records / $records_per_page);

require_once 'views/technicians/work-order-history.view.php';