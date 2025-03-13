<?php require_once VIEWS . 'partials/header.php'; ?>
<?php require_once VIEWS . 'partials/technicians/sidebar.php'; ?>


<div class="flex-1 ml-64 p-8">
    <h1 class="text-3xl font-bold mb-6">Work Order History</h1>

    <!-- Filters -->
    <form method="GET" class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border rounded">
                    <option value="">All Statuses</option>
                    <option value="assigned" <?php echo ($filter_status === 'assigned') ? 'selected' : ''; ?>>Assigned</option>
                    <option value="completed" <?php echo ($filter_status === 'completed') ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo ($filter_status === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Start Date</label>
                <input type="date" name="start_date" value="<?php echo htmlspecialchars($filter_start_date ?? ''); ?>" 
                        class="w-full px-3 py-2 border rounded">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">End Date</label>
                <input type="date" name="end_date" value="<?php echo htmlspecialchars($filter_end_date ?? ''); ?>" 
                        class="w-full px-3 py-2 border rounded">
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completion Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach($work_orders as $order): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">#<?php echo $order['order_id']; ?></td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium"><?php echo $order['company_name']; ?></div>
                        <div class="text-sm text-gray-500"><?php echo $order['customer_name']; ?></div>
                    </td>
                    <td class="px-6 py-4"><?php echo htmlspecialchars($order['title']); ?></td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                            <?php
                                switch($order['status']) {
                                    case 'pending':
                                        echo 'bg-gray-100 text-gray-800';
                                        break;
                                    case 'assigned':
                                        echo 'bg-blue-100 text-blue-800';
                                        break;
                                    case 'in_progress':
                                        echo 'bg-yellow-100 text-yellow-800';
                                        break;
                                    case 'completed':
                                        echo 'bg-green-100 text-green-800';
                                        break;
                                    case 'cancelled':
                                        echo 'bg-red-100 text-red-800';
                                        break;
                                }
                            ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <?php echo $order['completion_date'] ? date('M d, Y', strtotime($order['completion_date'])) : 'N/A'; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <button onclick="viewOrderDetails(<?php echo $order['order_id']; ?>)" 
                                class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $filter_status; ?>&start_date=<?php echo $filter_start_date; ?>&end_date=<?php echo $filter_end_date; ?>" 
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Previous
                    </a>
                <?php endif; ?>
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $filter_status; ?>&start_date=<?php echo $filter_start_date; ?>&end_date=<?php echo $filter_end_date; ?>" 
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
                            <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $filter_status; ?>&start_date=<?php echo $filter_start_date; ?>&end_date=<?php echo $filter_end_date; ?>" 
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Previous</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        <?php endif; ?>

                        <?php
                        // Display page numbers
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);
                        
                        for ($i = $start; $i <= $end; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&status=<?php echo $filter_status; ?>&start_date=<?php echo $filter_start_date; ?>&end_date=<?php echo $filter_end_date; ?>" 
                                class="<?php echo $i == $page ? 'bg-blue-50 border-blue-500 text-blue-600' : 'border-gray-300 text-gray-500 hover:bg-gray-50'; ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $filter_status; ?>&start_date=<?php echo $filter_start_date; ?>&end_date=<?php echo $filter_end_date; ?>" 
                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Next</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="/public/js/technicians/work-order-history.js"></script>
<?php require_once VIEWS . 'partials/footer.php'; ?>
