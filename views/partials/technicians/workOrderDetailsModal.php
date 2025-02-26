<!-- Work Order Details Modal -->
<div id="viewOrderModal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-0 border-0 w-full max-w-4xl shadow-2xl rounded-lg bg-white overflow-hidden">
        <!-- Header -->
        <div class="relative bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white">
            <button onclick="document.getElementById('viewOrderModal').classList.add('hidden'); document.body.style.overflow = 'auto';" 
                    class="absolute top-4 right-4 text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
            <h2 class="text-2xl font-bold mb-1" id="viewTitle"></h2>
            <div class="flex items-center mt-2">
                <span id="viewPriority" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mr-3"></span>
                <span id="viewStatus" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"></span>
            </div>
        </div>
        
        <!-- Content -->
        <div class="p-6" id="viewOrderContent">
            <!-- Quick Info Cards Row -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="rounded-lg bg-gray-50 p-4 shadow-sm">
                    <p class="text-sm text-gray-500 mb-1">Scheduled Date</p>
                    <p class="font-medium text-gray-900" id="viewScheduledDate"></p>
                </div>
                <div class="rounded-lg bg-gray-50 p-4 shadow-sm">
                    <p class="text-sm text-gray-500 mb-1">Scheduled Time</p>
                    <p class="font-medium text-gray-900" id="viewScheduledTime"></p>
                </div>
                <div class="rounded-lg bg-gray-50 p-4 shadow-sm">
                    <p class="text-sm text-gray-500 mb-1">Created At</p>
                    <p class="font-medium text-gray-900" id="viewCreatedAt"></p>
                </div>
                <div class="rounded-lg bg-gray-50 p-4 shadow-sm">
                    <p class="text-sm text-gray-500 mb-1">Technician</p>
                    <p class="font-medium text-gray-900" id="viewTechnician"></p>
                </div>
            </div>
            
            <!-- Main Content Section -->
            <div class="space-y-6">
                <!-- Customer Section -->
                <div class="bg-gray-50 rounded-lg p-5 shadow-sm">
                    <h3 class="text-lg font-medium mb-3 flex items-center text-gray-700">
                        <i class="fas fa-building mr-2 text-blue-500"></i>
                        Customer Information
                    </h3>
                    <div class="space-y-1">
                        <p class="text-gray-900" id="viewCustomer"></p>
                        <p class="text-gray-600" id="viewCustomerAddress"></p>
                        <div class="flex items-center mt-2">
                            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                                <i class="fas fa-phone-alt mr-1"></i>
                                <span id="viewCustomerPhone"></span>
                            </a>
                            <span class="mx-2 text-gray-300">|</span>
                            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                                <i class="fas fa-envelope mr-1"></i>
                                <span id="viewCustomerEmail"></span>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Description Section -->
                <div class="bg-gray-50 rounded-lg p-5 shadow-sm">
                    <h3 class="text-lg font-medium mb-3 flex items-center text-gray-700">
                        <i class="fas fa-clipboard-list mr-2 text-blue-500"></i>
                        Work Order Details
                    </h3>
                    <div class="prose max-w-none text-gray-800" id="viewDescription"></div>
                </div>
            </div>
            
            <!-- Footer Actions -->
            <div class="border-t border-gray-200 mt-6 pt-6 flex flex-col sm:flex-row justify-between items-center">
                <div class="flex space-x-2 mb-4 sm:mb-0">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Order
                    </button>
                    <button class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors flex items-center">
                        <i class="fas fa-print mr-2"></i>
                        Print
                    </button>
                </div>
                <div class="flex space-x-2">
                    <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center">
                        <i class="fas fa-check mr-2"></i>
                        Complete Order
                    </button>
                    <button class="px-4 py-2 bg-gray-200 text-red-600 rounded-md hover:bg-gray-300 transition-colors flex items-center">
                        <i class="fas fa-ban mr-2"></i>
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>