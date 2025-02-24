<?php
// get_customer_details.php
require_once 'config.php';

if (!isset($_GET['id'])) {
    die('Customer ID not provided');
}

$customer_id = sanitize($_GET['id']);

// Get customer details, including user information
$customer = $conn->query("
    SELECT c.customer_id, c.company_name, 
           u.name, u.email, u.phone, u.address, u.city, u.zipcode
    FROM customers c
    LEFT JOIN users u ON c.user_id = u.user_id
    WHERE c.customer_id = '$customer_id'
");

if (!$customer) {
    die('Error fetching customer details: ' . $conn->error);
}

$customer = $customer->fetch_assoc();

if (!$customer) {
    die('Customer not found');
}

// Get customer's work orders with technician details
$work_orders = $conn->query("
    SELECT wo.*, u.name AS tech_name
    FROM work_orders wo
    LEFT JOIN technicians t ON wo.tech_id = t.tech_id
    LEFT JOIN users u ON t.user_id = u.user_id
    WHERE wo.customer_id = '$customer_id'
    ORDER BY wo.created_at DESC
");

// Calculate statistics
$stats = $conn->query("
    SELECT 
        COUNT(*) AS total_orders,
        COUNT(CASE WHEN status = 'completed' THEN 1 END) AS completed_orders,
        COUNT(CASE WHEN status IN ('pending', 'assigned', 'in_progress') THEN 1 END) AS active_orders,
        AVG(CASE WHEN status = 'completed' THEN 
            TIMESTAMPDIFF(HOUR, created_at, completion_date) 
        END) AS avg_completion_time
    FROM work_orders
    WHERE customer_id = '$customer_id'
")->fetch_assoc();
?>


<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold"><?php echo $customer['company_name'] ? $customer['company_name'] : 'Resident' ; ?></h2>
    <button onclick="document.getElementById('customerDetailsModal').classList.add('hidden')"
            class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-times"></i>
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- Customer Information -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h3 class="text-lg font-medium mb-4">Contact Information</h3>
        <div class="space-y-2">
            <p><strong>Name:</strong> <?php echo $customer['name']; ?></p>
            <p><strong>Email:</strong> <?php echo $customer['email']; ?></p>
            <p><strong>Phone:</strong> <?php echo $customer['phone']; ?></p>
            <p><strong>Address:</strong> <?php echo $customer['address'] . ', ' . $customer['city'] . ', ' . $customer['zipcode']; ?></p>
        </div>
    </div>

    <!-- Statistics -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h3 class="text-lg font-medium mb-4">Service Statistics</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="text-center">
                <p class="text-2xl font-bold"><?php echo $stats['total_orders']; ?></p>
                <p class="text-sm text-gray-500">Total Orders</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold"><?php echo $stats['completed_orders']; ?></p>
                <p class="text-sm text-gray-500">Completed</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold"><?php echo $stats['active_orders']; ?></p>
                <p class="text-sm text-gray-500">Active Orders</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold"><?php echo round($stats['avg_completion_time'] ?? 0); ?>h</p>
                <p class="text-sm text-gray-500">Avg. Completion Time</p>
            </div>
        </div>
    </div>
</div>

<!-- Work Order History -->
<div class="bg-white">
    <h3 class="text-lg font-medium mb-4">Service History</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left">Date</th>
                    <th class="px-6 py-3 text-left">Service</th>
                    <th class="px-6 py-3 text-left">Technician</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Priority</th>
                </tr>
            </thead>
            <tbody>
                <?php while($order = $work_orders->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4"><?php echo formatDate($order['created_at']); ?></td>
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
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>