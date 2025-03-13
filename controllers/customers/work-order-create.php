<?php

$config = require_once 'config.php';
$db = new dbConnection($config['database']);

define("ROOT", dirname(__DIR__));
define("VIEWS", ROOT . "/../views/");

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if (isset($data["action"]) && $data["action"] === "create") {
        try {

            $userID = $_SESSION['user_id'] ?? null;
            if (!$userID) {
                throw new Exception("User not authenticated.");
            }

            $title = trim($data["title"] ?? "");
            $description = trim($data["description"] ?? "");
            $priority = trim($data["priority"] ?? "");
            $scheduledDate = trim($data["scheduled_date"] ?? "");
            $scheduledTime = trim($data["scheduled_time"] ?? "");
            $location = trim($data["location"] ?? "");

            // Default values
            $created_at = date("Y-m-d H:i:s");
            $status = "Pending";

            // Get the customerID
            $customer = $db->query("SELECT customer_id FROM customers WHERE user_id = :userID", ['userID' => $userID])->fetch();

            if (!$customer) {
                echo json_encode(["status" => "error", "message" => "Customer not found"]);
                exit;
            }

            // Insert into work_orders
            $db->query(
                "INSERT INTO work_orders (customer_id, title, description, priority, status, scheduled_date, scheduled_time, location, created_at) 
                 VALUES (:customerID, :title, :description, :priority, :status, :scheduledDate, :scheduledTime, :location, :created_at)",
                [
                    'customerID' => $customer["customer_id"],
                    'title' => $title,
                    'description' => $description,
                    'priority' => $priority,
                    'status' => $status,
                    'scheduledDate' => $scheduledDate,
                    'scheduledTime' => $scheduledTime,
                    'location' => $location,
                    'created_at' => $created_at
                ]
            );

            $_SESSION['success'] = "Work order created successfully.";
            echo json_encode(["status" => "success", "message" => "Work order created successfully"]);
            exit;

        } catch (Exception $e) {
            error_log("Transaction failed: " . $e->getMessage());
            $_SESSION['error'] = "Failed to process the request.";
            echo json_encode(["status" => "error", "message" => "Failed to process the request.", "error" => $e->getMessage()]);
            exit;
        }
    } elseif (isset($data["action"]) && $data["action"] === "cancel") {
        try {

            $requestID = trim($data["requestID"] ?? "");
            $customerID = $_SESSION['customer_id'] ?? null;

            if (!$customerID) {
                throw new Exception("Customer not authenticated.");
            }

            $db->query("UPDATE work_orders SET status = 'cancelled' WHERE order_id = :order_ID AND customer_id = :customer_ID", [
                'order_ID' => $requestID,
                'customer_ID' => $customerID
            ]);
            $_SESSION['success'] = "Request cancelled successfully.";
            echo json_encode(["status" => "success", "message" => "Request cancelled successfully"]);
            exit;

        } catch (Exception $e) {
            error_log("Transaction failed: " . $e->getMessage());
            $_SESSION['error'] = "Failed to process the request.";
            echo json_encode(["status" => "error", "message" => "Failed to process the request.", "error" => $e->getMessage()]);
            exit;
        }
    }
}

// Fetch customer's work orders
$userId = $_SESSION['user_id'];
$customer = $db->query("SELECT customer_id FROM customers WHERE user_id = :user_id", [$userId])->fetch();

if ($customer) {
    $customerId = $customer['customer_id'];

    $customer_orders = $db->query("
        SELECT wo.*, u.name AS technician_name 
        FROM work_orders wo
        LEFT JOIN technicians t ON wo.tech_id = t.tech_id
        LEFT JOIN users u ON t.user_id = u.user_id
        WHERE wo.customer_id = :customer_id
        ORDER BY wo.created_at DESC
    ", ['customer_id' => $customerId])->fetchAll();
}

require_once 'views/customers/work-order-create.view.php';