<?php require_once VIEWS . 'partials/header.php'; ?>
<?php require_once VIEWS . 'partials/technicians/sidebar.php'; ?>


<div class="flex-1 ml-64 p-8">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <!-- Order Header -->
        <div class="bg-gray-50 px-6 py-4 border-b">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">
                    Work Order #<?php echo $order['order_id']; ?>
                </h1>
                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                    <?php
                    switch($order['status']) {
                        case 'completed':
                            echo 'bg-green-100 text-green-800';
                            break;
                        case 'in_progress':
                            echo 'bg-blue-100 text-blue-800';
                            break;
                        case 'pending':
                            echo 'bg-yellow-100 text-yellow-800';
                            break;
                        default:
                            echo 'bg-gray-100 text-gray-800';
                    }
                    ?>">
                    <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                </span>
            </div>
        </div>

        <!-- Order Details Grid -->
        <div class="grid md:grid-cols-2 gap-6 p-6">
            <!-- Customer Information -->
            <div>
                <h2 class="text-xl font-semibold mb-4">Customer Details</h2>
                <div class="bg-gray-50 p-4 rounded">
                    <p><strong>Company:</strong> <?php echo $order['company_name']; ?></p>
                    <p><strong>Name:</strong> <?php echo $order['customer_name']; ?></p>
                    <p><strong>Email:</strong> <?php echo $order['customer_email']; ?></p>
                    <p><strong>Phone:</strong> <?php echo $order['customer_phone']; ?></p>
                </div>
            </div>

            <!-- Work Order Details -->
            <div>
                <h2 class="text-xl font-semibold mb-4">Order Information</h2>
                <div class="bg-gray-50 p-4 rounded">
                    <p><strong>Title:</strong> <?php echo $order['title']; ?></p>
                    <p><strong>Description:</strong> <?php echo $order['description']; ?></p>
                    <p><strong>Priority:</strong> 
                        <span class="
                            <?php
                            switch($order['priority']) {
                                case 'low':
                                    echo 'text-green-600';
                                    break;
                                case 'medium':
                                    echo 'text-blue-600';
                                    break;
                                case 'high':
                                    echo 'text-yellow-600';
                                    break;
                                case 'urgent':
                                    echo 'text-red-600';
                                    break;
                            }
                            ?>">
                            <?php echo ucfirst($order['priority']); ?>
                        </span>
                    </p>
                    <p><strong>Scheduled:</strong> 
                        <?php echo date('M d, Y', strtotime($order['scheduled_date'])); ?> 
                        at 
                        <?php echo date('h:i A', strtotime($order['scheduled_time'])); ?>
                    </p>
                    <?php if (!empty($required_skills)): ?>
                        <p><strong>Required Skills:</strong> 
                            <?php echo implode(', ', $required_skills); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Action Section -->
        <div class="bg-gray-50 px-6 py-4 border-t flex justify-between items-center">
            <?php if (in_array($order['status'], ['pending', 'assigned'])): ?>
                <div class="space-x-4">
                    <button onclick="startWork(<?php echo $order['order_id']; ?>)" 
                            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        Start Work
                    </button>
                    <button onclick="rescheduleOrder(<?php echo $order['order_id']; ?>)" 
                            class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                        Reschedule
                    </button>
                </div>
            <?php elseif ($order['status'] === 'in_progress'): ?>
                <div class="space-x-4">
                    <button onclick="completeWork(<?php echo $order['order_id']; ?>)" 
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Complete Work Order
                    </button>
                </div>
            <?php endif; ?>
            <a href="technician_dashboard.php" class="text-gray-600 hover:text-gray-800">
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Modal for Completing Work Order -->
<div id="completeWorkModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold mb-4">Complete Work Order</h2>
        <form id="completeWorkForm">
            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Work Completed Description
                </label>
                <textarea name="completion_notes" rows="4" 
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                            placeholder="Describe the work completed..."></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Work Duration (Hours)
                </label>
                <input type="number" name="work_duration" step="0.5" min="0" 
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex justify-between">
                <button type="button" onclick="closeModal()" 
                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" 
                        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Complete Work Order
                </button>
            </div>
        </form>
    </div>
</div>

<script src="/public/js/technicians/work-order-details.js"></script>
<?php require_once VIEWS . 'partials/footer.php'; ?>