<?php
define('PROJECT_DB2', $_SERVER['DOCUMENT_ROOT'] . '/field-service-web/employee/source-code');
define('BASE_URL_LOGOUT', '/field-service-web/employee/source-code');
include_once PROJECT_DB2 . "/Database/DBConnection.php";

class WorkOrder
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createRequest($CustomerID, $Title, $Description, $Priority, $Status, $Scheduled_Date, $Schedule_Time, $Location, $CreateAt)
    {
        try {
            $stmt1 = $this->conn->prepare("INSERT INTO work_orders (customer_id, title, description, priority, status, scheduled_date, scheduled_time, location, created_at) 
            VALUES (:customerID, :title, :description, :priority, :status, :date, :time, :location, :created)");

            $stmt1->execute([
                'customerID' => $CustomerID,
                'title' => $Title,
                'description' => $Description,
                'priority' => $Priority,
                'status' => $Status,
                'date' => $Scheduled_Date,
                'time' => $Schedule_Time,
                'location' => $Location,
                'created' => $CreateAt
            ]);

            $requestID = $this->conn->lastInsertId();

            if ($requestID) {
                return json_encode(["status" => "success", "message" => "Request created successfully"]);
            } else {
                return json_encode(["status" => "error", "message" => "An error occurred while creating request."]);
            }
        } catch (Exception $e) {
            error_log("Error updating customer: " . $e->getMessage());
            return json_encode(["status" => "error", "message" => "An error occurred while updating customer data."]);
        }
    }


    public function cancelRequest($requestID)
    {
        try {
            $stmt1 = $this->conn->prepare("SELECT order_id FROM work_orders WHERE order_id = :id LIMIT 1");
            $stmt1->execute(['id' => $requestID]);
            $order_id = $stmt1->fetch(PDO::FETCH_ASSOC);

            $customer_id = $_SESSION['customer_id'];

            if ($order_id && $customer_id) {
                $stmt2 = $this->conn->prepare("UPDATE work_orders SET status = :newStatus WHERE order_id = :id, customer_id = :cid");
                $stmt2->execute(['id' => $requestID, 'cid' => $customer_id]);
            } else {
                error_log("Customer Didn't Exist");
            }

        } catch (Exception $e) {
            error_log("Error deleting employee: " . $e->getMessage());
            throw new Exception("An error occurred while deleting customer data.");
        }
    }
    public function Logout($confirmation)
    {
        try {
            if($confirmation) {
                session_unset();
                session_destroy();
            }
            // Redirect URL
            $redirectUrl = BASE_URL_LOGOUT . "/Webpage/login.php";

            // Send success response
            header("Content-Type: application/json");
            echo json_encode(["status" => "success", "message" => "Registration successful", "redirect" => $redirectUrl]);
            exit;
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode(["status" => "error", "message" => "An error occurred while processing login."]);
            exit;
        }
    }
}

// Process request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? "";
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if ($action === "create") {
        try {
            $conn = Database::getInstance();
            $conn->beginTransaction();

            $orderHandler = new WorkOrder($conn);

            // Recieve Data!
            $requestTitle = trim($_POST["title"] ?? "");
            $requestDescription = trim($_POST["description"] ?? "");
            $requestPriority = trim($_POST["priority"] ?? "");
            $requestScheduledDate = trim($_POST["scheduled_date"] ?? "");
            $requestScheduledTime = trim($_POST["scheduled_time"] ?? "");
            $requestLocation = trim($_POST["location"] ?? "");

            //Asumming that we Have CustomerID!
            $customerID = $_SESSION["user_id"];

            // Default Value
            $status = "pending";

            $creationDate = date("Y-m-d");
            if (!empty($requestTitle)) {
                $orderHandler->createRequest($customerID, $requestTitle, $requestDescription, $requestPriority, $status, $requestScheduledDate, $requestScheduledTime, $requestLocation, $creationDate);
                echo json_encode(["status" => "success", "message" => "Customer updated successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid input! Customer ID is required."]);
                exit; // Stop further execution
            }

            $conn->commit();
            header('Content-Type: application/json');
            echo json_encode(["status" => "success", "message" => "Update successful", "reload" => true]);
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(value: ["status" => "error", "message" => $e->getMessage()]);
        }
    } elseif ($action === "cancel") {
        try {
            $conn = Database::getInstance();
            if (!$conn) {
                error_log("Database connection failed.");
                header('Content-Type: application/json');
                echo json_encode(["status" => "error", "message" => "Database connection failed."]);
                exit;
            }

            $conn->beginTransaction();
            $orderHandler = new WorkOrder($conn);

            $requestID = trim($_POST["requestID"] ?? "");

            if (!empty($requestID)) {
                $orderHandler->cancelRequest($customerID);
            }

            $conn->commit();
            header('Content-Type: application/json');
            echo json_encode(["status" => "success", "message" => "Update successful", "reload" => true]);
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to Delete. Please try again."]);
        }
    } elseif (isset($data["action"]) && $data["action"] === "logout") {
        try {
            error_log("logout Request received");

            $credentialsHandler = new WorkOrder($conn);

            $confirmation = trim($data["confirmation"] ?? "");

            $credentialsHandler->Logout($confirmation);

        } catch (Exception $e) {
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to process the request.", "error" => $e->getMessage()]);
            exit;
        }
    }
}
?>