/**
 * Simple Table API Integration
 * Replaces localStorage with API calls directly
 */

// IMMEDIATE LOG - Should appear first
console.log('%c🚀 TABLE API SIMPLE - FILE LOADED!', 'background: #222; color: #bada55; font-size: 20px; padding: 10px;');
console.log('Current URL:', window.location.href);
console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.content);

console.log('🚀 Table API Simple Integration Loading...');

// API Helper
const API_BASE = '/api/business';
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content;

async function apiCall(endpoint, method = 'GET', data = null) {
    const options = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json'
        }
    };

    if (data && (method === 'POST' || method === 'PUT')) {
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(`${API_BASE}${endpoint}`, options);
        const result = await response.json();
        
        if (!response.ok) {
            console.error('API Error:', result);
            throw new Error(result.message || 'API request failed');
        }
        
        return result;
    } catch (error) {
        console.error('API Call Failed:', error);
        throw error;
    }
}

// Wait for page to load
document.addEventListener('DOMContentLoaded', function() {
    console.log('📋 Initializing Table API...');
    
    // Load tables from API when switching to tables tab
    const tablesTab = document.querySelector('[data-tab="tables"], [data-view="tables"]');
    if (tablesTab) {
        tablesTab.addEventListener('click', async function() {
            console.log('🔄 Loading tables from API...');
            await loadTablesFromAPI();
        });
    }
    
    // Initial load if already on tables tab
    const tablesView = document.getElementById('tables-view');
    if (tablesView && tablesView.style.display !== 'none') {
        setTimeout(() => loadTablesFromAPI(), 500);
    }
});

// Load tables from API
async function loadTablesFromAPI() {
    try {
        console.log('📡 Fetching tables from API...');
        const result = await apiCall('/tables');
        const tables = result.data || [];
        
        console.log(`✅ Loaded ${tables.length} tables from API`);
        
        // Render tables
        const floorPlan = document.querySelector('.restaurant-floor-plan');
        if (!floorPlan) {
            console.error('Floor plan not found');
            return;
        }
        
        // Remove existing tables (keep areas)
        floorPlan.querySelectorAll('.table-item').forEach(el => el.remove());
        
        // Add tables from API
        tables.forEach(table => {
            const tableEl = createTableElement(table);
            floorPlan.appendChild(tableEl);
        });
        
        // Initialize drag and drop
        initializeDragDrop();
        
        console.log('✅ Tables rendered successfully');
    } catch (error) {
        console.error('❌ Failed to load tables:', error);
    }
}

// Create table element
function createTableElement(table) {
    const div = document.createElement('div');
    div.className = `table-item ${table.table_type} ${table.status}`;
    div.setAttribute('data-table', table.table_name);
    div.setAttribute('data-table-id', table.id);
    div.setAttribute('data-chairs', table.chair_count);
    div.setAttribute('data-custom', table.is_custom ? 'true' : 'false');
    div.draggable = true;

    // Apply position
    if (table.position_top) div.style.top = table.position_top;
    if (table.position_left) div.style.left = table.position_left;
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

    return div;
}

// Initialize drag and drop
function initializeDragDrop() {
    const tables = document.querySelectorAll('.table-item');
    
    tables.forEach(table => {
        table.addEventListener('dragstart', handleDragStart);
        table.addEventListener('dragend', handleDragEnd);
    });

    const floorPlan = document.querySelector('.restaurant-floor-plan');
    if (floorPlan) {
        floorPlan.addEventListener('dragover', handleDragOver);
        floorPlan.addEventListener('drop', handleDrop);
    }
}

function handleDragStart(e) {
    e.dataTransfer.effectAllowed = 'move';
    this.classList.add('dragging');
}

async function handleDragEnd(e) {
    this.classList.remove('dragging');
    
    const tableId = this.getAttribute('data-table-id');
    if (!tableId) {
        console.warn('Table ID not found, cannot save position');
        return;
    }
    
    const rect = this.getBoundingClientRect();
    const parent = this.parentElement.getBoundingClientRect();
    
    const position = {
        position_top: `${rect.top - parent.top}px`,
        position_left: `${rect.left - parent.left}px`,
        rotation: 0
    };

    try {
        console.log('💾 Saving table position...', position);
        await apiCall(`/tables/${tableId}/position`, 'POST', position);
        console.log('✅ Position saved');
    } catch (error) {
        console.error('❌ Failed to save position:', error);
        alert('Failed to save table position');
    }
}

function handleDragOver(e) {
    if (e.preventDefault) {
        e.preventDefault();
    }
    e.dataTransfer.dropEffect = 'move';
    return false;
}

function handleDrop(e) {
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    return false;
}

// Override the save new table button
setTimeout(function() {
    const saveNewTableBtn = document.getElementById('save-new-table');
    if (saveNewTableBtn) {
        // Remove old event listeners by cloning
        const newBtn = saveNewTableBtn.cloneNode(true);
        saveNewTableBtn.parentNode.replaceChild(newBtn, saveNewTableBtn);
        
        // Add new event listener
        newBtn.addEventListener('click', async function() {
            const tableName = document.getElementById('new-table-name').value.trim();
            const tableType = document.getElementById('new-table-type').value;
            const chairCount = parseInt(document.getElementById('new-table-chairs').value);

            if (!tableName) {
                alert('{{ __("Please enter table name") }}');
                return;
            }

            if (!tableType) {
                alert('{{ __("Please select table type") }}');
                return;
            }

            if (!chairCount || chairCount < 1) {
                alert('{{ __("Please enter valid number of chairs") }}');
                return;
            }

            try {
                console.log('➕ Creating new table...');
                const result = await apiCall('/tables', 'POST', {
                    table_name: tableName,
                    table_type: tableType,
                    chair_count: chairCount,
                    position_top: '100px',
                    position_left: '100px',
                    is_custom: true
                });
                
                console.log('✅ Table created:', result);
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addTableModal'));
                if (modal) modal.hide();
                
                // Reload tables
                await loadTablesFromAPI();
                
                // Clear form
                document.getElementById('new-table-name').value = '';
                document.getElementById('new-table-type').value = '';
                document.getElementById('new-table-chairs').value = '';
                
                // NO ALERT - Silent success
                console.log('✅ Table added successfully (no alert)');
                
            } catch (error) {
                console.error('❌ Failed to create table:', error);
                alert('Failed to create table: ' + error.message);
            }
        });
        
        console.log('✅ Save new table button overridden');
    }
}, 1000);

console.log('✅ Table API Simple Integration Loaded');
