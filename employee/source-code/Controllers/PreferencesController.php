<?php
define('PROJECT_DB6', $_SERVER['DOCUMENT_ROOT'] . '/field-service-web/employee/source-code');
define('URL_1', '/field-service-web/employee/source-code');
include_once PROJECT_DB6 . "/Database/DBConnection.php";

class Preferences
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function Retrieve($customerID)
    {
        try {
            if (empty($customerID)) {
                throw new Exception("Customer ID is required.");
            }

            $stmt1 = $this->conn->prepare("SELECT preferred_technician, preferred_time_of_day, preferred_contact_method FROM customer_preferences WHERE customer_id = :customer_id");
            $stmt1->execute(['customer_id' => $customerID]);
            $result = $stmt1->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                // Return success but indicate no data found
                header("Content-Type: application/json");
                echo json_encode(["status" => "success", "message" => "No preferences found.", "technician" => "", "time" => "", "method" => ""]);
                exit;
            }

            // Return found data
            header("Content-Type: application/json");
            echo json_encode([
                "status" => "success",
                "message" => "Preferences retrieved successfully.",
                "technician" => $result['preferred_technician'],
                "time" => $result['preferred_time_of_day'],
                "method" => $result['preferred_contact_method']
            ]);
            exit;
        } catch (Exception $e) {
            error_log("Error retrieving preferences: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode(["status" => "error", "message" => "An error occurred while retrieving preferences."]);
            exit;
        }
    }

    public function Update($customerID, $technician, $TimeDay, $method)
    {
        try {
            if (empty($customerID)) {
                throw new Exception("Customer ID is required.");
            }

            $stmt1 = $this->conn->prepare("SELECT preferred_technician, preferred_time_of_day, preferred_contact_method FROM customer_preferences WHERE customer_id = :customer_id");
            $stmt1->execute(['customer_id' => $customerID]);
            $result = $stmt1->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                $stmt2 = $this->conn->prepare("INSERT INTO customer_preferences (customer_id, preferred_technician, preferred_time_of_day, preferred_contact_method) VALUES (:customer_id, :preferred_technician, :preferred_time_of_day, :preferred_contact_method)");
                $stmt2->execute(['customer_id' => $customerID, 'preferred_technician' => $technician, 'preferred_time_of_day' => $TimeDay, 'preferred_contact_method' => $method]);
            } else {
                $stmt2 = $this->conn->prepare("UPDATE customer_preferences SET preferred_technician = :preferred_technician, preferred_time_of_day = :preferred_time_of_day, preferred_contact_method = :preferred_contact_method WHERE customer_id = :customer_id");
                $stmt2->execute(['customer_id' => $customerID, 'preferred_technician' => $technician, 'preferred_time_of_day' => $TimeDay, 'preferred_contact_method' => $method]);
            }

            header("Content-Type: application/json");
            echo json_encode(["status" => "success", "message" => "Preferences updated successfully."]);
            exit;
        } catch (Exception $e) {
            error_log("Error updating preferences: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode(["status" => "error", "message" => "An error occurred while updating preferences."]);
            exit;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if (isset($data["action"]) && $data["action"] === "retrieve") {
        try {
            error_log("Register request received");

            $conn = Database::getInstance();
            $prefrencesHandler = new Preferences($conn);

            $customerID = $_SESSION["customer_id"];

            Error_log("Customer ID: " . $customerID);

            $prefrencesHandler->Retrieve($customerID);
        } catch (Exception $e) {
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to process the request.", "error" => $e->getMessage()]);
            exit;
        }
    } elseif (isset($data["action"]) && $data["action"] === "update") {
        try {
            error_log("Update request received");

            $conn = Database::getInstance();
            $prefrencesHandler = new Preferences($conn);

            $customerID = $_SESSION["customer_id"];
            $technician = $data["technician"];
            $time = $data["time"];
            $method = $data["method"];

            Error_log("Customer ID: " . $customerID);

            $prefrencesHandler->Update($customerID, $technician, $time, $method);
        } catch (Exception $e) {
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to process the request.", "error" => $e->getMessage()]);
            exit;
        }
    }
}
