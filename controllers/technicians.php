<?php

$config = require_once 'config.php';
$db = new dbConnection($config['database']);
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
                ':role' => 'technician'
            ];
            
            // Insert into users table
            $db->query(
                "INSERT INTO users (
                    name, email, phone, address, city, zipcode, password, role, created_at
                ) VALUES (
                    :name, :email, :phone, :address, :city, :zipcode, :password, :role, NOW()
                )",
                $params
            );

            $user_id = $db->lastInsertId();

            $db->query(
                "INSERT INTO technicians (user_id, status) VALUES (:user_id, :status)",
                [':user_id' => $user_id, ':status' => $_POST['status']]
            );
            $_SESSION['success_message'] = "Technician created successfully";
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error creating technician " . $e->getMessage();
    }
    // Redirect to avoid form resubmission
    header('Location: /dashboard/technicians');
    exit;
}

try {
    // Pagination settings
    $items_per_page = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $items_per_page;

    // Get technicians count for pagination
    $tech_count = $db->query(
        "SELECT COUNT(*) as count FROM technicians"
    )->fetch()['count'];

    $total_pages = ceil($tech_count / $items_per_page);

    // Get all technicians with their work orders
    $technicians = $db->query(
        "SELECT 
            t.tech_id, 
            u.name, 
            u.email, 
            u.phone, 
            u.address, 
            u.city, 
            u.zipcode, 
            t.status,
            COUNT(wo.order_id) as total_orders,
            COUNT(CASE WHEN wo.status = 'completed' THEN 1 END) as completed_orders,
            COUNT(CASE WHEN wo.status IN ('assigned', 'in_progress') THEN 1 END) as active_orders
        FROM technicians t
        LEFT JOIN users u ON t.user_id = u.user_id
        LEFT JOIN work_orders wo ON t.tech_id = wo.tech_id
        GROUP BY t.tech_id, u.name, u.email, u.phone, u.address, u.city, u.zipcode, t.status
        ORDER BY u.name
        LIMIT :offset, :limit",
        [':offset' => (int) $offset, ':limit' => (int) $items_per_page]
    )->fetchAll();

} catch (Exception $e) {
    error_log("Technicians error: " . $e->getMessage());
    $_SESSION['error_message'] = "Error loading technicians";
    $technicians = [];
    $total_pages = 0;
}

require_once 'views/technicians.view.php';