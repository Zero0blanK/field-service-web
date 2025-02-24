<?php
// schedule.php
require_once 'config.php';

// Get technicians with their schedules
$technicians = $conn->query("
    SELECT t.*, u.name,
           COUNT(CASE WHEN wo.status IN ('assigned', 'in_progress') THEN 1 END) as active_orders
    FROM technicians t
    LEFT JOIN work_orders wo ON t.tech_id = wo.tech_id
    LEFT JOIN users u ON t.user_id = u.user_id
    GROUP BY t.tech_id, u.name
    ORDER BY u.name
");

// Get today's work orders
$today = date('Y-m-d');
$todays_orders = $conn->query("
    SELECT wo.*, c.company_name, u.address, u.name AS tech_name
    FROM work_orders wo
    LEFT JOIN customers c ON wo.customer_id = c.customer_id
    LEFT JOIN technicians t ON wo.tech_id = t.tech_id
    LEFT JOIN users u ON t.user_id = u.user_id
    WHERE DATE(wo.scheduled_date) = '$today'
    ORDER BY wo.scheduled_time
");

// Get weekly schedule
$week_start = date('Y-m-d', strtotime('monday this week'));
$week_end = date('Y-m-d', strtotime('sunday this week'));
$weekly_schedule = $conn->query("
    SELECT wo.*, c.company_name, u.name AS tech_name,
           (DAYOFWEEK(wo.scheduled_date) + 5) % 7 + 1 AS day_of_week
    FROM work_orders wo
    LEFT JOIN customers c ON wo.customer_id = c.customer_id
    LEFT JOIN technicians t ON wo.tech_id = t.tech_id
    LEFT JOIN users u ON t.user_id = u.user_id
    WHERE wo.scheduled_date BETWEEN '$week_start' AND '$week_end'
    ORDER BY wo.scheduled_date, wo.scheduled_time
");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule - Field Service Management</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <nav class="bg-indigo-800 w-64 py-4 px-2 fixed h-full">
            <div class="flex items-center justify-center mb-8">
                <h1 class="text-white text-2xl font-bold">Field Service Pro</h1>
            </div>
            <ul class="space-y-2">
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
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="flex-1 ml-64 p-8">
            <!-- Today's Overview -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Technician Status -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">Technician Status</h2>
                    <div class="space-y-4">
                        <?php while($tech = $technicians->fetch_assoc()): ?>
                            <div class="flex items-center justify-between p-4 border rounded-lg">
                                <div>
                                    <h3 class="font-medium"><?php echo $tech['name']; ?></h3>
                                    <p class="text-sm text-gray-500">
                                        <?php echo $tech['active_orders']; ?> active orders
                                    </p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-sm
                                    <?php
                                    switch($tech['status']) {
                                        case 'available':
                                            echo 'bg-green-100 text-green-800';
                                            break;
                                        case 'busy':
                                            echo 'bg-yellow-100 text-yellow-800';
                                            break;
                                        case 'off-duty':
                                            echo 'bg-gray-100 text-gray-800';
                                            break;
                                    }
                                    ?>">
                                    <?php echo ucfirst($tech['status']); ?>
                                </span>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Today's Schedule -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">Today's Schedule</h2>
                    <div class="space-y-4">
                        <?php while($order = $todays_orders->fetch_assoc()): ?>
                            <div class="p-4 border rounded-lg">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h3 class="font-medium"><?php echo $order['title']; ?></h3>
                                        <p class="text-sm text-gray-500">
                                            <?php echo $order['company_name']; ?>
                                        </p>
                                    </div>
                                    <span class="text-sm font-medium">
                                        <?php echo formatTime($order['scheduled_time']); ?>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    <?php echo $order['address']; ?>
                                </p>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">
                                        Assigned to: <?php echo $order['tech_name'] ?? 'Unassigned'; ?>
                                    </span>
                                    <span class="px-2 py-1 rounded-full text-xs
                                        <?php
                                        switch($order['status']) {
                                            case 'pending':
                                                echo 'bg-gray-100 text-gray-800';
                                                break;
                                            case 'assigned':
                                                echo 'bg-blue-100 text-blue-800';
                                                break;
                                            case 'in_progress':
                                                echo 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'completed':
                                                echo 'bg-green-100 text-green-800';
                                                break;
                                            case 'cancelled':
                                                echo 'bg-red-100 text-red-800';
                                                break;
                                        }
                                        ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <!-- Weekly Calendar -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Weekly Schedule</h2>
                <div class="grid grid-cols-7 gap-4">
                    <?php
                    $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                    $weekly_orders = [];
                    while($order = $weekly_schedule->fetch_assoc()) {
                        $weekly_orders[$order['day_of_week']][] = $order;
                    }
                    
                    foreach($days as $index => $day): 
                        $day_number = $index + 1;
                        $is_today = date('N') == $day_number;
                    ?>
                        <div class="border rounded-lg <?php echo $is_today ? 'bg-indigo-50 border-indigo-200' : ''; ?>">
                            <div class="p-2 border-b text-center font-medium">
                                <?php echo $day; ?>
                            </div>
                            <div class="p-2 space-y-2">
                                <?php
                                if (isset($weekly_orders[$day_number])) {
                                    foreach($weekly_orders[$day_number] as $order) {
                                        $tech_name = !empty($order['tech_name']) ? $order['tech_name'] : '<span class="text-red-500">Unassigned</span>';
                                        echo '<div class="text-sm p-2 border rounded bg-white">';
                                        echo '<div class="font-medium">' . formatTime($order['scheduled_time']) . '</div>';
                                        echo '<div>' . $order['title'] . '</div>';
                                        echo '<div class="text-gray-500">' . $tech_name . '</div>';
                                        echo '</div>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>