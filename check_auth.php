<?php
function checkAuth($requiredRole = null) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    // Check role
    switch ($_SESSION['role']) {
        case 'admin':
            // Admin can access everything
            return;
            
        case 'employee':

            if ($requiredRole !== 'employee') {
                header("Location: employee_dashboard.php");
                exit();
            }
            return;
            
        case 'customer':

            if ($requiredRole !== 'customer') {
                header("Location: customer.php");
                exit();
            }
            return;
            
        default:
            header("Location: login.php");
            exit();
    }


}