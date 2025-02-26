<?php 

function dd($value){
    echo "<pre>";
    var_dump($value);
    echo "</pre>";

    die();
}

function urlIs($value){
    return $_SERVER['REQUEST_URI'] == $value;
}

function abort($code = 404){
    http_response_code($code);
    require 'views/404.php';
    die();
}

function routeToController($uri, $routes){
    if(array_key_exists($uri, $routes)){
        require $routes[$uri];
    } else {
        abort();
    }
}

function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

function formatTime($time) {
    return date('h:i A', strtotime($time));
}

function getStatusColor($status) {
    $colors = [
        // Priority Status Colors
        'low' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800'],
        'medium' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
        'high' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
        'urgent' => ['bg' => 'bg-red-100', 'text' => 'text-red-800'],

        // Status Colors
        'pending' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800'],
        'assigned' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
        'in_progress' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
        'on_hold' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800'],
        'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
        'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-800']
    ];
    return $colors[$status] ?? $colors['pending'];
}

?>