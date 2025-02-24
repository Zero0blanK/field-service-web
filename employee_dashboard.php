<?php

require_once 'config.php';

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <nav class="bg-indigo-800 w-64 py-4 px-2 fixed h-full">
        <div class="flex items-center justify-center mb-8">
            <h1 class="text-white text-2xl font-bold">Field Service Pro</h1>
        </div>
        <ul class="space-y-2 mb-64">
            <li>
                <a href="index.php" class="flex items-center text-white py-2 px-4 rounded hover:bg-indigo-700">
                    <i class="fas fa-dashboard mr-3"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="work_orders.php" class="flex items-center text-white py-2 px-4 rounded hover:bg-indigo-700">
                    <i class="fas fa-clipboard-list mr-3"></i>
                    Work Orders
                </a>
            </li>
            <li>
                <a href="schedule.php" class="flex items-center text-white py-2 px-4 rounded hover:bg-indigo-700">
                    <i class="fas fa-calendar mr-3"></i>
                    Schedule
                </a>
            </li>
            <li>
                <a href="customers.php" class="flex items-center text-white py-2 px-4 rounded hover:bg-indigo-700">
                    <i class="fas fa-users mr-3"></i>
                    Customers
                </a>
            </li>
            <li>
                <a href="technicians.php" class="flex items-center text-white py-2 px-4 rounded hover:bg-indigo-700">
                    <i class="fas fa-hard-hat mr-3"></i>
                    Technicians
                </a>
            </li>
            <li>
                <a href="logout.php" class="flex items-center text-white py-2 px-4 rounded hover:bg-indigo-700">
                    <i class="fas fa-sign-out mr-3"></i>
                    Logout
                </a>
            </li>
        </ul>

    </nav>

    <div class="flex-1 ml-64 p-8">
        <h1>Employee Page</h1>
    </div>
</body>
</html