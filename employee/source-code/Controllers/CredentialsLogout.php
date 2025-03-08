<?php
define('LOGOUT', '/field-service-web/employee/source-code');

class LogoutSystem {
    public function Logout($confirmation) {
        try {
            if($confirmation) {
                session_unset();
                session_destroy();
            }
            // Redirect URL
            $redirectUrl = LOGOUT . "/Webpage/login.php";

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if (isset($data["action"]) && $data["action"] === "logout") {
        try {
            error_log("logout Request received");

            $credentialsHandler = new LogoutSystem();

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