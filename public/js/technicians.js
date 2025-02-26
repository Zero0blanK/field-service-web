function viewTechnicianDetails(techId) {
    const modal = document.getElementById('technicianDetailsModal');
    const content = document.getElementById('technicianDetailsContent');
    
    // Fetch technician details using AJAX
    fetch(`/dashboard/technicians/get-details?id=${techId}`)
        .then(response => response.text())
        .then(data => {
            content.innerHTML = data;
            modal.classList.remove('hidden');
        });
}

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

function viewWorkOrderDetails(orderId) {
const modal = document.getElementById('viewOrderModal');
const content = document.getElementById('viewOrderContent');

// Fetch work order details using AJAX
fetch(`/dashboard/work-orders/get-details?id=${orderId}`)
    .then(response => response.json())
    .then(data => {
        // Populate the modal with work order details
        document.getElementById('viewCustomer').textContent = data.company_name;
        document.getElementById('viewCustomerAddress').textContent = data.address;
        document.getElementById('viewCustomerPhone').textContent = data.phone;
        document.getElementById('viewCustomerEmail').textContent = data.email;
        document.getElementById('viewTitle').textContent = data.title;
        document.getElementById('viewDescription').textContent = data.description;
        document.getElementById('viewScheduledDate').textContent = formatDate(data.scheduled_date);
        document.getElementById('viewScheduledTime').textContent = formatTime(data.scheduled_time);
        document.getElementById('viewTechnician').textContent = data.tech_name || 'Unassigned';
        document.getElementById('viewCreatedAt').textContent = formatDateTime(data.created_at);

        // Set Status with appropriate styling
        const statusElem = document.getElementById('viewStatus');
        statusElem.textContent = data.status.replace('_', ' ').toUpperCase();
        statusElem.className = 'mt-1 p-2 rounded-full text-sm inline-block';

        const statusColors = getStatusColor(data.status);
        statusElem.classList.add(statusColors.bg, statusColors.text);

        // Set Priority with appropriate styling
        const priorityElem = document.getElementById('viewPriority');
        priorityElem.textContent = data.priority.toUpperCase();
        priorityElem.className = 'mt-1 p-2 rounded-full text-sm inline-block';
        
        const priorityColors = getStatusColor(data.priority);
        priorityElem.classList.add(priorityColors.bg, priorityColors.text);

        // Open the modal
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    });
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

function editTechnician(techId) {
    // Navigate to edit page or show edit modal

}

// Close modals when clicking outside
window.onclick = function(event) {
    const createModal = document.getElementById('createTechnicianModal');
    const detailsModal = document.getElementById('technicianDetailsModal');
    
    if (event.target == createModal) {
        createModal.classList.add('hidden');
    }
    
    if (event.target == detailsModal) {
        detailsModal.classList.add('hidden');
    }
}
