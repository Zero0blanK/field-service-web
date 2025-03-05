<?php

$config = require_once 'config.php';
$db = new dbConnection($config['database']);

define("ROOT", dirname(__DIR__));
define("VIEWS", ROOT . "/../views/");

// $tech_id = $_SESSION['tech_id'];
$tech_id = 2;


// Fetch assigned work orders
$assigned_orders = $db->query("
    SELECT wo.*, c.company_name, u.name AS customer_name 
    FROM work_orders wo
    JOIN customers c ON wo.customer_id = c.customer_id
    JOIN users u ON c.user_id = u.user_id
    WHERE wo.tech_id = :tech_id AND wo.status IN ('assigned', 'pending', 'in_progress')
    ORDER BY wo.scheduled_date, wo.scheduled_time
", [$tech_id]
)->fetchAll();


require_once 'views/technicians/tech-dashboard.view.php';