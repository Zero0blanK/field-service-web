<?php
define('PROJECT_ROOT_DB', $_SERVER['DOCUMENT_ROOT'] . '/field-service-web/employee/source-code');
include PROJECT_ROOT_DB . "/Database/DBConnection.php";

class AppointmentManager
{
    private $conn;
    private $default_order = "ORDER BY work_orders.order_id ASC"; // Define as a class property

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function fetchAppointments($order = null)
    {
        try {
            $order = $order ?? $this->default_order; // Use default order if not provided

            $stmt = $this->conn->prepare("SELECT 
                work_orders.order_id AS Ticket, 
                users.name AS Customer, 
                users_technician.name AS Technician, 
                work_orders.title AS Title, 
                work_orders.description AS Description, 
                work_orders.priority AS Priority, 
                work_orders.status AS Status,
                work_orders.scheduled_date AS Schedule_Date,
                work_orders.scheduled_time AS Schedule_Time,
                work_orders.location AS Address,
                work_orders.created_at AS Created_At,
                work_orders.completion_date AS Completion_Date
            FROM work_orders
            LEFT JOIN customers ON work_orders.customer_id = customers.customer_id
            LEFT JOIN users ON customers.user_id = users.user_id
            LEFT JOIN technicians ON work_orders.tech_id = technicians.tech_id
            LEFT JOIN users AS users_technician ON technicians.user_id = users_technician.user_id
            $order ");

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($results) > 0) {
                echo "<table border='1' class='work_orders-table'>";
                echo "<tr>";
                $headers = ['Ticket', 'Customer', 'Technician', 'Title', 'Description', 'Priority', 'Status', 'Schedule_Date', 'Schedule_Time', 'Address', 'Completion_Date'];
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
                $query .= " ORDER BY work_orders.order_id ASC";
            }

            $this->fetchAppointments($query);
            exit;
        }
    }
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
