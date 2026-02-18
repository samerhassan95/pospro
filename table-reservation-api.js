// Fetch all tables from backend
async function fetchTables() {
    try {
        const response = await fetch('/api/business/tables', {
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
        });
        const result = await response.json();
        return result.success ? result.data : [];
    } catch (error) {
        console.error('Error fetching tables:', error);
        return [];
    }
}

// Table Reservation API object
const TableReservationAPI = {
    // Fetch tables from database
    async fetchTables() {
        return await fetchTables();
    },
    
    // Initialize tables from backend
    async initializeTablesFromBackend() {
        const tables = await this.fetchTables();
        console.log('Tables loaded from backend:', tables);
        return tables;
    },
    
    // Get reservations from localStorage (keeping existing functionality)
    async getReservationsFromStorage() {
        return JSON.parse(localStorage.getItem('tableReservations') || '{}');
    },
    
    // Get orders from localStorage (keeping existing functionality)
    async getOrdersFromStorage() {
        return JSON.parse(localStorage.getItem('tableOrders') || '{}');
    },
    
    // Save order to backend (placeholder - can be implemented later)
    async saveOrderToBackend(tableName, orderData) {
        console.log('Saving order to backend:', tableName, orderData);
        // For now, save to localStorage
        const orders = JSON.parse(localStorage.getItem('tableOrders') || '{}');
        orders[tableName] = orderData;
        localStorage.setItem('tableOrders', JSON.stringify(orders));
        return { success: true };
    },
    
    // Remove order from backend (placeholder)
    async removeOrderFromBackend(orderId, tableName) {
        console.log('Removing order from backend:', orderId, tableName);
        const orders = JSON.parse(localStorage.getItem('tableOrders') || '{}');
        delete orders[tableName];
        localStorage.setItem('tableOrders', JSON.stringify(orders));
        return { success: true };
    },
    
    // Remove reservation from backend (placeholder)
    async removeReservationFromBackend(reservationId, tableName) {
        console.log('Removing reservation from backend:', reservationId, tableName);
        const reservations = JSON.parse(localStorage.getItem('tableReservations') || '{}');
        delete reservations[reservationId];
        localStorage.setItem('tableReservations', JSON.stringify(reservations));
        return { success: true };
    },
    
    // Create table in backend
    async createTable(tableData) {
        try {
            const response = await fetch('/api/business/tables', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(tableData)
            });
            return await response.json();
        } catch (error) {
            console.error('Error creating table:', error);
            return {success: false, message: error.message};
        }
    },
    
    // Delete table from backend
    async deleteTable(tableId) {
        try {
            const response = await fetch(`/api/business/tables/${tableId}`, {
                method: 'DELETE',
                headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
            });
            return await response.json();
        } catch (error) {
            console.error('Error deleting table:', error);
            return {success: false, message: error.message};
        }
    }
};

// Make it globally available
window.TableReservationAPI = TableReservationAPI;

// Create reservation
async function createReservation(data) {
    try {
        const response = await fetch('/api/business/reservations', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        return await response.json();
    } catch (error) {
        console.error('Error creating reservation:', error);
        return {success: false, message: 'Network error'};
    }
}

// Fetch all reservations
async function fetchReservations() {
    try {
        const response = await fetch('/api/business/reservations', {
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
        });
        const result = await response.json();
        return result.success ? result.data : [];
    } catch (error) {
        console.error('Error fetching reservations:', error);
        return [];
    }
}

// Cancel reservation
async function cancelReservation(reservationId) {
    try {
        const response = await fetch(`/api/business/reservations/${reservationId}`, {
            method: 'DELETE',
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
        });
        return await response.json();
    } catch (error) {
        console.error('Error canceling reservation:', error);
        return {success: false};
    }
}

// Mark guest as arrived
async function guestArrived(reservationId) {
    try {
        const response = await fetch(`/api/business/reservations/${reservationId}/arrived`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
        });
        return await response.json();
    } catch (error) {
        console.error('Error marking guest arrived:', error);
        return {success: false};
    }
}

// Create table order
async function createTableOrder(data) {
    try {
        const response = await fetch('/api/business/table-orders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        return await response.json();
    } catch (error) {
        console.error('Error creating table order:', error);
        return {success: false};
    }
}

// Fetch all table orders
async function fetchTableOrders() {
    try {
        const response = await fetch('/api/business/table-orders', {
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
        });
        const result = await response.json();
        return result.success ? result.data : [];
    } catch (error) {
        console.error('Error fetching table orders:', error);
        return [];
    }
}

// Complete table order
async function completeTableOrder(orderId) {
    try {
        const response = await fetch(`/api/business/table-orders/${orderId}/complete`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
        });
        return await response.json();
    } catch (error) {
        console.error('Error completing table order:', error);
        return {success: false};
    }
}

// Create custom table
async function createCustomTable(data) {
    try {
        const response = await fetch('/api/business/tables', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        return await response.json();
    } catch (error) {
        console.error('Error creating custom table:', error);
        return {success: false};
    }
}

// Update table position
async function updateTablePosition(tableId, position) {
    try {
        const response = await fetch(`/api/business/tables/${tableId}/position`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(position)
        });
        return await response.json();
    } catch (error) {
        console.error('Error updating table position:', error);
        return {success: false};
    }
}

// Rotate table
async function rotateTable(tableId, degrees = 90) {
    try {
        const response = await fetch(`/api/business/tables/${tableId}/rotate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({degrees})
        });
        return await response.json();
    } catch (error) {
        console.error('Error rotating table:', error);
        return {success: false};
    }
}

// Delete custom table
async function deleteCustomTable(tableId) {
    try {
        const response = await fetch(`/api/business/tables/${tableId}`, {
            method: 'DELETE',
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
        });
        return await response.json();
    } catch (error) {
        console.error('Error deleting table:', error);
        return {success: false};
    }
}