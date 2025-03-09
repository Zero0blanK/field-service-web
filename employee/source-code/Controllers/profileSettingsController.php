<?php
define('PROJECT_DB5', $_SERVER['DOCUMENT_ROOT'] . '/field-service-web/employee/source-code');
define('URL_1', '/field-service-web/employee/source-code');
include_once PROJECT_DB5 . "/Database/DBConnection.php";

class ProfileSettings
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function Update($fullName, $email, $phone, $address, $city, $zip, $password, $company)
    {
        try {
            error_log("Register function called");

            $userID = $_SESSION['user_id'];

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $stmt2 = $this->conn->prepare("UPDATE users SET name = :name, email = :email, phone = :phone, address = :address, city = :city, zipcode = :zipcode, password = :password WHERE user_id = :user_id");
            $stmt2->execute(['name' => $fullName, 'email' => $email, 'phone' => $phone, 'address' => $address, 'city' => $city, 'zipcode' => $zip, 'password' => $passwordHash, 'user_id' => $userID]);

            $stmt3 = $this->conn->prepare("UPDATE customers SET company_name = :company WHERE user_id = :user_id");
            $stmt3->execute(['user_id' => $userID, 'company' => $company]);

            error_log("Update successful");

            // Send success response
            header("Content-Type: application/json");
            echo json_encode(["status" => "success", "message" => "Registration successful"]);
            exit;
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode(["status" => "error", "message" => "An error occurred while processing Update."]);
            exit;
        }
    }
    public function Retrieve($userID) {
        try {
            if (!isset($userID)) {
                throw new Exception("User ID not set");
            }

            $stmt1 = $this->conn->prepare("SELECT name, email, phone, address, city, zipcode FROM users WHERE user_id = :user_id");
            $stmt1->execute(['user_id' => $userID]);
            $result = $stmt1->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                header("Content-Type: application/json");
                echo json_encode(["status" => "success", "message" => "No data found.", "name" => "", "email" => "", "phone" => "", "address" => "", "city" => "", "zip" => ""]);
                exit;
            } else {
                $stmt2 = $this->conn->prepare("SELECT company_name FROM customers WHERE user_id = :user_id");
                $stmt2->execute(['user_id' => $userID]);
                $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);

                if(!$result2) {
                    header("Content-Type: application/json");
                    echo json_encode(["status" => "success", "message" => "No data found.", "name" => $result['name'], "email" => $result['email'], "phone" => $result['phone'], "address" => $result['address'], "city" => $result['city'], "zip" => $result['zipcode'], "company" => ""]);
                    exit;
                }
            }

            error_log("Retrieval successful");

            // Send success response
            header("Content-Type: application/json");
            echo json_encode(["status" => "success", "message" => "Retrieval successful, data found.", "name" => $result['name'], "email" => $result['email'], "phone" => $result['phone'], "address" => $result['address'], "city" => $result['city'], "zip" => $result['zipcode'], "company" => $result2['company_name']]);
            exit;
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode(["status" => "error", "message" => "An error occurred while processing Retrieval."]);
            exit;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);
    
    if (isset($data["action"]) && $data["action"] === "update") {
        try {
            error_log("Register request received");

            $conn = Database::getInstance();
            $credentialsHandler = new ProfileSettings($conn);

            $fullName = trim($data["fullname"] ?? "");
            $email = trim($data["email"] ?? "");
            $phone = trim($data["phone"] ?? "");
            $address = trim($data["address"] ?? "");
            $city = trim($data["city"] ?? "");
            $zip = trim($data["zip"] ?? "");
            $password = trim($data["password"] ?? "");
            $company = trim($data["company"] ?? "");
            
            $credentialsHandler->Update($fullName, $email, $phone, $address, $city, $zip, $password, $company);

        } catch (Exception $e) {
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to process the request.", "error" => $e->getMessage()]);
            exit;
        }
    } elseif (isset($data["action"]) && $data["action"] === "retrieve") {
        try {
            error_log("Retrieve request received");

            $conn = Database::getInstance();
            $credentialsHandler = new ProfileSettings($conn);

            $userID = $_SESSION["user_id"];

            $credentialsHandler->Retrieve($userID);
        } catch (Exception $e) {
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to process the request.", "error" => $e->getMessage()]);
            exit;
        }
    }
}
?>