// Include the API functions at the top of the page
<script src="{{ asset('table-reservation-api.js') }}"></script>

<script>
// Updated JavaScript functions to use backend API instead of localStorage

// Replace the existing checkReservationTimes function
async function checkReservationTimes() {
    console.log('⏰ checkReservationTimes called');
    try {
        const reservations = await fetchReservations();
        console.log('⏰ Current reservations:', reservations);

        // First, remove all existing badges
        document.querySelectorAll('.reservation-badge').forEach(badge => badge.remove());

        for (const reservation of reservations) {
            console.log('⏰ Checking reservation:', reservation);
            const table = document.querySelector(`[data-table="${reservation.table.table_name}"]`);
            if (!table) {
                console.log('⏰ Table not found:', reservation.table.table_name);
                continue;
            }

            // Add reservation badge to table
            if (table.classList.contains('blocked')) {
                console.log('⏰ Adding badge to table:', reservation.table.table_name);
                const badge = document.createElement('div');
                badge.className = 'reservation-badge';
                badge.textContent = 'R';
                badge.title = `Reserved for ${reservation.customer_name}`;
                badge.onclick = function(e) {
                    e.stopPropagation();
                    showReservationDetails(reservation, reservation.id);
                };
                table.appendChild(badge);
            }
        }
    } catch (error) {
        console.error('Error fetching reservations:', error);
    }
    console.log('⏰ checkReservationTimes completed');
}

// Replace the existing restoreTableStatuses function
async function restoreTableStatuses() {
    console.log('Restoring table statuses...');
    try {
        // Fetch reservations and orders from backend
        const [reservations, tableOrders] = await Promise.all([
            fetchReservations(),
            fetchTableOrders()
        ]);

        console.log('Reservations:', reservations);
        console.log('Table Orders:', tableOrders);

        // First, reset all tables to free
        document.querySelectorAll('.table-item').forEach(table => {
            const tableName = table.getAttribute('data-table');

            // Check if table has a reservation
            let hasReservation = false;
            for (const reservation of reservations) {
                if (reservation.table && reservation.table.table_name === tableName) {
                    hasReservation = true;
                    // Table is reserved - set to blocked
                    table.classList.remove('free', 'utilized');
                    table.classList.add('blocked');
                    console.log(`${tableName}: Reserved (blocked)`);
                    break;
                }
            }

            // If no reservation, check if table has an active order
            if (!hasReservation) {
                const hasOrder = tableOrders.some(order => order.table && order.table.table_name === tableName);
                if (hasOrder) {
                    table.classList.remove('free', 'blocked');
                    table.classList.add('utilized');
                    // Add complete order button
                    addCompleteOrderButton(table);
                    console.log(`${tableName}: Has active order (utilized)`);
                } else {
                    table.classList.remove('blocked', 'utilized');
                    table.classList.add('free');
                    console.log(`${tableName}: No reservation or order (free)`);
                }
            }
        });

        console.log('Table statuses restored from backend');
    } catch (error) {
        console.error('Error restoring table statuses:', error);
    }
}

// Replace the existing showReservationDetails function
function showReservationDetails(reservation, reservationId) {
    console.log('🔍 showReservationDetails called for:', reservation.table.table_name, reservationId);
    console.log('🔍 Reservation data:', reservation);

    document.getElementById('detail-table').textContent = reservation.table.table_name;
    document.getElementById('detail-customer').textContent = reservation.customer_name;
    document.getElementById('detail-phone').textContent = reservation.customer_phone || 'N/A';
    document.getElementById('detail-date').textContent = reservation.reservation_date;
    document.getElementById('detail-time').textContent = reservation.reservation_time;
    document.getElementById('detail-guests').textContent = reservation.number_of_guests;
    document.getElementById('detail-notes').textContent = reservation.special_notes || '-';

    // Check table current status
    const table = document.querySelector(`[data-table="${reservation.table.table_name}"]`);
    let statusText = '🟡 Reserved';
    let showGuestArrivedBtn = true;

    if (table && table.classList.contains('utilized')) {
        // Table is already utilized (guest has arrived)
        statusText = '✅ Utilized - Guest Arrived';
        showGuestArrivedBtn = false;
    } else {
        // Check if reservation time has arrived
        const now = new Date();
        const currentDate = now.toISOString().split('T')[0];
        const currentTime = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
        const reservationDateTime = new Date(reservation.reservation_date + ' ' + reservation.reservation_time);
        const currentDateTime = new Date(currentDate + ' ' + currentTime);

        if (currentDateTime >= reservationDateTime) {
            statusText = '⏰ Time Arrived - Waiting for Guest';
        }
    }

    document.getElementById('detail-status').textContent = statusText;

    // Show/hide Guest Arrived button based on status
    const guestArrivedBtn = document.getElementById('guest-arrived-btn');
    if (guestArrivedBtn) {
        guestArrivedBtn.style.display = showGuestArrivedBtn ? 'inline-block' : 'none';
    }

    // Cancel reservation button
    document.getElementById('cancel-reservation-btn').onclick = async function() {
        if (confirm('{{ __("Are you sure you want to cancel this reservation?") }}')) {
            try {
                const result = await cancelReservation(reservationId);
                if (result.success) {
                    // Update table status
                    const table = document.querySelector(`[data-table="${reservation.table.table_name}"]`);
                    if (table) {
                        table.classList.remove('blocked');
                        table.classList.add('free');
                    }

                    // Close modal and refresh
                    bootstrap.Modal.getInstance(document.getElementById('reservationDetailsModal')).hide();
                    checkReservationTimes();
                    alert('{{ __("Reservation cancelled successfully") }}');
                } else {
                    alert('{{ __("Error cancelling reservation") }}');
                }
            } catch (error) {
                console.error('Error cancelling reservation:', error);
                alert('{{ __("Error cancelling reservation") }}');
            }
        }
    };

    // Guest arrived button
    document.getElementById('guest-arrived-btn').onclick = async function() {
        console.log('🚨 Guest Arrived button clicked!');
        console.log('🚨 Reservation ID:', reservationId);
        console.log('🚨 Table:', reservation.table.table_name);

        try {
            const result = await guestArrived(reservationId);
            if (result.success) {
                const table = document.querySelector(`[data-table="${reservation.table.table_name}"]`);
                if (table) {
                    table.classList.remove('blocked');
                    table.classList.add('utilized');

                    // Remove reservation badge
                    const badge = table.querySelector('.reservation-badge');
                    if (badge) badge.remove();

                    // Open order modal
                    selectedTable = table;
                    document.getElementById('order-table-name').textContent = reservation.table.table_name;
                    document.getElementById('order-customer-name').value = reservation.customer_name;
                    document.getElementById('order-guests').value = reservation.number_of_guests;
                    document.getElementById('order-notes').value = reservation.special_notes || '';

                    bootstrap.Modal.getInstance(document.getElementById('reservationDetailsModal')).hide();
                    const orderModal = new bootstrap.Modal(document.getElementById('tableOrderModal'));
                    orderModal.show();
                }
            } else {
                alert('{{ __("Error marking guest as arrived") }}');
            }
        } catch (error) {
            console.error('Error marking guest arrived:', error);
            alert('{{ __("Error marking guest as arrived") }}');
        }
    };

    // Open modal
    const detailsModal = new bootstrap.Modal(document.getElementById('reservationDetailsModal'));
    detailsModal.show();
}

// Replace the existing openManageReservationsModal function
async function openManageReservationsModal() {
    console.log('📋 Opening Manage Reservations modal...');

    try {
        const reservations = await fetchReservations();
        console.log('📋 Reservations from backend:', reservations);
        console.log('📋 Number of reservations:', reservations.length);
        
        const tbody = document.getElementById('reservations-table-body');
        const noReservationsMsg = document.getElementById('no-reservations-message');

        if (!tbody) {
            console.error('reservations-table-body element not found!');
            alert('Error: Table body element not found. Please refresh the page.');
            return;
        }

        tbody.innerHTML = '';

        if (reservations.length === 0) {
            if (tbody.closest('.table-responsive')) {
                tbody.closest('.table-responsive').style.display = 'none';
            }
            if (noReservationsMsg) {
                noReservationsMsg.style.display = 'block';
            }
        } else {
            if (tbody.closest('.table-responsive')) {
                tbody.closest('.table-responsive').style.display = 'block';
            }
            if (noReservationsMsg) {
                noReservationsMsg.style.display = 'none';
            }

            // Get current date/time for status check
            const now = new Date();
            const currentDate = now.toISOString().split('T')[0];
            const currentTime = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

            for (const reservation of reservations) {
                const row = document.createElement('tr');

                // Determine status
                let status = '🟡 Reserved';
                let statusClass = 'text-warning';
                const reservationDateTime = new Date(reservation.reservation_date + ' ' + reservation.reservation_time);
                const currentDateTime = new Date(currentDate + ' ' + currentTime);

                if (currentDateTime >= reservationDateTime) {
                    status = '⏰ Time Arrived';
                    statusClass = 'text-success';
                }

                row.innerHTML = `
                    <td><strong>${reservation.table.table_name}</strong></td>
                    <td>${reservation.customer_name}</td>
                    <td>${reservation.customer_phone || 'N/A'}</td>
                    <td>${reservation.reservation_date}</td>
                    <td>${reservation.reservation_time}</td>
                    <td>${reservation.number_of_guests}</td>
                    <td>${reservation.special_notes || '-'}</td>
                    <td class="${statusClass}">${status}</td>
                    <td>
                        <button class="btn btn-sm btn-danger delete-reservation" data-id="${reservation.id}" data-table="${reservation.table.table_name}">
                            {{ __('Cancel') }}
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            }

            // Add delete functionality
            document.querySelectorAll('.delete-reservation').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const reservationId = this.getAttribute('data-id');
                    const tableName = this.getAttribute('data-table');

                    if (confirm('{{ __("Are you sure you want to cancel this reservation?") }}')) {
                        try {
                            const result = await cancelReservation(reservationId);
                            if (result.success) {
                                // Update table status to free
                                const table = document.querySelector(`[data-table="${tableName}"]`);
                                if (table && table.classList.contains('blocked')) {
                                    table.classList.remove('blocked');
                                    table.classList.add('free');
                                }

                                // Reload modal
                                openManageReservationsModal();
                            } else {
                                alert('{{ __("Error cancelling reservation") }}');
                            }
                        } catch (error) {
                            console.error('Error cancelling reservation:', error);
                            alert('{{ __("Error cancelling reservation") }}');
                        }
                    }
                });
            });
        }

        // Open modal
        console.log('Opening manage reservations modal...');
        const manageModal = new bootstrap.Modal(document.getElementById('manageReservationsModal'));
        manageModal.show();
    } catch (error) {
        console.error('Error loading reservations:', error);
        alert('{{ __("Error loading reservations") }}');
    }
}

// Replace the existing search available tables functionality
async function searchAvailableTables() {
    const guests = parseInt(document.getElementById('reservation-guests').value);
    const date = document.getElementById('reservation-date').value;
    const time = document.getElementById('reservation-time').value;
    const customerName = document.getElementById('reservation-customer-name').value;

    if (!customerName || !date || !time) {
        alert('{{ __("Please fill in customer name, date and time") }}');
        return;
    }

    try {
        // Get all tables and reservations from backend
        const [allTables, reservations] = await Promise.all([
            fetchTables(),
            fetchReservations()
        ]);
        
        const availableTables = [];

        allTables.forEach(table => {
            // Check if table has enough chairs
            if (table.chair_count >= guests) {
                // Check if table is not reserved at this time
                const isReserved = reservations.some(reservation => 
                    reservation.table_id === table.id &&
                    reservation.reservation_date === date &&
                    reservation.reservation_time === time &&
                    reservation.status === 'reserved'
                );
                
                if (!isReserved && table.status === 'free') {
                    availableTables.push({
                        id: table.id,
                        name: table.table_name,
                        chairs: table.chair_count,
                        element: document.querySelector(`[data-table="${table.table_name}"]`)
                    });
                }
            }
        });

        // Display available tables
        const container = document.getElementById('available-tables-container');
        container.innerHTML = '';

        if (availableTables.length === 0) {
            container.innerHTML = '<p class="text-danger">{{ __("No available tables found for this time and guest count") }}</p>';
            document.getElementById('available-tables-list').style.display = 'block';
            return;
        }

        availableTables.forEach(table => {
            const tableBtn = document.createElement('button');
            tableBtn.className = 'btn btn-outline-success';
            tableBtn.textContent = `${table.name} (${table.chairs} {{ __("chairs") }})`;
            tableBtn.onclick = function() {
                // Remove selection from other buttons
                container.querySelectorAll('.btn').forEach(btn => {
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-success');
                });
                // Select this button
                this.classList.remove('btn-outline-success');
                this.classList.add('btn-success');
                selectedTableForReservation = table;
                document.getElementById('confirm-reservation').disabled = false;
            };
            container.appendChild(tableBtn);
        });

        document.getElementById('available-tables-list').style.display = 'block';
    } catch (error) {
        console.error('Error searching tables:', error);
        alert('{{ __("Error searching for available tables") }}');
    }
}

// Replace the existing confirm reservation functionality
async function confirmReservation() {
    console.log('✅ Confirm reservation clicked!');
    console.log('Selected table:', selectedTableForReservation);

    if (!selectedTableForReservation) {
        alert('{{ __("Please select a table") }}');
        return;
    }

    const customerName = document.getElementById('reservation-customer-name').value;
    const phone = document.getElementById('reservation-phone').value;
    const date = document.getElementById('reservation-date').value;
    const time = document.getElementById('reservation-time').value;
    const guests = parseInt(document.getElementById('reservation-guests').value);
    const notes = document.getElementById('reservation-notes').value;

    // Validate required fields
    if (!customerName || !date || !time) {
        alert('{{ __("Please fill in customer name, date and time") }}');
        return;
    }

    // Validate: guests cannot exceed table chairs
    if (guests > selectedTableForReservation.chairs) {
        alert(`{{ __("Number of guests (") }}${guests}{{ __(") cannot exceed table capacity (") }}${selectedTableForReservation.chairs}{{ __(") chairs)") }}`);
        return;
    }

    try {
        // Create reservation object
        const reservationData = {
            table_id: selectedTableForReservation.id,
            customer_name: customerName,
            customer_phone: phone,
            reservation_date: date,
            reservation_time: time,
            number_of_guests: guests,
            special_notes: notes
        };

        console.log('Creating reservation:', reservationData);

        // Save to backend
        const result = await createReservation(reservationData);
        
        if (result.success) {
            // Update table status to blocked
            if (selectedTableForReservation.element) {
                selectedTableForReservation.element.classList.remove('free', 'utilized');
                selectedTableForReservation.element.classList.add('blocked');
            }

            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('makeReservationModal'));
            modal.hide();

            // Reset form
            document.getElementById('reservation-customer-name').value = '';
            document.getElementById('reservation-phone').value = '';
            document.getElementById('reservation-guests').value = '2';
            document.getElementById('reservation-notes').value = '';
            document.getElementById('available-tables-list').style.display = 'none';
            document.getElementById('confirm-reservation').disabled = true;
            selectedTableForReservation = null;

            // Refresh reservation badges
            checkReservationTimes();

            alert('{{ __("Reservation created successfully!") }}');
            console.log('Reservation saved:', result.data);
        } else {
            alert(result.message || '{{ __("Error creating reservation") }}');
        }
    } catch (error) {
        console.error('Error creating reservation:', error);
        alert('{{ __("Error creating reservation") }}');
    }
}
</script>