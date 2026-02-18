/**
 * Table Reservation API Integration
 * Replaces localStorage with backend API calls
 */

class TableReservationAPI {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        this.baseUrl = '/api/business';
    }

    // Helper method for API calls
    async apiCall(endpoint, method = 'GET', data = null) {
        const config = {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
                'Accept': 'application/json'
            }
        };

        if (data && method !== 'GET') {
            config.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(`${this.baseUrl}${endpoint}`, config);
            const result = await response.json();
            
            if (!response.ok) {
                throw new Error(result.message || 'API call failed');
            }
            
            return result;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    // ========== TABLES API ==========
    
    async fetchTables() {
        const result = await this.apiCall('/tables');
        return result.data || [];
    }

    async createTable(tableData) {
        return await this.apiCall('/tables', 'POST', tableData);
    }

    async updateTable(tableId, tableData) {
        return await this.apiCall(`/tables/${tableId}`, 'PUT', tableData);
    }

    async updateTablePosition(tableId, position) {
        return await this.apiCall(`/tables/${tableId}/position`, 'POST', position);
    }

    async rotateTable(tableId, degrees = 90) {
        return await this.apiCall(`/tables/${tableId}/rotate`, 'POST', { degrees });
    }

    async deleteTable(tableId) {
        return await this.apiCall(`/tables/${tableId}`, 'DELETE');
    }

    // ========== RESERVATIONS API ==========
    
    async fetchReservations() {
        const result = await this.apiCall('/reservations');
        return result.data || [];
    }

    async createReservation(reservationData) {
        return await this.apiCall('/reservations', 'POST', reservationData);
    }

    async guestArrived(reservationId) {
        return await this.apiCall(`/reservations/${reservationId}/arrived`, 'POST');
    }

    async cancelReservation(reservationId) {
        return await this.apiCall(`/reservations/${reservationId}`, 'DELETE');
    }

    // ========== TABLE ORDERS API ==========
    
    async fetchTableOrders() {
        const result = await this.apiCall('/table-orders');
        return result.data || [];
    }

    async createTableOrder(orderData) {
        return await this.apiCall('/table-orders', 'POST', orderData);
    }

    async completeTableOrder(orderId) {
        return await this.apiCall(`/table-orders/${orderId}/complete`, 'POST');
    }

    // ========== HELPER METHODS ==========
    
    // Convert backend table data to frontend format
    formatTableForFrontend(table) {
        return {
            id: table.id,
            name: table.table_name,
            type: table.table_type,
            chairs: table.chair_count,
            status: table.status,
            position: {
                top: table.position_top,
                left: table.position_left,
                right: table.position_right,
                bottom: table.position_bottom,
                rotation: table.rotation || 0
            },
            isCustom: table.is_custom,
            activeReservation: table.active_reservation,
            activeOrder: table.active_order
        };
    }

    // Convert frontend table data to backend format
    formatTableForBackend(tableData) {
        return {
            table_name: tableData.name,
            table_type: tableData.type,
            chair_count: tableData.chairs,
            position_top: tableData.position?.top,
            position_left: tableData.position?.left,
            position_right: tableData.position?.right,
            position_bottom: tableData.position?.bottom,
            rotation: tableData.position?.rotation || 0,
            status: tableData.status || 'free',
            is_custom: tableData.isCustom || false
        };
    }

    // Convert backend reservation data to frontend format
    formatReservationForFrontend(reservation) {
        return {
            id: reservation.id,
            tableId: reservation.table_id,
            tableName: reservation.table?.table_name,
            customerName: reservation.customer_name,
            customerPhone: reservation.customer_phone,
            date: reservation.reservation_date,
            time: reservation.reservation_time,
            guests: reservation.number_of_guests,
            notes: reservation.special_notes,
            status: reservation.status,
            timeArrived: reservation.time_arrived
        };
    }

    // Convert frontend reservation data to backend format
    formatReservationForBackend(reservationData) {
        return {
            table_id: reservationData.tableId,
            customer_name: reservationData.customerName,
            customer_phone: reservationData.customerPhone,
            reservation_date: reservationData.date,
            reservation_time: reservationData.time,
            number_of_guests: reservationData.guests,
            special_notes: reservationData.notes
        };
    }

    // Convert backend order data to frontend format
    formatOrderForFrontend(order) {
        return {
            id: order.id,
            tableId: order.table_id,
            tableName: order.table?.table_name,
            customerName: order.customer_name,
            guests: order.number_of_guests,
            items: order.order_items,
            notes: order.special_notes,
            time: order.order_time,
            status: order.status,
            timestamp: order.created_at
        };
    }

    // Convert frontend order data to backend format
    formatOrderForBackend(orderData) {
        return {
            table_id: orderData.tableId,
            customer_name: orderData.customerName,
            number_of_guests: orderData.guests,
            order_items: orderData.items,
            special_notes: orderData.notes,
            order_time: orderData.time
        };
    }
}

// Create global instance
window.tableAPI = new TableReservationAPI();

// ========== MIGRATION FUNCTIONS ==========
// These functions replace localStorage operations with API calls

// Replace fetchTables function
async function fetchTablesFromAPI() {
    try {
        const tables = await window.tableAPI.fetchTables();
        return tables.map(table => window.tableAPI.formatTableForFrontend(table));
    } catch (error) {
        console.error('Error fetching tables:', error);
        return [];
    }
}

// Replace saveTablePosition function
async function saveTablePositionToAPI(tableName, tableElement) {
    try {
        // Find table by name
        const tables = await fetchTablesFromAPI();
        const table = tables.find(t => t.name === tableName);
        
        if (table) {
            const position = {
                position_top: tableElement.style.top,
                position_left: tableElement.style.left,
                position_right: tableElement.style.right,
                position_bottom: tableElement.style.bottom,
                rotation: parseInt(tableElement.getAttribute('data-rotation') || '0')
            };
            
            await window.tableAPI.updateTablePosition(table.id, position);
            console.log(`Table ${tableName} position saved to API`);
        }
    } catch (error) {
        console.error('Error saving table position:', error);
    }
}

// Replace createReservation function
async function createReservationAPI(reservationData) {
    try {
        const backendData = window.tableAPI.formatReservationForBackend(reservationData);
        const result = await window.tableAPI.createReservation(backendData);
        console.log('Reservation created:', result);
        return result;
    } catch (error) {
        console.error('Error creating reservation:', error);
        throw error;
    }
}

// Replace fetchReservations function
async function fetchReservationsFromAPI() {
    try {
        const reservations = await window.tableAPI.fetchReservations();
        return reservations.map(reservation => window.tableAPI.formatReservationForFrontend(reservation));
    } catch (error) {
        console.error('Error fetching reservations:', error);
        return [];
    }
}

// Replace createTableOrder function
async function createTableOrderAPI(orderData) {
    try {
        const backendData = window.tableAPI.formatOrderForBackend(orderData);
        const result = await window.tableAPI.createTableOrder(backendData);
        console.log('Table order created:', result);
        return result;
    } catch (error) {
        console.error('Error creating table order:', error);
        throw error;
    }
}

// Replace fetchTableOrders function
async function fetchTableOrdersFromAPI() {
    try {
        const orders = await window.tableAPI.fetchTableOrders();
        return orders.map(order => window.tableAPI.formatOrderForFrontend(order));
    } catch (error) {
        console.error('Error fetching table orders:', error);
        return [];
    }
}

// Replace completeTableOrder function
async function completeTableOrderAPI(orderId) {
    try {
        const result = await window.tableAPI.completeTableOrder(orderId);
        console.log('Table order completed:', result);
        return result;
    } catch (error) {
        console.error('Error completing table order:', error);
        throw error;
    }
}

// Replace rotateTable function
async function rotateTableAPI(tableName, degrees = 90) {
    try {
        const tables = await fetchTablesFromAPI();
        const table = tables.find(t => t.name === tableName);
        
        if (table) {
            const result = await window.tableAPI.rotateTable(table.id, degrees);
            console.log(`Table ${tableName} rotated ${degrees}°`);
            return result;
        }
    } catch (error) {
        console.error('Error rotating table:', error);
        throw error;
    }
}

// Replace saveCustomTable function
async function saveCustomTableAPI(tableElement) {
    try {
        const tableName = tableElement.getAttribute('data-table');
        
        // Get table type
        let tableType = 'circle';
        if (tableElement.classList.contains('table-rectangle')) tableType = 'rectangle';
        else if (tableElement.classList.contains('table-rectangle-h10')) tableType = 'rectangle-h10';
        else if (tableElement.classList.contains('table-rectangle-h')) tableType = 'rectangle-h';
        else if (tableElement.classList.contains('table-rounded')) tableType = 'rounded';
        
        // Count chairs
        const chairCount = tableElement.querySelectorAll('.chair').length;
        
        // Get status
        let status = 'free';
        if (tableElement.classList.contains('utilized')) status = 'utilized';
        else if (tableElement.classList.contains('blocked')) status = 'blocked';
        
        const tableData = {
            name: tableName,
            type: tableType,
            chairs: chairCount,
            status: status,
            position: {
                top: tableElement.style.top,
                left: tableElement.style.left,
                right: tableElement.style.right,
                bottom: tableElement.style.bottom,
                rotation: parseInt(tableElement.getAttribute('data-rotation') || '0')
            },
            isCustom: true
        };
        
        const backendData = window.tableAPI.formatTableForBackend(tableData);
        const result = await window.tableAPI.createTable(backendData);
        
        // Store table ID for future updates
        tableElement.setAttribute('data-table-id', result.data.id);
        
        console.log('Custom table saved:', result);
        return result;
    } catch (error) {
        console.error('Error saving custom table:', error);
        throw error;
    }
}

// Initialize tables from API on page load
async function initializeTablesFromAPI() {
    try {
        console.log('🔄 Loading tables from API...');
        const tables = await fetchTablesFromAPI();
        console.log('📊 Tables loaded:', tables.length);
        
        // Apply table positions and statuses
        tables.forEach(table => {
            const tableElement = document.querySelector(`[data-table="${table.name}"]`);
            if (tableElement) {
                // Apply position
                if (table.position.top) tableElement.style.top = table.position.top;
                if (table.position.left) tableElement.style.left = table.position.left;
                if (table.position.right) tableElement.style.right = table.position.right;
                if (table.position.bottom) tableElement.style.bottom = table.position.bottom;
                if (table.position.rotation) tableElement.setAttribute('data-rotation', table.position.rotation);
                
                // Apply status
                tableElement.classList.remove('free', 'utilized', 'blocked');
                tableElement.classList.add(table.status);
                
                // Store table ID
                tableElement.setAttribute('data-table-id', table.id);
                
                console.log(`✅ Table ${table.name} initialized`);
            }
        });
        
        // Load reservations and orders
        await initializeReservationsFromAPI();
        await initializeOrdersFromAPI();
        
        console.log('✅ All table data initialized from API');
    } catch (error) {
        console.error('❌ Error initializing tables from API:', error);
    }
}

// Initialize reservations from API
async function initializeReservationsFromAPI() {
    try {
        const reservations = await fetchReservationsFromAPI();
        console.log('📋 Reservations loaded:', reservations.length);
        
        reservations.forEach(reservation => {
            if (reservation.status === 'reserved') {
                const tableElement = document.querySelector(`[data-table="${reservation.tableName}"]`);
                if (tableElement) {
                    tableElement.classList.remove('free', 'utilized');
                    tableElement.classList.add('blocked');
                    
                    // Add reservation badge
                    addReservationBadge(tableElement, reservation);
                }
            }
        });
    } catch (error) {
        console.error('Error initializing reservations:', error);
    }
}

// Initialize orders from API
async function initializeOrdersFromAPI() {
    try {
        const orders = await fetchTableOrdersFromAPI();
        console.log('📦 Orders loaded:', orders.length);
        
        orders.forEach(order => {
            if (order.status === 'in_progress') {
                const tableElement = document.querySelector(`[data-table="${order.tableName}"]`);
                if (tableElement) {
                    tableElement.classList.remove('free', 'blocked');
                    tableElement.classList.add('utilized');
                    
                    // Add complete order button
                    addCompleteOrderButton(tableElement, order);
                }
            }
        });
    } catch (error) {
        console.error('Error initializing orders:', error);
    }
}

// Add reservation badge to table
function addReservationBadge(tableElement, reservation) {
    // Remove existing badge
    const existingBadge = tableElement.querySelector('.reservation-badge');
    if (existingBadge) existingBadge.remove();
    
    const badge = document.createElement('div');
    badge.className = 'reservation-badge';
    badge.textContent = 'R';
    badge.title = `Reserved for ${reservation.customerName}`;
    badge.onclick = function(e) {
        e.stopPropagation();
        showReservationDetailsAPI(reservation);
    };
    tableElement.appendChild(badge);
}

// Show reservation details from API
function showReservationDetailsAPI(reservation) {
    document.getElementById('detail-table').textContent = reservation.tableName;
    document.getElementById('detail-customer').textContent = reservation.customerName;
    document.getElementById('detail-phone').textContent = reservation.customerPhone || 'N/A';
    document.getElementById('detail-date').textContent = reservation.date;
    document.getElementById('detail-time').textContent = reservation.time;
    document.getElementById('detail-guests').textContent = reservation.guests;
    document.getElementById('detail-notes').textContent = reservation.notes || '-';
    document.getElementById('detail-status').textContent = reservation.status;
    
    // Cancel reservation handler
    document.getElementById('cancel-reservation-btn').onclick = async function() {
        if (confirm('Are you sure you want to cancel this reservation?')) {
            try {
                await window.tableAPI.cancelReservation(reservation.id);
                
                // Update table status
                const tableElement = document.querySelector(`[data-table="${reservation.tableName}"]`);
                if (tableElement) {
                    tableElement.classList.remove('blocked');
                    tableElement.classList.add('free');
                    
                    // Remove badge
                    const badge = tableElement.querySelector('.reservation-badge');
                    if (badge) badge.remove();
                }
                
                bootstrap.Modal.getInstance(document.getElementById('reservationDetailsModal')).hide();
                alert('Reservation cancelled successfully');
            } catch (error) {
                alert('Error cancelling reservation: ' + error.message);
            }
        }
    };
    
    // Guest arrived handler
    document.getElementById('guest-arrived-btn').onclick = async function() {
        try {
            await window.tableAPI.guestArrived(reservation.id);
            
            // Update table status
            const tableElement = document.querySelector(`[data-table="${reservation.tableName}"]`);
            if (tableElement) {
                tableElement.classList.remove('blocked');
                tableElement.classList.add('utilized');
                
                // Remove badge
                const badge = tableElement.querySelector('.reservation-badge');
                if (badge) badge.remove();
            }
            
            bootstrap.Modal.getInstance(document.getElementById('reservationDetailsModal')).hide();
            alert('Guest marked as arrived');
        } catch (error) {
            alert('Error marking guest as arrived: ' + error.message);
        }
    };
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('reservationDetailsModal'));
    modal.show();
}

// Add complete order button with API integration
function addCompleteOrderButton(tableElement, order) {
    // Remove existing button
    const existingBtn = tableElement.querySelector('.complete-order-btn');
    if (existingBtn) existingBtn.remove();
    
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
        
        if (confirm(`Complete order for table ${order.tableName}?`)) {
            try {
                await window.tableAPI.completeTableOrder(order.id);
                
                // Update table status
                tableElement.classList.remove('utilized', 'blocked');
                tableElement.classList.add('free');
                
                // Remove button
                completeBtn.remove();
                
                alert(`Order completed! Table ${order.tableName} is now free.`);
            } catch (error) {
                alert('Error completing order: ' + error.message);
            }
        }
    });
    
    tableElement.appendChild(completeBtn);
}

// Export functions for global use
window.tableReservationAPI = {
    fetchTablesFromAPI,
    saveTablePositionToAPI,
    createReservationAPI,
    fetchReservationsFromAPI,
    createTableOrderAPI,
    fetchTableOrdersFromAPI,
    completeTableOrderAPI,
    rotateTableAPI,
    saveCustomTableAPI,
    initializeTablesFromAPI,
    initializeReservationsFromAPI,
    initializeOrdersFromAPI
};

console.log('✅ Table Reservation API module loaded');