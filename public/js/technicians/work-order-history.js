// work-order-history.js
document.addEventListener('DOMContentLoaded', function() {
    // Create modal structure if it doesn't exist
    if (!document.getElementById('orderDetailsModal')) {
        const modal = document.createElement('div');
        modal.id = 'orderDetailsModal';
        modal.className = 'fixed inset-0 bg-gray-600/50 bg-opacity-50 flex items-center justify-center hidden z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-screen overflow-y-auto">
                <div class="flex justify-between items-center border-b p-4">
                    <h2 class="text-xl font-bold" id="modalTitle">Work Order Details</h2>
                    <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6" id="modalContent">
                    <div class="flex justify-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                    </div>
                </div>
                <div class="border-t p-4 flex justify-end space-x-3" id="modalFooter">
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        // Add event listener to close button
        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('orderDetailsModal').classList.add('hidden');
        });

        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    }
});

function viewOrderDetails(orderId) {
    const modal = document.getElementById('orderDetailsModal');
    const modalContent = document.getElementById('modalContent');
    const modalFooter = document.getElementById('modalFooter');

    // Show modal and loading spinner
    modal.classList.remove('hidden');
    modalContent.innerHTML = `
        <div class="flex justify-center">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
        </div>
    `;
    modalFooter.innerHTML = '';

    // Fetch work order details
    fetch('/technicians/work-order/get-details', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `order_id=${orderId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            modalContent.innerHTML = `<p class="text-red-500">${data.error}</p>`;
            return;
        }

        const order = data.order;
        
        // Format date and time
        const scheduledDate = new Date(order.scheduled_date);
        const formattedScheduleDate = scheduledDate.toLocaleDateString();
        
        let completionDateStr = 'Not completed yet';
        if (order.completion_date) {
            const completionDate = new Date(order.completion_date);
            completionDateStr = completionDate.toLocaleDateString() + ' ' + 
                                completionDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }
        
        // Generate HTML content for the modal
        modalContent.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-lg border-b pb-2 mb-2">Order Information</h3>
                    <p class="mb-1"><span class="font-medium">Order ID:</span> #${order.order_id}</p>
                    <p class="mb-1"><span class="font-medium">Title:</span> ${order.title}</p>
                    <p class="mb-1"><span class="font-medium">Status:</span> 
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                            ${order.status === 'completed' ? 'bg-green-100 text-green-800' : 
                              order.status === 'cancelled' ? 'bg-red-100 text-red-800' :
                              order.status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' :
                              'bg-gray-100 text-gray-800'}">
                            ${capitalizeFirstLetter(order.status.replace('_', ' '))}
                        </span>
                    </p>
                    <p class="mb-1"><span class="font-medium">Priority:</span> ${capitalizeFirstLetter(order.priority)}</p>
                    <p class="mb-1"><span class="font-medium">Scheduled:</span> ${formattedScheduleDate}</p>
                    <p class="mb-1"><span class="font-medium">Completion:</span> ${completionDateStr}</p>
                </div>
                
                <div>
                    <h3 class="font-semibold text-lg border-b pb-2 mb-2">Customer Information</h3>
                    <p class="mb-1"><span class="font-medium">Company:</span> ${order.company_name}</p>
                    <p class="mb-1"><span class="font-medium">Contact:</span> ${order.customer_name}</p>
                    <p class="mb-1"><span class="font-medium">Email:</span> ${order.customer_email}</p>
                    <p class="mb-1"><span class="font-medium">Phone:</span> ${order.customer_phone}</p>
                    <p class="mb-1"><span class="font-medium">Location:</span> ${order.location || `${order.address}, ${order.city}, ${order.zipcode}`}</p>
                </div>
            </div>
            
            <div class="mt-6">
                <h3 class="font-semibold text-lg border-b pb-2 mb-2">Work Order Description</h3>
                <p class="whitespace-pre-line">${order.description}</p>
            </div>
        `;
        
        // Add action buttons based on current status
        if (order.status === 'assigned') {
            modalFooter.innerHTML = `
                <button id="startWorkBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" 
                        data-order-id="${order.order_id}">
                    Start Working
                </button>
            `;
            
            document.getElementById('startWorkBtn').addEventListener('click', function() {
                updateOrderStatus(this.dataset.orderId, 'in_progress');
            });
        } else if (order.status === 'in_progress') {
            modalFooter.innerHTML = `
                <button id="completeOrderBtn" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600" 
                        data-order-id="${order.order_id}">
                    Complete Order
                </button>
            `;
            
            document.getElementById('completeOrderBtn').addEventListener('click', function() {
                updateOrderStatus(this.dataset.orderId, 'completed');
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        modalContent.innerHTML = `<p class="text-red-500">An error occurred. Please try again.</p>`;
    });
}

function updateOrderStatus(orderId, newStatus) {
    const modal = document.getElementById('orderDetailsModal');
    const modalContent = document.getElementById('modalContent');
    const modalFooter = document.getElementById('modalFooter');

    // Show loading state
    modalFooter.innerHTML = `
        <button disabled class="bg-gray-400 text-white px-4 py-2 rounded cursor-not-allowed">
            <div class="inline-block animate-spin h-4 w-4 border-t-2 border-b-2 border-white mr-2"></div>
            Processing...
        </button>
    `;

    // Send update request
    fetch('/technicians/work-order/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `order_id=${orderId}&status=${newStatus}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            modalFooter.innerHTML = `
                <p class="text-red-500">${data.error}</p>
                <button id="closeBtn" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                    Close
                </button>
            `;
            document.getElementById('closeBtn').addEventListener('click', function() {
                modal.classList.add('hidden');
            });
            return;
        }

        // Show success message
        modalContent.innerHTML = `
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="mt-2 text-lg font-medium text-gray-900">Success!</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Work order status has been updated to "${newStatus.replace('_', ' ')}".
                </p>
                <div class="mt-3">
                    <button id="refreshBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Refresh Page
                    </button>
                </div>
            </div>
        `;
        
        modalFooter.innerHTML = '';
        
        document.getElementById('refreshBtn').addEventListener('click', function() {
            window.location.reload();
        });
    })
    .catch(error => {
        console.error('Error:', error);
        modalFooter.innerHTML = `
            <p class="text-red-500">An error occurred. Please try again.</p>
            <button id="closeBtn" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                Close
            </button>
        `;
        document.getElementById('closeBtn').addEventListener('click', function() {
            modal.classList.add('hidden');
        });
    });
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}