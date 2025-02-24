<?php

define('DB_HOST', '127.0.0.1:3307'); // host and port
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'field_service_db');

session_start();

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

function formatTime($time) {
    return date('h:i A', strtotime($time));
}
?>