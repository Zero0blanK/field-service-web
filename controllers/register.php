<?php

$config = require_once 'config.php';
$db = new dbConnection($config['database']);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {

    $params = [
        ':name' => $_POST['contact_name'],
        ':email' => $_POST['email'],
        ':phone' => $_POST['phone'],
        ':address' => $_POST['address'],
        ':city' => $_POST['city'],
        ':zipcode' => $_POST['zipcode'],
        ':password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        ':role' => 'customer'
    ];

    if (in_array('', $params)) {
        $_SESSION['error'] = "Please fill in all required fields";
        exit();
    }

    $isEmail = $db->query(
        "SELECT 
            user_id 
        FROM 
            users 
        WHERE 
            email = :email",
        [':email' => $_POST['email']]
    )->fetch();
    

    if ($isEmail->num_rows > 0) {
        $_SESSION['error'] = "Email already registered!";
    } else {
        $company_name = $_POST['company_name'] ?? NULL;

        // Insert into users table
        $db->query(
            "INSERT INTO 
                users (
                    name, 
                    email, 
                    phone, 
                    address, 
                    city, 
                    zipcode, 
                    password, 
                    role, 
                    created_at) 
                VALUES (
                    :name, 
                    :email, 
                    :phone, 
                    :address,
                    :city, 
                    :zipcode, 
                    :password, 
                    :role, 
                    NOW()
                )", $params
            );
        

        $user_id = $db->lastInsertId();

        // Insert into customers table
        $db->query(
            "INSERT INTO 
            customers (
                user_id, 
                company_name
            ) 
            VALUES (
                :user_id,
                :company_name 
            )
            ", [':user_id' => $user_id, ':company_name' => $company_name]
        );

        $_SESSION['success'] = "Registration successful! Please login.";
        header("Location: /login");
        exit();
    }
    header("Location: /register"); // Reset the form after reloading the page
    exit();
}

require_once 'views/register.view.php';
