function viewCustomerDetails(customerId) {
    const modal = document.getElementById('customerDetailsModal');
    const content = document.getElementById('customerDetailsContent');
    
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
    }
    
    if (event.target == detailsModal) {
        detailsModal.classList.add('hidden');
    }
}

const searchInput = document.getElementById('searchInput');
const searchSpinner = document.getElementById('searchSpinner');
const tableBody = document.querySelector('tbody');
let debounceTimer;

searchInput.addEventListener('input', function(e) {
    // Show spinner
    searchSpinner.classList.remove('hidden');
    
    // Clear previous timer
    clearTimeout(debounceTimer);
    
    // Debounce the search
    debounceTimer = setTimeout(() => {
        const searchTerm = e.target.value.trim();
        
        // Make AJAX request
        fetch(`/controllers/customers.php?search=${encodeURIComponent(searchTerm)}&ajax=1`)  // Add /controllers/ to the path
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Clear current table contents
            tableBody.innerHTML = '';
            
            if (data.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No results found
                        </td>
                    </tr>
                `;
                return;
            }
            
            // Add new results
            data.forEach(company => {
                tableBody.innerHTML += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">${company.company_name || 'N/A'}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">${company.name || 'N/A'}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500">
                                <p><i class="fas fa-envelope mr-2 text-gray-400"></i>${company.email || 'N/A'}</p>
                                <p><i class="fas fa-phone mr-2 text-gray-400"></i>${company.phone || 'N/A'}</p>
                                <p><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>${company.address || 'N/A'}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">
                                <p>Total: ${company.total_orders || 0}</p>
                                <p>Completed: ${company.completed_orders || 0}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="viewCustomerDetails(${company.customer_id})"
                                    class="text-indigo-600 hover:text-indigo-900 hover:bg-indigo-100 border border-indigo-600 px-2 py-1 rounded">
                                Details
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            // Hide spinner
            searchSpinner.classList.add('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            searchSpinner.classList.add('hidden');
        });
    }, 300); // Debounce delay of 300ms
});