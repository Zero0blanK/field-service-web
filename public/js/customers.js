function viewCustomerDetails(customerId) {
    const modal = document.getElementById('customerDetailsModal');
    const content = document.getElementById('customerDetailsContent');

    document.body.style.overflow = 'hidden';

    // Fetch customer details using AJAX
    fetch(`/dashboard/customers/get-details?id=${customerId}`)
        .then(response => response.text())
        .then(data => {
            content.innerHTML = data;
            modal.classList.remove('hidden');
        });
}

// Close modals when clicking outside
window.onclick = function(event) {
    const createModal = document.getElementById('createCustomerModal');
    const detailsModal = document.getElementById('customerDetailsModal');
    
    if (event.target == createModal) {
        createModal.classList.add('hidden');
        document.body.style.overflow = '';
    }
    
    if (event.target == detailsModal) {
        detailsModal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

const searchInput = document.getElementById('searchInput');
const searchSpinner = document.getElementById('searchSpinner');
const tableBody = document.querySelector('tbody');

document.addEventListener('DOMContentLoaded', function() {
    // Setup companies search
    const companiesSearchInput = document.getElementById('companiesSearchInput');
    if (companiesSearchInput) {
        setupSearch(companiesSearchInput, 'companies');
    }
    
    // Setup residents search
    const residentsSearchInput = document.getElementById('residentsSearchInput');
    if (residentsSearchInput) {
        setupSearch(residentsSearchInput, 'residents');
    }
});

function setupSearch(searchInput, tableType) {
    let debounceTimer;
    
    searchInput.addEventListener('input', function() {
        // Clear the previous timer
        clearTimeout(debounceTimer);
        
        // Show the spinner
        const spinnerId = `${tableType}SearchSpinner`;
        const spinner = document.getElementById(spinnerId);
        if (spinner) spinner.classList.remove('hidden');
        
        // Set a new timer
        debounceTimer = setTimeout(() => {
            performSearch(this.value, tableType);
        }, 500); // Wait for 500ms after typing stops
    });
}

function performSearch(query, tableType) {
    // Make AJAX request to search endpoint
    fetch(`?search=${encodeURIComponent(query)}&type=${tableType}&ajax=1`)
        .then(response => response.json())
        .then(data => {
            // Update the appropriate table with search results
            updateTable(data, tableType);
            
            // Hide spinner
            const spinnerId = `${tableType}SearchSpinner`;
            const spinner = document.getElementById(spinnerId);
            if (spinner) spinner.classList.add('hidden');
        })
        .catch(error => {
            console.error('Search error:', error);
            
            // Hide spinner on error
            const spinnerId = `${tableType}SearchSpinner`;
            const spinner = document.getElementById(spinnerId);
            if (spinner) spinner.classList.add('hidden');
        });
}

function updateTable(data, tableType) {
    // Get the appropriate table body - first table for companies, second for residents
    const tableIndex = tableType === 'companies' ? 0 : 1;
    const tableBody = document.querySelectorAll('tbody')[tableIndex];
    
    // Clear existing table rows
    tableBody.innerHTML = '';
    
    // If no results, show empty message
    if (data.length === 0) {
        const emptyRow = document.createElement('tr');
        const colspan = tableType === 'companies' ? '5' : '4';
        emptyRow.innerHTML = `
            <td colspan="${colspan}" class="px-6 py-4 text-center text-gray-500">
                No results found
            </td>
        `;
        tableBody.appendChild(emptyRow);
        return;
    }
    
    // Add search results to table
    data.forEach(item => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        
        // Escape HTML to prevent XSS attacks
        const escapeHtml = (str) => {
            if (!str) return 'N/A';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        };
        
        if (tableType === 'companies') {
            // Company table row
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${escapeHtml(item.company_name)}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${escapeHtml(item.name)}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-500">
                        <p><i class="fas fa-envelope mr-2 text-gray-400"></i>${escapeHtml(item.email)}</p>
                        <p><i class="fas fa-phone mr-2 text-gray-400"></i>${escapeHtml(item.phone)}</p>
                        <p><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>${escapeHtml(item.address)}</p>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500">
                        <p>Total: ${parseInt(item.total_orders) || 0}</p>
                        <p>Completed: ${parseInt(item.completed_orders) || 0}</p>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button onclick="viewCustomerDetails(${parseInt(item.customer_id)})"
                            class="text-indigo-600 hover:text-indigo-900 hover:bg-indigo-100 border border-indigo-600 px-2 py-1 rounded">
                        Details
                    </button>
                </td>
            `;
        } else {
            // Residents table row
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${escapeHtml(item.name)}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-500">
                        <p><i class="fas fa-envelope mr-2 text-gray-400"></i>${escapeHtml(item.email)}</p>
                        <p><i class="fas fa-phone mr-2 text-gray-400"></i>${escapeHtml(item.phone)}</p>
                        <p><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>${escapeHtml(item.address)}</p>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500">
                        <p>Total: ${parseInt(item.total_orders) || 0}</p>
                        <p>Completed: ${parseInt(item.completed_orders) || 0}</p>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button onclick="viewCustomerDetails(${parseInt(item.customer_id)})"
                            class="text-indigo-600 hover:text-indigo-900 hover:bg-indigo-100 border border-indigo-600 px-2 py-1 rounded">
                        Details
                    </button>
                </td>
            `;
        }
        
        tableBody.appendChild(row);
    });
    
    // Hide pagination when showing search results
    const paginationSections = document.querySelectorAll('.bg-gray-50.px-4.py-3.border-t');
    if (paginationSections && paginationSections.length > 0) {
        paginationSections[tableIndex].style.display = 'none';
    } else if (paginationSections && paginationSections.length > 0) {
        paginationSections[tableIndex].style.display = 'block';
    }
}