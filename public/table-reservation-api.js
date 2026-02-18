/**
 * Table Reservation API Integration
 * Replaces localStorage with backend API calls
 */

// CSRF Token for API requests
function getCSRFToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

// API Base URLs
const API_BASE = '/api/business';
const API_ENDPOINTS = {
    tables: `${API_BASE}/tables`,
    reservations: `${API_BASE}/reservations`,
    tableOrders: `${API_BASE}/table-orders`
};

// ========== TABLE MANAGEMENT API ==========

/**
 * Fetch all tables from backend
 */
async function fetchTables() {
    try {
        const response = await fetch(API_ENDPOINTS.tables, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`,
                'X-CSRF-TOKEN': getCSRFToken(),
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        
        const result = await response.json();
        return result.success ? result.data : [];
    } catch (error) {
        console.error('Error fetching tables:', error);
        return [];
    }
}

/**
 * Create new custom table
 */
async function createTable(tableData) {
    try {
        const response = await fetch(API_ENDPOINTS.tables, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`,
                'X-CSRF-TOKEN': getCSRFToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify(tableData)
        });
        
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        
        const result = await response.json();
        return result.success ? result.data : null;
    } catch (error) {
        console.error('Error creating table:', error);
        return null;
    }
}

/**
 * Update table position and rotation
 */
async function updateTablePosition(tableId, positionData) {
    try {
        const response = await fetch(`${API_ENDPOINTS.tables}/${tableId}/position`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`,
                'X-CSRF-TOKEN': getCSRFToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify(positionData)
        });
        
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        
        const result = await response.json();
        return result.success;
    } catch (error) {
        console.error('Error updating table position:', error);
        return false;
    }
}

/**
 * Rotate table by degrees
 */
async function rotateTableAPI(tableId, degrees = 90) {
    try {
        const response = await fetch(`${API_ENDPOINTS.tables}/${tableId}/rotate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`,
                'X-CSRF-TOKEN': getCSRFToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ degrees })
        });
        
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        
        const result = await response.json();
        return result.success ? result.data : null;
    } catch (error) {
        console.error('Error rotating table:', error);
        return null;
    }
}

/**
 * Delete custom table
 */
async function deleteTable(tableId) {
    try {
        const response = await fetch(`${API_ENDPOINTS.tables}/${tableId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`,
                'X-CSRF-TOKEN': getCSRFToken(),
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        
        const result = await response.json();
        return result.success;
    } catch (error) {
        console.error('Error deleting table:', error);
        return false;
    }
}

// ========== RESERVATION MANAGEMENT API ==========

/**
 * Fetch all reservations
 */
async function fetchReservations() {
    try {
        const response = await fetch(API_ENDPOINTS.reservations, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`,
                'X-CSRF-TOKEN': getCSRFToken(),
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        
        const result = await response.json();
        return result.success ? result.data : [];
    } catch (error) {
        console.error('Error fetching reservations:', error);
        return [];
    }
}

/**
 * Create new reservation
 */
async function createReservation(reservationData) {
    try {
        const response = await fetch(API_ENDPOINTS.reservations, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`,
                'X-CSRF-TOKEN': getCSRFToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify(reservationData)
        });
        
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        
        const result = await response.json();
        return result.success ? result.data : null;
    } catch (error) {
        console.error('Error creating reservation:', error);
        return null;
    }
}

/**
 * Mark guest as arrived
 */
async function markGuestArrived(reservationId) {
    try {
        const response = await fetch(`${API_ENDPOINTS.reservations}/${reservationId}/arrived`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`,
                'X-CSRF-TOKEN': getCSRFToken(),
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        
        const result = await response.json();
        return result.success;
    } catch (error) {
        console.error('Error marking guest arrived:', error);
        return false;
    }
}

/**
 * Cancel reservation
 */
async function cancelReservation(reservationId) {
    try {
        const response = await fetch(`${API_ENDPOINTS.reservations}/${reservationId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`,
                'X-CSRF-TOKEN': getCSRFToken(),
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        
        const result = await response.json();
        return result.success;
    } catch (error) {
        console.error('Error cancelling reservation:', error);
        return false;
    }
}

// ========== TABLE ORDER MANAGEMENT API ==========

/**
 * Fetch active table orders
 */
async function fetchTableOrders() {
    try {
        const response = await fetch(API_ENDPOINTS.tableOrders, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`,
                'X-CSRF-TOKEN': getCSRFToken(),
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        
        const result = await response.json();
        return result.success ? result.data : [];
    } catch (error) {
        console.error('Error fetching table orders:', error);
        return [];
    }
}

/**
 * Create new table order
 */
async function createTableOrder(orderData) {
    try {
        const response = await fetch(API_ENDPOINTS.tableOrders, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`,
                'X-CSRF-TOKEN': getCSRFToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify(orderData)
        });
        
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        
        const result = await response.json();
        return result.success ? result.data : null;
    } catch (error) {
        console.error('Error creating table order:', error);
        return null;
    }
}

/**
 * Complete table order
 */
async function completeTableOrder(orderId) {
    try {
        const response = await fetch(`${API_ENDPOINTS.tableOrders}/${orderId}/complete`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`,
                'X-CSRF-TOKEN': getCSRFToken(),
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        
        const result = await response.json();
        return result.success;
    } catch (error) {
        console.error('Error completing table order:', error);
        return false;
    }
}

// ========== HELPER FUNCTIONS ==========

/**
 * Find table element by name
 */
function findTableElement(tableName) {
    return document.querySelector(`[data-table="${tableName}"]`);
}

/**
 * Update table status in DOM
 */
function updateTableStatus(tableName, status) {
    const table = findTableElement(tableName);
    if (table) {
        table.classList.remove('free', 'blocked', 'utilized');
        table.classList.add(status);
    }
}

/**
 * Add reservation badge to table
 */
function addReservationBadge(tableName, reservation) {
    const table = findTableElement(tableName);
    if (table) {
        // Remove existing badge
        const existingBadge = table.querySelector('.reservation-badge');
        if (existingBadge) existingBadge.remove();
        
        // Add new badge
        const badge = document.createElement('div');
        badge.className = 'reservation-badge';
        badge.textContent = 'R';
        badge.title = `Reserved for ${reservation.customer_name}`;
        badge.onclick = function(e) {
            e.stopPropagation();
            showReservationDetails(reservation);
        };
        table.appendChild(badge);
    }
}

/**
 * Add complete order button to table
 */
function addCompleteOrderButton(tableName, order) {
    const table = findTableElement(tableName);
    if (table) {
        // Remove existing button
        const existingBtn = table.querySelector('.complete-order-btn');
        if (existingBtn) existingBtn.remove();
        
        // Add new button
        const completeBtn = document.createElement('div');
        completeBtn.className = 'complete-order-btn';
        completeBtn.title = 'Complete Order & Free Table';
        completeBtn.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        `;
        
        completeBtn.addEventListener('click', async function(e) {
            e.stopPropagation();
            
            if (confirm(`Complete order for table ${tableName}?`)) {
                const success = await completeTableOrder(order.id);
                if (success) {
                    updateTableStatus(tableName, 'free');
                    this.remove();
                    alert(`Order completed! Table ${tableName} is now free.`);
                } else {
                    alert('Failed to complete order. Please try again.');
                }
            }
        });
        
        table.appendChild(completeBtn);
    }
}

// ========== INITIALIZATION ==========

/**
 * Load and sync table data from backend
 */
async function syncTablesFromBackend() {
    console.log('Syncing tables from backend...');
    
    const tables = await fetchTables();
    const reservations = await fetchReservations();
    const orders = await fetchTableOrders();
    
    // Update table statuses based on backend data
    tables.forEach(table => {
        // Check for active reservation
        const reservation = reservations.find(r => r.table_id === table.id && r.status === 'reserved');
        if (reservation) {
            updateTableStatus(table.table_name, 'blocked');
            addReservationBadge(table.table_name, reservation);
            return;
        }
        
        // Check for active order
        const order = orders.find(o => o.table_id === table.id && o.status === 'in_progress');
        if (order) {
            updateTableStatus(table.table_name, 'utilized');
            addCompleteOrderButton(table.table_name, order);
            return;
        }
        
        // Default to free
        updateTableStatus(table.table_name, 'free');
    });
    
    console.log('Tables synced successfully');
}

// ========== REPLACEMENT FUNCTIONS FOR EXISTING CODE ==========

/**
 * Replace localStorage-based reservation creation
 */
window.createReservationAPI = async function(reservationData) {
    const apiData = {
        table_id: reservationData.tableId,
        customer_name: reservationData.customerName,
        customer_phone: reservationData.phone,
        reservation_date: reservationData.date,
        reservation_time: reservationData.time,
        number_of_guests: reservationData.guests,
        special_notes: reservationData.notes
    };
    
    const result = await createReservation(apiData);
    if (result) {
        updateTableStatus(reservationData.tableName, 'blocked');
        addReservationBadge(reservationData.tableName, result);
        return true;
    }
    return false;
};

/**
 * Replace localStorage-based order creation
 */
window.createTableOrderAPI = async function(orderData) {
    const apiData = {
        table_id: orderData.tableId,
        customer_name: orderData.customerName,
        number_of_guests: orderData.guests,
        order_items: orderData.items,
        special_notes: orderData.notes,
        order_time: orderData.time
    };
    
    const result = await createTableOrder(apiData);
    if (result) {
        updateTableStatus(orderData.tableName, 'utilized');
        addCompleteOrderButton(orderData.tableName, result);
        return true;
    }
    return false;
};

/**
 * Initialize API integration when DOM is ready
 */
document.addEventListener('DOMContentLoaded', function() {
    // Sync tables on page load
    setTimeout(syncTablesFromBackend, 1000);
    
    // Sync every 30 seconds to keep data fresh
    setInterval(syncTablesFromBackend, 30000);
});

console.log('Table Reservation API integration loaded');