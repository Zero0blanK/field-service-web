<?php
define('PROJECT_DB2', $_SERVER['DOCUMENT_ROOT'] . '/field-service-web/employee/source-code');
include PROJECT_DB2 . "/Database/DBConnection.php";

class AppointmentManager
{
    private $conn;
    private $customer_id;
    private $default_order = "WHERE work_orders.customer_id = :customer_id ORDER BY work_orders.status ASC"; // Define as a class property

    public function __construct($db)
    {
        $this->conn = $db;
        $this->customer_id = $_SESSION['customer_id'] ?? null;
    }

    public function fetchAppointments($order = null)
    {
        try {
            $order = $order ?? $this->default_order;
            $stmt = $this->conn->prepare("SELECT
                work_orders.title AS Title, 
                work_orders.description AS Description, 
                work_orders.priority AS Priority, 
                work_orders.status AS Status,
                work_orders.scheduled_date AS Date,
                work_orders.scheduled_time AS Time,
                work_orders.location AS Address
            FROM work_orders
            LEFT JOIN technicians ON work_orders.tech_id = technicians.tech_id
            LEFT JOIN users AS users_technician ON technicians.user_id = users_technician.user_id
            $order");
            $stmt->execute(['customer_id' => $this->customer_id]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($results) > 0) {
                echo "<table border='1' class='work_orders-table'>";
                echo "<tr>";
                $headers = ['Title', 'Description', 'Priority', 'Status', 'Date', 'Time', 'Address'];
                foreach ($headers as $columnName) {
                    echo "<th>" . htmlspecialchars(str_replace("_", " ", $columnName)) . "</th>";
                }
                echo "</tr>";

                foreach ($results as $row) {
                    $rowData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                    foreach ($headers as $column) {
                        echo "<td>" . htmlspecialchars($row[$column]) . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No records found.";
            }
        } catch (PDOException $e) {
            echo "Error fetching data: " . $e->getMessage();
        }
    }

    public function handlePostRequest()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sql_query'])) {
            $query = trim($_POST['sql_query']);

            // Ensure the query always has a valid ORDER BY
            if (!str_contains($query, "ORDER BY")) {
                $query .= " ORDER BY work_orders.status ASC";
            }

            $this->fetchAppointments($query);
            exit;
        }
    }

    public function fetchRequestID()
    {
        session_start(); // Ensure session is active
        $fetchID = $_SESSION['customer_id']; // Retrieve customer ID again

        try {
            $stmt = $this->conn->prepare("
            SELECT order_id, title
            FROM work_orders
            WHERE customer_id = :customer_id AND status = 'pending'
            ORDER BY order_id ASC
        ");
            $stmt->execute(['customer_id' => $fetchID]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode($results);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error fetching appointment IDs: " . $e->getMessage()]);
        }
    }
}

// Check if request is made to fetch appointment IDs
if (isset($_GET['fetchRequestID'])) {
    $conn = Database::getInstance();
    $appointmentManager = new AppointmentManager($conn);
    $appointmentManager->fetchRequestID(); // Calls the function to output JSON
    exit; // Stop further execution
}
// Initialize the database connection
$conn = Database::getInstance();
$appointmentManager = new AppointmentManager($conn);

// Handle POST request if any
$appointmentManager->handlePostRequest();

// Fetch appointments for the initial page load
ob_start();
$appointmentManager->fetchAppointments();
$table_content = ob_get_clean();
