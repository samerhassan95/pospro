/**
 * Table Reservation System - Backend Integration
 * Replaces localStorage with full backend API calls
 */

(function() {
    'use strict';

    // API Endpoints
    const API = {
        tables: '/business/tables',
        reservations: '/business/table-reservations',
        orders: '/business/table-orders'
    };

    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    // ============================================
    // TABLES API
    // ============================================

    /**
     * Get all tables from backend
     */
    window.getTablesFromBackend = async function() {
        try {
            console.log('🔄 Fetching tables from backend:', API.tables);
            const response = await fetch(API.tables, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            console.log('📊 Response status:', response.status);
            console.log('📊 Response headers:', Object.fromEntries(response.headers.entries()));
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('❌ Response is not JSON. Content-Type:', contentType);
                console.error('❌ Response body (first 500 chars):', text.substring(0, 500));
                throw new Error('Server returned non-JSON response');
            }
            
            const data = await response.json();
            console.log('📥 Tables response:', data);
            console.log('📊 Response status:', response.status);
            console.log('📊 Response ok:', response.ok);
            
            if (!response.ok) {
                console.error('❌ HTTP Error:', response.status, response.statusText);
                console.error('❌ Response data:', data);
            }
            
            if (data.success && data.data) {
                console.log(`✅ Loaded ${data.data.length} tables from database`);
                return data.data;
            }
            
            // If no data field, return empty array
            if (data.success && !data.data) {
                console.log('⚠️ Success but no data field in response');
                return [];
            }
            
            throw new Error(data.message || 'Failed to fetch tables');
        } catch (error) {
            console.error('❌ Error fetching tables:', error);
            return [];
        }
    };

    /**
     * Create new table
     */
    window.createTableInBackend = async function(tableData) {
        try {
            console.log('🔄 Creating table in backend:', tableData);
            const response = await fetch(API.tables, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(tableData)
            });
            
            const data = await response.json();
            console.log('📥 Create table response:', data);
            
            if (data.success) {
                console.log('✅ Table created in database:', data.data);
                return data.data;
            }
            
            // If not successful, throw error with message from backend
            throw new Error(data.message || 'Failed to create table');
        } catch (error) {
            console.error('❌ Error creating table:', error);
            throw error;
        }
    };

    /**
     * Update table position
     */
    window.updateTablePosition = async function(tableId, positionData) {
        try {
            console.log('🔄 Updating table position:', tableId, positionData);
            const response = await fetch(`${API.tables}/${tableId}/position`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(positionData)
            });
            
            const data = await response.json();
            console.log('📥 Update position response:', data);
            
            if (data.success) {
                console.log('✅ Table position saved to database');
                return data.data;
            }
            throw new Error(data.message || 'Failed to update position');
        } catch (error) {
            console.error('❌ Error updating table position:', error);
            throw error;
        }
    };

    /**
     * Rotate table
     */
    window.rotateTableInBackend = async function(tableId, degrees = 90) {
        try {
            console.log('🔄 Rotating table:', tableId, degrees);
            const response = await fetch(`${API.tables}/${tableId}/rotate`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ degrees })
            });
            
            const data = await response.json();
            console.log('📥 Rotate response:', data);
            
            if (data.success) {
                console.log('✅ Table rotation saved to database');
                return data.data;
            }
            throw new Error(data.message || 'Failed to rotate table');
        } catch (error) {
            console.error('❌ Error rotating table:', error);
            throw error;
        }
    };

    /**
     * Delete table
     */
    window.deleteTableFromBackend = async function(tableId) {
        try {
            console.log('🔄 Deleting table:', tableId);
            const response = await fetch(`${API.tables}/${tableId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const data = await response.json();
            console.log('📥 Delete response:', data);
            
            if (data.success) {
                console.log('✅ Table deleted from database');
                return true;
            }
            throw new Error(data.message || 'Failed to delete table');
        } catch (error) {
            console.error('❌ Error deleting table:', error);
            throw error;
        }
    };

    // ============================================
    // RESERVATIONS API
    // ============================================

    /**
     * Get all reservations
     */
    window.getReservationsFromBackend = async function() {
        try {
            const response = await fetch(API.reservations, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const data = await response.json();
            if (data.success) {
                return data.data;
            }
            throw new Error(data.message || 'Failed to fetch reservations');
        } catch (error) {
            console.error('Error fetching reservations:', error);
            return [];
        }
    };

    /**
     * Create reservation
     */
    window.createReservationInBackend = async function(reservationData) {
        try {
            const response = await fetch(API.reservations, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(reservationData)
            });
            
            const data = await response.json();
            if (data.success) {
                return data.data;
            }
            throw new Error(data.message || 'Failed to create reservation');
        } catch (error) {
            console.error('Error creating reservation:', error);
            throw error;
        }
    };

    /**
     * Mark guest as arrived
     */
    window.guestArrivedInBackend = async function(reservationId) {
        try {
            const response = await fetch(`${API.reservations}/${reservationId}/guest-arrived`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const data = await response.json();
            if (data.success) {
                return true;
            }
            throw new Error(data.message || 'Failed to mark guest as arrived');
        } catch (error) {
            console.error('Error marking guest as arrived:', error);
            throw error;
        }
    };

    /**
     * Cancel reservation
     */
    window.cancelReservationInBackend = async function(reservationId) {
        try {
            const response = await fetch(`${API.reservations}/${reservationId}/cancel`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const data = await response.json();
            if (data.success) {
                return true;
            }
            throw new Error(data.message || 'Failed to cancel reservation');
        } catch (error) {
            console.error('Error cancelling reservation:', error);
            throw error;
        }
    };

    // ============================================
    // ORDERS API
    // ============================================

    /**
     * Get all orders
     */
    window.getOrdersFromBackend = async function() {
        try {
            const response = await fetch(API.orders, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const data = await response.json();
            if (data.success) {
                return data.data;
            }
            throw new Error(data.message || 'Failed to fetch orders');
        } catch (error) {
            console.error('Error fetching orders:', error);
            return [];
        }
    };

    /**
     * Create or update order
     */
    window.saveOrderToBackend = async function(orderData) {
        try {
            const response = await fetch(API.orders, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(orderData)
            });
            
            const data = await response.json();
            if (data.success) {
                return data.data;
            }
            throw new Error(data.message || 'Failed to save order');
        } catch (error) {
            console.error('Error saving order:', error);
            throw error;
        }
    };

    /**
     * Complete order
     */
    window.completeOrderInBackend = async function(orderId) {
        try {
            const response = await fetch(`${API.orders}/${orderId}/complete`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const data = await response.json();
            if (data.success) {
                return true;
            }
            throw new Error(data.message || 'Failed to complete order');
        } catch (error) {
            console.error('Error completing order:', error);
            throw error;
        }
    };

    // ============================================
    // HELPER FUNCTIONS
    // ============================================

    /**
     * Load all tables and render them
     */
    window.loadAndRenderTables = async function() {
        try {
            console.log('🔄 Loading tables from backend...');
            const tables = await getTablesFromBackend();
            const floorPlan = document.getElementById('restaurant-floor-plan');
            
            console.log('📊 Floor plan element:', floorPlan);
            console.log('📊 Tables to render:', tables.length);
            
            if (!floorPlan) {
                console.error('❌ Floor plan element not found!');
                return;
            }

            // Clear existing dynamic tables only (keep static areas like Bar, Toilets, Entrance)
            const existingTables = floorPlan.querySelectorAll('.table-item:not(.floor-area):not(.entrance-area)');
            console.log('🗑️ Removing existing tables:', existingTables.length);
            existingTables.forEach(table => table.remove());

            // Render each table from database
            tables.forEach(table => {
                console.log('🎨 Rendering table:', table.table_name);
                renderTable(table);
            });

            console.log(`✅ Loaded ${tables.length} tables from backend`);
        } catch (error) {
            console.error('❌ Error loading tables:', error);
        }
    };

    /**
     * Render a single table
     */
    function renderTable(tableData) {
        const floorPlan = document.getElementById('restaurant-floor-plan');
        if (!floorPlan) {
            console.error('❌ Floor plan not found for rendering table');
            return;
        }

        const tableElement = document.createElement('div');
        tableElement.className = `table-item ${tableData.status || 'free'}`;
        tableElement.dataset.table = tableData.table_name;
        tableElement.dataset.tableId = tableData.id;
        tableElement.dataset.type = tableData.table_type;
        tableElement.dataset.chairs = tableData.chair_count;
        tableElement.dataset.isCustom = tableData.is_custom;
        
        // Make it draggable
        tableElement.style.position = 'absolute';
        tableElement.style.cursor = 'grab';

        // Apply position
        if (tableData.position_top) tableElement.style.top = tableData.position_top;
        if (tableData.position_left) tableElement.style.left = tableData.position_left;
        if (tableData.position_right) tableElement.style.right = tableData.position_right;
        if (tableData.position_bottom) tableElement.style.bottom = tableData.position_bottom;
        if (tableData.rotation) tableElement.style.transform = `rotate(${tableData.rotation}deg)`;

        // Create table name label
        const nameSpan = document.createElement('span');
        nameSpan.className = 'table-name';
        nameSpan.textContent = tableData.table_name;
        tableElement.appendChild(nameSpan);

        // Add chairs
        const chairWrapper = document.createElement('div');
        chairWrapper.className = 'chair-wrapper';
        chairWrapper.innerHTML = renderChairs(tableData.table_type, tableData.chair_count);
        tableElement.appendChild(chairWrapper);

        floorPlan.appendChild(tableElement);

        // Make draggable
        makeTableDraggable(tableElement);

        // Add event listeners
        attachTableEventListeners(tableElement);
        
        console.log(`✅ Rendered table: ${tableData.table_name}`);
    }

    /**
     * Render chairs around table
     */
    function renderChairs(tableType, chairCount) {
        let chairsHTML = '';
        
        // Simple chair rendering based on count
        for (let i = 0; i < chairCount; i++) {
            const position = getChairPosition(i, chairCount, tableType);
            chairsHTML += `<div class="chair chair-${position}"></div>`;
        }
        
        return chairsHTML;
    }

    /**
     * Get chair position class
     */
    function getChairPosition(index, total, tableType) {
        if (tableType === 'circle' || tableType === 'rounded') {
            const positions = ['top', 'right', 'bottom', 'left', 'top-right', 'bottom-right', 'bottom-left', 'top-left'];
            return positions[index % positions.length];
        } else {
            // Rectangle tables
            const positions = ['top', 'right', 'bottom', 'left'];
            return positions[index % positions.length];
        }
    }

    /**
     * Make table draggable
     */
    function makeTableDraggable(tableElement) {
        let isDragging = false;
        let startX, startY, startLeft, startTop;

        tableElement.addEventListener('mousedown', function(e) {
            if (e.target.closest('.table-action-btn')) return;
            
            isDragging = true;
            startX = e.clientX;
            startY = e.clientY;
            startLeft = tableElement.offsetLeft;
            startTop = tableElement.offsetTop;
            
            tableElement.style.cursor = 'grabbing';
        });

        document.addEventListener('mousemove', function(e) {
            if (!isDragging) return;
            
            const deltaX = e.clientX - startX;
            const deltaY = e.clientY - startY;
            
            tableElement.style.left = (startLeft + deltaX) + 'px';
            tableElement.style.top = (startTop + deltaY) + 'px';
        });

        document.addEventListener('mouseup', async function() {
            if (!isDragging) return;
            
            isDragging = false;
            tableElement.style.cursor = 'grab';
            
            // Save position to backend
            const tableId = tableElement.dataset.tableId;
            const positionData = {
                position_top: tableElement.style.top,
                position_left: tableElement.style.left
            };
            
            try {
                await updateTablePosition(tableId, positionData);
                console.log('✅ Table position saved');
            } catch (error) {
                console.error('❌ Failed to save table position');
            }
        });
    }

    /**
     * Attach event listeners to table
     */
    function attachTableEventListeners(tableElement) {
        // Rotate button
        const rotateBtn = tableElement.querySelector('.rotate-btn');
        if (rotateBtn) {
            rotateBtn.addEventListener('click', async function(e) {
                e.stopPropagation();
                const tableId = tableElement.dataset.tableId;
                
                try {
                    const updatedTable = await rotateTableInBackend(tableId);
                    tableElement.style.transform = `rotate(${updatedTable.rotation}deg)`;
                    console.log('✅ Table rotated');
                } catch (error) {
                    console.error('❌ Failed to rotate table');
                }
            });
        }

        // Delete button
        const deleteBtn = tableElement.querySelector('.delete-btn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', async function(e) {
                e.stopPropagation();
                
                const tableId = tableElement.dataset.tableId;
                const tableName = tableElement.dataset.table;
                
                console.log(`🗑️ Delete requested for table: ${tableName}`);
                
                try {
                    await deleteTableFromBackend(tableId);
                    tableElement.remove();
                    console.log(`✅ Table ${tableName} deleted successfully`);
                } catch (error) {
                    console.error(`❌ Failed to delete table ${tableName}:`, error);
                }
            });
        }

        // Click to select/order
        tableElement.addEventListener('click', function() {
            // Handle table selection for ordering
            console.log('Table clicked:', tableElement.dataset.table);
            // You can add your order logic here
        });
    }

    // ============================================
    // TAB SWITCHING & UI FUNCTIONALITY
    // ============================================

    /**
     * Initialize tab switching between Products and Tables
     */
    function initTabSwitching() {
        console.log('🔄 Initializing tab switching...');
        
        const tabButtons = document.querySelectorAll('.pos-tab-btn, .pos-view-btn');
        const tablesSection = document.getElementById('tables-view');
        const productsSection = document.getElementById('products-section');
        const productsGridSection = document.querySelector('.pos-products-section');

        console.log('📊 Found tab buttons:', tabButtons.length);
        console.log('📊 Tables section:', tablesSection);
        console.log('📊 Products section:', productsSection);

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tab = this.getAttribute('data-tab') || this.getAttribute('data-view');
                console.log('🖱️ Tab clicked:', tab, this);

                // Remove active from all buttons
                tabButtons.forEach(btn => {
                    btn.classList.remove('pos-toggle-btn-active', 'active');
                });
                
                // Add active to clicked button
                if (this.classList.contains('pos-toggle-btn')) {
                    this.classList.add('pos-toggle-btn-active');
                } else {
                    this.classList.add('active');
                }

                // Show/hide sections
                if (tab === 'tables') {
                    console.log('✅ Switching to Tables view');
                    if (tablesSection) tablesSection.style.display = 'block';
                    if (productsSection) productsSection.style.display = 'none';
                    if (productsGridSection) productsGridSection.style.display = 'none';
                    
                    // Hide other views
                    const brandView = document.getElementById('brand-view');
                    const categoryView = document.getElementById('category-view');
                    const searchView = document.getElementById('search-view');
                    if (brandView) brandView.style.display = 'none';
                    if (categoryView) categoryView.style.display = 'none';
                    if (searchView) searchView.style.display = 'none';
                } else {
                    console.log('✅ Switching to Products view');
                    if (tablesSection) tablesSection.style.display = 'none';
                    if (productsSection) productsSection.style.display = 'block';
                    if (productsGridSection) productsGridSection.style.display = 'block';
                }
            });
        });
        
        console.log('✅ Tab switching initialized');
    }

    // ============================================
    // INITIALIZATION
    // ============================================

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        loadAndRenderTables();
        initTabSwitching();
    });

    console.log('✅ Table Backend Integration loaded');

})();
