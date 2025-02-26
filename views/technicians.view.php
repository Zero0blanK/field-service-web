<?php require_once 'partials/header.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>

<!-- Main Content -->
<div class="flex-1 ml-64 p-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Technicians</h1>
        <button onclick="document.getElementById('createTechnicianModal').classList.remove('hidden')" 
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
            <i class="fas fa-plus mr-2"></i>Add Technician
        </button>
    </div>

    <!-- Filter and Search Section -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status-filter" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="available">Available</option>
                    <option value="busy">Busy</option>
                    <option value="off-duty">Off Duty</option>
                </select>
            </div>
            <div class="flex-1 min-w-[250px]">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative rounded-md shadow-sm">
                    <input type="text" id="search" class="block w-full rounded-md border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Search by name, email...">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="flex-initial self-end">
                <button type="button" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Apply Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Technicians Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="min-h-[400px]">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active Orders</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Orders</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach($technicians as $tech): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 mr-3">
                                        <span class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-indigo-800 font-medium text-lg">
                                                <?php echo substr($tech['name'], 0, 1); ?>
                                            </span>
                                        </span>
                                    </div>
                                    <div class="ml-1">
                                        <div class="text-sm font-medium text-gray-900"><?php echo $tech['name']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500">
                                    <p><i class="fas fa-envelope mr-2 text-gray-400"></i><?php echo $tech['email']; ?></p>
                                    <p><i class="fas fa-phone mr-2 text-gray-400"></i><?php echo $tech['phone']; ?></p>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php 
                                        switch($tech['status']) {
                                            case 'available':
                                                echo 'bg-green-100 text-green-800';
                                                break;
                                            case 'busy':
                                                echo 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'off-duty':
                                                echo 'bg-gray-100 text-gray-800';
                                                break;
                                            default:
                                                echo 'bg-gray-100 text-gray-800';
                                        }
                                    ?>">
                                    <?php echo ucfirst($tech['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $tech['active_orders']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    <span><?php echo $tech['total_orders']; ?> total</span>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mt-1">
                                        <?php
                                        $completion_percent = $tech['total_orders'] > 0 
                                            ? round(($tech['completed_orders'] / $tech['total_orders']) * 100)
                                            : 0;
                                        ?>
                                        <div class="bg-indigo-600 h-2.5 rounded-full" style="width: <?php echo $completion_percent; ?>%"></div>
                                    </div>
                                    <span class="text-xs"><?php echo $completion_percent; ?>% completed</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex space-x-2 justify-end">
                                    <button onclick="viewTechnicianDetails(<?php echo $tech['tech_id']; ?>)"
                                            class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editTechnician(<?php echo $tech['tech_id']; ?>)"
                                            class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
            <nav class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing page <span class="font-medium"><?php echo $page; ?></span> of <span class="font-medium"><?php echo $total_pages; ?></span>
                    </p>
                </div>
                <div>
                    <ul class="flex pl-0 list-none rounded my-2">
                        <?php if ($page > 1): ?>
                            <li>
                                <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-1 text-indigo-600 hover:bg-indigo-100 rounded-md">Previous</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <li>
                                <a href="?page=<?php echo $i; ?>" class="px-3 py-1 <?php echo $i == $page ? 'bg-indigo-600 text-white' : 'text-indigo-600 hover:bg-indigo-100'; ?> rounded-md"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
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

<?php require_once 'partials/technicians/createModal.php'; ?>
<?php require_once 'partials/technicians/detailsModal.php'; ?>
<?php require_once 'partials/technicians/workOrderDetailsModal.php'; ?>
<script src="/public/js/technicians.js"></script>
<?php require_once 'partials/footer.php'; ?>
