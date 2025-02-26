<?php
// get_technician_details.php
define('PROJECT_DB', $_SERVER['DOCUMENT_ROOT'] . '/field-service-web');
include_once PROJECT_DB . "/config/dbConnection.php";

if (!isset($_GET['id'])) {
    die('Technician ID not provided');
}

$tech_id = sanitize($_GET['id']);

// Get technician details, including user info
$technician = $conn->query("
    SELECT t.*, u.name, u.email, u.phone, u.address, u.city, u.zipcode
    FROM technicians t
    LEFT JOIN users u ON t.user_id = u.user_id
    WHERE t.tech_id = '$tech_id'
");

if (!$technician) {
    die('Error fetching technician details: ' . $conn->error);
}

$technician = $technician->fetch_assoc();

if (!$technician) {
    die('Technician not found');
}

// Get technician's work orders
$work_orders = $conn->query("
    SELECT wo.*, c.company_name
    FROM work_orders wo
    LEFT JOIN customers c ON wo.customer_id = c.customer_id
    WHERE wo.tech_id = '$tech_id'
    ORDER BY wo.created_at DESC
");

if (!$work_orders) {
    die('Error fetching work orders: ' . $conn->error);
}

// Count active orders
$active_orders_count = 0;
$orders_data = [];
while ($order = $work_orders->fetch_assoc()) {
    $orders_data[] = $order;
    if ($order['status'] != 'completed' && $order['status'] != 'cancelled') {
        $active_orders_count++;
    }
}
?>


<div class="bg-white rounded-lg shadow-lg overflow-hidden max-w-4xl mx-auto">
    <!-- Header with technician info -->
    <div class="relative bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white">
        <button onclick="document.getElementById('technicianDetailsModal').classList.add('hidden')"
                class="absolute top-4 right-4 text-white hover:text-gray-200 transition-colors">
            <i class="fas fa-times text-lg"></i>
        </button>
        
        <div class="flex items-center">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-blue-600 mr-4 shadow-md">
                <span class="text-2xl font-bold"><?php echo substr($technician['name'], 0, 1); ?></span>
            </div>
            <div>
                <h2 class="text-2xl font-bold mb-1"><?php echo $technician['name']; ?></h2>
                <div class="flex items-center mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        <?php echo $technician['status'] == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                        <?php echo ucfirst($technician['status']); ?>
                    </span>
                    <span class="ml-3 text-sm opacity-75"><?php echo $active_orders_count; ?> active orders</span>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6">
        <!-- Contact Info Card -->
        <div class="bg-gray-50 rounded-lg p-5 shadow-sm mb-6">
            <h3 class="text-lg font-medium mb-4 flex items-center text-gray-700">
                <i class="fas fa-id-card mr-2 text-blue-500"></i>
                Contact Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start">
                    <i class="fas fa-envelope mt-1 mr-3 text-gray-400"></i>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="font-medium"><?php echo $technician['email']; ?></p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-phone-alt mt-1 mr-3 text-gray-400"></i>
                    <div>
                        <p class="text-sm text-gray-500">Phone</p>
                        <p class="font-medium"><?php echo $technician['phone']; ?></p>
                    </div>
                </div>
                <div class="flex items-start col-span-1 md:col-span-2">
                    <i class="fas fa-map-marker-alt mt-1 mr-3 text-gray-400"></i>
                    <div>
                        <p class="text-sm text-gray-500">City</p>
                        <p class="font-medium"><?php echo $technician['city']; echo ', ' . $technician['zipcode']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Work Orders -->
        <div>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium flex items-center text-gray-700">
                    <i class="fas fa-clipboard-list mr-2 text-blue-500"></i>
                    Work Orders
                </h3>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 text-xs font-medium rounded-md bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                        Filter
                    </button>
                    <button class="px-3 py-1 text-xs font-medium rounded-md bg-gray-50 text-gray-600 hover:bg-gray-100 transition-colors">
                        Sort
                    </button>
                </div>
            </div>
            
            <div class="space-y-3">
                <?php foreach($orders_data as $order): ?>
                    <div class="p-4 border border-gray-200 hover:border-blue-300 transition-colors rounded-lg bg-white hover:bg-blue-50 shadow-sm hover:shadow cursor-pointer">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-medium text-gray-900"><?php echo $order['title']; ?></h4>
                                <p class="text-sm text-gray-500 flex items-center">
                                    <i class="fas fa-building mr-1 text-xs"></i>
                                    <?php echo $order['company_name']; ?>
                                </p>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-sm font-medium text-gray-900">
                                    <?php echo formatTime($order['scheduled_time']); ?>
                                </span>
                                <span class="px-2 py-0.5 mt-1 rounded-full text-xs font-medium
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
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-2 flex items-center">
                            <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>
                            <?php echo $order['address']; ?>
                        </p>
                        <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-100">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?php
                                switch($order['status']) {
                                    case 'pending':
                                        echo 'bg-yellow-100 text-yellow-800';
                                        break;
                                    case 'in_progress':
                                        echo 'bg-blue-100 text-blue-800';
                                        break;
                                    case 'completed':
                                        echo 'bg-green-100 text-green-800';
                                        break;
                                    case 'cancelled':
                                        echo 'bg-gray-100 text-gray-800';
                                        break;
                                }
                                ?>">
                                <?php echo str_replace('_', ' ', ucfirst($order['status'])); ?>
                            </span>
                            <button type="button" onclick="viewWorkOrderDetails(<?php echo $order['order_id']; ?>)" class="text-sm text-blue-600 hover:text-blue-800 hover:underline transition-colors">
                                View Details
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (count($orders_data) == 0): ?>
                    <div class="text-center py-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                            <i class="fas fa-clipboard text-2xl"></i>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-1">No work orders found</h4>
                        <p class="text-gray-500">This technician has no assigned work orders</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
