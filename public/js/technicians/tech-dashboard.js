function acceptOrder(orderId) {
    if (confirm('Are you sure you want to accept this work order?')) {
        fetch('ajax/update_order_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                order_id: orderId,
                status: 'accepted'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to accept order: ' + data.message);
            }
        });
    }
}

function rejectOrder(orderId) {
    let reason = prompt('Please provide a reason for rejecting this work order:');
    if (reason !== null) {
        fetch('ajax/update_order_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                order_id: orderId,
                status: 'rejected',
                reason: reason
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to reject order: ' + data.message);
            }
        });
    }
}

function viewOrderDetails(orderId) {
    window.location.href = 'order_details.php?id=' + orderId;
}