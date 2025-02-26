<?php
// technicians.php
define('PROJECT_DB', $_SERVER['DOCUMENT_ROOT'] . '/field-service-web');
include_once PROJECT_DB . "/config/dbConnection.php";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $address = sanitize($_POST['address']);
        $city = sanitize($_POST['city']);
        $zipcode = sanitize($_POST['zipcode']);
        $password = password_hash(sanitize($_POST['password']), PASSWORD_BCRYPT);
        $status = sanitize($_POST['status']);

        // Insert into `users` table first
        $sql_user = "INSERT INTO users (name, email, phone, address, city, zipcode, password, role, created_at) 
                     VALUES ('$name', '$email', '$phone', '$address', '$city', '$zipcode', '$password', 'technician', NOW())";
        
        if ($conn->query($sql_user)) {
            $user_id = $conn->insert_id; // Get the last inserted user_id

            // Insert into `technicians` table
            $sql_tech = "INSERT INTO technicians (user_id, status) VALUES ('$user_id', '$status')";
            $conn->query($sql_tech);
        }

        // Redirect to avoid form resubmission
        header('Location: technicians.php');
        exit;
    }
}

// Pagination settings
$items_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Get technicians count for pagination
$tech_count = $conn->query("SELECT COUNT(*) as count FROM technicians")->fetch_assoc()['count'];
$total_pages = ceil($tech_count / $items_per_page);

// Get all technicians with their active work orders
$technicians = $conn->query("
    SELECT t.tech_id, u.name, u.email, u.phone, u.address, u.city, u.zipcode, t.status,
           COUNT(CASE WHEN wo.status IN ('assigned', 'in_progress') THEN 1 END) as active_orders,
           COUNT(wo.order_id) as total_orders,
           COUNT(CASE WHEN wo.status = 'completed' THEN 1 END) as completed_orders
    FROM technicians t
    LEFT JOIN users u ON t.user_id = u.user_id
    LEFT JOIN work_orders wo ON t.tech_id = wo.tech_id
    GROUP BY t.tech_id, u.name, u.email, u.phone, u.address, u.city, u.zipcode, t.status
    ORDER BY u.name
    LIMIT $offset, $items_per_page
");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technicians - Field Service Management</title>
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
                    <a href="technicians.php" class="flex items-center text-white py-2 px-4 rounded hover:bg-indigo-700 bg-indigo-700">
                        <i class="fas fa-hard-hat mr-3"></i>
                        Technicians
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="flex-1 ml-64 p-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Technicians</h1>
                <button onclick="document.getElementById('createTechnicianModal').classList.remove('hidden')" 
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i>Add Technician
                </button>
            </div>

            <!-- Filter and Search Section -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="status-filter" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Statuses</option>
                            <option value="available">Available</option>
                            <option value="busy">Busy</option>
                            <option value="off-duty">Off Duty</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-[250px]">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <div class="relative rounded-md shadow-sm">
                            <input type="text" id="search" class="block w-full rounded-md border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Search by name, email...">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex-initial self-end">
                        <button type="button" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Apply Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Technicians Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="min-h-[400px]">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active Orders</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Orders</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while($tech = $technicians->fetch_assoc()): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0 mr-3">
                                                <span class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                    <span class="text-indigo-800 font-medium text-lg">
                                                        <?php echo substr($tech['name'], 0, 1); ?>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="ml-1">
                                                <div class="text-sm font-medium text-gray-900"><?php echo $tech['name']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500">
                                            <p><i class="fas fa-envelope mr-2 text-gray-400"></i><?php echo $tech['email']; ?></p>
                                            <p><i class="fas fa-phone mr-2 text-gray-400"></i><?php echo $tech['phone']; ?></p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
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
                                                    default:
                                                        echo 'bg-gray-100 text-gray-800';
                                                }
                                            ?>">
                                            <?php echo ucfirst($tech['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $tech['active_orders']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">
                                            <span><?php echo $tech['total_orders']; ?> total</span>
                                            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-1">
                                                <?php
                                                $completion_percent = $tech['total_orders'] > 0 
                                                    ? round(($tech['completed_orders'] / $tech['total_orders']) * 100)
                                                    : 0;
                                                ?>
                                                <div class="bg-indigo-600 h-2.5 rounded-full" style="width: <?php echo $completion_percent; ?>%"></div>
                                            </div>
                                            <span class="text-xs"><?php echo $completion_percent; ?>% completed</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex space-x-2 justify-end">
                                            <button onclick="viewTechnicianDetails(<?php echo $tech['tech_id']; ?>)"
                                                    class="text-indigo-600 hover:text-indigo-900">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button onclick="editTechnician(<?php echo $tech['tech_id']; ?>)"
                                                    class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
                    <nav class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing page <span class="font-medium"><?php echo $page; ?></span> of <span class="font-medium"><?php echo $total_pages; ?></span>
                            </p>
                        </div>
                        <div>
                            <ul class="flex pl-0 list-none rounded my-2">
                                <?php if ($page > 1): ?>
                                    <li>
                                        <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-1 text-indigo-600 hover:bg-indigo-100 rounded-md">Previous</a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                    <li>
                                        <a href="?page=<?php echo $i; ?>" class="px-3 py-1 <?php echo $i == $page ? 'bg-indigo-600 text-white' : 'text-indigo-600 hover:bg-indigo-100'; ?> rounded-md"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                    <li>
                                        <a href="?page=<?php echo $page + 1; ?>" class="px-3 py-1 text-indigo-600 hover:bg-indigo-100 rounded-md">Next</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Technician Modal -->
    <div id="createTechnicianModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Add New Technician</h3>
                <button onclick="document.getElementById('createTechnicianModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="create">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" required 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" required 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="tel" name="phone" required 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea name="address" required 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="available">Available</option>
                        <option value="busy">Busy</option>
                        <option value="off-duty">Off Duty</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="document.getElementById('createTechnicianModal').classList.add('hidden')"
                            class="bg-gray-200 px-4 py-2 rounded-md hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Add Technician
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Technician Details Modal -->
    <div id="technicianDetailsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-14 mx-auto">
            <div class="flex justify-between items-center mb-4">
                <button onclick="document.getElementById('technicianDetailsModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mt-3" id="technicianDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- Work Order Details Modal -->
    <div id="viewOrderModal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-0 border-0 w-full max-w-4xl shadow-2xl rounded-lg bg-white overflow-hidden">
            <!-- Header -->
            <div class="relative bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white">
                <button onclick="document.getElementById('viewOrderModal').classList.add('hidden'); document.body.style.overflow = 'auto';" 
                        class="absolute top-4 right-4 text-white hover:text-gray-200 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
                <h2 class="text-2xl font-bold mb-1" id="viewTitle"></h2>
                <div class="flex items-center mt-2">
                    <span id="viewPriority" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mr-3"></span>
                    <span id="viewStatus" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"></span>
                </div>
            </div>
            
            <!-- Content -->
            <div class="p-6" id="viewOrderContent">
                <!-- Quick Info Cards Row -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="rounded-lg bg-gray-50 p-4 shadow-sm">
                        <p class="text-sm text-gray-500 mb-1">Scheduled Date</p>
                        <p class="font-medium text-gray-900" id="viewScheduledDate"></p>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4 shadow-sm">
                        <p class="text-sm text-gray-500 mb-1">Scheduled Time</p>
                        <p class="font-medium text-gray-900" id="viewScheduledTime"></p>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4 shadow-sm">
                        <p class="text-sm text-gray-500 mb-1">Created At</p>
                        <p class="font-medium text-gray-900" id="viewCreatedAt"></p>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4 shadow-sm">
                        <p class="text-sm text-gray-500 mb-1">Technician</p>
                        <p class="font-medium text-gray-900" id="viewTechnician"></p>
                    </div>
                </div>
                
                <!-- Main Content Section -->
                <div class="space-y-6">
                    <!-- Customer Section -->
                    <div class="bg-gray-50 rounded-lg p-5 shadow-sm">
                        <h3 class="text-lg font-medium mb-3 flex items-center text-gray-700">
                            <i class="fas fa-building mr-2 text-blue-500"></i>
                            Customer Information
                        </h3>
                        <div class="space-y-1">
                            <p class="text-gray-900" id="viewCustomer"></p>
                            <p class="text-gray-600" id="viewCustomerAddress"></p>
                            <div class="flex items-center mt-2">
                                <a href="#" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                                    <i class="fas fa-phone-alt mr-1"></i>
                                    <span id="viewCustomerPhone"></span>
                                </a>
                                <span class="mx-2 text-gray-300">|</span>
                                <a href="#" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                                    <i class="fas fa-envelope mr-1"></i>
                                    <span id="viewCustomerEmail"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description Section -->
                    <div class="bg-gray-50 rounded-lg p-5 shadow-sm">
                        <h3 class="text-lg font-medium mb-3 flex items-center text-gray-700">
                            <i class="fas fa-clipboard-list mr-2 text-blue-500"></i>
                            Work Order Details
                        </h3>
                        <div class="prose max-w-none text-gray-800" id="viewDescription"></div>
                    </div>
                </div>
                
                <!-- Footer Actions -->
                <div class="border-t border-gray-200 mt-6 pt-6 flex flex-col sm:flex-row justify-between items-center">
                    <div class="flex space-x-2 mb-4 sm:mb-0">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Order
                        </button>
                        <button class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors flex items-center">
                            <i class="fas fa-print mr-2"></i>
                            Print
                        </button>
                    </div>
                    <div class="flex space-x-2">
                        <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center">
                            <i class="fas fa-check mr-2"></i>
                            Complete Order
                        </button>
                        <button class="px-4 py-2 bg-gray-200 text-red-600 rounded-md hover:bg-gray-300 transition-colors flex items-center">
                            <i class="fas fa-ban mr-2"></i>
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        function viewTechnicianDetails(techId) {
            const modal = document.getElementById('technicianDetailsModal');
            const content = document.getElementById('technicianDetailsContent');
            
            // Fetch technician details using AJAX
            fetch(`get_technician_details.php?id=${techId}`)
                .then(response => response.text())
                .then(data => {
                    content.innerHTML = data;
                    modal.classList.remove('hidden');
                });
        }

        function viewWorkOrderDetails(orderId) {
        const modal = document.getElementById('viewOrderModal');
        const content = document.getElementById('viewOrderContent');
        
        // Fetch work order details using AJAX
        fetch(`get_work_order_details.php?id=${orderId}`)
            .then(response => response.json())
            .then(data => {
                // Populate the modal with work order details
                document.getElementById('viewCustomer').textContent = data.company_name;
                document.getElementById('viewCustomerAddress').textContent = data.address;
                document.getElementById('viewCustomerPhone').textContent = data.phone;
                document.getElementById('viewCustomerEmail').textContent = data.email;
                document.getElementById('viewTitle').textContent = data.title;
                document.getElementById('viewDescription').textContent = data.description;
                document.getElementById('viewScheduledDate').textContent = formatDate(data.scheduled_date);
                document.getElementById('viewScheduledTime').textContent = formatTime(data.scheduled_time);
                document.getElementById('viewTechnician').textContent = data.tech_name || 'Unassigned';
                document.getElementById('viewCreatedAt').textContent = formatDateTime(data.created_at);

                // Set Status with appropriate styling
                const statusElem = document.getElementById('viewStatus');
                statusElem.textContent = data.status.replace('_', ' ').toUpperCase();
                statusElem.className = 'mt-1 p-2 rounded-full text-sm inline-block';
                
                switch(data.status) {
                    case 'pending':
                        statusElem.classList.add('bg-gray-100', 'text-gray-800');
                        break;
                    case 'assigned':
                        statusElem.classList.add('bg-blue-100', 'text-blue-800');
                        break;
                    case 'in_progress':
                        statusElem.classList.add('bg-yellow-100', 'text-yellow-800');
                        break;
                    case 'completed':
                        statusElem.classList.add('bg-green-100', 'text-green-800');
                        break;
                    case 'cancelled':
                        statusElem.classList.add('bg-red-100', 'text-red-800');
                        break;
                }

                // Set Priority with appropriate styling
                const priorityElem = document.getElementById('viewPriority');
                priorityElem.textContent = data.priority.toUpperCase();
                priorityElem.className = 'mt-1 p-2 rounded-full text-sm inline-block';
                
                switch(data.priority) {
                    case 'low':
                        priorityElem.classList.add('bg-gray-100', 'text-gray-800');
                        break;
                    case 'medium':
                        priorityElem.classList.add('bg-blue-100', 'text-blue-800');
                        break;
                    case 'high':
                        priorityElem.classList.add('bg-yellow-100', 'text-yellow-800');
                        break;
                    case 'urgent':
                        priorityElem.classList.add('bg-red-100', 'text-red-800');
                        break;
                }

                // Open the modal
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            });
        }

        // Helper functions for date formatting
        function formatDate(dateStr) {
            if (!dateStr) return 'Not scheduled';
            const date = new Date(dateStr);
            return date.toLocaleDateString();
        }

        function formatTime(timeStr) {
            if (!timeStr) return 'Not scheduled';
            return timeStr;
        }

        function formatDateTime(dateTimeStr) {
            if (!dateTimeStr) return 'Unknown';
            const date = new Date(dateTimeStr);
            return date.toLocaleString();
        }

        function editTechnician(techId) {
            // Navigate to edit page or show edit modal
            // For now, just redirect to a hypothetical edit page
            window.location.href = `edit_technician.php?id=${techId}`;
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const createModal = document.getElementById('createTechnicianModal');
            const detailsModal = document.getElementById('technicianDetailsModal');
            
            if (event.target == createModal) {
                createModal.classList.add('hidden');
            }
            
            if (event.target == detailsModal) {
                detailsModal.classList.add('hidden');
            }
        }
    </script>
</body>
</html>