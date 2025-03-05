<?php
session_start();

define('PROJECT_DB2', $_SERVER['DOCUMENT_ROOT'] . '/field-service-web/employee/source-code');
define('BASE_URL_VALIDATOR', '/field-service-web/employee/source-code');
include_once PROJECT_DB2 . "/Database/DBConnection.php";

class Credentials
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function Login($email, $password)
    {
        try {
            error_log("Login function called");

            $stmt1 = $this->conn->prepare("SELECT user_id, name, email, password, role FROM users WHERE email = :email");
            $stmt1->execute(['email' => $email]);
            $user = $stmt1->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user["password"])) {
                header("Content-Type: application/json");
                echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
                exit;
            }

            error_log("Login successful");

            // Store session data
            $_SESSION['user_id'] = $user["user_id"];
            $_SESSION['name'] = $user["name"];
            $_SESSION['role'] = $user["role"];

            // Redirect URL
            $redirectUrl = BASE_URL_VALIDATOR . "/Webpage/newRequest.php";

            // Send success response
            header("Content-Type: application/json");
            echo json_encode(["status" => "success", "message" => "Login successful", "redirect" => $redirectUrl]);
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

    if (isset($data["action"]) && $data["action"] === "login") {
        try {
            error_log("Login request received");

            $conn = Database::getInstance();
            $credentialsHandler = new Credentials($conn);

            $customer_Email = trim($data["email"] ?? "");
            $customer_Password = trim($data["password"] ?? "");
            
            $credentialsHandler->Login($customer_Email, $customer_Password);
        } catch (Exception $e) {
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to process the request.", "error" => $e->getMessage()]);
            exit;
        }
    }
}
?>