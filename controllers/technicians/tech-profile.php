<?php



$config = require_once 'config.php';
$db = new dbConnection($config['database']);

define("ROOT", dirname(__DIR__));
define("VIEWS", ROOT . "/../views/");

// checkTechnicianAccess();

$userId = $_SESSION['user_id'];

$tech_id = $db->query("SELECT tech_id FROM technicians WHERE user_id = :user_id", [$userId])->fetch()['tech_id'];


// Fetch technician profile details
$profile = $db->query("
    SELECT u.*, t.status AS technician_status 
    FROM users u
    JOIN technicians t ON u.user_id = t.user_id
    WHERE t.tech_id = :tech_id",
    [$tech_id]
)->fetch();


// Fetch technician's work statistics
$work_stats = $db->query("
    SELECT 
        COUNT(*) AS total_orders,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_orders,
        AVG(CASE WHEN status = 'completed' THEN TIMESTAMPDIFF(HOUR, created_at, completion_date) ELSE NULL END) AS avg_completion_time
    FROM work_orders
    WHERE tech_id = :tech_id",
    [$tech_id]
)->fetch();

// Fetch technician's current skills
$current_skills = $db->query("
    SELECT s.skill_id, s.name, s.description 
    FROM skills s
    JOIN technician_skills ts ON s.skill_id = ts.skill_id
    WHERE ts.tech_id = :tech_id",
    [$tech_id]
)->fetchAll();

require_once 'views/technicians/tech-profile.view.php';