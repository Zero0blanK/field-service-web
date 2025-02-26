<?php 

require_once 'functions.php';

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

$routes = [
    '/' => 'controllers/index.php',
    '/dashboard' => 'controllers/dashboard.php',
    '/dashboard/work-orders' => 'controllers/work-orders.php',
    '/dashboard/work-orders/get-details' => 'controllers/work-order-get-details.php',
    '/dashboard/work-orders/assign-technician' => 'controllers/work-order-assign-technician.php',
    '/dashboard/schedule' => 'controllers/schedule.php',
    '/dashboard/customers' => 'controllers/customers.php',
    '/dashboard/customers/get-details' => 'controllers/customer-get-details.php',
    '/dashboard/technicians' => 'controllers/technicians.php',
    '/dashboard/technicians/get-details' => 'controllers/technician-get-details.php',
    '/logout' => 'controllers/logout.php',
    '/login' => 'controllers/login.php',
    '/register' => 'controllers/register.php'

];

routeToController($uri, $routes);

?>