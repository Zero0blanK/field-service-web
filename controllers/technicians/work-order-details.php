<?php



$config = require_once 'config.php';
$db = new dbConnection($config['database']);

define("ROOT", dirname(__DIR__));
define("VIEWS", ROOT . "/../views/");

// Validate order ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Order not found!');
}
$order_id = $_GET['id'];

// Fetch order details with all relevant information
$order = $db->query("
    SELECT 
        wo.*,
        c.company_name,
        u.name AS customer_name,
        u.email AS customer_email,
        u.phone AS customer_phone,
        t.name AS technician_name,
        t.email AS technician_email
    FROM work_orders wo
    JOIN customers c ON wo.customer_id = c.customer_id
    JOIN users u ON c.user_id = u.user_id
    LEFT JOIN technicians tech ON wo.tech_id = tech.tech_id
    LEFT JOIN users t ON tech.user_id = t.user_id
    WHERE wo.order_id = :order_id",
    [$order_id]
)->fetch();

require_once 'views/technicians/work-order-details.view.php';