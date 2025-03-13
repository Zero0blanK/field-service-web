async function showAvailableTechs(orderId, scheduleDate) {
    try {
        // Fetch both work order details and available technicians
        const [orderResponse, techsResponse] = await Promise.all([
            fetch(`?action=get_details&order_id=${orderId}`),
            fetch(`?action=get_techs&order_id=${orderId}&scheduled_date=${scheduleDate}`)
        ]);

        const orderDetails = await orderResponse.json();
        const techs = await techsResponse.json();

        // Update Work Order Overview
        const overviewHtml = `
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h4 class="font-medium text-lg mb-3">Work Order Details</h4>
                    <p class="text-m mb-2"><span class="font-medium">Order ID:</span> #${orderDetails.order_id}</p>
                    <p class="text-m mb-2"><span class="font-medium">Title:</span> ${orderDetails.title}</p>
                    <p class="text-m mb-2"><span class="font-medium">Priority:</span> 
                        <span class="px-4 py-2 font-medium rounded-full text-xs ${getPriorityClass(orderDetails.priority)}">
                            ${orderDetails.priority.toUpperCase()}
                        </span>
                    </p>
                    <p class="text-m mb-1"><span class="font-medium">Schedule:</span> 
                        ${formatDateTime(orderDetails.scheduled_date, orderDetails.scheduled_time)}
                    </p>
                </div>
                <div>
                    <h4 class="font-medium text-lg mb-2">Customer Details</h4>
                    <p class="text-m mb-1"><span class="font-medium">Customer Name:</span> ${orderDetails.customer_name}</p>
                    <p class="text-m mb-1"><span class="font-medium">Address:</span> ${orderDetails.address}</p>
                    <p class="text-m mb-1"><span class="font-medium">City:</span> ${orderDetails.customer_city}</p>
                    <p class="text-m mb-1"><span class="font-medium">Phone:</span> ${orderDetails.phone}</p>
                    <p class="text-m mb-1"><span class="font-medium">Email:</span> ${orderDetails.email}</p>
                    <p class="text-m mb-1"><span class="font-medium">Preferred Time:</span> 
                        ${orderDetails.preferred_time_of_day ? orderDetails.preferred_time_of_day.charAt(0).toUpperCase() + orderDetails.preferred_time_of_day.slice(1) : 'Not specified'}

                    </p>
                    <p class="text-m mb-1"><span class="font-medium">Preferred Contact Method:</span> 
                        ${orderDetails.preferred_contact_method ? orderDetails.preferred_contact_method.charAt(0).toUpperCase() + orderDetails.preferred_contact_method.slice(1) : 'Not specified'}
                    </p>
                    </div>
            </div>
        `;
        document.getElementById('workOrderOverview').innerHTML = overviewHtml;

        // Update Technicians List
        const techsList = document.getElementById('availableTechsList');
        techsList.innerHTML = '';

        techs.forEach(tech => {
            const isAvailable = tech.current_orders < 1;
            const row = `
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3">${tech.name}</td>
                    <td class="px-4 py-3 text-sm">${tech.skills || 'No skills listed'}</td>
                    <td class="px-4 py-3">${tech.city}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs ${
                            isAvailable ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                        }">
                            ${isAvailable ? 'Available' : 'Not Available'}
                        </span>
                    </td>
                    <td class="px-4 py-3">${tech.current_orders} / 1</td>
                    <td class="px-4 py-3">
                        <form method="POST" class="inline">
                            <input type="hidden" name="order_id" value="${orderId}">
                            <input type="hidden" name="tech_id" value="${tech.tech_id}">
                            <button type="submit" name="assign_technician" 
                                    class="bg-indigo-600 text-white px-3 py-1 rounded-md hover:bg-indigo-700 ${
                                        !isAvailable ? 'opacity-50 cursor-not-allowed' : ''
                                    }"
                                    ${!isAvailable ? 'disabled' : ''}>
                                Assign
                            </button>
                        </form>
                    </td>
                </tr>
            `;
            techsList.innerHTML += row;
        });

        openModal('assignTechModal');
    } catch (error) {
        console.error("Error fetching data:", error);
    }
}

function getPriorityClass(priority) {
    const classes = {
        'low': 'bg-gray-200 text-gray-800',
        'medium': 'bg-blue-100 text-blue-800',
        'high': 'bg-yellow-100 text-yellow-800',
        'urgent': 'bg-red-100 text-red-800'
    };
    return classes[priority] || classes.low;
}

function formatDateTime(date, time) {
    const formattedDate = new Date(date + ' ' + time).toLocaleString('en-US', {
        dateStyle: 'medium',
        timeStyle: 'short'
    });
    return formattedDate;
}

function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}