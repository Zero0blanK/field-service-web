<?php 


$config = require_once 'config.php';
$db = new dbConnection($config['database']);

// Function to get work order details
function getWorkOrderDetails($order_id, $db) {
    return $db->query(
            "SELECT 
                wo.*, 
                c.company_name, 
                u.name AS customer_name, 
                u.email, 
                u.phone, 
                u.address, 
                u.city AS customer_city,
                cp.*
            FROM 
                work_orders wo
            JOIN customers c 
                ON wo.customer_id = c.customer_id
            JOIN users u 
                ON c.user_id = u.user_id
            LEFT JOIN customer_preferences cp 
                ON c.customer_id = cp.customer_id
            WHERE 
                wo.order_id = :order_id",
            [':order_id' => $order_id]
    )->fetch();
}

// Function to get available technicians
function getAvailableTechnicians($order_id, $scheduled_date, $db) {
    return $db->query(
            "SELECT DISTINCT 
                t.*,
                u.*,
                GROUP_CONCAT(s.name) AS skills,
                (SELECT COUNT(*) FROM work_orders 
            WHERE 
                tech_id = t.tech_id AND DATE(scheduled_date) = :scheduled_date
            AND 
                status NOT IN ('completed', 'cancelled')) AS current_orders
            FROM 
                technicians t
            LEFT JOIN technician_skills ts 
                ON t.tech_id = ts.tech_id
            LEFT JOIN skills s 
                ON ts.skill_id = s.skill_id
            JOIN users u 
                ON t.user_id = u.user_id
            JOIN work_orders wo 
                ON wo.order_id = :order_id
            JOIN customers c 
                ON wo.customer_id = c.customer_id
            JOIN users cu 
                ON c.user_id = cu.user_id
            WHERE 
                cu.city = u.city
            GROUP BY 
                t.tech_id
            ORDER BY 
                u.name ASC", 
            [':scheduled_date' => $scheduled_date, ':order_id' => $order_id]
    )->fetchAll();
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    if ($_GET['action'] === 'get_details') {
        $order_id = $_GET['order_id'];
        $details = getWorkOrderDetails($order_id, $db);
        echo json_encode($details);
        exit;
    }
    
    if ($_GET['action'] === 'get_techs') {
        $order_id = $_GET['order_id'];
        $schedule_date = $_GET['scheduled_date'];
        $techs = [];
        $result = getAvailableTechnicians($order_id, $schedule_date, $db);
        foreach ($result as $tech) {
            $techs[] = $tech;
        }
        echo json_encode($techs);
        exit;
    }
}

// Handle POST request for assigning technician
if (isset($_POST['assign_technician'])) {
    try {
        $order_id = $_POST['order_id'];
        $tech_id = $_POST['tech_id'];
        
        $db->query(
            "UPDATE 
                work_orders 
            SET 
                tech_id = :tech_id, 
                status = 'assigned'
            WHERE 
                order_id = :order_id"
            ,[':tech_id' => $tech_id, ':order_id' => $order_id]
        );
        
        $_SESSION['success'] = "Technician successfully assigned to work order.";
        header("Location: /dashboard/work-orders/assign-technician");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Error assigning technician: " . $e->getMessage();
    }
}

// Get unassigned work orders
$unassigned_orders = $db->query(
    "SELECT 
        wo.*, 
        c.company_name,
        u.city AS customer_city
    FROM 
        work_orders wo
    JOIN customers c 
        ON wo.customer_id = c.customer_id
    JOIN users u 
        ON c.user_id = u.user_id
    WHERE 
        wo.tech_id IS NULL
    AND 
        wo.status = 'pending'
    ORDER BY 
        wo.priority DESC, 
        wo.scheduled_date ASC
");


require_once 'views/work-order-assign-technician.view.php';
