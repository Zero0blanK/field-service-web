<?php

$config = require_once 'config.php';
$db = new dbConnection($config['database']);

// Handle AJAX search request
if (isset($_GET['search']) && isset($_GET['ajax'])) {
    try {
        $search = $_GET['search'];
        $params = [':search' => "%{$search}%"];
        
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
                c.company_name LIKE :search 
                OR u.name LIKE :search
                OR u.city LIKE :search
            )
            GROUP BY c.customer_id
            ORDER BY c.company_name",
            $params
        )->fetchAll();
        
        header('Content-Type: application/json');
        echo json_encode($companies);
        exit;
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action']) && $_POST['action'] === 'create') {
            $params = [
                ':name' => $_POST['name'],
                ':email' => $_POST['email'],
                ':phone' => $_POST['phone'],
                ':address' => $_POST['address'],
                ':city' => $_POST['city'],
                ':zipcode' => $_POST['zipcode'],
                ':password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                ':role' => 'customer'
            ];

            // Insert into users table
            $db->query(
                "INSERT INTO 
                    users (
                        name, 
                        email, 
                        phone, 
                        address, 
                        city, 
                        zipcode, 
                        password, 
                        role, 
                        created_at
                    ) 
                VALUES (
                    :name, 
                    :email, 
                    :phone, 
                    :address, 
                    :city, 
                    :zipcode, 
                    :password, 
                    :role, 
                    NOW())
                ", $params
            );

            $user_id = $db->lastInsertId();

            // Insert into customers table
            $db->query(
                "INSERT INTO
                    customers (
                        user_id,
                        company_name
                    ) 
                VALUES (
                    :user_id,
                    :company_name
                )",
                [':user_id' => $user_id, ':company_name' => $_POST['company_name']]
            );

            $_SESSION['success_message'] = "Customer created successfully";
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error creating customer: " . $e->getMessage();
    }
    header("Location: /customers");
    exit;
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
        WHERE c.company_name = '' OR c.company_name IS NULL
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