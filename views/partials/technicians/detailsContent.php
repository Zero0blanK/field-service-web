<div class="bg-white rounded-lg shadow-lg overflow-hidden max-w-4xl mx-auto">
    <!-- Header with technician info -->
    <div class="relative bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white">
        <button onclick="document.getElementById('technicianDetailsModal').classList.add('hidden'); document.body.style.overflow = '';"
                class="absolute top-4 right-4 text-white hover:text-gray-200 transition-colors">
            <i class="fas fa-times text-lg"></i>
        </button>
        
        <div class="flex items-center">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-blue-600 mr-4 shadow-md">
                <span class="text-2xl font-bold"><?php echo substr($technician['name'], 0, 1); ?></span>
            </div>
            <div>
                <h2 class="text-2xl font-bold mb-1"><?php echo $technician['name']; ?></h2>
                <div class="flex items-center mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        <?php echo $technician['status'] == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                        <?php echo ucfirst($technician['status']); ?>
                    </span>
                    <span class="ml-3 text-sm opacity-75"><?php echo $active_orders_count; ?> active orders</span>
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
                    <i class="fas fa-envelope mt-1 mr-3 text-gray-400"></i>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="font-medium"><?php echo $technician['email']; ?></p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-phone-alt mt-1 mr-3 text-gray-400"></i>
                    <div>
                        <p class="text-sm text-gray-500">Phone</p>
                        <p class="font-medium"><?php echo $technician['phone']; ?></p>
                    </div>
                </div>
                <div class="flex items-start col-span-1 md:col-span-2">
                    <i class="fas fa-map-marker-alt mt-1 mr-3 text-gray-400"></i>
                    <div>
                        <p class="text-sm text-gray-500">City</p>
                        <p class="font-medium"><?php echo $technician['address']; echo ', ' . $technician['city']; echo ', ' . $technician['zipcode']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Work Orders -->
        <div>
            <div class="flex justify-start items-center mb-4">
                <h3 class="text-lg font-medium flex items-center text-gray-700">
                    <i class="fas fa-clipboard-list mr-2 text-blue-500"></i>
                    Work Orders
                </h3>
            </div>
            <?php if (!empty($orders_data) && is_array($orders_data)): ?>
                <div class="space-y-3">
                    <?php foreach($orders_data as $order): ?>
                        <div class="p-4 border border-gray-200 hover:border-blue-300 transition-colors rounded-lg bg-white hover:bg-blue-50 shadow-sm hover:shadow cursor-pointer">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="font-medium text-gray-900"><?php echo $order['title']; ?></h4>
                                    <p class="text-sm text-gray-500 flex items-center">
                                        <i class="fas fa-building mr-1 text-xs"></i>
                                        <?php echo $order['company_name']; ?>
                                    </p>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-sm font-medium text-gray-900">
                                        <?php echo formatTime($order['scheduled_time']); ?>
                                    </span>
                                    <?php $priorityColors = getStatusColor($order['priority']); ?>
                                    <span class="px-2 py-0.5 mt-1 rounded-full text-xs font-medium <?php echo $priorityColors['bg'] . ' ' . $priorityColors['text']; ?>">
                                        <?php echo ucfirst($order['priority']); ?>
                                    </span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mb-2 flex items-center">
                                <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>
                                <?php echo $order['address']; ?>
                            </p>
                            <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-100">
                            <?php $statusColors = getStatusColor($order['status']); ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $statusColors['bg'] . ' ' . $statusColors['text']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                                </span>
                                <button type="button" onclick="viewWorkOrderDetails(<?php echo $order['order_id']; ?>)" class="text-sm text-blue-600 hover:text-blue-800 hover:underline transition-colors">
                                    View Details
                                </button>
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
                    <p class="text-gray-500">This technician has no assigned work orders</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
