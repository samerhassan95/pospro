// ========== UPDATED TABLE RESERVATION INTEGRATION ==========
// Replace the existing localStorage functions with these API-based functions

// Override the existing reservation confirmation function
document.addEventListener('DOMContentLoaded', function() {
    // Wait for existing code to load, then override
    setTimeout(function() {
        const confirmReservationBtn = document.getElementById('confirm-reservation');
        if (confirmReservationBtn) {
            // Remove existing event listeners by cloning
            const newBtn = confirmReservationBtn.cloneNode(true);
            confirmReservationBtn.parentNode.replaceChild(newBtn, confirmReservationBtn);
            
            // Add new API-based event listener
            newBtn.addEventListener('click', async function() {
                console.log('✅ API-based reservation confirmation');
                
                if (!selectedTableForReservation) {
                    alert('Please select a table');
                    return;
                }
                
                const customerName = document.getElementById('reservation-customer-name').value;
                const phone = document.getElementById('reservation-phone').value;
                const date = document.getElementById('reservation-date').value;
                const time = document.getElementById('reservation-time').value;
                const guests = parseInt(document.getElementById('reservation-guests').value);
                const notes = document.getElementById('reservation-notes').value;
                
                if (!customerName || !date || !time) {
                    alert('Please fill in customer name, date and time');
                    return;
                }
                
                // Create reservation via API
                const success = await window.createReservationAPI({
                    tableId: selectedTableForReservation.id || selectedTableForReservation.name,
                    tableName: selectedTableForReservation.name,
                    customerName,
                    phone,
                    date,
                    time,
                    guests,
                    notes
                });
                
                if (success) {
                    // Close modal and reset form
                    const modal = bootstrap.Modal.getInstance(document.getElementById('makeReservationModal'));
                    modal.hide();
                    
                    // Reset form
                    document.getElementById('reservation-customer-name').value = '';
                    document.getElementById('reservation-phone').value = '';
                    document.getElementById('reservation-guests').value = '2';
                    document.getElementById('reservation-notes').value = '';
                    document.getElementById('available-tables-list').style.display = 'none';
                    newBtn.disabled = true;
                    
                    alert('Reservation created successfully!');
                } else {
                    alert('Failed to create reservation. Please try again.');
                }
            });
        }
        
        // Override save table order function
        const saveTableOrderBtn = document.getElementById('save-table-order');
        if (saveTableOrderBtn) {
            const newOrderBtn = saveTableOrderBtn.cloneNode(true);
            saveTableOrderBtn.parentNode.replaceChild(newOrderBtn, saveTableOrderBtn);
            
            newOrderBtn.addEventListener('click', async function() {
                const customerName = document.getElementById('order-customer-name').value;
                const guests = document.getElementById('order-guests').value;
                const orderItems = document.getElementById('order-items').value;
                const notes = document.getElementById('order-notes').value;
                const time = document.getElementById('order-time').value;
                
                if (!customerName) {
                    alert('Please enter customer name');
                    return;
                }
                
                if (selectedTable) {
                    const tableName = selectedTable.getAttribute('data-table');
                    
                    // Create order via API
                    const success = await window.createTableOrderAPI({
                        tableId: tableName, // Will be converted to actual ID in backend
                        tableName,
                        customerName,
                        guests,
                        items: orderItems,
                        notes,
                        time
                    });
                    
                    if (success) {
                        // Close modal and reset form
                        const modal = bootstrap.Modal.getInstance(document.getElementById('tableOrderModal'));
                        modal.hide();
                        
                        // Reset form
                        document.getElementById('order-customer-name').value = '';
                        document.getElementById('order-guests').value = '1';
                        document.getElementById('order-items').value = '';
                        document.getElementById('order-notes').value = '';
                        
                        alert('Order saved successfully!');
                    } else {
                        alert('Failed to save order. Please try again.');
                    }
                }
            });
        }
        
        // Override manage reservations modal
        function openManageReservationsModalAPI() {
            console.log('📋 Opening API-based Manage Reservations modal...');
            
            fetchReservations().then(reservations => {
                const tbody = document.getElementById('reservations-table-body');
                const noReservationsMsg = document.getElementById('no-reservations-message');
                
                if (!tbody) {
                    alert('Error: Table body element not found. Please refresh the page.');
                    return;
                }
                
                tbody.innerHTML = '';
                
                if (reservations.length === 0) {
                    tbody.closest('.table-responsive').style.display = 'none';
                    noReservationsMsg.style.display = 'block';
                } else {
                    tbody.closest('.table-responsive').style.display = 'block';
                    noReservationsMsg.style.display = 'none';
                    
                    reservations.forEach(reservation => {
                        const row = document.createElement('tr');
                        
                        // Determine status
                        let status = '🟡 Reserved';
                        let statusClass = 'text-warning';
                        
                        if (reservation.status === 'arrived') {
                            status = '✅ Arrived';
                            statusClass = 'text-success';
                        } else if (reservation.status === 'cancelled') {
                            status = '❌ Cancelled';
                            statusClass = 'text-danger';
                        }
                        
                        row.innerHTML = `
                            <td><strong>${reservation.table?.table_name || 'N/A'}</strong></td>
                            <td>${reservation.customer_name}</td>
                            <td>${reservation.customer_phone || 'N/A'}</td>
                            <td>${reservation.reservation_date}</td>
                            <td>${reservation.reservation_time}</td>
                            <td>${reservation.number_of_guests}</td>
                            <td>${reservation.special_notes || '-'}</td>
                            <td class="${statusClass}">${status}</td>
                            <td>
                                ${reservation.status === 'reserved' ? `
                                    <button class="btn btn-sm btn-success mark-arrived" data-id="${reservation.id}">
                                        Guest Arrived
                                    </button>
                                    <button class="btn btn-sm btn-danger cancel-reservation" data-id="${reservation.id}" data-table="${reservation.table?.table_name}">
                                        Cancel
                                    </button>
                                ` : '-'}
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                    
                    // Add event listeners for actions
                    document.querySelectorAll('.mark-arrived').forEach(btn => {
                        btn.addEventListener('click', async function() {
                            const reservationId = this.getAttribute('data-id');
                            const success = await markGuestArrived(reservationId);
                            if (success) {
                                openManageReservationsModalAPI(); // Refresh modal
                                alert('Guest marked as arrived!');
                            } else {
                                alert('Failed to mark guest as arrived.');
                            }
                        });
                    });
                    
                    document.querySelectorAll('.cancel-reservation').forEach(btn => {
                        btn.addEventListener('click', async function() {
                            if (confirm('Are you sure you want to cancel this reservation?')) {
                                const reservationId = this.getAttribute('data-id');
                                const tableName = this.getAttribute('data-table');
                                const success = await cancelReservation(reservationId);
                                if (success) {
                                    updateTableStatus(tableName, 'free');
                                    openManageReservationsModalAPI(); // Refresh modal
                                    alert('Reservation cancelled successfully');
                                } else {
                                    alert('Failed to cancel reservation.');
                                }
                            }
                        });
                    });
                }
                
                // Open modal
                const manageModal = new bootstrap.Modal(document.getElementById('manageReservationsModal'));
                manageModal.show();
            });
        }
        
        // Override manage orders modal
        function openManageOrdersModalAPI() {
            console.log('📦 Opening API-based Manage Orders modal...');
            
            fetchTableOrders().then(orders => {
                const tbody = document.getElementById('orders-table-body');
                const noOrdersMsg = document.getElementById('no-orders-message');
                
                if (!tbody) {
                    alert('Error: Table body element not found. Please refresh the page.');
                    return;
                }
                
                tbody.innerHTML = '';
                
                if (orders.length === 0) {
                    tbody.closest('.table-responsive').style.display = 'none';
                    noOrdersMsg.style.display = 'block';
                } else {
                    tbody.closest('.table-responsive').style.display = 'block';
                    noOrdersMsg.style.display = 'none';
                    
                    orders.forEach(order => {
                        const row = document.createElement('tr');
                        
                        const startedAt = new Date(order.created_at).toLocaleString();
                        
                        row.innerHTML = `
                            <td><strong>${order.table?.table_name || 'N/A'}</strong></td>
                            <td>${order.customer_name || 'N/A'}</td>
                            <td>${order.number_of_guests || 'N/A'}</td>
                            <td style="max-width: 200px; white-space: pre-wrap;">${order.order_items || '-'}</td>
                            <td>${order.special_notes || '-'}</td>
                            <td>${startedAt}</td>
                            <td>
                                <button class="btn btn-sm btn-success complete-order-api" data-id="${order.id}" data-table="${order.table?.table_name}">
                                    Complete
                                </button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                    
                    // Add complete functionality
                    document.querySelectorAll('.complete-order-api').forEach(btn => {
                        btn.addEventListener('click', async function() {
                            const orderId = this.getAttribute('data-id');
                            const tableName = this.getAttribute('data-table');
                            
                            if (confirm(`Complete order for table ${tableName}?`)) {
                                const success = await completeTableOrder(orderId);
                                if (success) {
                                    updateTableStatus(tableName, 'free');
                                    openManageOrdersModalAPI(); // Refresh modal
                                    alert(`Order completed! Table ${tableName} is now free.`);
                                } else {
                                    alert('Failed to complete order.');
                                }
                            }
                        });
                    });
                }
                
                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('manageOrdersModal'));
                modal.show();
            });
        }
        
        // Replace the existing button event listeners
        const btnManageAllTables = document.getElementById('btn-manage-all-tables');
        if (btnManageAllTables) {
            const newManageBtn = btnManageAllTables.cloneNode(true);
            btnManageAllTables.parentNode.replaceChild(newManageBtn, btnManageAllTables);
            newManageBtn.addEventListener('click', openManageReservationsModalAPI);
        }
        
        const btnManageOrders = document.getElementById('btn-manage-orders');
        if (btnManageOrders) {
            const newOrdersBtn = btnManageOrders.cloneNode(true);
            btnManageOrders.parentNode.replaceChild(newOrdersBtn, btnManageOrders);
            newOrdersBtn.addEventListener('click', openManageOrdersModalAPI);
        }
        
        console.log('API integration overrides applied successfully');
    }, 2000); // Wait for existing code to initialize
});

// ========== TABLE POSITION SYNC ==========

/**
 * Save table position to backend when dragged
 */
window.saveTablePositionAPI = async function(tableName, tableElement) {
    // Get table ID from backend data
    const tables = await fetchTables();
    const table = tables.find(t => t.table_name === tableName);
    
    if (table) {
        const positionData = {
            position_top: tableElement.style.top,
            position_left: tableElement.style.left,
            position_right: tableElement.style.right,
            position_bottom: tableElement.style.bottom,
            rotation: parseInt(tableElement.getAttribute('data-rotation') || '0')
        };
        
        await updateTablePosition(table.id, positionData);
    }
};

// Override the existing saveTablePosition function
window.saveTablePosition = window.saveTablePositionAPI;

console.log('Table position API integration ready');