<?php require_once 'partials/header.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>

<!-- Main Content -->
<div class="flex-1 ml-64 p-8">

    <!-- Companies Table -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Companies</h2>
            <div>
                <div class="w-[450px] relative">
                    <input type="text" 
                        id="searchInput"
                        class="w-full px-4 py-2.5 pl-10 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="Search companies by name or city...">
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
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="min-h-[400px]">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($companies)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No companies found
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($companies as $company): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($company['company_name'] ?? 'N/A'); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo htmlspecialchars($company['name'] ?? 'N/A'); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500">
                                            <p><i class="fas fa-envelope mr-2 text-gray-400"></i><?php echo htmlspecialchars($company['email'] ?? 'N/A'); ?></p>
                                            <p><i class="fas fa-phone mr-2 text-gray-400"></i><?php echo htmlspecialchars($company['phone'] ?? 'N/A'); ?></p>
                                            <p><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i><?php echo htmlspecialchars($company['address'] ?? 'N/A'); ?></p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">
                                            <p>Total: <?php echo (int)($company['total_orders'] ?? 0); ?></p>
                                            <p>Completed: <?php echo (int)($company['completed_orders'] ?? 0); ?></p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="viewCustomerDetails(<?php echo (int)$company['customer_id']; ?>)"
                                                class="text-indigo-600 hover:text-indigo-900 hover:bg-indigo-100 border border-indigo-600 px-2 py-1 rounded">
                                            Details
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination for Companies -->
            <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
                <nav class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing page <span class="font-medium"><?php echo $page; ?></span> of <span class="font-medium"><?php echo $companies_pages; ?></span>
                        </p>
                    </div>
                    <div>
                        <ul class="flex pl-0 list-none rounded my-2">
                            <?php if ($page > 1): ?>
                                <li>
                                    <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-1 text-indigo-600 hover:bg-indigo-100 rounded-md">Previous</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = max(1, $page - 2); $i <= min($companies_pages, $page + 2); $i++): ?>
                                <li>
                                    <a href="?page=<?php echo $i; ?>" class="px-3 py-1 <?php echo $i == $page ? 'bg-indigo-600 text-white' : 'text-indigo-600 hover:bg-indigo-100'; ?> rounded-md"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $companies_pages): ?>
                                <li>
                                    <a href="?page=<?php echo $page + 1; ?>" class="px-3 py-1 text-indigo-600 hover:bg-indigo-100 rounded-md">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <!-- Residents Table -->
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Residents</h2>
            <div>
                <div class="w-[450px] relative">
                    <input type="text" 
                        id="searchInput"
                        class="w-full px-4 py-2.5 pl-10 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="Search residents by name or city...">
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
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="min-h-[400px]">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($residents)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    No residents found
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($residents as $resident): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($resident['name'] ?? 'N/A'); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500">
                                            <p><i class="fas fa-envelope mr-2 text-gray-400"></i><?php echo htmlspecialchars($resident['email'] ?? 'N/A'); ?></p>
                                            <p><i class="fas fa-phone mr-2 text-gray-400"></i><?php echo htmlspecialchars($resident['phone'] ?? 'N/A'); ?></p>
                                            <p><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i><?php echo htmlspecialchars($resident['address'] ?? 'N/A'); ?></p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">
                                            <p>Total: <?php echo (int)($resident['total_orders'] ?? 0); ?></p>
                                            <p>Completed: <?php echo (int)($resident['completed_orders'] ?? 0); ?></p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="viewCustomerDetails(<?php echo (int)$resident['customer_id']; ?>)"
                                                class="text-indigo-600 hover:text-indigo-900 hover:bg-indigo-100 border border-indigo-600 px-2 py-1 rounded">
                                            Details
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination for Residents -->
            <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
                <nav class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing page <span class="font-medium"><?php echo $page; ?></span> of <span class="font-medium"><?php echo $residents_pages; ?></span>
                        </p>
                    </div>
                    <div>
                        <ul class="flex pl-0 list-none rounded my-2">
                            <?php if ($page > 1): ?>
                                <li>
                                    <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-1 text-indigo-600 hover:bg-indigo-100 rounded-md">Previous</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = max(1, $page - 2); $i <= min($residents_pages, $page + 2); $i++): ?>
                                <li>
                                    <a href="?page=<?php echo $i; ?>" class="px-3 py-1 <?php echo $i == $page ? 'bg-indigo-600 text-white' : 'text-indigo-600 hover:bg-indigo-100'; ?> rounded-md"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $residents_pages): ?>
                                <li>
                                    <a href="?page=<?php echo $page + 1; ?>" class="px-3 py-1 text-indigo-600 hover:bg-indigo-100 rounded-md">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</div>
</div>

<?php require_once 'partials/customers/detailsModal.php'; ?>
<script src="/public/js/customers.js"></script>
<?php require_once 'partials/footer.php'; ?>
