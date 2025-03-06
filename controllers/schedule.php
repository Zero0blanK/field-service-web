<?php


$config = require_once 'config.php';
$db = new dbConnection($config['database']);

try{
    // Get technicians with their schedules
    $technicians = $db->query("
        SELECT t.*, u.name,
            COUNT(CASE WHEN wo.status IN ('assigned', 'in_progress') THEN 1 END) as active_orders
        FROM technicians t
        LEFT JOIN work_orders wo ON t.tech_id = wo.tech_id
        LEFT JOIN users u ON t.user_id = u.user_id
        GROUP BY t.tech_id, u.name
        ORDER BY u.name
    ")->fetchAll();

    // Get today's work orders
    $today = date('Y-m-d');
    $todays_orders = $db->query(
        "SELECT 
            wo.*,
            c.company_name, 
            u.address, 
            u.name AS tech_name
        FROM 
            work_orders wo
        LEFT JOIN customers c 
            ON wo.customer_id = c.customer_id
        LEFT JOIN technicians t 
            ON wo.tech_id = t.tech_id
        LEFT JOIN users u 
            ON t.user_id = u.user_id
        WHERE
            DATE(wo.scheduled_date) = '$today'
        ORDER BY
            wo.scheduled_time
    ")->fetchAll();

    // Get weekly schedule
    $week_start = date('Y-m-d', strtotime('monday this week'));
    $week_end = date('Y-m-d', strtotime('sunday this week'));
    $weekly_schedule = $db->query(
        "SELECT wo.*, 
            c.company_name, 
            u.name AS tech_name,
            (DAYOFWEEK(wo.scheduled_date) + 5) % 7 + 1 AS day_of_week
        FROM 
            work_orders wo
        LEFT JOIN customers c 
            ON wo.customer_id = c.customer_id
        LEFT JOIN technicians t 
            ON wo.tech_id = t.tech_id
        LEFT JOIN users u 
            ON t.user_id = u.user_id
        WHERE wo.scheduled_date 
            BETWEEN 
                '$week_start' 
            AND 
                '$week_end'
        ORDER BY 
            wo.scheduled_date, 
            wo.scheduled_time
    ")->fetchAll();

} catch (Exception $e) {
    // Log the error
    error_log("Schedule error: " . $e->getMessage());
    // Set default values for stats

}


require_once 'views/schedule.view.php';