<?php

$config = require_once 'config.php';
$db = new dbConnection($config['database']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'create':

                    $params = [
                        ':customer_id' => $_POST['customer_id'],
                        ':tech_id' => !empty($_POST['tech_id']) ? $_POST['tech_id'] : null,
                        ':title' => $_POST['title'],
                        ':description' => $_POST['description'],
                        ':priority' => $_POST['priority'],
                        ':status' => !empty($_POST['tech_id']) ? 'assigned' : 'pending',
                        ':scheduled_date' => $_POST['scheduled_date'],
                        ':scheduled_time' => $_POST['scheduled_time']
                    ];

                    $db->query(
                        "INSERT INTO 
                            work_orders (
                                customer_id, 
                                tech_id, 
                                title, 
                                description, 
                                priority, 
                                status, 
                                scheduled_date, 
                                scheduled_time
                            )
                        VALUES 
                            (
                            :customer_id, 
                            :tech_id, 
                            :title, 
                            :description, 
                            :priority, 
                            :status, 
                            :scheduled_date, 
                            :scheduled_time
                            )
                    ", $params);
                    header("Location: /dashboard/work-orders");
                    break;

                case 'update':
                    $params = [
                        ':status' => $_POST['status'],
                        ':tech_id' => $_POST['tech_id'],
                        ':priority' => $_POST['priority'],
                        ':scheduled_date' => $_POST['scheduled_date'],
                        ':scheduled_time' => $_POST['scheduled_time'],
                        ':order_id' => $_POST['order_id']
                    ];

                    $db->query(
                        "UPDATE 
                            work_orders 
                        SET 
                            status = :status, 
                            tech_id = :tech_id, 
                            priority = :priority, 
                            scheduled_date = :scheduled_date, 
                            scheduled_time = :scheduled_time
                        WHERE 
                            order_id = :order_id
                    ", $params);
                    break;
            }
            $_SESSION['success_message'] = "Work order successfully " . ($_POST['action'] === 'create' ? 'created' : 'updated');
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }
    header("Location: /dashboard/work-orders");
    exit;
}

try {
    // Work Order Filters
    $conditions = [];
    $params = [];


    if (!empty($_GET['status'])) {
        $conditions[] = "wo.status = :status";
        $params[':status'] = $_GET['status'];
    }

    if (!empty($_GET['priority'])) {
        $conditions[] = "wo.priority = :priority";
        $params[':priority'] = $_GET['priority'];
    }

    if (!empty($_GET['tech_id'])) {
        $conditions[] = "wo.tech_id = :tech_id";
        $params[':tech_id'] = $_GET['tech_id'];
    }

    if (!empty($_GET['search'])) {
        $search = '%' . $_GET['search'] . '%';
        $conditions[] = "(wo.title LIKE :search_title OR wo.description LIKE :search_desc OR uc.name LIKE :search_name OR c.company_name LIKE :search_company)";
        $params[':search_title'] = $search;
        $params[':search_desc'] = $search;
        $params[':search_name'] = $search;
        $params[':search_company'] = $search;
    }

    $where_clause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

    $allowed_sort_fields = ['created_at', 'priority', 'scheduled_date', 'status'];
    $sort_field = in_array($_GET['sort'] ?? '', $allowed_sort_fields) ? $_GET['sort'] : 'created_at';
    $sort_direction = strtoupper($_GET['direction'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

    // Main work orders query
    $work_orders = $db->query("
        SELECT 
            wo.*, 
            uc.name AS customer_name, 
            uc.phone AS customer_phone, 
            c.*,
            ut.name AS tech_name, 
            ut.email AS tech_email,
            cp.preferred_contact_method,
            GROUP_CONCAT(s.name ORDER BY s.name ASC) AS tech_skills
        FROM work_orders wo 
        LEFT JOIN customers c ON wo.customer_id = c.customer_id
        LEFT JOIN users uc ON c.user_id = uc.user_id AND uc.role = 'customer'
        LEFT JOIN customer_preferences cp ON c.customer_id = cp.customer_id
        LEFT JOIN technicians t ON wo.tech_id = t.tech_id
        LEFT JOIN users ut ON t.user_id = ut.user_id AND ut.role = 'technician'
        LEFT JOIN technician_skills ts ON t.tech_id = ts.tech_id
        LEFT JOIN skills s ON ts.skill_id = s.skill_id
        {$where_clause}
        GROUP BY wo.order_id
        ORDER BY wo.{$sort_field} {$sort_direction}",
        $params
    )->fetchAll();

    // Fetch available technicians
    $technicians = $db->query("
        SELECT 
            t.tech_id,
            u.*,
            GROUP_CONCAT(s.name SEPARATOR ', ') AS tech_skills
        FROM technicians t
        JOIN users u ON t.user_id = u.user_id
        LEFT JOIN technician_skills ts ON t.tech_id = ts.tech_id
        LEFT JOIN skills s ON ts.skill_id = s.skill_id
        WHERE u.role = 'technician' AND t.status = 'available'
        GROUP BY t.tech_id
        ORDER BY u.name
    ")->fetchAll();

    // Fetch customers
    $customers = $db->query("
        SELECT 
            u.user_id,
            u.name AS customer_name,
            u.phone,
            c.company_name,
            cp.preferred_contact_method,
            ut.name AS preferred_tech_name
        FROM users u
        LEFT JOIN customers c ON u.user_id = c.user_id
        LEFT JOIN customer_preferences cp ON c.customer_id = cp.customer_id
        LEFT JOIN technicians t ON cp.preferred_technician = t.tech_id
        LEFT JOIN users ut ON t.user_id = ut.user_id
        WHERE u.role = 'customer'
        ORDER BY u.name
    ")->fetchAll();
} catch (Exception $e) {
    // Log the error
    error_log("Work Order error: " . $e->getMessage());
}


require_once 'views/work-orders.view.php';