<?php

session_start();

function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

function authorize($roles = []) {
    if (!isAuthenticated() || !in_array($_SESSION['role'], $roles)) {
        header("Location: /login");
        exit();
    }
}

function redirectToDashboard() {
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: /dashboard");
            break;
        case 'technician':
            header("Location: /technicians/dashboard");
            break;
        case 'customer':
            header("Location: /customers/dashboard");
            break;
        default:
            header("Location: /");
            break;
    }
    exit();
}

