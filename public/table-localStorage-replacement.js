// Complete localStorage replacement for table reservation system

// Override localStorage methods completely
const originalLocalStorage = window.localStorage;

// Create a new localStorage-like object that uses API calls
window.localStorage = {
    async getItem(key) {
        if (key === 'tableReservations') {
            try {
                const response = await fetch('/api/business/reservations', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                return JSON.stringify(data.success ? data.data : {});
            } catch (error) {
                console.error('Error fetching reservations:', error);
                return '{}';
            }
        }
        
        if (key === 'tableOrders') {
            try {
                const response = await fetch('/api/business/table-orders', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                return JSON.stringify(data.success ? data.data : {});
            } catch (error) {
                console.error('Error fetching orders:', error);
                return '{}';
            }
        }
        
        // For other keys, use original localStorage
        return originalLocalStorage.getItem(key);
    },
    
    async setItem(key, value) {
        if (key === 'tableReservations') {
            try {
                const reservations = JSON.parse(value);
                for (const [id, reservation] of Object.entries(reservations)) {
                    await fetch('/api/business/reservations', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(reservation)
                    });
                }
            } catch (error) {
                console.error('Error saving reservations:', error);
            }
            return;
        }
        
        if (key === 'tableOrders') {
            try {
                const orders = JSON.parse(value);
                for (const [table, order] of Object.entries(orders)) {
                    await fetch('/api/business/table-orders', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(order)
                    });
                }
            } catch (error) {
                console.error('Error saving orders:', error);
            }
            return;
        }
        
        // For other keys, use original localStorage
        return originalLocalStorage.setItem(key, value);
    },
    
    removeItem(key) {
        if (key === 'tableReservations' || key === 'tableOrders') {
            // Handle API deletion if needed
            return;
        }
        return originalLocalStorage.removeItem(key);
    },
    
    clear() {
        return originalLocalStorage.clear();
    },
    
    key(index) {
        return originalLocalStorage.key(index);
    },
    
    get length() {
        return originalLocalStorage.length;
    }
};

// Replace JSON.parse(localStorage.getItem()) calls with async versions
window.getTableReservationsSync = function() {
    return new Promise(async (resolve) => {
        const data = await localStorage.getItem('tableReservations');
        resolve(JSON.parse(data));
    });
};

window.getTableOrdersSync = function() {
    return new Promise(async (resolve) => {
        const data = await localStorage.getItem('tableOrders');
        resolve(JSON.parse(data));
    });
};

// Helper functions for common operations
window.saveReservation = async function(reservation) {
    try {
        const response = await fetch('/api/business/reservations', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(reservation)
        });
        return await response.json();
    } catch (error) {
        console.error('Error saving reservation:', error);
        return { success: false };
    }
};

window.saveTableOrder = async function(order) {
    try {
        const response = await fetch('/api/business/table-orders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(order)
        });
        return await response.json();
    } catch (error) {
        console.error('Error saving order:', error);
        return { success: false };
    }
};

window.deleteReservation = async function(reservationId) {
    try {
        const response = await fetch(`/api/business/reservations/${reservationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        return await response.json();
    } catch (error) {
        console.error('Error deleting reservation:', error);
        return { success: false };
    }
};

window.deleteTableOrder = async function(table) {
    try {
        const response = await fetch(`/api/business/table-orders/${table}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        return await response.json();
    } catch (error) {
        console.error('Error deleting order:', error);
        return { success: false };
    }
};

console.log('✅ localStorage replacement loaded - all table operations will use API');