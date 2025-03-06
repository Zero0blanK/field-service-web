<?php 

require_once 'functions.php';

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

$routes = [
    '/' => 'controllers/index.php',
    '/logout' => 'controllers/logout.php',
    '/login' => 'controllers/login.php',
    '/register' => 'controllers/customer-register.php',

    // Admin only
    '/dashboard' => ['file' => 'controllers/dashboard.php', 'roles' => ['admin']],
    '/dashboard/work-orders' => ['file' => 'controllers/work-orders.php', 'roles' => ['admin']],
    '/dashboard/work-orders/get-details' => ['file' => 'controllers/work-order-get-details.php', 'roles' => ['admin']],
    '/dashboard/work-orders/assign-technician' => ['file' => 'controllers/work-order-assign-technician.php', 'roles' => ['admin']],
    '/dashboard/schedule' => ['file' => 'controllers/schedule.php', 'roles' => ['admin']],
    '/dashboard/customers' => ['file' => 'controllers/customers.php', 'roles' => ['admin']],
    '/dashboard/customers/get-details' => ['file' => 'controllers/customer-get-details.php', 'roles' => ['admin']],
    '/dashboard/technicians' => ['file' => 'controllers/technicians.php', 'roles' => ['admin']],
    '/dashboard/technicians/get-details' => ['file' => 'controllers/technician-get-details.php', 'roles' => ['admin']],

    // Technician only
    '/technicians/dashboard' => ['file' => 'controllers/technicians/tech-dashboard.php', 'roles' => ['technician']],
    '/technicians/work-order/history' => ['file' => 'controllers/technicians/work-order-history.php', 'roles' => ['technician']],
    '/technicians/work-order/details' => ['file' => 'controllers/technicians/work-order-details.php', 'roles' => ['technician']],
    '/technicians/profile/skills' => ['file' => 'controllers/technicians/tech-skills.php', 'roles' => ['technician']],
    '/technicians/profile' => ['file' => 'controllers/technicians/tech-profile.php', 'roles' => ['technician']]
];

routeToController($uri, $routes);

?>