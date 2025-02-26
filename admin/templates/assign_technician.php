<?php
define('PROJECT_DB', $_SERVER['DOCUMENT_ROOT'] . '/field-service-web');
include_once PROJECT_DB . "/config/dbConnection.php";

// Function to get work order details
function getWorkOrderDetails($order_id, $conn) {
    $query = "SELECT wo.*, c.company_name, u.name AS customer_name, u.email, u.phone, u.address, u.city AS customer_city,
              cp.*
              FROM work_orders wo
              JOIN customers c ON wo.customer_id = c.customer_id
              JOIN users u ON c.user_id = u.user_id
              LEFT JOIN customer_preferences cp ON c.customer_id = cp.customer_id
              WHERE wo.order_id = ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Function to get available technicians
function getAvailableTechnicians($order_id, $schedule_date, $conn) {
    $query = "SELECT DISTINCT t.*, u.name AS technician_name, u.city, 
              GROUP_CONCAT(s.name) AS skills,
              (SELECT COUNT(*) FROM work_orders 
               WHERE tech_id = t.tech_id 
               AND DATE(scheduled_date) = ?
               AND status NOT IN ('completed', 'cancelled')) AS current_orders
              FROM technicians t
              LEFT JOIN technician_skills ts ON t.tech_id = ts.tech_id
              LEFT JOIN skills s ON ts.skill_id = s.skill_id
              JOIN users u ON t.user_id = u.user_id
              JOIN work_orders wo ON wo.order_id = ?
              JOIN customers c ON wo.customer_id = c.customer_id
              JOIN users cu ON c.user_id = cu.user_id
              WHERE cu.city = u.city
              GROUP BY t.tech_id
              ORDER BY u.name ASC";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $schedule_date, $order_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    if ($_GET['action'] === 'get_details') {
        $order_id = $_GET['order_id'];
        $details = getWorkOrderDetails($order_id, $conn);
        echo json_encode($details);
        exit;
    }
    
    if ($_GET['action'] === 'get_techs') {
        $order_id = $_GET['order_id'];
        $schedule_date = $_GET['scheduled_date'];
        $techs = [];
        $result = getAvailableTechnicians($order_id, $schedule_date, $conn);
        while ($tech = $result->fetch_assoc()) {
            $techs[] = $tech;
        }
        echo json_encode($techs);
        exit;
    }
}

// Handle POST request for assigning technician
if (isset($_POST['assign_technician'])) {
    $order_id = $_POST['order_id'];
    $tech_id = $_POST['tech_id'];
    
    $query = "UPDATE work_orders 
              SET tech_id = ?, 
                  status = 'assigned'
              WHERE order_id = ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $tech_id, $order_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Technician successfully assigned to work order.";
    } else {
        $_SESSION['error'] = 'Error assigning technician: ' . $conn->error;
    }
    
    header("Location: assign_technician.php");
    exit();
}

// Get unassigned work orders
$unassigned_orders = $conn->query("
    SELECT wo.*, 
           c.company_name,
           u.city AS customer_city
    FROM work_orders wo
    JOIN customers c ON wo.customer_id = c.customer_id
    JOIN users u ON c.user_id = u.user_id
    WHERE wo.tech_id IS NULL
    AND wo.status = 'pending'
    ORDER BY wo.priority DESC, wo.scheduled_date ASC
");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Technicians - Field Service Management</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
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

    <div class="flex-1 ml-64 p-8">
        <h1 class="text-2xl font-bold mb-6">Assign Technicians</h1>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Unassigned Work Orders</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-6 py-3 text-left">Order ID</th>
                            <th class="px-6 py-3 text-left">Customer</th>
                            <th class="px-6 py-3 text-left">City</th>
                            <th class="px-6 py-3 text-left">Schedule</th>
                            <th class="px-6 py-3 text-left">Priority</th>
                            <th class="px-6 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($order = $unassigned_orders->fetch_assoc()):?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4">#<?php echo $order['order_id']; ?></td>
                            <td class="px-6 py-4"><?php echo $order['company_name']; ?></td>
                            <td class="px-6 py-4"><?php echo $order['customer_city']; ?></td>
                            <td class="px-6 py-4">
                                <?php echo date('M d, Y', strtotime($order['scheduled_date'])); ?>
                                <br>
                                <?php echo date('h:i A', strtotime($order['scheduled_time'])); ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-4 py-2 rounded-full text-sm font-medium
                                    <?php
                                    switch($order['priority']) {
                                        case 'low':
                                            echo 'bg-gray-200 text-gray-800';
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
                                <button onclick="showAvailableTechs(<?php echo $order['order_id']; ?>, '<?php echo $order['scheduled_date']; ?>')"
                                        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                    Assign Technician
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Assign Technician Modal -->
    <div id="assignTechModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Work Order Assignment</h3>
                <button onclick="closeModal('assignTechModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Work Order Overview Section -->
            <div id="workOrderOverview" class="mb-6 p-4 bg-gray-50 rounded-lg">
                <!-- Populated via JavaScript -->
            </div>

            <!-- Available Technicians Section -->
            <div class="mb-4">
                <h4 class="text-lg font-medium mb-3">Available Technicians</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left">Name</th>
                                <th class="px-4 py-2 text-left">Skills</th>
                                <th class="px-4 py-2 text-left">City</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Current Orders</th>
                                <th class="px-4 py-2 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody id="availableTechsList">
                            <!-- Populated via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    async function showAvailableTechs(orderId, scheduleDate) {
        try {
            // Fetch both work order details and available technicians
            const [orderResponse, techsResponse] = await Promise.all([
                fetch(`?action=get_details&order_id=${orderId}`),
                fetch(`?action=get_techs&order_id=${orderId}&scheduled_date=${scheduleDate}`)
            ]);

            const orderDetails = await orderResponse.json();
            const techs = await techsResponse.json();

            // Update Work Order Overview
            const overviewHtml = `
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-medium text-lg mb-3">Work Order Details</h4>
                        <p class="text-m mb-2"><span class="font-medium">Order ID:</span> #${orderDetails.order_id}</p>
                        <p class="text-m mb-2"><span class="font-medium">Title:</span> ${orderDetails.title}</p>
                        <p class="text-m mb-2"><span class="font-medium">Priority:</span> 
                            <span class="px-4 py-2 font-medium rounded-full text-xs ${getPriorityClass(orderDetails.priority)}">
                                ${orderDetails.priority.toUpperCase()}
                            </span>
                        </p>
                        <p class="text-m mb-1"><span class="font-medium">Schedule:</span> 
                            ${formatDateTime(orderDetails.scheduled_date, orderDetails.scheduled_time)}
                        </p>
                    </div>
                    <div>
                        <h4 class="font-medium text-lg mb-2">Customer Details</h4>
                        <p class="text-m mb-1"><span class="font-medium">Company:</span> ${orderDetails.company_name}</p>
                        <p class="text-m mb-1"><span class="font-medium">Customer Name:</span> ${orderDetails.customer_name}</p>
                        <p class="text-m mb-1"><span class="font-medium">Address:</span> ${orderDetails.address}</p>
                        <p class="text-m mb-1"><span class="font-medium">City:</span> ${orderDetails.customer_city}</p>
                        <p class="text-m mb-1"><span class="font-medium">Phone:</span> ${orderDetails.phone}</p>
                        <p class="text-m mb-1"><span class="font-medium">Email:</span> ${orderDetails.email}</p>
                        <p class="text-m mb-1"><span class="font-medium">Preferred Time:</span> 
                            ${orderDetails.preferred_time_of_day ? orderDetails.preferred_time_of_day.charAt(0).toUpperCase() + orderDetails.preferred_time_of_day.slice(1) : 'Not specified'}

                        </p>
                        <p class="text-m mb-1"><span class="font-medium">Preferred Contact Method:</span> 
                            ${orderDetails.preferred_contact_method.charAt(0).toUpperCase() + orderDetails.preferred_contact_method.slice(1) || 'Not specified'}
                        </p>
                        </div>
                </div>
            `;
            document.getElementById('workOrderOverview').innerHTML = overviewHtml;

            // Update Technicians List
            const techsList = document.getElementById('availableTechsList');
            techsList.innerHTML = '';

            techs.forEach(tech => {
                const isAvailable = tech.current_orders < 3;
                const row = `
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3">${tech.technician_name}</td>
                        <td class="px-4 py-3 text-sm">${tech.skills || 'No skills listed'}</td>
                        <td class="px-4 py-3">${tech.city}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs ${
                                isAvailable ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                            }">
                                ${isAvailable ? 'Available' : 'Not Available'}
                            </span>
                        </td>
                        <td class="px-4 py-3">${tech.current_orders} / 3</td>
                        <td class="px-4 py-3">
                            <form method="POST" class="inline">
                                <input type="hidden" name="order_id" value="${orderId}">
                                <input type="hidden" name="tech_id" value="${tech.tech_id}">
                                <button type="submit" name="assign_technician" 
                                        class="bg-indigo-600 text-white px-3 py-1 rounded-md hover:bg-indigo-700 ${
                                            !isAvailable ? 'opacity-50 cursor-not-allowed' : ''
                                        }"
                                        ${!isAvailable ? 'disabled' : ''}>
                                    Assign
                                </button>
                            </form>
                        </td>
                    </tr>
                `;
                techsList.innerHTML += row;
            });

            openModal('assignTechModal');
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    }

    function getPriorityClass(priority) {
        const classes = {
            'low': 'bg-gray-200 text-gray-800',
            'medium': 'bg-blue-100 text-blue-800',
            'high': 'bg-yellow-100 text-yellow-800',
            'urgent': 'bg-red-100 text-red-800'
        };
        return classes[priority] || classes.low;
    }

    function formatDateTime(date, time) {
        const formattedDate = new Date(date + ' ' + time).toLocaleString('en-US', {
            dateStyle: 'medium',
            timeStyle: 'short'
        });
        return formattedDate;
    }

    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
    </script>
</body>
</html>