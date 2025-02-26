<?php

$config = require_once 'config.php';
$db = new dbConnection($config['database']);

try {
    $stats = $db->query(
        "SELECT
            COUNT(
                CASE WHEN status = 'pending' THEN 1 END
            ) AS pending,
            COUNT(
                CASE WHEN status = 'in_progress' THEN 1 END
            ) AS in_progress,
            COUNT(
                CASE WHEN status = 'completed' THEN 1 END
            ) AS completed
        FROM work_orders
        ")->fetch();

    // Add total technicians count
    $tech_stats = $db->query(
        "SELECT 
            COUNT(
                *
            ) AS total_technicians,
            COUNT(
                CASE WHEN t.status = 'available' THEN 1 END
            ) AS active_technicians
        FROM technicians t
        LEFT JOIN users u 
            ON t.user_id = u.user_id
        WHERE u.role = 'technician'
    ")->fetch();
    
    $stats['total_technicians'] = $tech_stats['total_technicians'];
    $stats['active_technicians'] = $tech_stats['active_technicians'];

    // Get recent work orders with correct JOINs
    $recent_orders = $db->query(
        "SELECT 
            wo.*, 
            c.company_name, 
            t_status.name AS tech_name,
            c_user.name AS customer_name,
            cp.*
        FROM work_orders wo 
        LEFT JOIN customers c 
            ON wo.customer_id = c.customer_id 
        LEFT JOIN users c_user 
            ON c.user_id = c_user.user_id
        LEFT JOIN customer_preferences cp 
            ON c.customer_id = cp.customer_id
        LEFT JOIN technicians t 
            ON wo.tech_id = t.tech_id
        LEFT JOIN users t_status 
            ON t.user_id = t_status.user_id
        ORDER BY wo.created_at DESC 
        LIMIT 5
    ")->fetchAll();

} catch (Exception $e) {
    // Log the error
    error_log("Dashboard error: " . $e->getMessage());
    // Set default values for stats
    $stats = [
        'pending' => 0,
        'in_progress' => 0,
        'completed' => 0,
        'total_technicians' => 0,
        'active_technicians' => 0
    ];
    $recent_orders = null;
}


require_once 'views/dashboard.view.php';