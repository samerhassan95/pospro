/**
 * Table Reservation API Integration - Override Functions
 * This file overrides existing localStorage functions with API calls
 */

// Override existing functions when the page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 Initializing API integration...');
    
    // Initialize tables from API
    if (typeof initializeTablesFromAPI === 'function') {
        initializeTablesFromAPI();
    }
    
    // Override localStorage functions with API calls
    overrideLocalStorageFunctions();
});

function overrideLocalStorageFunctions() {
    // Override saveTablePosition function
    if (typeof saveTablePosition !== 'undefined') {
        window.originalSaveTablePosition = saveTablePosition;
        window.saveTablePosition = async function(tableName, tableElement) {
            try {
                await saveTablePositionToAPI(tableName, tableElement);
            } catch (error) {
                console.error('Error saving table position via API, falling back to localStorage:', error);
                if (window.originalSaveTablePosition) {
                    window.originalSaveTablePosition(tableName, tableElement);
                }
            }
        };
    }

    // Override saveCustomTable function
    if (typeof saveCustomTable !== 'undefined') {
        window.originalSaveCustomTable = saveCustomTable;
        window.saveCustomTable = async function(tableElement) {
            try {
                await saveCustomTableAPI(tableElement);
            } catch (error) {
                console.error('Error saving custom table via API, falling back to localStorage:', error);
                if (window.originalSaveCustomTable) {
                    window.originalSaveCustomTable(tableElement);
                }
            }
        };
    }

    // Override checkReservationTimes function
    if (typeof checkReservationTimes !== 'undefined') {
        window.originalCheckReservationTimes = checkReservationTimes;
        window.checkReservationTimes = async function() {
            try {
                console.log('⏰ checkReservationTimes called (API version)');
                const reservations = await fetchReservationsFromAPI();
                console.log('⏰ Current reservations from API:', reservations);
                
                const now = new Date();
                const currentDate = now.toISOString().split('T')[0];
                const currentTime = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
                
                // Remove all existing badges
                document.querySelectorAll('.reservation-badge').forEach(badge => badge.remove());
                
                reservations.forEach(reservation => {
                    const table = document.querySelector(`[data-table="${reservation.tableName}"]`);
                    if (!table) return;
                    
                    // Add reservation badge to blocked tables
                    if (table.classList.contains('blocked')) {
                        const badge = document.createElement('div');
                        badge.className = 'reservation-badge';
                        badge.textContent = 'R';
                        badge.title = `Reserved for ${reservation.customerName}`;
                        badge.onclick = function(e) {
                            e.stopPropagation();
                            showReservationDetailsAPI(reservation);
                        };
                        table.appendChild(badge);
                    }
                });
                
                console.log('⏰ checkReservationTimes completed (API version)');
            } catch (error) {
                console.error('Error checking reservations via API, falling back to localStorage:', error);
                if (window.originalCheckReservationTimes) {
                    window.originalCheckReservationTimes();
                }
            }
        };
    }

    // Override rotateTable function
    if (typeof rotateTable !== 'undefined') {
        window.originalRotateTable = rotateTable;
        window.rotateTable = async function(table) {
            try {
                const tableName = table.getAttribute('data-table');
                await rotateTableAPI(tableName, 90);
                
                // Update DOM
                const currentRotation = parseInt(table.getAttribute('data-rotation') || '0');
                const newRotation = (currentRotation + 90) % 360;
                table.setAttribute('data-rotation', newRotation);
                
                console.log(`Table ${tableName} rotated to ${newRotation}° via API`);
            } catch (error) {
                console.error('Error rotating table via API, falling back to localStorage:', error);
                if (window.originalRotateTable) {
                    window.originalRotateTable(table);
                }
            }
        };
    }

    console.log('✅ API integration functions overridden');
}

// Update reservation confirmation handler
function updateReservationConfirmHandler() {
    const confirmReservationBtn = document.getElementById('confirm-reservation');
    if (confirmReservationBtn) {
        // Remove existing event listeners by cloning the element
        const newBtn = confirmReservationBtn.cloneNode(true);
        confirmReservationBtn.parentNode.replaceChild(newBtn, confirmReservationBtn);
        
        // Add new API-based event listener
        newBtn.addEventListener('click', async function() {
            console.log('✅ Confirm reservation clicked (API version)!');
            
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

            // Validate required fields
            if (!customerName || !date || !time) {
                alert('Please fill in customer name, date and time');
                return;
            }

            // Validate: guests cannot exceed table chairs
            if (guests > selectedTableForReservation.chairs) {
                alert(`Number of guests (${guests}) cannot exceed table capacity (${selectedTableForReservation.chairs} chairs)`);
                return;
            }

            const tableName = selectedTableForReservation.name;
            console.log('💾 Creating reservation via API:', {tableName, customerName, phone, date, time, guests, notes});

            try {
                // Get table ID from element
                const tableElement = selectedTableForReservation.element;
                let tableId = tableElement.getAttribute('data-table-id');
                
                // If no table ID, try to find it from API
                if (!tableId) {
                    const tables = await fetchTablesFromAPI();
                    const table = tables.find(t => t.name === tableName);
                    if (table) {
                        tableId = table.id;
                        tableElement.setAttribute('data-table-id', tableId);
                    }
                }

                // Create reservation via API
                const reservationData = {
                    tableId: tableId,
                    customerName: customerName,
                    customerPhone: phone,
                    date: date,
                    time: time,
                    guests: guests,
                    notes: notes
                };

                await createReservationAPI(reservationData);
                console.log('💾 Reservation created via API');

                // Update table status
                selectedTableForReservation.element.classList.remove('free', 'utilized');
                selectedTableForReservation.element.classList.add('blocked');
                console.log('Table status changed to blocked');

                // Add reservation badge
                console.log('✅ Calling checkReservationTimes to add badge...');
                if (typeof checkReservationTimes === 'function') {
                    await checkReservationTimes();
                }
                console.log('✅ Badge added, now closing modal...');

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

                alert(`Reservation confirmed for ${customerName} at table ${tableName}`);
            } catch (error) {
                console.error('Error creating reservation:', error);
                alert('Error creating reservation: ' + error.message);
            }
        });
        
        console.log('✅ Reservation confirm handler updated for API');
    }
}

// Update table order save handler
function updateTableOrderSaveHandler() {
    const saveTableOrderBtn = document.getElementById('save-table-order');
    if (saveTableOrderBtn) {
        // Remove existing event listeners by cloning the element
        const newBtn = saveTableOrderBtn.cloneNode(true);
        saveTableOrderBtn.parentNode.replaceChild(newBtn, saveTableOrderBtn);
        
        // Add new API-based event listener
        newBtn.addEventListener('click', async function() {
            const customerName = document.getElementById('order-customer-name').value;
            const guests = document.getElementById('order-guests').value;
            const orderItems = document.getElementById('order-items').value;
            const notes = document.getElementById('order-notes').value;
            const time = document.getElementById('order-time').value;
            const status = document.getElementById('order-table-status').value;

            if (!customerName) {
                alert('Please enter customer name');
                return;
            }

            if (selectedTable) {
                const tableName = selectedTable.getAttribute('data-table');
                
                try {
                    if (status === 'utilized') {
                        // Get table ID
                        let tableId = selectedTable.getAttribute('data-table-id');
                        if (!tableId) {
                            const tables = await fetchTablesFromAPI();
                            const table = tables.find(t => t.name === tableName);
                            if (table) {
                                tableId = table.id;
                                selectedTable.setAttribute('data-table-id', tableId);
                            }
                        }

                        // Create order via API
                        const orderData = {
                            tableId: tableId,
                            customerName: customerName,
                            guests: parseInt(guests),
                            items: orderItems,
                            notes: notes,
                            time: time
                        };

                        const result = await createTableOrderAPI(orderData);
                        
                        // Store order ID for future reference
                        if (result.data && result.data.id) {
                            selectedTable.setAttribute('data-order-id', result.data.id);
                        }

                        // Update table status
                        selectedTable.classList.remove('free', 'blocked');
                        selectedTable.classList.add('utilized');

                        // Add complete order button
                        addCompleteOrderButton(selectedTable);
                        
                        console.log('Order created via API:', result);
                    } else if (status === 'completed') {
                        // Complete order via API
                        const orderId = selectedTable.getAttribute('data-order-id');
                        if (orderId) {
                            await completeTableOrderAPI(orderId);
                        }
                        
                        // Update table status
                        selectedTable.classList.remove('utilized', 'blocked');
                        selectedTable.classList.add('free');
                        
                        // Remove complete button
                        const completeBtn = selectedTable.querySelector('.complete-order-btn');
                        if (completeBtn) completeBtn.remove();
                    }

                    console.log('Order saved via API:', {table: tableName, customer: customerName, status: status});
                } catch (error) {
                    console.error('Error saving order via API:', error);
                    alert('Error saving order: ' + error.message);
                    return;
                }
            }

            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('tableOrderModal'));
            modal.hide();

            // Reset form
            document.getElementById('order-customer-name').value = '';
            document.getElementById('order-guests').value = '1';
            document.getElementById('order-items').value = '';
            document.getElementById('order-notes').value = '';
            document.getElementById('order-table-status').value = 'utilized';

            if (status === 'completed') {
                alert('Order completed! Table is now free.');
            } else {
                alert('Order saved successfully!');
            }
        });
        
        console.log('✅ Table order save handler updated for API');
    }
}

// Initialize API handlers when DOM is ready
setTimeout(() => {
    updateReservationConfirmHandler();
    updateTableOrderSaveHandler();
}, 1000);

console.log('✅ Table Reservation API Override module loaded');