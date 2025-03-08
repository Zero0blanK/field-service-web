<?php
define('PROJECT_DB2', $_SERVER['DOCUMENT_ROOT'] . '/field-service-web/employee/source-code');
include_once PROJECT_DB2 . "/Database/DBConnection.php";

class WorkOrder
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function CreateRequest($userID, $title, $description, $priority, $status, $scheduledDate, $scheduledTime, $location, $created_at) {
        try {
            //Get the customerID
            $stmt1 = $this->conn->prepare("SELECT customer_id FROM customers WHERE user_id = :userID");
            $stmt1->execute(['userID' => $userID]);
            $customerID = $stmt1->fetch(PDO::FETCH_ASSOC);

            if(!$customerID) {
                header("Content-Type: application/json");
                echo json_encode(["status" => "error", "message" => "Customer not found"]);
                exit;
            } else {
                $stmt2 = $this->conn->prepare("INSERT INTO work_orders (customer_id, title, description, priority, status, scheduled_date, scheduled_time, location, created_at) VALUES (:customerID, :title, :description, :priority, :status, :scheduledDate, :scheduledTime, :location, :created_at)");
                $stmt2->execute(['customerID' => $customerID["customer_id"], 'title' => $title, 'description' => $description, 'priority' => $priority, 'status' => $status, 'scheduledDate' => $scheduledDate, 'scheduledTime' => $scheduledTime, 'location' => $location, 'created_at' => $created_at]);
            }

            header("Content-Type: application/json");
            echo json_encode(["status" => "success", "message" => "Registration successful"]);
            exit;
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode(["status" => "error", "message" => "An error occurred while processing login."]);
            exit;
        }
    }
    public function CancelRequest($requestID) {
        try {
            $stmt = $this->conn->prepare("UPDATE work_orders SET status = 'cancelled' WHERE order_id = :order_ID AND customer_id = :customer_ID");
            $stmt->execute(['order_ID' => $requestID, 'customer_ID' => $_SESSION['customer_id']]);

            header("Content-Type: application/json");
            echo json_encode(["status" => "success", "message" => "Request cancelled successfully"]);
            exit;
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode(["status" => "error", "message" => "An error occurred while processing login."]);
            exit;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if (isset($data["action"]) && $data["action"] === "create") {
        try {
            error_log("create request received");

            $conn = Database::getInstance();
            $WorkHandler = new WorkOrder($conn);

            $userID = $_SESSION['user_id'];
            $title = trim($data["title"] ?? "");
            $description = trim($data["description"] ?? "");
            $priority = trim($data["priority"] ?? "");
            $scheduledDate = trim($data["scheduled_date"] ?? "");
            $scheduledTime = trim($data["scheduled_time"] ?? "");
            $location = trim($data["location"] ?? "");

            //Default Value
            $created_at = date("Y-m-d H:i:s");
            $status = "Pending";

            $WorkHandler->CreateRequest($userID, $title, $description, $priority, $status, $scheduledDate, $scheduledTime, $location, $created_at);
        } catch (Exception $e) {
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to process the request.", "error" => $e->getMessage()]);
            exit;
        }
    } elseif (isset($data["action"]) && $data["action"] === "cancel") {
        try {
            error_log("cancel request received");

            $conn = Database::getInstance();
            $WorkHandler = new WorkOrder($conn);

            $requestID = trim($data["requestID"] ?? "");

            $WorkHandler->CancelRequest($requestID);
        } catch (Exception $e) {
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to process the request.", "error" => $e->getMessage()]);
            exit;
        }
    }
}
?>