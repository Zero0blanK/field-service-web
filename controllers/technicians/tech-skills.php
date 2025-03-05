<?php

$config = require_once 'config.php';
$db = new dbConnection($config['database']);

define("ROOT", dirname(__DIR__));
define("VIEWS", ROOT . "/../views/");

// checkTechnicianAccess();

$tech_id = 2;

// Fetch all available skills
$all_skills = $db->query("SELECT * FROM skills ORDER BY name")->fetchAll();

// Fetch technician's current skills
$current_skills = $db->query("
    SELECT s.skill_id, s.name, s.description 
    FROM skills s
    JOIN technician_skills ts ON s.skill_id = ts.skill_id
    WHERE ts.tech_id = :tech_id",
    [$tech_id]
)->fetchAll();


// Convert current skills to an array of skill IDs for easy checking
$current_skill_ids = array_column($current_skills, 'skill_id');

require_once 'views/technicians/tech-skills.view.php';