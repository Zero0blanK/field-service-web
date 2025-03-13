
// View details button handling
const viewButtons = document.querySelectorAll('.view-details-btn');
const detailsDialog = document.querySelector('.work-order-details-dialog');
const detailsContent = document.getElementById('orderDetailsContent');

// View order details
window.viewOrderDetails = function(orderId) {
    // Fetch order details via AJAX
    fetch(`/user/work-order/get-details?id=${orderId}`)
        .then(response => response.json())
        .then(data => {
            // Populate the modal content
            document.getElementById('orderDetailsContent').innerHTML = formatOrderDetails(data);
            // Show the modal
            workOrderModal.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error fetching work order details:', error.message);
            alert('Failed to load work order details:', error.message);
    });
};

// Cancel work order function
window.cancelWorkOrder = function(orderId) {
    // Show confirmation dialog
    document.getElementById('cancelConfirmModal').classList.remove('hidden');
    
    // Set the order ID on the confirm button for reference
    document.getElementById('confirmCancelBtn').setAttribute('data-order-id', orderId);
};

window.closeModal = function() {
    document.getElementById('workOrderModal').classList.add('hidden');
};

// Close confirmation dialog
window.closeCancelDialog = function() {
    document.getElementById('cancelConfirmModal').classList.add('hidden');
};

// Format order details for display
function formatOrderDetails(data) {
    if (!data.success || !data.order) {
        return `<p>Error loading order details.</p>`;
    }
    const order = data.order;
    return `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Order ID</p>
                <p class="font-medium">#${order.order_id}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                <p class="font-medium">${order.status}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Title</p>
                <p class="font-medium">${order.title}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Priority</p>
                <p class="font-medium">${order.priority}</p>
            </div>
            <div class="col-span-2">
                <p class="text-sm text-gray-500">Description</p>
                <p>${order.description}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Location</p>
                <p class="font-medium">${order.location}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Scheduled Date</p>
                <p class="font-medium">${order.scheduled_date ? order.scheduled_date : 'Not scheduled'}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Created At</p>
                <p class="font-medium">${order.created_at}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Completed At</p>
                <p class="font-medium">${order.completion_date ? order.completion_date : 'Not completed'}</p>
            </div>
            ${order.technician_name ? `
            <div class="col-span-2">
                <p class="text-sm text-gray-500">Technician</p>
                <p class="font-medium">${order.technician_name} (${order.technician_phone})</p>
            </div>` : ''}
        </div>
    `;
}

document.getElementById('closeModalBtn').addEventListener('click', closeModal);
document.getElementById('closeModal').addEventListener('click', closeModal);
// Cancel dialog close button
const cancelDialogBtn = document.getElementById('cancelDialogBtn');
if (cancelDialogBtn) {
    cancelDialogBtn.addEventListener('click', closeCancelDialog);
}

// Initialize any other event listeners that might be needed
const confirmCancelBtn = document.getElementById('confirmCancelBtn');
if (confirmCancelBtn) {
    confirmCancelBtn.addEventListener('click', function() {
        const orderId = this.getAttribute('data-order-id');
        if (orderId) {
            // Send cancel request to the server
            fetch('/user/work-order/cancel', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ order_id: orderId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
                closeCancelDialog();
            })
            .catch(error => {
                console.error('Error cancelling work order:', error);
                alert('An error occurred while cancelling the work order.');
                closeCancelDialog();
            });
        }
    });
}