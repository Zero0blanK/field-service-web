<?php
session_start();
define('PROJECT_DB1', $_SERVER['DOCUMENT_ROOT'] . '/field-service-web/employee/source-code');
define('URL_1', '/field-service-web/employee/source-code');
include_once PROJECT_DB1 . "/Database/DBConnection.php";

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
            
            $stmt4 = $this->conn->prepare("SELECT customer_id FROM customers WHERE user_id = :user_id");
            $stmt4->execute(['user_id' => $user["user_id"]]);
            $customerID = $stmt4->fetch(PDO::FETCH_ASSOC);

            error_log("Login successful");

            // Store session data
            $_SESSION['user_id'] = $user["user_id"];
            $_SESSION['name'] = $user["name"];
            $_SESSION['role'] = $user["role"];
            $_SESSION['customer_id'] = $customerID["customer_id"];

            error_log("Login successful");

            // Redirect URL
            $redirectUrl = URL_1 . "/Webpage/newRequest.php";

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

    public function Register($fullName, $email, $phone, $address, $city, $zipconde, $password, $role, $createdAt, $company, $userType)
    {
        try {
            error_log("Register function called");

            $stmt1 = $this->conn->prepare("SELECT user_id FROM users WHERE email = :email");
            $stmt1->execute(['email' => $email]);
            $user = $stmt1->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                header("Content-Type: application/json");
                echo json_encode(["status" => "error", "message" => "Email already exists"]);
                exit;
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $stmt2 = $this->conn->prepare("INSERT INTO users (name, email, phone, address, city, zipcode, password, role, created_at) VALUES (:name, :email, :phone, :address, :city, :zipcode, :password, :role, :created_at)");
            $stmt2->execute(['name' => $fullName, 'email' => $email, 'phone' => $phone, 'address' => $address, 'city' => $city, 'zipcode' => $zipconde, 'password' => $passwordHash, 'role' => $role, 'created_at' => $createdAt]);
            $user_id = $this->conn->lastInsertId();

            if ($user_id) {
                $stmt3 = $this->conn->prepare("INSERT INTO customers (user_id, company_name) VALUES (:user_id, :company)");
                $stmt3->execute(['user_id' => $user_id, 'company' => $company]);
            }

            $stmt3 = $this->conn->prepare("SELECT user_id, name, role FROM users WHERE email = :email");
            $stmt3->execute(['email' => $email]);
            $users = $stmt3->fetch(PDO::FETCH_ASSOC);

            $stmt4 = $this->conn->prepare("SELECT customer_id FROM customers WHERE user_id = :user_id");
            $stmt4->execute(['user_id' => $users["user_id"]]);
            $customerID = $stmt4->fetch(PDO::FETCH_ASSOC);

            // Store session data
            $_SESSION['user_id'] = $users["user_id"];
            $_SESSION['name'] = $users["name"];
            $_SESSION['role'] = $users["role"];
            $_SESSION['customer_id'] = $customerID["customer_id"];

            error_log("Login successful");

            // Redirect URL
            $redirectUrl = URL_1 . "/Webpage/newRequest.php";

            // Send success response
            header("Content-Type: application/json");
            echo json_encode(["status" => "success", "message" => "Registration successful", "redirect" => $redirectUrl]);
            exit;
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode(["status" => "error", "message" => "An error occurred while processing registration."]);
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
    } elseif (isset($data["action"]) && $data["action"] === "register") {
        try {
            error_log("Register request received");

            $conn = Database::getInstance();
            $credentialsHandler = new Credentials($conn);

            $userType = trim($data["userType"] ?? "");
            $fullName = trim($data["fullname"] ?? "");
            $email = trim($data["email"] ?? "");
            $phone = trim($data["phone"] ?? "");
            $address = trim($data["address"] ?? "");
            $company = trim($data["company"] ?? "");
            $city = trim($data["city"] ?? "");
            $zip = trim($data["zip"] ?? "");
            $password = trim($data["password"] ?? "");
            
            // Default values
            $role = "customer";
            $createdAt = date("Y-m-d H:i:s");

            $credentialsHandler->Register($fullName, $email, $phone, $address, $city, $zip, $password, $role, $createdAt, $company, $userType);

        } catch (Exception $e) {
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to process the request.", "error" => $e->getMessage()]);
            exit;
        }
    }
}
?>