<?php
$config = require_once 'config.php';
$db = new dbConnection($config['database']);
require_once 'includes/register-user.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    registerUser($db, [
        ':name' => $_POST['contact_name'],
        ':email' => $_POST['email'],
        ':phone' => $_POST['phone'],
        ':address' => $_POST['address'],
        ':city' => $_POST['city'],
        ':zipcode' => $_POST['zipcode'],
        ':password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        ':role' => 'customer'
    ], 'customer', ['company_name' => $_POST['company_name'] ?? NULL]);
}


require_once 'views/register.view.php';
