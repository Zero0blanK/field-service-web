<?php
// index.php
require_once 'config.php';

// Enhanced dashboard statistics with error handling
try {
    // Work order statistics using a single query
    $status_query = "SELECT 
        COUNT(CASE WHEN status = 'pending' THEN 1 END) AS pending,
        COUNT(CASE WHEN status = 'in_progress' THEN 1 END) AS in_progress,
        COUNT(CASE WHEN status = 'completed' THEN 1 END) AS completed
    FROM work_orders";
    
    $result = $conn->query($status_query);
    $stats = $result->fetch_assoc();
    
    // Add total technicians count
    $tech_query = "SELECT 
        COUNT(*) AS total_technicians,
        COUNT(CASE WHEN t.status = 'available' THEN 1 END) AS active_technicians
    FROM technicians t
    LEFT JOIN users u ON t.user_id = u.user_id
    WHERE u.role = 'technician'";
    
    $tech_result = $conn->query($tech_query);
    $tech_stats = $tech_result->fetch_assoc();
    $stats['total_technicians'] = $tech_stats['total_technicians'];
    $stats['active_technicians'] = $tech_stats['active_technicians'];

    // Get recent work orders with correct JOINs
    $recent_orders = $conn->query("
        SELECT 
            wo.*, 
            c.company_name, 
            t_status.name AS tech_name,
            c_user.name AS customer_name,
            cp.*
        FROM work_orders wo 
        LEFT JOIN customers c ON wo.customer_id = c.customer_id 
        LEFT JOIN users c_user ON c.user_id = c_user.user_id
        LEFT JOIN customer_preferences cp ON c.customer_id = cp.customer_id
        LEFT JOIN technicians t ON wo.tech_id = t.tech_id
        LEFT JOIN users t_status ON t.user_id = t_status.user_id
        ORDER BY wo.created_at DESC 
        LIMIT 5
    ");

} catch (Exception $e) {
    // Log the error
    error_log("Dashboard error: " . $e->getMessage());
    // Set default values for stats
    $stats = [
        'pending' => 0,
        'in_progress' => 0,
        'completed' => 0,
        'total_technicians' => 0,
        'active_technicians' => 0
    ];
    $recent_orders = null;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Field Service Management</title>
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

        <!-- Main Content -->
        <div class="flex-1 ml-64 p-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-9 col-span-1">
                    <div class="flex items-center">
                        <div class="p-5 rounded-full bg-indigo-100 mr-4">
                            <i class="fas fa-clock text-indigo-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-lg">Pending Orders</p>
                            <p class="text-3xl font-bold"><?php echo $stats['pending'];?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-9 col-span-1">
                    <div class="flex items-center">
                        <div class="p-5 rounded-full bg-yellow-100 mr-4">
                            <i class="fas fa-spinner text-yellow-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-lg">In Progress</p>
                            <p class="text-3xl font-bold"><?php echo $stats['in_progress']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-9 col-span-1">
                    <div class="flex items-center">
                        <div class="p-5 rounded-full bg-green-100 mr-4">
                            <i class="fas fa-check text-green-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-lg">Completed</p>
                            <p class="text-3xl font-bold"><?php echo $stats['completed']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-9 col-span-1">
                    <div class="flex items-center">
                        <div class="p-5 rounded-full bg-blue-100 mr-4">
                            <i class="fas fa-user-tie text-blue-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-lg">Technicians</p>
                            <p class="text-3xl font-bold"><?php echo $stats['total_technicians']; ?></p>
                        </div>
                    </div>
                </div>
                <!-- New card for active technicians -->
                <div class="bg-white rounded-lg shadow p-9 col-span-[1.5px]">
                    <div class="flex items-center">
                        <div class="p-5 rounded-full bg-purple-100 mr-4">
                            <i class="fas fa-user-check text-purple-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-lg">Active Techs</p>
                            <p class="text-3xl font-bold"><?php echo $tech_stats['active_technicians']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Recent Work Orders -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Recent Work Orders</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-6 py-3 text-left">Order ID</th>
                                <th class="px-6 py-3 text-left">Customer</th>
                                <th class="px-6 py-3 text-left">Title</th>
                                <th class="px-6 py-3 text-left">Technician</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-left">Priority</th>
                                <th class="px-6 py-3 text-left">Scheduled</th>
                                <th class="px-6 py-3 text-left">Preferred Time</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while($order = $recent_orders->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">#<?php echo $order['order_id']; ?></td>
                                <td class="px-6 py-4"><?php echo $order['customer_name']; ?></td>
                                <td class="px-6 py-4"><?php echo $order['title']; ?></td>
                                <td class="px-6 py-4"><?php echo $order['tech_name'] ?? 'Unassigned'; ?></td>
                                <td class="px-6 py-4">
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
                                            default:
                                                echo 'bg-gray-100 text-gray-800';
                                        }
                                        ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs
                                        <?php
                                        switch($order['priority']) {
                                            case 'low':
                                                echo 'bg-gray-100 text-gray-800';
                                                break;
                                            case 'medium':
                                                echo 'bg-blue-100 text-blue-800';
                                                break;
                                            case 'high':
                                                echo 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'urgent':
                                                echo 'bg-red-100 text-red-800';
                                                break;
                                        }
                                        ?>">
                                        <?php echo ucfirst($order['priority']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php echo formatDate($order['scheduled_date']) . ' ' . formatTime($order['scheduled_time']); ?>
                                </td>
                                <td class="px-6 py-4"><?php echo ucwords($order['preferred_time_of_day']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>