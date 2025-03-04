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

    public function UpdateCustomer($CustomerID, $newName, $newContactNumber, $newAddress)
    {
        try {
            // Check if customer exists
            $stmt1 = $this->conn->prepare("SELECT id FROM customer WHERE id = :id LIMIT 1");
            $stmt1->execute(['id' => $CustomerID]);
            $customer = $stmt1->fetch(PDO::FETCH_ASSOC);

            if (!$customer) {
                error_log("Customer ID $CustomerID doesn't exist in the database.");
                return json_encode(["status" => "error", "message" => "Customer ID not found."]);
            }

            // Perform the update query
            $stmt2 = $this->conn->prepare("UPDATE customer SET name = :newName, contact_number = :newContactNumber, address = :newAddress WHERE id = :id");

            $stmt2->execute(['newName' => $newName,'newContactNumber' => $newContactNumber,'newAddress' => $newAddress,'id' => $CustomerID]);

            // Check if any rows were updated
            if ($stmt2->rowCount() > 0) {
                return json_encode(["status" => "success", "message" => "Customer updated successfully."]);
            } else {
                return json_encode(["status" => "error", "message" => "No changes were made."]);
            }

        } catch (Exception $e) {
            error_log("Error updating customer: " . $e->getMessage());
            return json_encode(["status" => "error", "message" => "An error occurred while updating customer data."]);
        }
    }


    public function DeleteCustomer($customerID)
    {
        try {
            $stmt1 = $this->conn->prepare("SELECT id FROM customer WHERE id = :id LIMIT 1");
            $stmt1->execute(['id' => $customerID]);
            $customer = $stmt1->fetch(PDO::FETCH_ASSOC);

            if ($customer) {
                $stmt2 = $this->conn->prepare("DELETE FROM customer WHERE id = :id");
                $stmt2->execute(['id' => $customerID]);
            } else {
                error_log("Customer Didn't Exist");
            }

        } catch (Exception $e) {
            error_log("Error deleting employee: " . $e->getMessage());
            throw new Exception("An error occurred while deleting customer data.");
        }
    }
}


// Process request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "create") {
        try {
            $conn = Database::getInstance();
            $conn->beginTransaction();

            $customerHandler = new WorkOrder($conn);

            // Recieve Data!
            $requestTitle = trim($_POST["title"] ?? "");
            $requestDescription = trim($_POST["description"] ?? "");
            $requestPriority = trim($_POST["priority"] ?? "");
            $requestScheduledDate = trim($_POST["scheduled_date"] ?? "");
            $requestScheduledTime = trim($_POST["scheduled_time"] ?? "");
            $requestLocation = trim($_POST["location"] ?? "");

            $customerID = $_SESSION["customer_ID"];
            $creationDate = date("Y-m-d");


            if (!empty($requestTitle)) {
                $customerHandler->UpdateCustomer($customerID, $customerName, $customerContactNumber, $customerAddress);
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
            $customerHandler = new WorkOrder($conn);

            $requestID = trim($_POST["requestID"] ?? "");

            if (!empty($requestID)) {
                $customerHandler->DeleteCustomer($customerID);
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
    }
}
?>