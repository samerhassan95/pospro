// Complete replacement for localStorage usage in table reservation system
// This file contains updated functions to replace localStorage with API calls

// Replace localStorage.getItem('tableReservations') with API call
async function getTableReservations() {
    const reservations = await fetchReservations();
    const reservationsObj = {};
    reservations.forEach(reservation => {
        const key = `${reservation.table_id}_${reservation.reservation_date}_${reservation.reservation_time}`;
        reservationsObj[key] = {
            table: reservation.table ? reservation.table.table_name : `Table_${reservation.table_id}`,
            customerName: reservation.customer_name,
            phone: reservation.customer_phone,
            date: reservation.reservation_date,
            time: reservation.reservation_time,
            guests: reservation.number_of_guests,
            notes: reservation.special_notes,
            status: reservation.status,
            timeArrived: reservation.time_arrived,
            id: reservation.id
        };
    });
    return reservationsObj;
}

// Replace localStorage.getItem('tableOrders') with API call
async function getTableOrders() {
    const orders = await fetchTableOrders();
    const ordersObj = {};
    orders.forEach(order => {
        const tableName = order.table ? order.table.table_name : `Table_${order.table_id}`;
        ordersObj[tableName] = {
            customer: order.customer_name,
            guests: order.number_of_guests,
            items: order.order_items,
            notes: order.special_notes,
            time: order.order_time,
            status: order.status,
            timestamp: order.created_at,
            id: order.id
        };
    });
    return ordersObj;
}

// Replace localStorage.setItem('tableReservations') with API call
async function saveTableReservation(reservationData) {
    const data = {
        table_id: reservationData.tableId,
        customer_name: reservationData.customerName,
        customer_phone: reservationData.phone,
        reservation_date: reservationData.date,
        reservation_time: reservationData.time,
        number_of_guests: reservationData.guests,
        special_notes: reservationData.notes
    };
    return await createReservation(data);
}

// Replace localStorage.setItem('tableOrders') with API call
async function saveTableOrder(orderData) {
    const data = {
        table_id: orderData.tableId,
        customer_name: orderData.customer,
        number_of_guests: orderData.guests,
        order_items: orderData.items,
        special_notes: orderData.notes,
        order_time: orderData.time
    };
    return await createTableOrder(data);
}

// Updated function to handle table click events with API calls
async function handleTableClick(tableElement) {
    const tableName = tableElement.getAttribute('data-table');
    
    if (tableElement.classList.contains('blocked')) {
        // Table is reserved - show reservation details
        const reservations = await getTableReservations();
        let reservationInfo = null;
        let reservationKey = null;
        
        for (const [key, reservation] of Object.entries(reservations)) {
            if (reservation.table === tableName) {
                reservationInfo = reservation;
                reservationKey = key;
                break;
            }
        }
        
        if (reservationInfo) {
            showReservationDetails(reservationInfo, reservationKey);
        } else {
            alert('This table is blocked/reserved');
        }
        return;
    }
    
    if (tableElement.classList.contains('utilized')) {
        // Table is occupied - show order details
        const tableOrders = await getTableOrders();
        if (tableOrders[tableName]) {
            showOrderDetails(tableOrders[tableName], tableName);
        }
        return;
    }
    
    // Table is free - open reservation modal
    openReservationModal(tableName, tableElement);
}

// Updated function to save table position with API
async function saveTablePositionAPI(tableName, tableElement) {
    const tables = await fetchTables();
    const table = tables.find(t => t.table_name === tableName);
    
    if (table) {
        const position = {
            top: tableElement.style.top,
            left: tableElement.style.left,
            right: tableElement.style.right,
            bottom: tableElement.style.bottom,
            rotation: tableElement.getAttribute('data-rotation') || '0'
        };
        
        await updateTablePosition(table.id, position);
    }
}

// Updated function to restore table statuses from API
async function restoreTableStatusesFromAPI() {
    const reservations = await getTableReservations();
    const tableOrders = await getTableOrders();
    
    console.log('Restoring table statuses from API...');
    console.log('Reservations:', reservations);
    console.log('Table Orders:', tableOrders);
    
    // Reset all tables to free first
    document.querySelectorAll('.table-item').forEach(table => {
        const tableName = table.getAttribute('data-table');
        
        // Check if table has a reservation
        let hasReservation = false;
        for (const [key, reservation] of Object.entries(reservations)) {
            if (reservation.table === tableName) {
                hasReservation = true;
                table.classList.remove('free', 'utilized');
                table.classList.add('blocked');
                console.log(`${tableName}: Reserved (blocked)`);
                break;
            }
        }
        
        // If no reservation, check if table has an active order
        if (!hasReservation && tableOrders[tableName]) {
            table.classList.remove('free', 'blocked');
            table.classList.add('utilized');
            addCompleteOrderButton(table);
            console.log(`${tableName}: Has active order (utilized)`);
        }
        
        // If neither reservation nor order, ensure it's free
        if (!hasReservation && !tableOrders[tableName]) {
            table.classList.remove('blocked', 'utilized');
            table.classList.add('free');
            console.log(`${tableName}: No reservation or order (free)`);
        }
    });
    
    console.log('Table statuses restored from API');
}

// Updated function to check reservation times with API
async function checkReservationTimesAPI() {
    console.log('⏰ checkReservationTimesAPI called');
    const reservations = await getTableReservations();
    console.log('⏰ Current reservations from API:', reservations);
    
    const now = new Date();
    const currentDate = now.toISOString().split('T')[0];
    const currentTime = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
    console.log('⏰ Current date/time:', currentDate, currentTime);
    
    // Remove all existing badges first
    document.querySelectorAll('.reservation-badge').forEach(badge => badge.remove());
    
    for (const [key, reservation] of Object.entries(reservations)) {
        console.log('⏰ Checking reservation:', key, reservation);
        const table = document.querySelector(`[data-table="${reservation.table}"]`);
        if (!table) {
            console.log('⏰ Table not found:', reservation.table);
            continue;
        }
        
        // Add reservation badge to table
        if (table.classList.contains('blocked')) {
            console.log('⏰ Adding badge to table:', reservation.table);
            const badge = document.createElement('div');
            badge.className = 'reservation-badge';
            badge.textContent = 'R';
            badge.title = `Reserved for ${reservation.customerName}`;
            badge.onclick = function(e) {
                e.stopPropagation();
                showReservationDetails(reservation, key);
            };
            table.appendChild(badge);
        }
    }
    
    console.log('⏰ checkReservationTimesAPI completed');
}

// Updated function to handle guest arrival with API
async function handleGuestArrival(reservationId, tableName) {
    console.log('🚨 Guest Arrived - API call');
    console.log('🚨 Reservation ID:', reservationId);
    console.log('🚨 Table:', tableName);
    
    const success = await markGuestArrived(reservationId);
    
    if (success) {
        const table = document.querySelector(`[data-table="${tableName}"]`);
        if (table) {
            table.classList.remove('blocked');
            table.classList.add('utilized');
            
            // Remove reservation badge
            const badge = table.querySelector('.reservation-badge');
            if (badge) badge.remove();
            
            console.log('🚨 Guest arrival processed successfully');
            return true;
        }
    }
    
    console.error('🚨 Failed to process guest arrival');
    return false;
}

// Updated function to handle reservation cancellation with API
async function handleReservationCancellation(reservationId, tableName) {
    const success = await cancelReservation(reservationId);
    
    if (success) {
        const table = document.querySelector(`[data-table="${tableName}"]`);
        if (table) {
            table.classList.remove('blocked');
            table.classList.add('free');
            
            // Remove reservation badge
            const badge = table.querySelector('.reservation-badge');
            if (badge) badge.remove();
        }
        return true;
    }
    
    return false;
}

// Updated function to handle order completion with API
async function handleOrderCompletion(orderId, tableName) {
    const success = await completeTableOrder(orderId);
    
    if (success) {
        const table = document.querySelector(`[data-table="${tableName}"]`);
        if (table) {
            table.classList.remove('utilized', 'blocked');
            table.classList.add('free');
            
            // Remove complete order button
            const completeBtn = table.querySelector('.complete-order-btn');
            if (completeBtn) completeBtn.remove();
        }
        return true;
    }
    
    return false;
}

// Initialize API-based table system
async function initializeTableSystemAPI() {
    console.log('🚀 Initializing API-based table system...');
    
    // Restore table statuses from API
    await restoreTableStatusesFromAPI();
    
    // Check reservation times
    await checkReservationTimesAPI();
    
    // Set up periodic checks (every minute)
    setInterval(checkReservationTimesAPI, 60000);
    
    console.log('✅ API-based table system initialized');
}

// Call initialization when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeTableSystemAPI);
} else {
    initializeTableSystemAPI();
}