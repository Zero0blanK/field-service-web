<?php require_once VIEWS . 'partials/header.php'; ?>
<?php require_once VIEWS . 'partials/customers/sidebar.php'; ?>

<script src="/public/js/customers/work-order-history.js" defer></script>

<div class="flex-1 ml-64 p-8">
    <h1 class="text-3xl font-bold mb-6">Work Order Requests</h1>

    <!-- Filters -->
    <form method="GET" class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Order By</label>
                <select name="orderBy" class="w-full px-3 py-2 border rounded">
                    <option value="work_orders.order_id" <?php echo ($orderBy === 'work_orders.order_id') ? 'selected' : ''; ?>>Ticket</option>
                    <option value="work_orders.priority" <?php echo ($orderBy === 'work_orders.priority') ? 'selected' : ''; ?>>Priority</option>
                    <option value="work_orders.scheduled_date" <?php echo ($orderBy === 'work_orders.scheduled_date') ? 'selected' : ''; ?>>Date</option>
                    <option value="work_orders.status" <?php echo ($orderBy === 'work_orders.status') ? 'selected' : ''; ?>>Status</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Filter By</label>
                <select name="filterBy" class="w-full px-3 py-2 border rounded">
                    <option value="pending" <?php echo ($filterBy === 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="assigned" <?php echo ($filterBy === 'assigned') ? 'selected' : ''; ?>>Assigned</option>
                    <option value="in-progress" <?php echo ($filterBy === 'in-progress') ? 'selected' : ''; ?>>In-Progress</option>
                    <option value="completed" <?php echo ($filterBy === 'completed') ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo ($filterBy === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Sort By</label>
                <select name="sortBy" class="w-full px-3 py-2 border rounded">
                    <option value="">None</option>
                    <option value="ASC" <?php echo ($sortBy === 'ASC') ? 'selected' : ''; ?>>Ascending</option>
                    <option value="DESC" <?php echo ($sortBy === 'DESC') ? 'selected' : ''; ?>>Descending</option>
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Apply Filters
                </button>
                <a href="?" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Work Order Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scheduled Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Technician</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach($work_orders as $order): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">#<?php echo $order['order_id']; ?></td>
                    <td class="px-6 py-4"><?php echo htmlspecialchars($order['title']); ?></td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                            <?php
                            switch($order['priority']) {
                                case 'high':
                                    echo 'bg-red-100 text-red-800';
                                    break;
                                case 'medium':
                                    echo 'bg-yellow-100 text-yellow-800';
                                    break;
                                default:
                                    echo 'bg-green-100 text-green-800';
                            }
                            ?>">
                            <?php echo ucfirst($order['priority']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                            <?php
                            switch($order['status']) {
                                case 'pending':
                                    echo 'bg-yellow-100 text-yellow-800';
                                    break;
                                case 'assigned':
                                    echo 'bg-blue-100 text-blue-800';
                                    break;
                                case 'in-progress':
                                    echo 'bg-purple-100 text-purple-800';
                                    break;
                                case 'completed':
                                    echo 'bg-green-100 text-green-800';
                                    break;
                                case 'cancelled':
                                    echo 'bg-red-100 text-red-800';
                                    break;
                                default:
                                    echo 'bg-gray-100 text-gray-800';
                            }
                            ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4"><?php echo htmlspecialchars($order['location']); ?></td>
                    <td class="px-6 py-4">
                        <?php echo $order['scheduled_date'] ? date('M d, Y', strtotime($order['scheduled_date'])) : 'Not Scheduled'; ?>
                        <?php if($order['scheduled_time']): ?>
                            <span class="text-sm text-gray-500"><?php echo date('g:i A', strtotime($order['scheduled_time'])); ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php if($order['technician_name']): ?>
                            <div class="text-sm font-medium"><?php echo htmlspecialchars($order['technician_name']); ?></div>
                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($order['technician_phone']); ?></div>
                        <?php else: ?>
                            <span class="text-gray-500">Not Assigned</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <button onclick="viewOrderDetails(<?php echo $order['order_id']; ?>)" 
                                class="text-blue-600 hover:text-blue-900 mr-2">
                            <i class="fas fa-eye"></i>
                        </button>
                        <?php if($order['status'] == 'pending' || $order['status'] == 'assigned'): ?>
                        <button onclick="cancelWorkOrder(<?php echo $order['order_id']; ?>)" 
                                class="text-red-600 hover:text-red-900">
                            <i class="fas fa-times"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(empty($work_orders)): ?>
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">No work orders found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination (if needed) -->
        <?php if (isset($total_pages) && $total_pages > 1): ?>
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&orderBy=<?php echo $orderBy; ?>&filterBy=<?php echo $filterBy; ?>&sortBy=<?php echo $sortBy; ?>" 
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Previous
                    </a>
                <?php endif; ?>
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&orderBy=<?php echo $orderBy; ?>&filterBy=<?php echo $filterBy; ?>&sortBy=<?php echo $sortBy; ?>" 
                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Next
                    </a>
                <?php endif; ?>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing
                        <span class="font-medium"><?php echo (($page - 1) * $records_per_page) + 1; ?></span>
                        to
                        <span class="font-medium"><?php echo min($page * $records_per_page, $total_records); ?></span>
                        of
                        <span class="font-medium"><?php echo $total_records; ?></span>
                        results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&orderBy=<?php echo $orderBy; ?>&filterBy=<?php echo $filterBy; ?>&sortBy=<?php echo $sortBy; ?>" 
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>

                        <?php
                        // Display page numbers
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);
                        
                        for ($i = $start; $i <= $end; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&orderBy=<?php echo $orderBy; ?>&filterBy=<?php echo $filterBy; ?>&sortBy=<?php echo $sortBy; ?>" 
                                class="<?php echo $i == $page ? 'bg-blue-50 border-blue-500 text-blue-600' : 'border-gray-300 text-gray-500 hover:bg-gray-50'; ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&orderBy=<?php echo $orderBy; ?>&filterBy=<?php echo $filterBy; ?>&sortBy=<?php echo $sortBy; ?>" 
                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal for Work Order Details -->
<div id="workOrderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-4/5 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="text-xl font-semibold">Work Order Details</h3>
            <button id="closeModal" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="orderDetailsContent" class="py-4">
            <!-- Content will be loaded via JavaScript -->
        </div>
        <div class="border-t pt-3 flex justify-end">
            <button id="closeModalBtn" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 close-dialog-btn">Close</button>
        </div>
    </div>
</div>

<!-- Confirmation Dialog for Cancellation -->
<div id="cancelConfirmModal" class="cancelConfirmModal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-4/5 md:w-2/3 lg:w-1/3 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Confirm Cancellation</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to cancel this work order?
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    This action cannot be undone.
                </p>
            </div>
            <div class="flex justify-center space-x-4 mt-3">
                <button id="cancelDialogBtn" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 cancel-dialog-btn">
                    No, Keep It
                </button>
                <button id="confirmCancelBtn" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 confirm-cancel-btn">
                    Yes, Cancel Order
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>