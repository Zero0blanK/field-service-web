<!-- View Work Order Modal -->
<div id="viewOrderModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Work Order Details</h3>
            <button onclick="closeModal('viewOrderModal')" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Customer</label>
                    <p id="viewCustomer" class="mt-1 p-2 w-full rounded-md bg-gray-50"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Priority</label>
                    <p id="viewPriority" class="mt-1 p-2 rounded-full text-sm inline-block"></p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Title</label>
                <p id="viewTitle" class="mt-1 p-2 w-full rounded-md bg-gray-50"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <p id="viewDescription" class="mt-1 p-2 w-full rounded-md bg-gray-50 min-h-[75px] whitespace-pre-wrap"></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Scheduled Date</label>
                    <p id="viewScheduledDate" class="mt-1 p-2 w-full rounded-md bg-gray-50"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Scheduled Time</label>
                    <p id="viewScheduledTime" class="mt-1 p-2 w-full rounded-md bg-gray-50"></p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Assigned Technician</label>
                <p id="viewTechnician" class="mt-1 p-2 w-full rounded-md bg-gray-50"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <p id="viewStatus" class="mt-1 p-2 rounded-full text-sm inline-block"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Created At</label>
                <p id="viewCreatedAt" class="mt-1 p-2 w-full rounded-md bg-gray-50"></p>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button onclick="openEditModal(currentOrder)" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    <i class="fas fa-edit mr-2"></i>Edit
                </button>
                
                <button onclick="closeModal('viewOrderModal')"
                        class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>