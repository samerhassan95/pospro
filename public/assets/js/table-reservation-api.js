/**
 * ============================================
 * Table Reservation System - API Integration
 * ============================================
 *
 * هذا الملف يحتوي على جميع الدوال اللي بتتعامل مع الـ Backend API
 * بدلاً من localStorage
 *
 * استخدام: استبدل جميع localStorage calls بالدوال الموجودة هنا
 */

// ============================================
// Helper Functions
// ============================================

/**
 * Get CSRF Token from meta tag
 */
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

/**
 * Make API Request with error handling
 */
async function apiRequest(url, method = 'GET', data = null) {
    console.log(`🌐 API Request: ${method} ${url}`, data);
    
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    };

    if (data && (method === 'POST' || method === 'PUT')) {
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(url, options);
        console.log(`📊 API Response Status: ${response.status}`);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('API Response Error:', response.status, errorText);
            throw new Error(`HTTP ${response.status}: ${errorText}`);
        }
        
        const result = await response.json();
        console.log('📊 API Response Data:', result);
        return result;
    } catch (error) {
        console.error('❌ API Error:', error);
        throw error;
    }
}

// ============================================
// TABLES API
// ============================================

/**
 * جلب جميع الطاولات
 * @returns {Promise<Array>} - قائمة الطاولات
 */
async function fetchTables() {
    const result = await apiRequest('/business/api/tables');
    return result.data || [];
}

/**
 * إنشاء طاولة جديدة
 * @param {Object} tableData - بيانات الطاولة
 * @returns {Promise<Object>} - الطاولة المنشأة
 */
async function createTable(tableData) {
    const result = await apiRequest('/business/api/tables', 'POST', tableData);
    return result.data;
}

/**
 * تحديث طاولة موجودة
 * @param {number} tableId - معرف الطاولة
 * @param {Object} tableData - البيانات المحدثة
 * @returns {Promise<Object>}
 */
async function updateTable(tableId, tableData) {
    const result = await apiRequest(`/business/api/tables/${tableId}`, 'PUT', tableData);
    return result.data;
}

/**
 * حذف طاولة
 * @param {number} tableId - معرف الطاولة
 */
async function deleteTable(tableId) {
    return await apiRequest(`/business/api/tables/${tableId}`, 'DELETE');
}

/**
 * تحديث حالة الطاولة (free, utilized, blocked)
 * @param {number} tableId - معرف الطاولة
 * @param {string} status - الحالة الجديدة
 */
async function updateTableStatus(tableId, status) {
    const result = await apiRequest(`/api/business/tables/${tableId}/status`, 'PUT', { status });
    return result.data;
}

/**
 * تحديث موقع الطاولة
 * @param {number} tableId - معرف الطاولة
 * @param {Object} position - {top, left, right, bottom, rotation}
 */
async function updateTablePosition(tableId, position) {
    const result = await apiRequest(`/api/business/tables/${tableId}/position`, 'PUT', position);
    return result.data;
}

/**
 * تدوير الطاولة
 * @param {number} tableId - معرف الطاولة
 * @param {number} degrees - درجة الدوران (90, 180, 270)
 */
async function rotateTable(tableId, degrees = 90) {
    const result = await apiRequest(`/api/business/tables/${tableId}/rotate`, 'POST', { degrees });
    return result.data;
}

// ============================================
// RESERVATIONS API
// ============================================

/**
 * جلب جميع الحجوزات
 * @param {string} date - التاريخ (optional)
 * @returns {Promise<Array>}
 */
async function fetchReservations(date = null) {
    let url = '/api/business/reservations';
    if (date) {
        url += `?date=${date}`;
    }
    const result = await apiRequest(url);
    return result.data || [];
}

/**
 * إنشاء حجز جديد
 * @param {Object} reservationData - بيانات الحجز
 * @returns {Promise<Object>}
 */
async function createReservation(reservationData) {
    const result = await apiRequest('/api/business/reservations', 'POST', reservationData);
    return result.data;
}

/**
 * تحديث حجز موجود
 * @param {number} reservationId - معرف الحجز
 * @param {Object} reservationData - البيانات المحدثة
 */
async function updateReservation(reservationId, reservationData) {
    const result = await apiRequest(`/api/business/reservations/${reservationId}`, 'PUT', reservationData);
    return result.data;
}

/**
 * إلغاء حجز
 * @param {number} reservationId - معرف الحجز
 */
async function cancelReservation(reservationId) {
    return await apiRequest(`/api/business/reservations/${reservationId}/cancel`, 'POST');
}

/**
 * تأكيد وصول العميل
 * @param {number} reservationId - معرف الحجز
 */
async function markReservationAsArrived(reservationId) {
    return await apiRequest(`/api/business/reservations/${reservationId}/arrive`, 'POST');
}

/**
 * البحث عن الطاولات المتاحة
 * @param {Object} criteria - {date, time, guests}
 */
async function searchAvailableTables(criteria) {
    const result = await apiRequest('/api/business/reservations/available-tables', 'POST', criteria);
    return result.data || [];
}

// ============================================
// TABLE ORDERS API
// ============================================

/**
 * جلب جميع الطلبات
 * @returns {Promise<Array>}
 */
async function fetchTableOrders() {
    const result = await apiRequest('/api/business/table-orders');
    return result.data || [];
}

/**
 * جلب طلبات طاولة معينة
 * @param {number} tableId - معرف الطاولة
 */
async function fetchTableOrdersByTable(tableId) {
    const result = await apiRequest(`/api/business/table-orders/table/${tableId}`);
    return result.data || [];
}

/**
 * إنشاء طلب جديد لطاولة
 * @param {Object} orderData - بيانات الطلب
 */
async function createTableOrder(orderData) {
    const result = await apiRequest('/api/business/table-orders', 'POST', orderData);
    return result.data;
}

/**
 * تحديث طلب موجود
 * @param {number} orderId - معرف الطلب
 * @param {Object} orderData - البيانات المحدثة
 */
async function updateTableOrder(orderId, orderData) {
    const result = await apiRequest(`/api/business/table-orders/${orderId}`, 'PUT', orderData);
    return result.data;
}

/**
 * إنهاء طلب
 * @param {number} orderId - معرف الطلب
 */
async function completeTableOrder(orderId) {
    return await apiRequest(`/api/business/table-orders/${orderId}/complete`, 'POST');
}

/**
 * ربط طلب بفاتورة بيع
 * @param {number} orderId - معرف الطلب
 * @param {number} saleId - معرف الفاتورة
 */
async function linkTableOrderToSale(orderId, saleId) {
    return await apiRequest(`/api/business/table-orders/${orderId}/link-sale`, 'POST', { sale_id: saleId });
}

/**
 * حذف طلب
 * @param {number} orderId - معرف الطلب
 */
async function deleteTableOrder(orderId) {
    return await apiRequest(`/api/business/table-orders/${orderId}`, 'DELETE');
}

// ============================================
// FLOOR PLAN LAYOUTS API
// ============================================

/**
 * جلب جميع تخطيطات الأرضية
 */
async function fetchFloorLayouts() {
    const result = await apiRequest('/api/business/floor-layouts');
    return result.data || [];
}

/**
 * جلب التخطيط النشط حالياً
 */
async function fetchActiveLayout() {
    const result = await apiRequest('/api/business/floor-layouts/active');
    return result.data;
}

/**
 * جلب التخطيط الافتراضي
 */
async function fetchDefaultLayout() {
    const result = await apiRequest('/api/business/floor-layouts/default');
    return result.data;
}

/**
 * حفظ تخطيط جديد
 * @param {Object} layoutData - {layout_name, description, is_default, layout_data}
 */
async function saveFloorLayout(layoutData) {
    const result = await apiRequest('/api/business/floor-layouts', 'POST', layoutData);
    return result.data;
}

/**
 * تطبيق/تفعيل تخطيط
 * @param {number} layoutId - معرف التخطيط
 */
async function activateFloorLayout(layoutId) {
    return await apiRequest(`/api/business/floor-layouts/${layoutId}/activate`, 'POST');
}

/**
 * تعيين تخطيط كافتراضي
 * @param {number} layoutId - معرف التخطيط
 */
async function setDefaultLayout(layoutId) {
    return await apiRequest(`/api/business/floor-layouts/${layoutId}/set-default`, 'POST');
}

/**
 * نسخ/تكرار تخطيط
 * @param {number} layoutId - معرف التخطيط
 */
async function duplicateFloorLayout(layoutId) {
    const result = await apiRequest(`/api/business/floor-layouts/${layoutId}/duplicate`, 'POST');
    return result.data;
}

/**
 * حذف تخطيط
 * @param {number} layoutId - معرف التخطيط
 */
async function deleteFloorLayout(layoutId) {
    return await apiRequest(`/api/business/floor-layouts/${layoutId}`, 'DELETE');
}

// ============================================
// MIGRATION HELPERS
// ============================================

/**
 * دوال مساعدة لتحويل localStorage إلى API
 * استخدمها لتحديث الكود القديم تدريجياً
 */

/**
 * استبدال localStorage.getItem('tableReservations')
 */
async function getReservationsFromStorage() {
    try {
        const reservations = await fetchReservations();
        // تحويل إلى نفس الشكل المستخدم في localStorage (object with table names as keys)
        const reservationsObj = {};
        reservations.forEach(res => {
            if (!reservationsObj[res.table.table_name]) {
                reservationsObj[res.table.table_name] = [];
            }
            reservationsObj[res.table.table_name].push({
                id: res.id,
                customerName: res.customer_name,
                phone: res.customer_phone,
                date: res.reservation_date,
                time: res.reservation_time,
                guests: res.number_of_guests,
                notes: res.special_notes,
                status: res.status,
                timeArrived: res.time_arrived
            });
        });
        return reservationsObj;
    } catch (error) {
        console.error('Error fetching reservations:', error);
        return {};
    }
}

/**
 * استبدال localStorage.getItem('tableOrders')
 */
async function getOrdersFromStorage() {
    try {
        const orders = await fetchTableOrders();
        // تحويل إلى نفس الشكل المستخدم في localStorage
        const ordersObj = {};
        orders.forEach(order => {
            if (!ordersObj[order.table.table_name]) {
                ordersObj[order.table.table_name] = [];
            }
            ordersObj[order.table.table_name].push({
                id: order.id,
                customerName: order.customer_name,
                guests: order.number_of_guests,
                orderItems: order.order_items,
                notes: order.special_notes,
                orderTime: order.order_time,
                status: order.status,
                saleId: order.sale_id
            });
        });
        return ordersObj;
    } catch (error) {
        console.error('Error fetching orders:', error);
        return {};
    }
}

/**
 * حفظ حجز جديد (استبدال localStorage.setItem)
 */
async function saveReservationToBackend(tableName, reservationData) {
    try {
        // أولاً، نحتاج معرف الطاولة
        const tables = await fetchTables();
        const table = tables.find(t => t.table_name === tableName);

        if (!table) {
            throw new Error(`Table ${tableName} not found`);
        }

        // إنشاء الحجز
        const reservation = await createReservation({
            table_id: table.id,
            customer_name: reservationData.customerName,
            customer_phone: reservationData.phone,
            reservation_date: reservationData.date,
            reservation_time: reservationData.time,
            number_of_guests: reservationData.guests,
            special_notes: reservationData.notes
        });

        // تحديث حالة الطاولة إلى blocked
        await updateTableStatus(table.id, 'blocked');

        return reservation;
    } catch (error) {
        console.error('Error saving reservation:', error);
        throw error;
    }
}

/**
 * حفظ طلب جديد (استبدال localStorage.setItem)
 */
async function saveOrderToBackend(tableName, orderData) {
    try {
        // جلب معرف الطاولة
        const tables = await fetchTables();
        const table = tables.find(t => t.table_name === tableName);

        if (!table) {
            throw new Error(`Table ${tableName} not found`);
        }

        // إنشاء الطلب
        const order = await createTableOrder({
            table_id: table.id,
            customer_name: orderData.customerName,
            number_of_guests: orderData.guests,
            order_items: orderData.orderItems,
            special_notes: orderData.notes,
            order_time: orderData.orderTime
        });

        // تحديث حالة الطاولة إلى utilized
        await updateTableStatus(table.id, 'utilized');

        return order;
    } catch (error) {
        console.error('Error saving order:', error);
        throw error;
    }
}

/**
 * حذف حجز (استبدال delete من localStorage)
 */
async function removeReservationFromBackend(reservationId, tableId) {
    try {
        await cancelReservation(reservationId);

        // تحديث حالة الطاولة إلى free
        await updateTableStatus(tableId, 'free');

        return true;
    } catch (error) {
        console.error('Error removing reservation:', error);
        throw error;
    }
}

/**
 * حذف طلب (استبدال delete من localStorage)
 */
async function removeOrderFromBackend(orderId, tableId) {
    try {
        await deleteTableOrder(orderId);

        // تحديث حالة الطاولة إلى free
        await updateTableStatus(tableId, 'free');

        return true;
    } catch (error) {
        console.error('Error removing order:', error);
        throw error;
    }
}

// ============================================
// PAGE INITIALIZATION
// ============================================

/**
 * تحميل البيانات عند فتح الصفحة
 * استبدال restoreTableStatuses()
 */
async function initializeTablesFromBackend() {
    try {
        const tables = await fetchTables();

        // تحديث حالة كل طاولة في الواجهة
        tables.forEach(table => {
            const tableElement = document.querySelector(`[data-table="${table.table_name}"]`);
            if (tableElement) {
                // إزالة جميع الحالات
                tableElement.classList.remove('free', 'utilized', 'blocked');
                // إضافة الحالة الصحيحة
                tableElement.classList.add(table.status);

                // تحديث الموقع إذا كان موجود
                if (table.position_top) {
                    tableElement.style.top = table.position_top;
                }
                if (table.position_left) {
                    tableElement.style.left = table.position_left;
                }
                if (table.position_right) {
                    tableElement.style.right = table.position_right;
                }
                if (table.position_bottom) {
                    tableElement.style.bottom = table.position_bottom;
                }
                if (table.rotation) {
                    tableElement.style.transform = `rotate(${table.rotation}deg)`;
                }
            }
        });

        // عرض الحجوزات
        await displayReservationsFromBackend();

        // عرض الطلبات
        await displayOrdersFromBackend();

        console.log('✅ Tables initialized from backend');
    } catch (error) {
        console.error('❌ Error initializing tables:', error);
    }
}

/**
 * عرض الحجوزات على الطاولات
 */
async function displayReservationsFromBackend() {
    try {
        const reservations = await fetchReservations();

        reservations.forEach(reservation => {
            const tableName = reservation.table.table_name;
            const tableElement = document.querySelector(`[data-table="${tableName}"]`);

            if (tableElement && reservation.status === 'reserved') {
                // إضافة badge للحجز
                const badge = document.createElement('div');
                badge.className = 'reservation-badge';
                badge.innerHTML = `
                    <i class="fas fa-calendar-check"></i>
                    ${reservation.reservation_time}
                `;
                tableElement.appendChild(badge);
            }
        });
    } catch (error) {
        console.error('Error displaying reservations:', error);
    }
}

/**
 * عرض الطلبات على الطاولات
 */
async function displayOrdersFromBackend() {
    try {
        const orders = await fetchTableOrders();

        orders.forEach(order => {
            const tableName = order.table.table_name;
            const tableElement = document.querySelector(`[data-table="${tableName}"]`);

            if (tableElement && order.status === 'in_progress') {
                // إضافة badge للطلب
                const badge = document.createElement('div');
                badge.className = 'order-badge';
                badge.innerHTML = `
                    <i class="fas fa-utensils"></i>
                    ${order.number_of_guests} guests
                `;
                tableElement.appendChild(badge);
            }
        });
    } catch (error) {
        console.error('Error displaying orders:', error);
    }
}

// ============================================
// EXPORT FOR GLOBAL USE
// ============================================

// جعل الدوال متاحة globally
window.TableReservationAPI = {
    // Tables
    fetchTables,
    createTable,
    updateTable,
    deleteTable,
    updateTableStatus,
    updateTablePosition,
    rotateTable,

    // Reservations
    fetchReservations,
    createReservation,
    updateReservation,
    cancelReservation,
    markReservationAsArrived,
    searchAvailableTables,

    // Orders
    fetchTableOrders,
    fetchTableOrdersByTable,
    createTableOrder,
    updateTableOrder,
    completeTableOrder,
    linkTableOrderToSale,
    deleteTableOrder,

    // Layouts
    fetchFloorLayouts,
    fetchActiveLayout,
    fetchDefaultLayout,
    saveFloorLayout,
    activateFloorLayout,
    setDefaultLayout,
    duplicateFloorLayout,
    deleteFloorLayout,

    // Migration Helpers
    getReservationsFromStorage,
    getOrdersFromStorage,
    saveReservationToBackend,
    saveOrderToBackend,
    removeReservationFromBackend,
    removeOrderFromBackend,

    // Initialization
    initializeTablesFromBackend,
    displayReservationsFromBackend,
    displayOrdersFromBackend
};

// Compatibility alias for existing code
window.tableAPI = {
    fetchTables,
    createTable,
    updateTable,
    deleteTable
};

console.log('✅ Table Reservation API loaded successfully');
