/**
 * Table Reservation System - API Integration
 * Replaces localStorage with backend API calls
 * Maintains existing UI/UX design
 */

(function() {
    'use strict';

    // API Configuration
    const API_BASE = '/api/business';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content;

    // Helper function for API calls
    async function apiCall(endpoint, method = 'GET', data = null) {
        const options = {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        };

        if (data && (method === 'POST' || method === 'PUT' || method === 'PATCH')) {
            options.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(`${API_BASE}${endpoint}`, options);
            const result = await response.json();
            
            if (!response.ok) {
                throw new Error(result.message || 'API request failed');
            }
            
            return result;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    // ============================================
    // TABLE MANAGEMENT API
    // ============================================

    window.TableAPI = {
        // Fetch all tables
        async fetchTables() {
            const result = await apiCall('/tables');
            return result.data || [];
        },

        // Create custom table
        async createTable(tableData) {
            const result = await apiCall('/tables', 'POST', tableData);
            return result.data;
        },

        // Update table
        async updateTable(tableId, tableData) {
            const result = await apiCall(`/tables/${tableId}`, 'PUT', tableData);
            return result.data;
        },

        // Update table position (drag & drop)
        async updatePosition(tableId, position) {
            const result = await apiCall(`/tables/${tableId}/position`, 'POST', {
                position_top: position.top,
                position_left: position.left,
                position_right: position.right || null,
                position_bottom: position.bottom || null,
                rotation: position.rotation || 0
            });
            return result.data;
        },

        // Rotate table
        async rotateTable(tableId, degrees = 90) {
            const result = await apiCall(`/tables/${tableId}/rotate`, 'POST', { degrees });
            return result.data;
        },

        // Delete custom table
        async deleteTable(tableId) {
            await apiCall(`/tables/${tableId}`, 'DELETE');
        }
    };

    // ============================================
    // RESERVATION MANAGEMENT API
    // ============================================

    window.ReservationAPI = {
        // Fetch all reservations
        async fetchReservations() {
            const result = await apiCall('/reservations');
            return result.data || [];
        },

        // Create reservation
        async createReservation(reservationData) {
            const result = await apiCall('/reservations', 'POST', reservationData);
            return result.data;
        },

        // Mark guest as arrived
        async guestArrived(reservationId) {
            await apiCall(`/reservations/${reservationId}/arrived`, 'POST');
        },

        // Cancel reservation
        async cancelReservation(reservationId) {
            await apiCall(`/reservations/${reservationId}/cancel`, 'POST');
        },

        // Update reservation
        async updateReservation(reservationId, reservationData) {
            const result = await apiCall(`/reservations/${reservationId}`, 'PUT', reservationData);
            return result.data;
        },

        // Delete reservation
        async deleteReservation(reservationId) {
            await apiCall(`/reservations/${reservationId}`, 'DELETE');
        }
    };

    // ============================================
    // ORDER MANAGEMENT API
    // ============================================

    window.OrderAPI = {
        // Fetch all active orders
        async fetchOrders() {
            const result = await apiCall('/table-orders');
            return result.data || [];
        },

        // Create order
        async createOrder(orderData) {
            const result = await apiCall('/table-orders', 'POST', orderData);
            return result.data;
        },

        // Complete order
        async completeOrder(orderId) {
            await apiCall(`/table-orders/${orderId}/complete`, 'POST');
        },

        // Update order
        async updateOrder(orderId, orderData) {
            const result = await apiCall(`/table-orders/${orderId}`, 'PUT', orderData);
            return result.data;
        },

        // Delete order
        async deleteOrder(orderId) {
            await apiCall(`/table-orders/${orderId}`, 'DELETE');
        }
    };

    // ============================================
    // FLOOR PLAN LAYOUT API
    // ============================================

    window.FloorPlanAPI = {
        // Fetch all layouts
        async fetchLayouts() {
            const result = await apiCall('/floor-layouts');
            return result.data || [];
        },

        // Get active layout
        async getActiveLayout() {
            const result = await apiCall('/floor-layouts/active');
            return result.data;
        },

        // Get default layout
        async getDefaultLayout() {
            const result = await apiCall('/floor-layouts/default');
            return result.data;
        },

        // Save current floor plan as layout
        async saveLayout(layoutData) {
            const result = await apiCall('/floor-layouts', 'POST', layoutData);
            return result.data;
        },

        // Activate layout
        async activateLayout(layoutId) {
            const result = await apiCall(`/floor-layouts/${layoutId}/activate`, 'POST');
            return result.data;
        },

        // Set as default
        async setDefaultLayout(layoutId) {
            await apiCall(`/floor-layouts/${layoutId}/set-default`, 'POST');
        },

        // Duplicate layout
        async duplicateLayout(layoutId) {
            const result = await apiCall(`/floor-layouts/${layoutId}/duplicate`, 'POST');
            return result.data;
        },

        // Delete layout
        async deleteLayout(layoutId) {
            await apiCall(`/floor-layouts/${layoutId}`, 'DELETE');
        }
    };

    // ============================================
    // DATA CACHE (for performance)
    // ============================================

    const DataCache = {
        tables: null,
        reservations: null,
        orders: null,
        lastFetch: {
            tables: 0,
            reservations: 0,
            orders: 0
        },
        CACHE_DURATION: 30000, // 30 seconds

        isCacheValid(type) {
            return (Date.now() - this.lastFetch[type]) < this.CACHE_DURATION;
        },

        async getTables(forceRefresh = false) {
            if (!forceRefresh && this.isCacheValid('tables') && this.tables) {
                return this.tables;
            }
            this.tables = await TableAPI.fetchTables();
            this.lastFetch.tables = Date.now();
            return this.tables;
        },

        async getReservations(forceRefresh = false) {
            if (!forceRefresh && this.isCacheValid('reservations') && this.reservations) {
                return this.reservations;
            }
            this.reservations = await ReservationAPI.fetchReservations();
            this.lastFetch.reservations = Date.now();
            return this.reservations;
        },

        async getOrders(forceRefresh = false) {
            if (!forceRefresh && this.isCacheValid('orders') && this.orders) {
                return this.orders;
            }
            this.orders = await OrderAPI.fetchOrders();
            this.lastFetch.orders = Date.now();
            return this.orders;
        },

        invalidate(type) {
            if (type) {
                this[type] = null;
                this.lastFetch[type] = 0;
            } else {
                this.tables = null;
                this.reservations = null;
                this.orders = null;
                this.lastFetch = { tables: 0, reservations: 0, orders: 0 };
            }
        }
    };

    window.TableDataCache = DataCache;

    // ============================================
    // UI UPDATE FUNCTIONS
    // ============================================

    window.TableUI = {
        // Render all tables on floor plan
        async renderTables() {
            const tables = await DataCache.getTables(true);
            const reservations = await DataCache.getReservations(true);
            const orders = await DataCache.getOrders(true);
            
            const floorPlan = document.querySelector('.restaurant-floor-plan');
            if (!floorPlan) return;

            // Remove existing table elements (keep areas)
            floorPlan.querySelectorAll('.table-item').forEach(el => el.remove());

            // Create table elements
            tables.forEach(table => {
                const tableEl = this.createTableElement(table, reservations, orders);
                floorPlan.appendChild(tableEl);
            });

            // Restore drag functionality
            this.initializeDragDrop();
        },

        // Create table DOM element
        createTableElement(table, reservations, orders) {
            const div = document.createElement('div');
            div.className = `table-item ${table.table_type} ${table.status}`;
            div.setAttribute('data-table', table.table_name);
            div.setAttribute('data-table-id', table.id);
            div.setAttribute('data-chairs', table.chair_count);
            div.setAttribute('data-custom', table.is_custom ? 'true' : 'false');
            div.draggable = true;

            // Apply saved position
            if (table.position_top) div.style.top = table.position_top;
            if (table.position_left) div.style.left = table.position_left;
            if (table.position_right) div.style.right = table.position_right;
            if (table.position_bottom) div.style.bottom = table.position_bottom;
            if (table.rotation) div.style.transform = `rotate(${table.rotation}deg)`;

            // Table name
            const nameSpan = document.createElement('span');
            nameSpan.className = 'table-name';
            nameSpan.textContent = table.table_name;
            div.appendChild(nameSpan);

            // Chairs
            for (let i = 0; i < table.chair_count; i++) {
                const chair = document.createElement('div');
                chair.className = 'chair chair-free';
                div.appendChild(chair);
            }

            // Rotation badge
            if (table.rotation && table.rotation !== 0) {
                const badge = document.createElement('span');
                badge.className = 'rotation-badge';
                badge.textContent = `${table.rotation}°`;
                div.appendChild(badge);
            }

            return div;
        },

        // Initialize drag and drop
        initializeDragDrop() {
            const tables = document.querySelectorAll('.table-item');
            
            tables.forEach(table => {
                table.addEventListener('dragstart', this.handleDragStart);
                table.addEventListener('dragend', this.handleDragEnd);
            });

            const floorPlan = document.querySelector('.restaurant-floor-plan');
            if (floorPlan) {
                floorPlan.addEventListener('dragover', this.handleDragOver);
                floorPlan.addEventListener('drop', this.handleDrop);
            }
        },

        handleDragStart(e) {
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.innerHTML);
            this.classList.add('dragging');
        },

        async handleDragEnd(e) {
            this.classList.remove('dragging');
            
            // Get table ID and new position
            const tableId = this.getAttribute('data-table-id');
            const rect = this.getBoundingClientRect();
            const parent = this.parentElement.getBoundingClientRect();
            
            const position = {
                top: `${rect.top - parent.top}px`,
                left: `${rect.left - parent.left}px`,
                rotation: this.style.transform ? parseInt(this.style.transform.match(/\d+/)?.[0] || 0) : 0
            };

            // Save position to backend
            try {
                await TableAPI.updatePosition(tableId, position);
                console.log('Table position saved:', position);
            } catch (error) {
                console.error('Failed to save table position:', error);
                toastr.error('Failed to save table position');
            }
        },

        handleDragOver(e) {
            if (e.preventDefault) {
                e.preventDefault();
            }
            e.dataTransfer.dropEffect = 'move';
            return false;
        },

        handleDrop(e) {
            if (e.stopPropagation) {
                e.stopPropagation();
            }
            return false;
        },

        // Update table status visually
        updateTableStatus(tableName, status) {
            const table = document.querySelector(`.table-item[data-table="${tableName}"]`);
            if (table) {
                table.classList.remove('free', 'blocked', 'utilized');
                table.classList.add(status);
            }
        }
    };

    // ============================================
    // INITIALIZATION
    // ============================================

    document.addEventListener('DOMContentLoaded', async function() {
        console.log('Table Reservation API Integration loaded');
        
        // Initial render
        try {
            await TableUI.renderTables();
            console.log('Tables rendered successfully');
        } catch (error) {
            console.error('Failed to render tables:', error);
        }

        // Refresh data periodically
        setInterval(async () => {
            try {
                await TableUI.renderTables();
            } catch (error) {
                console.error('Failed to refresh tables:', error);
            }
        }, 60000); // Every minute
    });

})();
