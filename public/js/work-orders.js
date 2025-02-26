// Initialize Flatpickr date/time pickers
flatpickr(".flatpickr-date", {
    enableTime: false,
    dateFormat: "Y-m-d",
    minDate: "today"
});

flatpickr(".flatpickr-time", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});

function getStatusColor(status) {
    const colors = {
        // Priority Status Colors
        'low': { bg: 'bg-gray-100', text: 'text-gray-800' },
        'medium': { bg: 'bg-blue-100', text: 'text-blue-800' },
        'high': { bg: 'bg-yellow-100', text: 'text-yellow-800' },
        'urgent': { bg: 'bg-red-100', text: 'text-red-800' },

        // Status Colors
        'pending': { bg: 'bg-gray-100', text: 'text-gray-800' },
        'assigned': { bg: 'bg-blue-100', text: 'text-blue-800' },
        'in_progress': { bg: 'bg-yellow-100', text: 'text-yellow-800' },
        'on_hold': { bg: 'bg-orange-100', text: 'text-orange-800' },
        'completed': { bg: 'bg-green-100', text: 'text-green-800' },
        'cancelled': { bg: 'bg-red-100', text: 'text-red-800' }
    };
    return colors[status] || colors['pending'];
}

// Modal handlers
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function openEditModal(order) {
    closeModal('viewOrderModal');

    const modal = document.getElementById('editOrderModal');
    const form = modal.querySelector('form');
    
    // Populate the form fields
    form.querySelector('[name="customer_id"]').value = order.customer_id;
    form.querySelector('[name="priority"]').value = order.priority;
    form.querySelector('[name="title"]').value = order.title;
    form.querySelector('[name="description"]').value = order.description;
    form.querySelector('[name="scheduled_date"]').value = order.scheduled_date;
    form.querySelector('[name="scheduled_time"]').value = order.scheduled_time;
    form.querySelector('[name="tech_id"]').value = order.tech_id || '';
    
    // Add hidden input for edit action and order ID
    let actionInput = form.querySelector('[name="action"]');
    if (!actionInput) {
        actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        form.appendChild(actionInput);
    }
    actionInput.value = 'edit';

    let orderIdInput = form.querySelector('[name="order_id"]');
    if (!orderIdInput) {
        orderIdInput = document.createElement('input');
        orderIdInput.type = 'hidden';
        orderIdInput.name = 'order_id';
        form.appendChild(orderIdInput);
    }
    orderIdInput.value = order.order_id;

    openModal('editOrderModal');
}

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('bg-gray-600') && 
        event.target.classList.contains('bg-opacity-50')) {
        event.target.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Optional: Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = document.querySelectorAll('.modal:not(.hidden)');
        modals.forEach(modal => {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        });
    }
});

// Add this to your existing JavaScript
function openViewModal(order) {
    // Store current order for edit button
    window.currentOrder = order;
    
    // Set basic information
    document.getElementById('viewCustomer').textContent = (order.company_name === null) ? order.customer_name + ' (Resident)' : order.company_name+ ' - ' + order.customer_name;
    document.getElementById('viewTitle').textContent = order.title;
    document.getElementById('viewDescription').textContent = order.description;
    document.getElementById('viewScheduledDate').textContent = formatDate(order.scheduled_date);
    document.getElementById('viewScheduledTime').textContent = formatTime(order.scheduled_time);
    document.getElementById('viewTechnician').textContent = order.tech_name || 'Unassigned';
    document.getElementById('viewCreatedAt').textContent = formatDateTime(order.created_at);

    // Set Status with appropriate styling
    const statusElem = document.getElementById('viewStatus');
    statusElem.textContent = order.status.replace('_', ' ').toUpperCase();
    statusElem.className = 'mt-1 p-2 rounded-full text-sm inline-block';
    
    const statusColors = getStatusColor(order.status);
    statusElem.classList.add(statusColors.bg, statusColors.text);

    // Set Priority with appropriate styling
    const priorityElem = document.getElementById('viewPriority');
    priorityElem.textContent = order.priority.toUpperCase();
    priorityElem.className = 'mt-1 p-2 rounded-full text-sm inline-block';
    
    const priorityColors = getStatusColor(order.priority);
    priorityElem.classList.add(priorityColors.bg, priorityColors.text);

    openModal('viewOrderModal');
}

// Helper functions for date formatting
function formatDate(dateStr) {
    if (!dateStr) return 'Not scheduled';
    const date = new Date(dateStr);
    return date.toLocaleDateString();
}

function formatTime(timeStr) {
    if (!timeStr) return 'Not scheduled';
    return timeStr;
}

function formatDateTime(dateTimeStr) {
    if (!dateTimeStr) return 'Unknown';
    const date = new Date(dateTimeStr);
    return date.toLocaleString();
}
