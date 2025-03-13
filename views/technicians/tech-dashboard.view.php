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

<!-- Order Details Modal -->
<div id="orderDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4">
        <div class="flex justify-between items-center border-b px-6 py-4">
            <h3 class="text-xl font-bold" id="modalTitle">Work Order Details</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6" id="modalContent">
            <div class="text-center py-10">
                <i class="fas fa-spinner fa-spin text-blue-500 text-3xl"></i>
                <p class="mt-2 text-gray-600">Loading order details...</p>
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-4 flex justify-end border-t">
            <button onclick="closeModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg mr-2">
                Close
            </button>
            <button id="updateStatusBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                Update Status
            </button>
        </div>
    </div>
</div>


<script src="/public/js/technicians/tech-dashboard.js" defer </script>
<?php require_once VIEWS . 'partials/footer.php'; ?>
