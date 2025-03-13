// Modal Functions
function viewOrderDetails(orderId) {
    // Show modal
    document.getElementById('orderDetailsModal').classList.remove('hidden');
    
    // Fetch order details with AJAX
    fetch(`/technicians/dashboard/get-details?action=get_details&id=${orderId}`)
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.error) {
                document.getElementById('modalContent').innerHTML = `
                    <div class="bg-red-100 text-red-700 p-4 rounded-lg">
                        <p>${data.error}</p>
                    </div>
                `;
                return;
            }
            // Update modal with order details
            document.getElementById('modalTitle').innerHTML = `Work Order #${data.order_id}: ${data.title}`;
            
            // Format the customer information and order details
            const customerInfo = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h4 class="text-lg font-semibold mb-2">Customer Information</h4>
                        <p class="mb-1"><span class="font-medium">Name:</span> ${data.customer_name}</p>
                        ${data.company_name ? `<p class="mb-1"><span class="font-medium">Company:</span> ${data.company_name}</p>` : ''}
                        <p class="mb-1"><span class="font-medium">Email:</span> ${data.email}</p>
                        <p class="mb-1"><span class="font-medium">Phone:</span> ${data.phone}</p>
                        <p class="mb-1"><span class="font-medium">Address:</span> ${data.address}, ${data.city}, ${data.zipcode}</p>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-2">Order Information</h4>
                        <p class="mb-1"><span class="font-medium">Status:</span> <span class="capitalize">${data.status.replace('_', ' ')}</span></p>
                        <p class="mb-1"><span class="font-medium">Priority:</span> <span class="capitalize">${data.priority}</span></p>
                        <p class="mb-1"><span class="font-medium">Created:</span> ${new Date(data.created_at).toLocaleString()}</p>
                        <p class="mb-1"><span class="font-medium">Scheduled:</span> ${new Date(data.scheduled_date + ' ' + data.scheduled_time).toLocaleString()}</p>
                        ${data.completion_date ? `<p class="mb-1"><span class="font-medium">Completed:</span> ${new Date(data.completion_date).toLocaleString()}</p>` : ''}
                    </div>
                </div>
                <div class="mb-6">
                    <h4 class="text-lg font-semibold mb-2">Work Order Description</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        ${data.description}
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-2">Location</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        ${data.location}
                    </div>
                </div>
            `;
            
            document.getElementById('modalContent').innerHTML = customerInfo;
            
            // Update the status button based on current status
            const updateBtn = document.getElementById('updateStatusBtn');
            if (data.status === 'assigned') {
                updateBtn.innerHTML = 'Start Work';
                updateBtn.onclick = () => updateOrderStatus(data.order_id, 'in_progress');
                updateBtn.classList.remove('hidden');
            } else if (data.status === 'in_progress') {
                updateBtn.innerHTML = 'Complete Work';
                updateBtn.onclick = () => updateOrderStatus(data.order_id, 'completed');
                updateBtn.classList.remove('hidden');
            } else {
                updateBtn.classList.add('hidden');
            }
        })
        .catch(error => {
            document.getElementById('modalContent').innerHTML = `
                <div class="bg-red-100 text-red-700 p-4 rounded-lg">
                    <p>Error fetching order details. Please try again.</p>
                </div>
            `;
            console.error('Error fetching order details:', error);
        });
}

function closeModal() {
    document.getElementById('orderDetailsModal').classList.add('hidden');
}

function updateOrderStatus(orderId, newStatus) {
    fetch('/technicians/dashboard/get-details', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=update_status&id=${orderId}&status=${newStatus}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the page to refresh the work orders list
            window.location.reload();
        } else {
            alert('Error updating order status: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error updating order status:', error);
        alert('An error occurred while updating the order status.');
    });
}

function acceptOrder(orderId) {
    if (confirm('Are you sure you want to accept this work order?')) {
        updateOrderStatus(orderId, 'in_progress');
    }
}

// Close modal when clicking outside of it
window.addEventListener('click', function(event) {
    const modal = document.getElementById('orderDetailsModal');
    if (event.target === modal) {
        closeModal();
    }
});

// Close modal with Escape key
window.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});