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
                <?php if (empty($work_orders)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No work orders found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($work_orders as $order): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4"><?php echo formatDate($order['created_at']); ?></td>
                            <td class="px-6 py-4"><?php echo $order['title']; ?></td>
                            <td class="px-6 py-4"><?php echo $order['tech_name'] ?? 'Unassigned'; ?></td>
                            <td class="px-6 py-4">
                                <?php $statusColors = getStatusColor($order['status']); ?>
                                <span class="px-2 py-1 rounded-full text-xs <?php echo $statusColors['bg'] . ' ' . $statusColors['text']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <?php $priorityColors = getStatusColor($order['priority']); ?>
                                <span class="px-2 py-1 rounded-full text-xs <?php echo $priorityColors['bg'] . ' ' . $priorityColors['text']; ?>">
                                    <?php echo ucfirst($order['priority']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>