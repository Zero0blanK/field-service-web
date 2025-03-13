<?php

$config = require_once 'config.php';
$db = new dbConnection($config['database']);

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Handle GET requests (fetching data)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get order details
    if (isset($_GET['action']) && $_GET['action'] === 'get_details' && isset($_GET['id'])) {
        $order_id = (int)$_GET['id'];
        
        try {
            // Get the technician's ID
            $tech_id = $db->query(
                "SELECT tech_id FROM technicians WHERE user_id = :user_id", 
                [':user_id' => $_SESSION['user_id']]
            )->fetch()['tech_id'];
            
            // Get the work order details with customer information
            $order = $db->query(
                "SELECT 
                    wo.*,
                    c.company_name,
                    u.name AS customer_name,
                    u.email,
                    u.phone,
                    u.address,
                    u.city,
                    u.zipcode
                FROM work_orders wo
                JOIN customers c ON wo.customer_id = c.customer_id
                JOIN users u ON c.user_id = u.user_id
                WHERE wo.order_id = :order_id AND wo.tech_id = :tech_id",
                [':order_id' => $order_id, ':tech_id' => $tech_id]
            )->fetch();
            
            if (!$order) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Work order not found or not assigned to you']);
                exit;
            }
            
            header('Content-Type: application/json');
            echo json_encode($order);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }
}



// Handle POST requests (updating data)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update order status
    if (isset($_POST['action']) && $_POST['action'] === 'update_status' && isset($_POST['id']) && isset($_POST['status'])) {
        $order_id = (int)$_POST['id'];
        $status = $_POST['status'];
        
        // Validate status
        $valid_statuses = ['assigned', 'in_progress', 'completed', 'cancelled'];
        if (!in_array($status, $valid_statuses)) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid status']);
            exit;
        }
        
        try {
            // Get the technician's ID
            $tech_id = $db->query(
                "SELECT tech_id FROM technicians WHERE user_id = :user_id", 
                [':user_id' => $_SESSION['user_id']]
            )->fetch()['tech_id'];
            
            // Check if the work order exists and is assigned to this technician
            $order = $db->query(
                "SELECT * FROM work_orders WHERE order_id = :order_id AND tech_id = :tech_id",
                [':order_id' => $order_id, ':tech_id' => $tech_id]
            )->fetch();
            
            if (!$order) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Work order not found or not assigned to you']);
                exit;
            }
            
            // Update the status (and completion date if completed)
            if ($status === 'completed') {
                $db->query(
                    "UPDATE work_orders SET status = :status, completion_date = NOW() WHERE order_id = :order_id",
                    [':status' => $status, ':order_id' => $order_id]
                );
            } else {
                $db->query(
                    "UPDATE work_orders SET status = :status WHERE order_id = :order_id",
                    [':status' => $status, ':order_id' => $order_id]
                );
            }
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }
}

header('Content-Type: application/json');
echo json_encode(['error' => 'Invalid request']);
exit;