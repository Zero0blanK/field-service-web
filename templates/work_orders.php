<?php
define('PROJECT_DB', $_SERVER['DOCUMENT_ROOT'] . '/field-service-web');
include_once PROJECT_DB . "/config/dbConnection.php";

// Utility function for status colors
function getStatusColor($status) {
    $colors = [
        'pending' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800'],
        'assigned' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
        'in_progress' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
        'on_hold' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800'],
        'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
        'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-800']
    ];
    return $colors[$status] ?? $colors['pending'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'create':
                    $stmt = $conn->prepare("INSERT INTO work_orders (
                        customer_id, tech_id, title, description, priority, status, scheduled_date, scheduled_time
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

                    $tech_id = !empty($_POST['tech_id']) ? $_POST['tech_id'] : null;
                    $status = $tech_id ? 'assigned' : 'pending';

                    $stmt->bind_param(
                        'iissssss',
                        $_POST['customer_id'],
                        $tech_id,
                        $_POST['title'],
                        $_POST['description'],
                        $_POST['priority'],
                        $status,
                        $_POST['scheduled_date'],
                        $_POST['scheduled_time']
                    );
                    $stmt->execute();
                    break;

                case 'update':
                    $stmt = $conn->prepare("UPDATE work_orders 
                        SET status = ?, tech_id = ?, priority = ?, scheduled_date = ?, scheduled_time = ?
                        WHERE order_id = ?");

                    $stmt->bind_param(
                        'sisssi',
                        $_POST['status'],
                        $_POST['tech_id'],
                        $_POST['priority'],
                        $_POST['scheduled_date'],
                        $_POST['scheduled_time'],
                        $_POST['order_id']
                    );
                    $stmt->execute();
                    break;
            }
            $_SESSION['success_message'] = "Work order successfully " . ($_POST['action'] === 'create' ? 'created' : 'updated');
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }
    header("Location: work_orders.php");
    exit;
}


// Work Order Filters

$where_conditions = [];
$params = [];
$param_types = "";

if (!empty($_GET['status'])) {
    $where_conditions[] = "wo.status = ?";
    $params[] = $_GET['status'];
    $param_types .= "s";
}

if (!empty($_GET['priority'])) {
    $where_conditions[] = "wo.priority = ?";
    $params[] = $_GET['priority'];
    $param_types .= "s";
}

if (!empty($_GET['tech_id'])) {
    $where_conditions[] = "wo.tech_id = ?";
    $params[] = $_GET['tech_id'];
    $param_types .= "i";
}

if (!empty($_GET['search'])) {
    $search_term = "%" . $_GET['search'] . "%";
    $where_conditions[] = "(wo.title LIKE ? OR wo.description LIKE ? OR uc.name LIKE ?)";
    $params = array_merge($params, [$search_term, $search_term, $search_term]);
    $param_types .= "sss";
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$sort_field = $_GET['sort'] ?? 'created_at';
$sort_direction = $_GET['direction'] ?? 'DESC';
$allowed_sort_fields = ['created_at', 'priority', 'scheduled_date', 'status'];
$sort_field = in_array($sort_field, $allowed_sort_fields) ? $sort_field : 'created_at';
$sort_direction = strtoupper($sort_direction) === 'ASC' ? 'ASC' : 'DESC';

$query = 
    "SELECT 
        wo.*, 
        uc.name AS customer_name, uc.phone AS customer_phone, c.*,
        ut.name AS tech_name, ut.email AS tech_email,
        cp.preferred_contact_method,
        GROUP_CONCAT(s.name ORDER BY s.name ASC) AS tech_skills
    FROM work_orders wo 
    -- Join customers to users
    LEFT JOIN customers c ON wo.customer_id = c.customer_id
    LEFT JOIN users uc ON c.user_id = uc.user_id AND uc.role = 'customer'

    -- Join customer preferences
    LEFT JOIN customer_preferences cp ON c.customer_id = cp.customer_id

    -- Join technicians to users
    LEFT JOIN technicians t ON wo.tech_id = t.tech_id
    LEFT JOIN users ut ON t.user_id = ut.user_id AND ut.role = 'technician'

    -- Join technician skills
    LEFT JOIN technician_skills ts ON t.tech_id = ts.tech_id
    LEFT JOIN skills s ON ts.skill_id = s.skill_id

    $where_clause
    GROUP BY wo.order_id
    ORDER BY wo.$sort_field $sort_direction";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$work_orders = $stmt->get_result();

// Fetch work orders with customer and technician details
$technicians = $conn->query("
    SELECT 
        wo.*, 
        c.*, 
        t.*, 
        u.*,
        GROUP_CONCAT(s.name SEPARATOR ', ') AS tech_skills
    FROM work_orders wo
    LEFT JOIN customers c ON wo.customer_id = c.customer_id
    LEFT JOIN technicians t ON wo.tech_id = t.tech_id
    LEFT JOIN users u ON t.user_id = u.user_id
    LEFT JOIN technician_skills ts ON t.tech_id = ts.tech_id
    LEFT JOIN skills s ON ts.skill_id = s.skill_id
    WHERE u.role = 'technician' AND t.status = 'available'
    GROUP BY t.tech_id
    ORDER BY u.name
");

// Fetch customers with their preferences
$customers = $conn->query("
    SELECT u.user_id, u.name AS customer_name, u.phone, c.company_name,
        cp.preferred_contact_method,
        ut.name AS preferred_tech_name
    FROM users u
    LEFT JOIN customers c ON u.user_id = c.user_id
    LEFT JOIN customer_preferences cp ON c.customer_id = cp.customer_id
    LEFT JOIN technicians t ON cp.preferred_technician = t.tech_id
    LEFT JOIN users ut ON t.user_id = ut.user_id
    WHERE u.role = 'customer'
    ORDER BY u.name
");

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Orders - Field Service Pro</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Add Flatpickr for better date/time picking -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
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
        <div class="flex-1 ml-64 py-8 pr-12 pl-8">
            <!-- Flash Messages -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <?php 
                    echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <?php 
                    echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>

            <!-- Header Section -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Work Orders</h1>
                <button onclick="openModal('createOrderModal')" 
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 flex items-center">
                    <i class="fas fa-plus mr-2"></i>Create Work Order
                </button>
            </div>

            <!-- Filters Section -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="mt-1 h-10 px-2 block w-full rounded-md border-gray-300 shadow-sm"
                                onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="pending" <?php echo $_GET['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="assigned" <?php echo $_GET['status'] === 'assigned' ? 'selected' : ''; ?>>Assigned</option>
                            <option value="in_progress" <?php echo $_GET['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                            <option value="completed" <?php echo $_GET['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Priority</label>
                        <select name="priority" class="mt-1 h-10 px-2 block w-full rounded-md border-gray-300 shadow-sm"
                                onchange="this.form.submit()">
                            <option value="">All Priorities</option>
                            <option value="low" <?php echo $_GET['priority'] === 'low' ? 'selected' : ''; ?>>Low</option>
                            <option value="medium" <?php echo $_GET['priority'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
                            <option value="high" <?php echo $_GET['priority'] === 'high' ? 'selected' : ''; ?>>High</option>
                            <option value="urgent" <?php echo $_GET['priority'] === 'urgent' ? 'selected' : ''; ?>>Urgent</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Technician</label>
                        <select name="tech_id" class="mt-1 h-10 px-2 block w-full rounded-md border-gray-300 shadow-sm"
                                onchange="this.form.submit()">
                            <option value="">All Technicians</option>
                            <?php 
                            $technicians->data_seek(0);
                            while($tech = $technicians->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $tech['tech_id']; ?>"
                                        <?php echo $_GET['tech_id'] == $tech['tech_id'] ? 'selected' : ''; ?>>
                                    <?php echo $tech['name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Search</label>
                        <div class="relative">
                            <input type="text" 
                                id="searchInput"
                                name="search" 
                                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                                class="w-full px-4 py-2 pl-10 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Search orders...">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <div id="searchSpinner" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden">
                                <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Work Orders Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <?php
                                $columns = [
                                    'order_id' => 'Order ID',
                                    'created_at' => 'Created',
                                    'company_name' => 'Customer',
                                    'title' => 'Title',
                                    'tech_name' => 'Technician',
                                    'status' => 'Status',
                                    'priority' => 'Priority',
                                    'scheduled_date' => 'Scheduled'
                                ];
                                
                                foreach ($columns as $field => $label):
                                    $sort_url = "?sort=$field&direction=";
                                    $sort_url .= ($sort_field === $field && $sort_direction === 'ASC') ? 'DESC' : 'ASC';
                                ?>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="<?php echo $sort_url; ?>" class="flex items-center space-x-1 hover:text-gray-700">
                                        <span><?php echo $label; ?></span>
                                        <?php if ($sort_field === $field): ?>
                                            <i class="fas fa-sort-<?php echo $sort_direction === 'ASC' ? 'up' : 'down'; ?>"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <?php endforeach; ?>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                                </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while($order = $work_orders->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #<?php echo $order['order_id']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo formatDate($order['created_at']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo $order['company_name']; ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo $order['customer_name']; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900"><?php echo $order['title']; ?></div>
                                    <div class="text-sm text-gray-500 truncate max-w-xs">
                                        <?php echo $order['description']; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($order['tech_name']): ?>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo $order['tech_name']; ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo $order['tech_email']; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-500">Unassigned</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                    $status_colors = getStatusColor($order['status']);
                                    ?>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php echo $status_colors['bg'] . ' ' . $status_colors['text']; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div><?php echo formatDate($order['scheduled_date']); ?></div>
                                    <div><?php echo formatTime($order['scheduled_time']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($order)); ?>)"
                                                class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="openViewModal(<?php echo htmlspecialchars(json_encode($order)); ?>)"
                                           class="text-gray-600 hover:text-gray-900">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Work Order Modal -->
    <div id="createOrderModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Create Work Order</h3>
                <button onclick="closeModal('createOrderModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="create">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Customer</label>
                        <select name="customer_id" required 
                                class="mt-1 h-11 w-full px-2 rounded-md border-gray-800 shadow-sm">
                            <?php 
                            $customers->data_seek(0);
                            while($customer = $customers->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $customer['user_id']; ?>">
                                    <?php echo $customer['company_name']; ?> - 
                                    <?php echo $customer['customer_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Priority</label>
                        <select name="priority" required 
                                class="mt-1 h-11 px-2 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" required 
                           class="mt-1 h-11 px-3 text-lg block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" required rows="3"
                              class="mt-1 h-15 p-3 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Scheduled Date</label>
                        <input type="date" name="scheduled_date" required placeholder="Pick a date"
                               class="mt-1 h-10 px-2 block w-full rounded-md border-gray-300 shadow-sm flatpickr-date">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Scheduled Time</label>
                        <input type="time" name="scheduled_time" required 
                               class="mt-1 h-10 px-2 block w-full rounded-md border-gray-300 shadow-sm flatpickr">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Assign Technician (Optional)</label>
                    <select name="tech_id" class="mt-1 h-11 px-2 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Select Technician</option>
                        <?php 
                        $technicians->data_seek(0);
                        while($tech = $technicians->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $tech['tech_id']; ?>">
                                <?php echo $tech['name']; ?> - 
                                <?php echo $tech['tech_skills']; ?> 
                                (<?php echo $tech['city']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal('createOrderModal')"
                            class="bg-white px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Create Work Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Work Order Modal -->
    <div id="editOrderModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit Work Order</h3>
                <button onclick="closeModal('editOrderModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="create">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Customer</label>
                        <select name="customer_id" required 
                                class="mt-1 h-11 w-full px-2 rounded-md border-gray-800 shadow-sm">
                            <?php 
                            $customers->data_seek(0);
                            while($customer = $customers->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $customer['user_id']; ?>">
                                    <?php echo $customer['company_name']; ?> - 
                                    <?php echo $customer['customer_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Priority</label>
                        <select name="priority" required 
                                class="mt-1 h-11 px-2 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" required 
                           class="mt-1 h-11 px-3 text-lg block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" required rows="3"
                              class="mt-1 h-15 p-3 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Scheduled Date</label>
                        <input type="date" name="scheduled_date" required placeholder="Pick a date"
                               class="mt-1 h-10 px-2 block w-full rounded-md border-gray-300 shadow-sm flatpickr-date">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Scheduled Time</label>
                        <input type="time" name="scheduled_time" required 
                               class="mt-1 h-10 px-2 block w-full rounded-md border-gray-300 shadow-sm flatpickr">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Assign Technician (Optional)</label>
                    <select name="tech_id" class="mt-1 h-11 px-2 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Select Technician</option>
                        <?php 
                        $technicians->data_seek(0);
                        while($tech = $technicians->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $tech['tech_id']; ?>">
                                <?php echo $tech['name']; ?> - 
                                <?php echo $tech['tech_skills']; ?> 
                                (<?php echo $tech['city']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal('editOrderModal')"
                            class="bg-white px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Update Work Order
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- View Work Order Modal -->
    <div id="viewOrderModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Work Order Details</h3>
                <button onclick="closeModal('viewOrderModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Customer</label>
                        <p id="viewCustomer" class="mt-1 p-2 w-full rounded-md bg-gray-50"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Priority</label>
                        <p id="viewPriority" class="mt-1 p-2 rounded-full text-sm inline-block"></p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Title</label>
                    <p id="viewTitle" class="mt-1 p-2 w-full rounded-md bg-gray-50"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <p id="viewDescription" class="mt-1 p-2 w-full rounded-md bg-gray-50 min-h-[75px] whitespace-pre-wrap"></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Scheduled Date</label>
                        <p id="viewScheduledDate" class="mt-1 p-2 w-full rounded-md bg-gray-50"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Scheduled Time</label>
                        <p id="viewScheduledTime" class="mt-1 p-2 w-full rounded-md bg-gray-50"></p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Assigned Technician</label>
                    <p id="viewTechnician" class="mt-1 p-2 w-full rounded-md bg-gray-50"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <p id="viewStatus" class="mt-1 p-2 rounded-full text-sm inline-block"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Created At</label>
                    <p id="viewCreatedAt" class="mt-1 p-2 w-full rounded-md bg-gray-50"></p>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button onclick="openEditModal(currentOrder)" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </button>
                    
                    <button onclick="closeModal('viewOrderModal')"
                            class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <script>
        // Initialize Flatpickr date/time pickers
        flatpickr(".flatpickr-date", {
            enableTime: false,
            dateFormat: "Y-m-d",
            minDate: "today"
        });

        flatpickr(".flatpickr-time", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true
        });

        // Modal handlers
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openEditModal(order) {
            closeModal('viewOrderModal');

            const modal = document.getElementById('editOrderModal');
            const form = modal.querySelector('form');
            
            // Populate the form fields
            form.querySelector('[name="customer_id"]').value = order.customer_id;
            form.querySelector('[name="priority"]').value = order.priority;
            form.querySelector('[name="title"]').value = order.title;
            form.querySelector('[name="description"]').value = order.description;
            form.querySelector('[name="scheduled_date"]').value = order.scheduled_date;
            form.querySelector('[name="scheduled_time"]').value = order.scheduled_time;
            form.querySelector('[name="tech_id"]').value = order.tech_id || '';
            
            // Add hidden input for edit action and order ID
            let actionInput = form.querySelector('[name="action"]');
            if (!actionInput) {
                actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                form.appendChild(actionInput);
            }
            actionInput.value = 'edit';

            let orderIdInput = form.querySelector('[name="order_id"]');
            if (!orderIdInput) {
                orderIdInput = document.createElement('input');
                orderIdInput.type = 'hidden';
                orderIdInput.name = 'order_id';
                form.appendChild(orderIdInput);
            }
            orderIdInput.value = order.order_id;

            openModal('editOrderModal');
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('bg-gray-600') && 
                event.target.classList.contains('bg-opacity-50')) {
                event.target.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        // Optional: Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modals = document.querySelectorAll('.modal:not(.hidden)');
                modals.forEach(modal => {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                });
            }
        });

        // Add this to your existing JavaScript
        function openViewModal(order) {
            // Store current order for edit button
            window.currentOrder = order;
            
            // Set basic information
            document.getElementById('viewCustomer').textContent = (order.company_name === null) ? order.customer_name + ' (Resident)' : order.company_name+ ' - ' + order.customer_name;
            document.getElementById('viewTitle').textContent = order.title;
            document.getElementById('viewDescription').textContent = order.description;
            document.getElementById('viewScheduledDate').textContent = formatDate(order.scheduled_date);
            document.getElementById('viewScheduledTime').textContent = formatTime(order.scheduled_time);
            document.getElementById('viewTechnician').textContent = order.tech_name || 'Unassigned';
            document.getElementById('viewCreatedAt').textContent = formatDateTime(order.created_at);

            // Set Status with appropriate styling
            const statusElem = document.getElementById('viewStatus');
            statusElem.textContent = order.status.replace('_', ' ').toUpperCase();
            statusElem.className = 'mt-1 p-2 rounded-full text-sm inline-block';
            
            switch(order.status) {
                case 'pending':
                    statusElem.classList.add('bg-gray-100', 'text-gray-800');
                    break;
                case 'in_progress':
                    statusElem.classList.add('bg-yellow-100', 'text-yellow-800');
                    break;
                case 'completed':
                    statusElem.classList.add('bg-green-100', 'text-green-800');
                    break;
            }

            // Set Priority with appropriate styling
            const priorityElem = document.getElementById('viewPriority');
            priorityElem.textContent = order.priority.toUpperCase();
            priorityElem.className = 'mt-1 p-2 rounded-full text-sm inline-block';
            
            switch(order.priority) {
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

            openModal('viewOrderModal');
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

    </script>
</body>
</html>