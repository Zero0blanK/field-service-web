<?php require_once 'partials/header.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>

<!-- Main Content -->
<div class="flex-1 ml-64 p-8">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-9 col-span-1">
            <div class="flex items-center">
                <div class="p-5 rounded-full bg-indigo-100 mr-4">
                    <i class="fas fa-clock text-indigo-500"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-lg">Pending Orders</p>
                    <p class="text-3xl font-bold"><?php echo $stats['pending'];?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-9 col-span-1">
            <div class="flex items-center">
                <div class="p-5 rounded-full bg-yellow-100 mr-4">
                    <i class="fas fa-spinner text-yellow-500"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-lg">In Progress</p>
                    <p class="text-3xl font-bold"><?php echo $stats['in_progress']; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-9 col-span-1">
            <div class="flex items-center">
                <div class="p-5 rounded-full bg-green-100 mr-4">
                    <i class="fas fa-check text-green-500"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-lg">Completed</p>
                    <p class="text-3xl font-bold"><?php echo $stats['completed']; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-9 col-span-1">
            <div class="flex items-center">
                <div class="p-5 rounded-full bg-blue-100 mr-4">
                    <i class="fas fa-user-tie text-blue-500"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-lg">Technicians</p>
                    <p class="text-3xl font-bold"><?php echo $stats['total_technicians']; ?></p>
                </div>
            </div>
        </div>
        <!-- New card for active technicians -->
        <div class="bg-white rounded-lg shadow p-9 col-span-[1.5px]">
            <div class="flex items-center">
                <div class="p-5 rounded-full bg-purple-100 mr-4">
                    <i class="fas fa-user-check text-purple-500"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-lg">Active Techs</p>
                    <p class="text-3xl font-bold"><?php echo $tech_stats['active_technicians']; ?></p>
                </div>
            </div>
        </div>
    </div>
    <!-- Recent Work Orders -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Recent Work Orders</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-6 py-3 text-left">Order ID</th>
                        <th class="px-6 py-3 text-left">Customer</th>
                        <th class="px-6 py-3 text-left">Title</th>
                        <th class="px-6 py-3 text-left">Technician</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Priority</th>
                        <th class="px-6 py-3 text-left">Scheduled</th>
                        <th class="px-6 py-3 text-left">Preferred Time</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($recent_orders)): ?>
                        <?php foreach($recent_orders as $order): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">#<?php echo $order['order_id']; ?></td>
                            <td class="px-6 py-4"><?php echo $order['customer_name']; ?></td>
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
                            <td class="px-6 py-4">
                                <?php echo formatDate($order['scheduled_date']) . ' ' . formatTime($order['scheduled_time']); ?>
                            </td>
                            <td class="px-6 py-4"><?php echo ucwords($order['preferred_time_of_day']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td class="px-6 py-4" colspan="8">No work orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?php require_once 'partials/footer.php'; ?>
