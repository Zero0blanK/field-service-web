<?php


$config = require_once 'config.php';
$db = new dbConnection($config['database']);
require_once 'register-user.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    registerUser($db, [
        ':name' => $_POST['tech_name'],
        ':email' => $_POST['email'],
        ':phone' => $_POST['phone'],
        ':address' => $_POST['address'],
        ':city' => $_POST['city'],
        ':zipcode' => $_POST['zipcode'],
        ':password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        ':role' => 'technician'
    ], 'technician');
    // Redirect to avoid form resubmission
    header('Location: /dashboard/technicians');
    exit;
}

try {
    // Pagination settings
    $items_per_page = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $items_per_page;

    // Initialize filter variables
    $status_filter = isset($_GET['status']) && !empty($_GET['status']) ? $_GET['status'] : '';
    $search_term = isset($_GET['search']) && !empty($_GET['search']) ? trim($_GET['search']) : '';

    // Base query
    $base_query = "FROM technicians t 
        LEFT JOIN users u ON t.user_id = u.user_id
        LEFT JOIN work_orders wo ON t.tech_id = wo.tech_id";

    // Where clauses
    $query_params = [];
    $where_clauses = [];

    if (!empty($status_filter)) {
        $where_clauses[] = "t.status = :status";
        $query_params[':status'] = $status_filter;
    }

    if (!empty($search_term)) {
        $search_param = "%{$search_term}%";
        $where_clauses[] = "(u.name LIKE :search_name OR u.email LIKE :search_email OR u.phone LIKE :search_phone OR u.city LIKE :search_city)";
        $query_params[':search_name'] = $search_param;
        $query_params[':search_email'] = $search_param; 
        $query_params[':search_phone'] = $search_param;
        $query_params[':search_city'] = $search_param;
    }

    // Apply filters if any
    if (!empty($where_clauses)) {
        $base_query .= " WHERE " . implode(" AND ", $where_clauses);
    }

    // Count query (for pagination)
    $count_query = "SELECT COUNT(DISTINCT t.tech_id) as count $base_query";

    // Execute count query
    $stmt = $db->query($count_query, $query_params);
    $tech_count = $stmt ? $stmt->fetch()['count'] : 0;
    $total_pages = ($tech_count > 0) ? ceil($tech_count / $items_per_page) : 1;

    // Add GROUP BY and ORDER BY
    $base_query .= " GROUP BY t.tech_id ORDER BY u.name";

    // Technicians data query
    $technicians_query = "SELECT 
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
        $base_query
        LIMIT :offset, :limit";

    // Add pagination params
    $query_params[':offset'] = (int) $offset;
    $query_params[':limit'] = (int) $items_per_page;

    $technicians = $db->query($technicians_query, $query_params)->fetchAll();

} catch (Exception $e) {
    error_log("Technicians error: " . $e->getMessage());
    $_SESSION['error_message'] = "Error loading technicians";
    $technicians = [];
    $total_pages = 0;
}

require_once 'views/technicians.view.php';