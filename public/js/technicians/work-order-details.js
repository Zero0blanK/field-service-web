function startWork(orderId) {
    if (confirm('Are you sure you want to start working on this order?')) {
        fetch('ajax/update_order_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                order_id: orderId,
                status: 'in_progress'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to start work: ' + data.message);
            }
        });
    }
}

function rescheduleOrder(orderId) {
    const newDate = prompt('Enter new scheduled date (YYYY-MM-DD):');
    const newTime = prompt('Enter new scheduled time (HH:MM):');
    
    if (newDate && newTime) {
        fetch('ajax/reschedule_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                order_id: orderId,
                new_date: newDate,
                new_time: newTime
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to reschedule: ' + data.message);
            }
        });
    }
}

function completeWork(orderId) {
    document.getElementById('completeWorkModal').classList.remove('hidden');
    document.getElementById('completeWorkModal').classList.add('flex');
}

function closeModal() {
    document.getElementById('completeWorkModal').classList.remove('flex');
    document.getElementById('completeWorkModal').classList.add('hidden');
}


// Handle Complete Work Form
document.getElementById('completeWorkForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('ajax/complete_work_order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'work_order_history.php';
        } else {
            alert('Failed to complete work order: ' + data.message);
        }
    });
});