<?php require_once 'partials/header.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>

<!-- Main Content -->
<div class="flex-1 ml-64 py-8 pr-12 pl-8">
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
                    <option value="pending" <?php echo ($_GET['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="assigned" <?php echo ($_GET['status'] ?? '') === 'assigned' ? 'selected' : ''; ?>>Assigned</option>
                    <option value="in_progress" <?php echo ($_GET['status'] ?? '') === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed" <?php echo ($_GET['status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Priority</label>
                <select name="priority" class="mt-1 h-10 px-2 block w-full rounded-md border-gray-300 shadow-sm"
                        onchange="this.form.submit()">
                    <option value="">All Priorities</option>
                    <option value="low" <?php echo ($_GET['priority'] ?? '') === 'low' ? 'selected' : ''; ?>>Low</option>
                    <option value="medium" <?php echo ($_GET['priority'] ?? '') === 'medium' ? 'selected' : ''; ?>>Medium</option>
                    <option value="high" <?php echo ($_GET['priority'] ?? '') === 'high' ? 'selected' : ''; ?>>High</option>
                    <option value="urgent" <?php echo ($_GET['priority'] ?? '') === 'urgent' ? 'selected' : ''; ?>>Urgent</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Technician</label>
                <select name="tech_id" class="mt-1 h-10 px-2 block w-full rounded-md border-gray-300 shadow-sm"
                        onchange="this.form.submit()">
                    <option value="">All Technicians</option>
                    <?php 
                    foreach($technicians as $tech): 
                    ?>
                        <option value="<?php echo $tech['tech_id']; ?>"
                                <?php echo ($_GET['tech_id'] ?? '') == $tech['tech_id'] ? 'selected' : ''; ?>>
                            <?php echo $tech['name']; ?>
                        </option>
                    <?php endforeach; ?>
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
                    <?php foreach($work_orders as $order): ?>
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
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?php require_once 'partials/work_orders/createModal.php'; ?>
<?php require_once 'partials/work_orders/editModal.php'; ?>
<?php require_once 'partials/work_orders/viewModal.php'; ?>
<script src="../public/js/work-orders.js"></script>
<?php require_once 'partials/footer.php'; ?>
