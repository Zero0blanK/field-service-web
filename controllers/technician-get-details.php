<?php


$config = require_once 'config.php';
$db = new dbConnection($config['database']);

if (!isset($_GET['id'])) {
    die('Technician ID not provided');
}

try {

    $technician = $db->query(
        "SELECT 
            t.*, 
            u.name, 
            u.email, 
            u.phone, 
            u.address, 
            u.city, 
            u.zipcode
        FROM 
            technicians t
        LEFT JOIN users u 
            ON t.user_id = u.user_id
        WHERE 
            t.tech_id = :tech_id
        ",[':tech_id' => $_GET['id']]
    )->fetch();

    if (!$technician) {
        die('Error fetching technician details');
    }

    // Get technician's work orders
    $work_orders = $db->query(
        "SELECT 
            wo.*, 
            c.company_name,
            u.name AS customer_name,
            u.phone AS customer_phone,
            u.address,
            u.zipcode,
            u.city
        FROM 
            work_orders wo
        LEFT JOIN customers c 
            ON wo.customer_id = c.customer_id
        LEFT JOIN users u 
            ON c.user_id = u.user_id 
        WHERE 
            wo.tech_id = :tech_id
        ORDER BY 
            wo.created_at DESC
    ",[':tech_id' => $_GET['id']]
    )->fetchAll() ?? [];
    
    // Count active orders
    $active_orders_count = 0;

    if (is_array($work_orders)) {
        $orders_data = $work_orders;
    } else {
        $orders_data = []; // Default empty array to prevent errors
    }

    foreach ($work_orders as $order) {
        if ($order['status'] != 'completed' && $order['status'] != 'cancelled') {
            $active_orders_count++;
        }
    }

    require_once 'views/partials/technicians/detailsContent.php';
} catch (Exception $e) {
    error_log("Error in Technician get details: " . $e->getMessage());
    die('Error loading Technician get details');
}
?>
