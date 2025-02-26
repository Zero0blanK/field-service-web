<?php
// customers.php
define('PROJECT_DB', $_SERVER['DOCUMENT_ROOT'] . '/field-service-web');
include_once PROJECT_DB . "/config/dbConnection.php";

// Handle AJAX search request
if (isset($_GET['search']) && isset($_GET['ajax'])) {
    $search = sanitize($_GET['search']);
    
    $companies = $conn->query("
        SELECT c.*, u.name, u.email, u.phone, u.address, u.city, u.zipcode,
               COUNT(wo.order_id) as total_orders,
               COUNT(CASE WHEN wo.status = 'completed' THEN 1 END) as completed_orders
        FROM customers c
        LEFT JOIN users u ON c.user_id = u.user_id
        LEFT JOIN work_orders wo ON c.customer_id = wo.customer_id
        WHERE c.company_name != '' 
        AND (
            c.company_name LIKE '%$search%' 
            OR u.name LIKE '%$search%'
            OR u.city LIKE '%$search%'
        )
        GROUP BY c.customer_id
        ORDER BY c.company_name
    ");

    $results = [];
    while ($row = $companies->fetch_assoc()) {
        $results[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($results);
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $company_name = sanitize($_POST['company_name']);
                $name = sanitize($_POST['name']);
                $email = sanitize($_POST['email']);
                $phone = sanitize($_POST['phone']);
                $address = sanitize($_POST['address']);
                $city = sanitize($_POST['city']);
                $zipcode = sanitize($_POST['zipcode']);
                $password = sanitize($_POST['password']);
                $role = 'customer';

                // Insert into users table
                $conn->query("INSERT INTO users (name, email, phone, address, city, zipcode, password, role, created_at) 
                              VALUES ('$name', '$email', '$phone', '$address', '$city', '$zipcode', '$password', '$role', NOW())");

                $user_id = $conn->insert_id; // Get last inserted user ID

                // Insert into customers table
                $conn->query("INSERT INTO customers (user_id, company_name) VALUES ('$user_id', '$company_name')");
                break;
        }
    }
}

// Pagination settings
$items_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Get company customers count for pagination
$companies_count = $conn->query("
    SELECT COUNT(*) as count
    FROM customers
    WHERE company_name != ''
")->fetch_assoc()['count'];

$companies_pages = ceil($companies_count / $items_per_page);

// Get company customers with pagination
$companies = $conn->query("
    SELECT c.*, u.name, u.email, u.phone, u.address, u.city, u.zipcode,
           COUNT(wo.order_id) as total_orders,
           COUNT(CASE WHEN wo.status = 'completed' THEN 1 END) as completed_orders
    FROM customers c
    LEFT JOIN users u ON c.user_id = u.user_id
    LEFT JOIN work_orders wo ON c.customer_id = wo.customer_id
    WHERE c.company_name != ''
    GROUP BY c.customer_id
    ORDER BY c.company_name
    LIMIT $offset, $items_per_page
");

// Get individual (non-company) customers count for pagination
$residents_count = $conn->query("
    SELECT COUNT(*) as count
    FROM customers
    WHERE company_name = '' OR company_name IS NULL
")->fetch_assoc()['count'];

$residents_pages = ceil($residents_count / $items_per_page);

// Get individual customers (residents) with pagination
$residents = $conn->query("
    SELECT c.*, u.name, u.email, u.phone, u.address, u.city, u.zipcode,
           COUNT(wo.order_id) as total_orders,
           COUNT(CASE WHEN wo.status = 'completed' THEN 1 END) as completed_orders
    FROM customers c
    LEFT JOIN users u ON c.user_id = u.user_id
    LEFT JOIN work_orders wo ON c.customer_id = wo.customer_id
    WHERE c.company_name = '' OR c.company_name IS NULL
    GROUP BY c.customer_id
    ORDER BY u.name
    LIMIT $offset, $items_per_page
");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - Field Service Management</title>
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
            <!-- Add Customer Button -->
            <div class="mb-6">
                <button onclick="document.getElementById('createCustomerModal').classList.remove('hidden')" 
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i>Add Customer
                </button>
            </div>

            <!-- Companies Table -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Companies</h2>
                    <div>
                        <div class="w-[450px] relative">
                            <input type="text" 
                                id="searchInput"
                                class="w-full px-4 py-2.5 pl-10 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Search companies by name, customer name, or city...">
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
                </div>
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="min-h-[400px]">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php while($company = $companies->fetch_assoc()): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo $company['company_name']; ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo $company['name']; ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-500">
                                                <p><i class="fas fa-envelope mr-2 text-gray-400"></i><?php echo $company['email']; ?></p>
                                                <p><i class="fas fa-phone mr-2 text-gray-400"></i><?php echo $company['phone']; ?></p>
                                                <p><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i><?php echo $company['address']; ?></p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                <p>Total: <?php echo $company['total_orders']; ?></p>
                                                <p>Completed: <?php echo $company['completed_orders']; ?></p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="viewCustomerDetails(<?php echo $company['customer_id']; ?>)"
                                                    class="text-indigo-600 hover:text-indigo-900 hover:bg-indigo-100 border border-indigo-600 px-2 py-1">
                                                Details
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination for Companies -->
                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
                        <nav class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing page <span class="font-medium"><?php echo $page; ?></span> of <span class="font-medium"><?php echo $companies_pages; ?></span>
                                </p>
                            </div>
                            <div>
                                <ul class="flex pl-0 list-none rounded my-2">
                                    <?php if ($page > 1): ?>
                                        <li>
                                            <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-1 text-indigo-600 hover:bg-indigo-100 rounded-md">Previous</a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = max(1, $page - 2); $i <= min($companies_pages, $page + 2); $i++): ?>
                                        <li>
                                            <a href="?page=<?php echo $i; ?>" class="px-3 py-1 <?php echo $i == $page ? 'bg-indigo-600 text-white' : 'text-indigo-600 hover:bg-indigo-100'; ?> rounded-md"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($page < $companies_pages): ?>
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

            <!-- Residents Table -->
            <div>
                <h2 class="text-2xl font-bold mb-4">Residents</h2>
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="min-h-[400px]">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php while($resident = $residents->fetch_assoc()): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo $resident['name']; ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-500">
                                                <p><i class="fas fa-envelope mr-2 text-gray-400"></i><?php echo $resident['email']; ?></p>
                                                <p><i class="fas fa-phone mr-2 text-gray-400"></i><?php echo $resident['phone']; ?></p>
                                                <p><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i><?php echo $resident['address']; ?></p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                <p>Total: <?php echo $resident['total_orders']; ?></p>
                                                <p>Completed: <?php echo $resident['completed_orders']; ?></p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="viewCustomerDetails(<?php echo $resident['customer_id']; ?>)"
                                            class="text-indigo-600 hover:text-indigo-900 hover:bg-indigo-100 border border-indigo-600 px-2 py-1">
                                                Details
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination for Residents -->
                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
                        <nav class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing page <span class="font-medium"><?php echo $page; ?></span> of <span class="font-medium"><?php echo $residents_pages; ?></span>
                                </p>
                            </div>
                            <div>
                                <ul class="flex pl-0 list-none rounded my-2">
                                    <?php if ($page > 1): ?>
                                        <li>
                                            <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-1 text-indigo-600 hover:bg-indigo-100 rounded-md">Previous</a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = max(1, $page - 2); $i <= min($residents_pages, $page + 2); $i++): ?>
                                        <li>
                                            <a href="?page=<?php echo $i; ?>" class="px-3 py-1 <?php echo $i == $page ? 'bg-indigo-600 text-white' : 'text-indigo-600 hover:bg-indigo-100'; ?> rounded-md"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($page < $residents_pages): ?>
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
    </div>

    <!-- Create Customer Modal -->
    <div id="createCustomerModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Add New Customer</h3>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="create">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Company Name</label>
                        <input type="text" name="company_name" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Contact Name</label>
                        <input type="text" name="contact_name" required 
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

                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                onclick="document.getElementById('createCustomerModal').classList.add('hidden')"
                                class="bg-gray-200 px-4 py-2 rounded-md hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Add Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Customer Details Modal -->
    <div id="customerDetailsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-8 border w-full max-w-4xl shadow-lg rounded-md bg-white">
            <div class="" id="customerDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        function viewCustomerDetails(customerId) {
            const modal = document.getElementById('customerDetailsModal');
            const content = document.getElementById('customerDetailsContent');
            
            // Fetch customer details using AJAX
            fetch(`get_customer_details.php?id=${customerId}`)
                .then(response => response.text())
                .then(data => {
                    content.innerHTML = data;
                    modal.classList.remove('hidden');
                });
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const createModal = document.getElementById('createCustomerModal');
            const detailsModal = document.getElementById('customerDetailsModal');
            
            if (event.target == createModal) {
                createModal.classList.add('hidden');
            }
            
            if (event.target == detailsModal) {
                detailsModal.classList.add('hidden');
            }
        }
    </script>
    <script>
        const searchInput = document.getElementById('searchInput');
        const searchSpinner = document.getElementById('searchSpinner');
        const tableBody = document.querySelector('tbody');
        let debounceTimer;

        searchInput.addEventListener('input', function(e) {
            // Show spinner
            searchSpinner.classList.remove('hidden');
            
            // Clear previous timer
            clearTimeout(debounceTimer);
            
            // Debounce the search
            debounceTimer = setTimeout(() => {
                const searchTerm = e.target.value.trim();
                
                // Make AJAX request
                fetch(`customers.php?search=${encodeURIComponent(searchTerm)}&ajax=1`)
                    .then(response => response.json())
                    .then(data => {
                        // Clear current table contents
                        tableBody.innerHTML = '';
                        
                        // Add new results
                        data.forEach(company => {
                            tableBody.innerHTML += `
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">${company.company_name}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">${company.name}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500">
                                            <p><i class="fas fa-envelope mr-2 text-gray-400"></i>${company.email}</p>
                                            <p><i class="fas fa-phone mr-2 text-gray-400"></i>${company.phone}</p>
                                            <p><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>${company.address}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">
                                            <p>Total: ${company.total_orders}</p>
                                            <p>Completed: ${company.completed_orders}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="viewCustomerDetails(${company.customer_id})"
                                                class="text-indigo-600 hover:text-indigo-900 hover:bg-indigo-100 border border-indigo-600 px-2 py-1">
                                            Details
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        // Hide spinner
                        searchSpinner.classList.add('hidden');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        searchSpinner.classList.add('hidden');
                    });
            }, 300); // Debounce delay of 300ms
        });
    </script>
</body>
</html>