<?php require_once VIEWS . 'partials/header.php'; ?>
<?php require_once VIEWS . 'partials/technicians/sidebar.php'; ?>

<div class="flex-1 ml-64 p-8">
    <h1 class="text-3xl font-bold mb-6">Welcome, <?php echo $_SESSION['name']; ?></h1>
    <!-- Assigned Work Orders -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-4">Assigned Work Orders</h2>
        <?php if (empty($assigned_orders)): ?>
            <p class="text-gray-500">No assigned work orders at the moment.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scheduled</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($assigned_orders as $order): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">#<?php echo $order['order_id']; ?></td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium"><?php echo $order['company_name']; ?></div>
                                <div class="text-sm text-gray-500"><?php echo $order['customer_name']; ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900"><?php echo $order['title']; ?></div>
                                <div class="text-sm text-gray-500 truncate max-w-xs">
                                    <?php echo $order['description']; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <?php echo date('M d, Y', strtotime($order['scheduled_date'])); ?>
                                <div class="text-sm text-gray-500">
                                    <?php echo date('h:i A', strtotime($order['scheduled_time'])); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end space-x-2">
                                    <button onclick="acceptOrder(<?php echo $order['order_id']; ?>)" 
                                            class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button onclick="rejectOrder(<?php echo $order['order_id']; ?>)" 
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <button onclick="viewOrderDetails(<?php echo $order['order_id']; ?>)" 
                                            class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="/public/js/technicians/tech-dashboard.js" </script>
<?php require_once VIEWS . 'partials/footer.php'; ?>
