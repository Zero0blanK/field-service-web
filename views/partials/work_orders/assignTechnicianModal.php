<!-- Assign Technician Modal -->
<div id="assignTechModal" class="hidden fixed inset-0 bg-gray-600/50 bg-opacity-50 overflow-y-auto h-full w-full z-[100]">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-900">Work Order Assignment</h3>
            <button onclick="closeModal('assignTechModal')" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Work Order Overview Section -->
        <div id="workOrderOverview" class="mb-6 p-4 bg-gray-50 rounded-lg">
            <!-- Populated via JavaScript -->
        </div>

        <!-- Available Technicians Section -->
        <div class="mb-4">
            <h4 class="text-lg font-bold mb-3">Available Technicians</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Skills</th>
                            <th class="px-4 py-2 text-left">City</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Current Orders</th>
                            <th class="px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody id="availableTechsList">
                        <!-- Populated via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>