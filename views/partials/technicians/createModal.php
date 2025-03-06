<!-- Create Technician Modal -->
<div id="createTechnicianModal" class="hidden fixed inset-0 z-50 bg-gray-600/35 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
    <div class="relative mx-auto p-6 border w-full max-w-xl shadow-xl rounded-lg bg-white">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-xl font-semibold text-gray-900">Add New Technician</h3>
            <button onclick="document.getElementById('createTechnicianModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form method="POST" class="space-y-5">
            <input type="hidden" name="registration" value="technician_reg">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Personal Information -->
                <div class="md:col-span-2">
                    <h4 class="font-medium text-gray-700 mb-3 border-b pb-2">Personal Information</h4>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="tech_name" required placeholder="Enter full name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" required placeholder="email@example.com"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" name="password" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 text-gray-500 hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" name="phone" required placeholder="(555) 123-4567"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>

                <!-- Contact Information -->
                <div class="md:col-span-2 mt-2">
                    <h4 class="font-medium text-gray-700 mb-3 border-b pb-2">Contact Information</h4>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" name="address" required placeholder="Street address" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text" name="city" required placeholder="City"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Zip Code</label>
                    <input type="text" name="zipcode" required placeholder="Zip code"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t mt-6">
                <button type="button" 
                        onclick="document.getElementById('createTechnicianModal').classList.add('hidden')"
                        class="px-4 py-2 rounded-md border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 rounded-md bg-indigo-600 text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all">
                    Add Technician
                </button>
            </div>
        </form>
    </div>
</div>