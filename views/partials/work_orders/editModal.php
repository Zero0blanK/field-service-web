<!-- Edit Work Order Modal -->
<div id="editOrderModal" class="hidden fixed inset-0 bg-gray-600/50 bg-opacity-50 overflow-y-auto h-full w-full z-[100]">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Edit Work Order</h3>
            <button onclick="closeModal('editOrderModal')" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="create">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Customer</label>
                    <select name="customer_id" required 
                            class="mt-1 h-11 w-full px-2 rounded-md border-gray-800 shadow-sm">
                        <?php 
                        foreach($customers as $customer): 
                        ?>
                            <option value="<?php echo $customer['user_id']; ?>">
                                <?php echo $customer['company_name']; ?> - 
                                <?php echo $customer['customer_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Priority</label>
                    <select name="priority" required 
                            class="mt-1 h-11 px-2 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" name="title" required 
                        class="mt-1 h-11 px-3 text-lg block w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" required rows="3"
                            class="mt-1 h-15 p-3 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Scheduled Date</label>
                    <input type="date" name="scheduled_date" required placeholder="Pick a date"
                            class="mt-1 h-10 px-2 block w-full rounded-md border-gray-300 shadow-sm flatpickr-date">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Scheduled Time</label>
                    <input type="time" name="scheduled_time" required 
                            class="mt-1 h-10 px-2 block w-full rounded-md border-gray-300 shadow-sm flatpickr">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Assign Technician (Optional)</label>
                <select name="tech_id" class="mt-1 h-11 px-2 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">Select Technician</option>
                    <?php foreach($technicians as $tech): ?>
                        <option value="<?php echo $tech['tech_id']; ?>">
                            <?php echo $tech['name']; ?> - 
                            <?php echo $tech['tech_skills']; ?> 
                            (<?php echo $tech['city']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeModal('editOrderModal')"
                        class="bg-white px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" 
                        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Update Work Order
                </button>
            </div>
        </form>
    </div>
</div>