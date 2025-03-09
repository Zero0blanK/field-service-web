<?php

$config = require_once 'config.php';
$db = new dbConnection($config['database']);

define("ROOT", dirname(__DIR__));
define("VIEWS", ROOT . "/../views/");

$userId = $_SESSION['user_id'];
$customer_id = $db->query("SELECT customer_id FROM customers WHERE user_id = :user_id", [$userId])->fetch()['customer_id'];

// Define default order
$default_order = "work_orders.status ASC";

// Handle AJAX requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    if ($action === "fetch_orders") {
        $orderBy = $_POST['orderBy'] ?? 'work_orders.order_id';
        $filterBy = $_POST['filterBy'] ?? 'all';
        $sortBy = $_POST['sortBy'] ?? 'ASC';

        $query = "SELECT
            work_orders.order_id AS Ticket, 
            work_orders.title AS Title, 
            work_orders.status AS Status,
            work_orders.created_at AS Created,
            users_technician.name AS Technician,
            work_orders.completion_date AS Completion
        FROM work_orders
        LEFT JOIN technicians ON work_orders.tech_id = technicians.tech_id
        LEFT JOIN users AS users_technician ON technicians.user_id = users_technician.user_id
        WHERE work_orders.customer_id = :customer_id";

        if ($filterBy !== "all") {
            $query .= " AND work_orders.status = :filterBy";
        }
        
        $query .= " ORDER BY $orderBy $sortBy";
        
        $params = [':customer_id' => $customer_id];
        if ($filterBy !== "all") {
            $params[':filterBy'] = $filterBy;
        }
        
        $appointments = $db->query($query, $params)->fetchAll(PDO::FETCH_ASSOC);
        
        ob_start();
        include 'views/customers/track-status-table.php';
        echo ob_get_clean();
        exit;
    }

    if ($action === "cancel_order") {
        $requestID = $_POST['requestID'] ?? '';
        
        if (!$requestID) {
            echo json_encode(["status" => "error", "message" => "Invalid Request ID"]);
            exit;
        }

        $stmt = $db->query("UPDATE work_orders SET status = 'cancelled' WHERE order_id = :requestID AND customer_id = :customer_id", [':requestID' => $requestID, ':customer_id' => $customer_id]);
        
        if ($stmt) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to cancel order."]);
        }
        exit;
    }
}

// Fetch appointments for the initial page load
$appointments = $db->query("SELECT
    work_orders.order_id AS Ticket, 
    work_orders.title AS Title, 
    work_orders.status AS Status,
    work_orders.created_at AS Created,
    users_technician.name AS Technician,
    work_orders.completion_date AS Completion
FROM work_orders
LEFT JOIN technicians ON work_orders.tech_id = technicians.tech_id
LEFT JOIN users AS users_technician ON technicians.user_id = users_technician.user_id
WHERE work_orders.customer_id = :customer_id
ORDER BY $default_order", [':customer_id' => $customer_id])->fetchAll(PDO::FETCH_ASSOC);

require_once 'views/customers/track-status.view.php';
