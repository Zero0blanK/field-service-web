<?php

$config = require_once 'config.php';
$db = new dbConnection($config['database']);

// Handle resident search AJAX request
if (isset($_GET['search']) && isset($_GET['ajax']) && isset($_GET['type']) && $_GET['type'] == 'residents') {
    try {
        $search = $_GET['search'];
        $params = [
            "%{$search}%",
            "%{$search}%"
        ];
        
        $residents = $db->query("
            SELECT 
                c.*, 
                u.name, 
                u.email, 
                u.phone, 
                u.address, 
                u.city, 
                u.zipcode,
                COUNT(wo.order_id) as total_orders,
                COUNT(CASE WHEN wo.status = 'completed' THEN 1 END) as completed_orders
            FROM customers c
            LEFT JOIN users u ON c.user_id = u.user_id
            LEFT JOIN work_orders wo ON c.customer_id = wo.customer_id
            WHERE (c.company_name = '' OR c.company_name IS NULL) 
            AND u.role = 'customer'
            AND (
                u.name LIKE ?
                OR u.city LIKE ?
            )
            GROUP BY c.customer_id
            ORDER BY u.name",
            $params
        )->fetchAll();
        
        header('Content-Type: application/json');

        echo json_encode($residents);
        exit;
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

// Handle Company AJAX search request
if (isset($_GET['search']) && isset($_GET['ajax']) && (!isset($_GET['type']) || $_GET['type'] == 'companies')) {
    try {
        $search = $_GET['search'];
        $params = [
            "%{$search}%",
            "%{$search}%",
            "%{$search}%"
        ];
        
        $companies = $db->query("
            SELECT 
                c.*, 
                u.name, 
                u.email, 
                u.phone, 
                u.address, 
                u.city, 
                u.zipcode,
                COUNT(wo.order_id) as total_orders,
                COUNT(CASE WHEN wo.status = 'completed' THEN 1 END) as completed_orders
            FROM customers c
            LEFT JOIN users u ON c.user_id = u.user_id
            LEFT JOIN work_orders wo ON c.customer_id = wo.customer_id
            WHERE c.company_name != '' 
            AND (
                c.company_name LIKE ?
                OR u.name LIKE ?
                OR u.city LIKE ?
            )
            GROUP BY c.customer_id
            ORDER BY c.company_name",
            $params
        )->fetchAll();
        
        header('Content-Type: application/json');
        echo json_encode($companies);
        exit;
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

try {
    // Pagination settings
    $items_per_page = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $items_per_page;

    // Get company customers count
    $companies_count = $db->query(
        "SELECT COUNT(*) as count FROM customers WHERE company_name != ''"
    )->fetch()['count'];

    $companies_pages = ceil($companies_count / $items_per_page);

    // Get company customers with pagination
    $companies = $db->query(
        "SELECT 
            c.*, 
            u.name, 
            u.email, 
            u.phone, 
            u.address, 
            u.city, 
            u.zipcode,
            COUNT(wo.order_id) as total_orders,
            COUNT(CASE WHEN wo.status = 'completed' THEN 1 END) as completed_orders
        FROM 
            customers c
        LEFT JOIN users u 
            ON c.user_id = u.user_id
        LEFT JOIN work_orders wo 
            ON c.customer_id = wo.customer_id
        WHERE 
            c.company_name != ''
        GROUP BY 
            c.customer_id
        ORDER BY 
            c.company_name
        LIMIT :offset, :limit
        ",[':offset' => (int) $offset, ':limit' => (int) $items_per_page]
    )->fetchAll();

    
    // Get individual customers count
    $residents_count = $db->query(
        "SELECT COUNT(*) as count FROM customers WHERE company_name = '' OR company_name IS NULL"
    )->fetch()['count'];

    $residents_pages = ceil($residents_count / $items_per_page);

    // Get individual customers with pagination
    $residents = $db->query(
        "SELECT 
            c.*, 
            u.name, 
            u.email, 
            u.phone, 
            u.address, 
            u.city, 
            u.zipcode,
            COUNT(wo.order_id) as total_orders,
            COUNT(CASE WHEN wo.status = 'completed' THEN 1 END) as completed_orders
        FROM customers c
        LEFT JOIN users u ON c.user_id = u.user_id
        LEFT JOIN work_orders wo ON c.customer_id = wo.customer_id
        WHERE c.company_name = '' OR c.company_name IS NULL AND u.role = 'customer'
        GROUP BY c.customer_id
        ORDER BY u.name
        LIMIT :offset, :limit",
        [':offset' => (int) $offset, ':limit' => (int) $items_per_page]
    )->fetchAll();

} catch (Exception $e) {
    error_log("Customers error: " . $e->getMessage());
    $_SESSION['error_message'] = "Error loading customers";
    $companies = [];
    $residents = [];
    $companies_pages = 0;
    $residents_pages = 0;
}

require_once 'views/customers.view.php';