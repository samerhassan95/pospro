/**
 * Table Reservation System - localStorage Override
 * Replaces localStorage operations with API calls
 * Maintains backward compatibility with existing code
 */

(function() {
    'use strict';

    // ============================================
    // OVERRIDE: Save Custom Table
    // ============================================
    window.saveCustomTable = async function(tableElement) {
        const tableName = tableElement.getAttribute('data-table');
        const tableType = tableElement.classList.contains('circle') ? 'circle' :
                         tableElement.classList.contains('rounded') ? 'rounded' :
                         tableElement.classList.contains('rectangle-h') ? 'rectangle-h' :
                         tableElement.classList.contains('rectangle-h10') ? 'rectangle-h10' : 'rectangle';
        
        const chairCount = tableElement.querySelectorAll('.chair').length;
        const rect = tableElement.getBoundingClientRect();
        const parent = tableElement.parentElement.getBoundingClientRect();

        const tableData = {
            table_name: tableName,
            table_type: tableType,
            chair_count: chairCount,
            position_top: `${rect.top - parent.top}px`,
            position_left: `${rect.left - parent.left}px`,
            is_custom: true
        };

        try {
            const result = await TableAPI.createTable(tableData);
            tableElement.setAttribute('data-table-id', result.id);
            console.log('Custom table saved:', result);
            toastr.success('Table created successfully');
            return result;
        } catch (error) {
            console.error('Failed to save custom table:', error);
            toastr.error('Failed to create table');
            throw error;
        }
    };

    // ============================================
    // OVERRIDE: Delete Custom Table
    // ============================================
    window.deleteCustomTable = async function(tableName) {
        const tableElement = document.querySelector(`.table-item[data-table="${tableName}"]`);
        if (!tableElement) return;

        const tableId = tableElement.getAttribute('data-table-id');
        if (!tableId) {
            console.error('Table ID not found');
            return;
        }

        try {
            await TableAPI.deleteTable(tableId);
            tableElement.remove();
            TableDataCache.invalidate('tables');
            console.log('Custom table deleted:', tableName);
            toastr.success('Table deleted successfully');
        } catch (error) {
            console.error('Failed to delete custom table:', error);
            toastr.error('Failed to delete table');
        }
    };

    // ============================================
    // OVERRIDE: Save Table Position
    // ============================================
    window.saveTablePosition = async function(tableName, tableElement) {
        const tableId = tableElement.getAttribute('data-table-id');
        if (!tableId) {
            console.warn('Table ID not found, skipping position save');
            return;
        }

        const rect = tableElement.getBoundingClientRect();
        const parent = tableElement.parentElement.getBoundingClientRect();
        const rotation = tableElement.style.transform ? 
                        parseInt(tableElement.style.transform.match(/\d+/)?.[0] || 0) : 0;

        const position = {
            top: `${rect.top - parent.top}px`,
            left: `${rect.left - parent.left}px`,
            rotation: rotation
        };

        try {
            await TableAPI.updatePosition(tableId, position);
            console.log('Table position saved:', tableName, position);
        } catch (error) {
            console.error('Failed to save table position:', error);
        }
    };

    // ============================================
    // OVERRIDE: Restore Custom Tables
    // ============================================
    window.restoreCustomTables = async function() {
        try {
            const tables = await TableDataCache.getTables(true);
            const customTables = tables.filter(t => t.is_custom);
            
            const floorPlan = document.querySelector('.restaurant-floor-plan');
            if (!floorPlan) return;

            customTables.forEach(table => {
                const tableEl = TableUI.createTableElement(table, [], []);
                floorPlan.appendChild(tableEl);
            });

            console.log('Custom tables restored:', customTables.length);
        } catch (error) {
            console.error('Failed to restore custom tables:', error);
        }
    };

    // ============================================
    // OVERRIDE: Restore Table Positions
    // ============================================
    window.restoreTablePositions = async function() {
        try {
            const tables = await TableDataCache.getTables(true);
            
            tables.forEach(table => {
                const tableEl = document.querySelector(`.table-item[data-table="${table.table_name}"]`);
                if (tableEl) {
                    if (table.position_top) tableEl.style.top = table.position_top;
                    if (table.position_left) tableEl.style.left = table.position_left;
                    if (table.position_right) tableEl.style.right = table.position_right;
                    if (table.position_bottom) tableEl.style.bottom = table.position_bottom;
                    if (table.rotation) tableEl.style.transform = `rotate(${table.rotation}deg)`;
                    
                    tableEl.setAttribute('data-table-id', table.id);
                }
            });

            console.log('Table positions restored');
        } catch (error) {
            console.error('Failed to restore table positions:', error);
        }
    };

    // ============================================
    // OVERRIDE: Restore Table Statuses
    // ============================================
    window.restoreTableStatuses = async function() {
        try {
            const reservations = await TableDataCache.getReservations(true);
            const orders = await TableDataCache.getOrders(true);

            console.log('Restoring table statuses...');
            console.log('Reservations:', reservations.length);
            console.log('Orders:', orders.length);

            // Reset all tables to free
            document.querySelectorAll('.table-item').forEach(table => {
                table.classList.remove('blocked', 'utilized');
                table.classList.add('free');
            });

            // Apply reservation statuses (blocked)
            reservations.forEach(reservation => {
                if (reservation.status === 'reserved') {
                    const tableEl = document.querySelector(`.table-item[data-table-id="${reservation.table_id}"]`);
                    if (tableEl) {
                        tableEl.classList.remove('free');
                        tableEl.classList.add('blocked');
                        tableEl.setAttribute('data-reservation-id', reservation.id);
                    }
                } else if (reservation.status === 'arrived') {
                    const tableEl = document.querySelector(`.table-item[data-table-id="${reservation.table_id}"]`);
                    if (tableEl) {
                        tableEl.classList.remove('free');
                        tableEl.classList.add('utilized');
                        tableEl.setAttribute('data-reservation-id', reservation.id);
                    }
                }
            });

            // Apply order statuses (utilized)
            orders.forEach(order => {
                if (order.status === 'in_progress') {
                    const tableEl = document.querySelector(`.table-item[data-table-id="${order.table_id}"]`);
                    if (tableEl) {
                        tableEl.classList.remove('free', 'blocked');
                        tableEl.classList.add('utilized');
                        tableEl.setAttribute('data-order-id', order.id);
                    }
                }
            });

            console.log('Table statuses restored');
        } catch (error) {
            console.error('Failed to restore table statuses:', error);
        }
    };

    // ============================================
    // OVERRIDE: Create Reservation
    // ============================================
    window.createReservation = async function(reservationData) {
        try {
            const result = await ReservationAPI.createReservation(reservationData);
            
            // Update table status
            TableUI.updateTableStatus(reservationData.table_name, 'blocked');
            TableDataCache.invalidate('reservations');
            
            console.log('Reservation created:', result);
            toastr.success('Reservation created successfully');
            return result;
        } catch (error) {
            console.error('Failed to create reservation:', error);
            toastr.error('Failed to create reservation');
            throw error;
        }
    };

    // ============================================
    // OVERRIDE: Cancel Reservation
    // ============================================
    window.cancelReservation = async function(reservationId, tableName) {
        try {
            await ReservationAPI.cancelReservation(reservationId);
            
            // Update table status
            TableUI.updateTableStatus(tableName, 'free');
            TableDataCache.invalidate('reservations');
            
            console.log('Reservation cancelled');
            toastr.success('Reservation cancelled successfully');
        } catch (error) {
            console.error('Failed to cancel reservation:', error);
            toastr.error('Failed to cancel reservation');
            throw error;
        }
    };

    // ============================================
    // OVERRIDE: Guest Arrived
    // ============================================
    window.guestArrived = async function(reservationId, tableName) {
        try {
            await ReservationAPI.guestArrived(reservationId);
            
            // Update table status
            TableUI.updateTableStatus(tableName, 'utilized');
            TableDataCache.invalidate('reservations');
            
            console.log('Guest arrived');
            toastr.success('Guest marked as arrived');
        } catch (error) {
            console.error('Failed to mark guest as arrived:', error);
            toastr.error('Failed to update reservation');
            throw error;
        }
    };

    // ============================================
    // OVERRIDE: Create Order
    // ============================================
    window.createTableOrder = async function(orderData) {
        try {
            const result = await OrderAPI.createOrder(orderData);
            
            // Update table status
            const tableEl = document.querySelector(`.table-item[data-table-id="${orderData.table_id}"]`);
            if (tableEl) {
                const tableName = tableEl.getAttribute('data-table');
                TableUI.updateTableStatus(tableName, 'utilized');
            }
            TableDataCache.invalidate('orders');
            
            console.log('Order created:', result);
            toastr.success('Order created successfully');
            return result;
        } catch (error) {
            console.error('Failed to create order:', error);
            toastr.error('Failed to create order');
            throw error;
        }
    };

    // ============================================
    // OVERRIDE: Complete Order
    // ============================================
    window.completeTableOrder = async function(orderId, tableName) {
        try {
            await OrderAPI.completeOrder(orderId);
            
            // Update table status
            TableUI.updateTableStatus(tableName, 'free');
            TableDataCache.invalidate('orders');
            
            console.log('Order completed');
            toastr.success('Order completed successfully');
        } catch (error) {
            console.error('Failed to complete order:', error);
            toastr.error('Failed to complete order');
            throw error;
        }
    };

    // ============================================
    // OVERRIDE: Get Reservations
    // ============================================
    window.getTableReservations = async function() {
        try {
            return await TableDataCache.getReservations(true);
        } catch (error) {
            console.error('Failed to get reservations:', error);
            return [];
        }
    };

    // ============================================
    // OVERRIDE: Get Orders
    // ============================================
    window.getTableOrders = async function() {
        try {
            return await TableDataCache.getOrders(true);
        } catch (error) {
            console.error('Failed to get orders:', error);
            return [];
        }
    };

    // ============================================
    // OVERRIDE: Rotate Table
    // ============================================
    window.rotateTable = async function(tableName, degrees = 90) {
        const tableEl = document.querySelector(`.table-item[data-table="${tableName}"]`);
        if (!tableEl) return;

        const tableId = tableEl.getAttribute('data-table-id');
        if (!tableId) {
            console.warn('Table ID not found, skipping rotation');
            return;
        }

        try {
            const result = await TableAPI.rotateTable(tableId, degrees);
            
            // Update visual rotation
            tableEl.style.transform = `rotate(${result.rotation}deg)`;
            
            // Update or add rotation badge
            let badge = tableEl.querySelector('.rotation-badge');
            if (result.rotation && result.rotation !== 0) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'rotation-badge';
                    tableEl.appendChild(badge);
                }
                badge.textContent = `${result.rotation}°`;
            } else if (badge) {
                badge.remove();
            }
            
            console.log('Table rotated:', result);
        } catch (error) {
            console.error('Failed to rotate table:', error);
            toastr.error('Failed to rotate table');
        }
    };

    // ============================================
    // OVERRIDE: Clear All Data
    // ============================================
    window.clearAllTableData = async function() {
        if (!confirm('Are you sure you want to clear all table data? This will remove all reservations, orders, and custom tables.')) {
            return;
        }

        try {
            // Get all data
            const reservations = await TableDataCache.getReservations(true);
            const orders = await TableDataCache.getOrders(true);
            const tables = await TableDataCache.getTables(true);
            const customTables = tables.filter(t => t.is_custom);

            // Delete all reservations
            for (const reservation of reservations) {
                await ReservationAPI.deleteReservation(reservation.id);
            }

            // Delete all orders
            for (const order of orders) {
                await OrderAPI.deleteOrder(order.id);
            }

            // Delete all custom tables
            for (const table of customTables) {
                await TableAPI.deleteTable(table.id);
            }

            // Invalidate cache
            TableDataCache.invalidate();

            // Refresh UI
            await TableUI.renderTables();

            toastr.success('All table data cleared successfully');
            console.log('All table data cleared');
        } catch (error) {
            console.error('Failed to clear table data:', error);
            toastr.error('Failed to clear table data');
        }
    };

    console.log('Table localStorage override loaded');

})();
