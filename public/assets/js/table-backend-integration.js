/**
 * ============================================
 * Table Backend Integration Functions
 * ============================================
 * 
 * دوال مساعدة لتحميل وعرض الطاولات من Backend API
 */

// Load and display tables from backend (don't replace existing)
async function loadAndDisplayTablesFromBackend() {
    try {
        console.log('🔄 Loading tables from backend...');
        const tables = await TableReservationAPI.fetchTables();
        console.log('📋 Tables loaded from database:', tables);
        
        if (tables && tables.length > 0) {
            // Add tables from database that don't exist in DOM
            const floorPlan = document.querySelector('.restaurant-floor-plan');
            if (floorPlan) {
                tables.forEach(table => {
                    // Only add if table doesn't already exist in DOM
                    if (!document.querySelector(`[data-table="${table.table_name}"]`)) {
                        const tableElement = createTableElementFromBackend(table);
                        floorPlan.appendChild(tableElement);
                        
                        // Add event listeners
                        if (typeof addTableEventListeners === 'function') {
                            addTableEventListeners(tableElement);
                        }
                        if (typeof makeDraggable === 'function') {
                            makeDraggable(tableElement);
                        }
                    }
                });
            }
        }
        
        console.log('✅ Tables loaded and displayed successfully');
    } catch (error) {
        console.error('❌ Error loading tables:', error);
        // Don't show error to user, just log it
    }
}

// Create table element from backend data
function createTableElementFromBackend(table) {
    const tableElement = document.createElement('div');
    tableElement.className = `table-item ${table.status}`;
    tableElement.setAttribute('data-table', table.table_name);
    tableElement.setAttribute('data-custom', table.is_custom ? 'true' : 'false');
    
    // Set position
    if (table.position_top) tableElement.style.top = table.position_top;
    if (table.position_left) tableElement.style.left = table.position_left;
    if (table.position_right) tableElement.style.right = table.position_right;
    if (table.position_bottom) tableElement.style.bottom = table.position_bottom;
    
    // Set rotation
    if (table.rotation) tableElement.setAttribute('data-rotation', table.rotation);
    
    // Set table type class
    const typeClass = getTableTypeClass(table.table_type, table.chair_count);
    tableElement.classList.add(typeClass);
    
    // Add table name
    const nameSpan = document.createElement('span');
    nameSpan.className = 'table-name';
    nameSpan.textContent = table.table_name;
    tableElement.appendChild(nameSpan);
    
    // Add chairs
    const chairWrapper = document.createElement('div');
    chairWrapper.className = 'chair-wrapper';
    
    const chairClasses = getChairClasses(table.table_type, table.chair_count);
    chairClasses.forEach(chairClass => {
        const chair = document.createElement('div');
        chair.className = `chair ${chairClass}`;
        chairWrapper.appendChild(chair);
    });
    
    tableElement.appendChild(chairWrapper);
    return tableElement;
}

// Get table type CSS class
function getTableTypeClass(tableType, chairCount) {
    if (tableType === 'rectangle' && chairCount === 12) return 'table-rectangle';
    if (tableType === 'rectangle-h10' && chairCount === 10) return 'table-rectangle-h10';
    if (tableType === 'rectangle-h' && chairCount === 8) return 'table-rectangle-h';
    if (tableType === 'rounded' && chairCount === 6) return 'table-rounded';
    return 'table-circle';
}

// Get chair classes based on table type and count
function getChairClasses(tableType, chairCount) {
    if (chairCount === 12) {
        return ['chair-top', 'chair-right-1', 'chair-right-2', 'chair-right-3', 'chair-right-4', 'chair-right-5', 'chair-bottom', 'chair-left-1', 'chair-left-2', 'chair-left-3', 'chair-left-4', 'chair-left-5'];
    } else if (chairCount === 10) {
        return ['chair-top-1', 'chair-top-2', 'chair-top-3', 'chair-top-4', 'chair-right', 'chair-bottom-1', 'chair-bottom-2', 'chair-bottom-3', 'chair-bottom-4', 'chair-left'];
    } else if (chairCount === 8) {
        return ['chair-top-1', 'chair-top-2', 'chair-top-3', 'chair-right', 'chair-bottom-1', 'chair-bottom-2', 'chair-bottom-3', 'chair-left'];
    } else if (chairCount === 6) {
        return ['chair-top-left', 'chair-top-right', 'chair-right', 'chair-bottom-right', 'chair-bottom-left', 'chair-left'];
    } else if (chairCount === 4) {
        return ['chair-top', 'chair-right', 'chair-bottom', 'chair-left'];
    } else if (chairCount === 2) {
        return ['chair-top', 'chair-bottom'];
    }
    return ['chair-top', 'chair-right', 'chair-bottom', 'chair-left'];
}

// Save custom table to backend API
async function saveCustomTableToBackend(tableElement) {
    const tableName = tableElement.getAttribute('data-table');
    const chairCount = tableElement.querySelectorAll('.chair').length;
    
    // Get table type based on classes
    let tableType = 'circle';
    if (tableElement.classList.contains('table-rectangle')) tableType = 'rectangle';
    else if (tableElement.classList.contains('table-rectangle-h10')) tableType = 'rectangle-h10';
    else if (tableElement.classList.contains('table-rectangle-h')) tableType = 'rectangle-h';
    else if (tableElement.classList.contains('table-rounded')) tableType = 'rounded';
    
    const tableData = {
        table_name: tableName,
        table_type: tableType,
        chair_count: chairCount,
        position_top: tableElement.style.top,
        position_left: tableElement.style.left,
        position_right: tableElement.style.right || null,
        position_bottom: tableElement.style.bottom || null,
        rotation: tableElement.getAttribute('data-rotation') || '0',
        status: 'free',
        is_custom: true
    };
    
    try {
        const result = await TableReservationAPI.createTable(tableData);
        console.log('✅ Table saved to backend:', result);
        return result;
    } catch (error) {
        console.error('❌ Error saving table to backend:', error);
        alert('خطأ في حفظ الطاولة: ' + error.message);
        throw error;
    }
}

console.log('✅ Table Backend Integration loaded successfully');