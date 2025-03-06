<div class="bg-white rounded-lg shadow-lg overflow-hidden max-w-4xl mx-auto">
    <!-- Header with customer info -->
    <div class="relative bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white">
        <button onclick="document.getElementById('customerDetailsModal').classList.add('hidden'); document.body.style.overflow = '';"
                class="absolute top-4 right-4 text-white hover:text-gray-200 transition-colors">
            <i class="fas fa-times text-lg"></i>
        </button>
        
        <div class="flex items-center">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-blue-600 mr-4 shadow-md">
                <span class="text-2xl font-bold"><?php echo substr($customer['name'], 0, 1); ?></span>
            </div>
            <div>
                <h2 class="text-2xl font-bold mb-1"><?php echo $customer['company_name'] ? $customer['company_name'] : 'Resident'; ?></h2>
                <div class="flex items-center mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <?php echo $stats['active_orders'] > 0 ? 'Active' : 'Inactive'; ?>
                    </span>
                    <span class="ml-3 text-sm opacity-75"><?php echo $stats['active_orders']; ?> active orders</span>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6">
        <!-- Contact Info Card -->
        <div class="flex justify-start items-center">
            <h3 class="text-lg font-medium mb-4 flex items-center text-gray-700">
                <i class="fas fa-id-card mr-2 text-blue-500"></i>
                Contact Information
            </h3>
        </div>
        <div class="bg-gray-50 rounded-lg p-5 shadow-sm mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start">
                    <i class="fas fa-user mt-1 mr-3 text-gray-400"></i>
                    <div>
                        <p class="text-sm text-gray-500">Name</p>
                        <p class="font-medium"><?php echo $customer['name']; ?></p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-envelope mt-1 mr-3 text-gray-400"></i>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="font-medium"><?php echo $customer['email']; ?></p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-phone-alt mt-1 mr-3 text-gray-400"></i>
                    <div>
                        <p class="text-sm text-gray-500">Phone</p>
                        <p class="font-medium"><?php echo $customer['phone']; ?></p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-map-marker-alt mt-1 mr-3 text-gray-400"></i>
                    <div>
                        <p class="text-sm text-gray-500">Address</p>
                        <p class="font-medium"><?php echo $customer['address']; echo ', ' . $customer['city']; echo ', ' . $customer['zipcode']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="flex justify-start items-center mb-4">
            <h3 class="text-lg font-medium flex items-center text-gray-700">
                <i class="fas fa-chart-bar mr-2 text-blue-500"></i>
                Service Statistics
            </h3>
        </div>
        <div class="bg-gray-50 rounded-lg p-5 shadow-sm mb-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-3 bg-white rounded-lg shadow-sm">
                    <p class="text-2xl font-bold text-blue-600"><?php echo $stats['total_orders']; ?></p>
                    <p class="text-sm text-gray-500">Total Orders</p>
                </div>
                <div class="text-center p-3 bg-white rounded-lg shadow-sm">
                    <p class="text-2xl font-bold text-green-600"><?php echo $stats['completed_orders']; ?></p>
                    <p class="text-sm text-gray-500">Completed</p>
                </div>
                <div class="text-center p-3 bg-white rounded-lg shadow-sm">
                    <p class="text-2xl font-bold text-orange-600"><?php echo $stats['active_orders']; ?></p>
                    <p class="text-sm text-gray-500">Active Orders</p>
                </div>
                <div class="text-center p-3 bg-white rounded-lg shadow-sm">
                    <p class="text-2xl font-bold text-indigo-600"><?php echo round($stats['avg_completion_time'] ?? 0); ?>h</p>
                    <p class="text-sm text-gray-500">Avg. Completion</p>
                </div>
            </div>
        </div>

        <!-- Work Orders -->
        <div>
            <div class="flex justify-start items-center mb-4">
                <h3 class="text-lg font-medium flex items-center text-gray-700">
                    <i class="fas fa-clipboard-list mr-2 text-blue-500"></i>
                    Service History
                </h3>
            </div>
            <?php if (!empty($work_orders)): ?>
                <div class="space-y-3">
                    <?php foreach($work_orders as $order): ?>
                        <div class="p-4 border border-gray-200 hover:border-blue-300 transition-colors rounded-lg bg-white hover:bg-blue-50 shadow-sm hover:shadow cursor-pointer">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="font-medium text-gray-900"><?php echo $order['title']; ?></h4>
                                    <p class="text-sm text-gray-500 flex items-center">
                                        <i class="fas fa-calendar-alt mr-1 text-xs"></i>
                                        <?php echo formatDate($order['created_at']); ?>
                                    </p>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-sm font-medium text-gray-900">
                                        <?php echo $order['tech_name'] ?? 'Unassigned'; ?>
                                    </span>
                                    <?php $priorityColors = getStatusColor($order['priority']); ?>
                                    <span class="px-2 py-0.5 mt-1 rounded-full text-xs font-medium <?php echo $priorityColors['bg'] . ' ' . $priorityColors['text']; ?>">
                                        <?php echo ucfirst($order['priority']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="flex justify-start items-center mt-3 pt-3 border-t border-gray-100">
                                <?php $statusColors = getStatusColor($order['status']); ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $statusColors['bg'] . ' ' . $statusColors['text']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                        <i class="fas fa-clipboard text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-1">No work orders found</h4>
                    <p class="text-gray-500">This customer has no service history</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>