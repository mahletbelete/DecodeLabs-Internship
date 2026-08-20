document.querySelectorAll('.update-status-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const orderId = btn.dataset.orderId;
        const select  = document.querySelector(`.status-select[data-order-id="${orderId}"]`);
        const status  = select.value;

        btn.disabled    = true;
        btn.textContent = 'Saving...';

        try {
            const response = await fetch('../php/update_order_status.php', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify({ order_id: parseInt(orderId), status })
            });

            const data = await response.json();

            if (data.success) {
                const row    = btn.closest('tr');
                const badge  = row.querySelector('.badge');
                const labels  = { pending: 'Pending', confirmed: 'Confirmed', completed: 'Completed', cancelled: 'Cancelled' };
                const classes = { pending: 'badge-yellow', confirmed: 'badge-blue', completed: 'badge-green', cancelled: 'badge-red' };

                badge.textContent = labels[status];
                badge.className   = `badge ${classes[status]}`;
            } else {
                alert(data.message || 'Could not update status.');
            }

        } catch (err) {
            alert('Could not connect to the server.');
        }

        btn.disabled    = false;
        btn.textContent = 'Save';
    });
});
