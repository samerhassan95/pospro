/**
 * Table Delete Enhancement
 * Enhanced table deletion functionality with backend API integration
 */

// Enhanced delete table function with API integration
async function deleteTableWithAPI(tableName, tableId = null) {
    try {
        console.log(`🗑️ Attempting to delete table: ${tableName} (ID: ${tableId})`);
        
        // If no table ID provided, try to find it from backend
        if (!tableId) {
            const tables = await window.tableAPI.fetchTables();
            const table = tables.find(t => t.table_name === tableName);
            tableId = table?.id;
        }
        
        if (tableId) {
            // Delete from backend API
            const success = await window.tableAPI.deleteTable(tableId);
            
            if (success) {
                console.log(`✅ Table ${tableName} deleted from backend`);
                return true;
            } else {
                console.error(`❌ Failed to delete table ${tableName} from backend`);
                return false;
            }
        } else {
            console.warn(`⚠️ Table ${tableName} not found in backend, removing from localStorage only`);
            return true; // Allow localStorage cleanup
        }
    } catch (error) {
        console.error(`❌ Error deleting table ${tableName}:`, error);
        throw error;
    }
}

// Enhanced manage tables modal with better delete functionality
function createEnhancedManageTablesModal() {
    return new Promise(async (resolve) => {
        try {
            // Fetch tables from backend API
            const backendTables = await window.tableAPI.fetchTables();
            console.log('📊 Backend tables:', backendTables);
            
            // Get custom tables from localStorage for comparison
            const customTables = JSON.parse(localStorage.getItem('customTables') || '[]');
            console.log('💾 Custom tables from localStorage:', customTables);
            
            // Get all DOM tables
            const allDOMTables = document.querySelectorAll('.table-item');
            console.log('🏠 DOM tables count:', allDOMTables.length);
            
            let tablesList = `
                <div style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('Table Name') }}</th>
                                <th>{{ __('Chairs') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Source') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            // Process all DOM tables
            allDOMTables.forEach(table => {
                const tableName = table.getAttribute('data-table');
                const tableId = table.getAttribute('data-table-id');
                const chairCount = table.querySelectorAll('.chair').length;
                
                let status = 'Free';
                if (table.classList.contains('utilized')) status = 'Utilized';
                else if (table.classList.contains('blocked')) status = 'Reserved';
                
                // Check if table exists in backend
                const backendTable = backendTables.find(t => t.table_name === tableName);
                const isCustomFromBackend = backendTable?.is_custom || false;
                const isCustomFromLocalStorage = customTables.some(t => t.name === tableName);
                
                let tableType = 'Default';
                let source = 'Backend';
                let canDelete = false;
                
                if (backendTable) {
                    tableType = isCustomFromBackend ? 'Custom' : 'Default';
                    source = 'Backend';
                    canDelete = isCustomFromBackend; // Only custom tables can be deleted
                } else if (isCustomFromLocalStorage) {
                    tableType = 'Custom';
                    source = 'localStorage';
                    canDelete = true;
                } else {
                    tableType = 'Default';
                    source = 'DOM Only';
                    canDelete = false;
                }
                
                const deleteBtn = canDelete 
                    ? `<button class="btn btn-sm btn-danger delete-table-btn" data-table="${tableName}" data-table-id="${tableId || ''}" data-source="${source}">
                         <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="margin-right: 4px;">
                             <path d="M2 4H14M6 4V2C6 1.44772 6.44772 1 7 1H9C9.55228 1 10 1.44772 10 2V4M8 6V12M5 6L5.5 12M11 6L10.5 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                         </svg>
                         {{ __('Delete') }}
                       </button>` 
                    : '<span class="text-muted">{{ __('Cannot Delete') }}</span>';

                tablesList += `
                    <tr>
                        <td><strong>${tableName}</strong></td>
                        <td>${chairCount} {{ __('chairs') }}</td>
                        <td>
                            <span class="badge bg-${status === 'Free' ? 'success' : status === 'Utilized' ? 'danger' : 'warning'}">
                                ${status}
                            </span>
                        </td>
                        <td>${tableType}</td>
                        <td>
                            <small class="text-muted">${source}</small>
                            ${tableId ? `<br><small class="text-info">ID: ${tableId}</small>` : ''}
                        </td>
                        <td>${deleteBtn}</td>
                    </tr>
                `;
            });

            tablesList += `
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 p-3 bg-light rounded">
                    <h6>{{ __('Legend') }}:</h6>
                    <ul class="mb-0 small">
                        <li><strong>{{ __('Default') }}:</strong> {{ __('Built-in tables that cannot be deleted') }}</li>
                        <li><strong>{{ __('Custom') }}:</strong> {{ __('User-created tables that can be deleted') }}</li>
                        <li><strong>{{ __('Backend') }}:</strong> {{ __('Stored in database') }}</li>
                        <li><strong>{{ __('localStorage') }}:</strong> {{ __('Stored locally (needs migration)') }}</li>
                    </ul>
                </div>
            `;

            resolve(tablesList);
        } catch (error) {
            console.error('Error creating enhanced modal:', error);
            resolve('<p class="text-danger">{{ __("Error loading tables. Please try again.") }}</p>');
        }
    });
}

// Enhanced delete functionality with better error handling
function attachEnhancedDeleteHandlers(manageTablesModal) {
    document.querySelectorAll('.delete-table-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const tableName = this.getAttribute('data-table');
            const tableId = this.getAttribute('data-table-id');
            const source = this.getAttribute('data-source');
            
            console.log(`🗑️ Delete button clicked for: ${tableName} (ID: ${tableId}, Source: ${source})`);
            
            if (confirm(`{{ __('Are you sure you want to delete table') }} ${tableName}?\\n{{ __('This action cannot be undone.') }}`)) {
                // Show loading state
                this.disabled = true;
                this.innerHTML = `
                    <div class="spinner-border spinner-border-sm" role="status" style="width: 12px; height: 12px; margin-right: 4px;">
                        <span class="visually-hidden">{{ __('Loading...') }}</span>
                    </div>
                    {{ __('Deleting...') }}
                `;
                
                try {
                    let deleteSuccess = false;
                    
                    if (source === 'Backend' && tableId) {
                        // Delete from backend API
                        deleteSuccess = await deleteTableWithAPI(tableName, tableId);
                    } else if (source === 'localStorage') {
                        // Delete from localStorage only
                        deleteSuccess = true;
                    }
                    
                    if (deleteSuccess) {
                        // Remove from DOM
                        const tableElement = document.querySelector(`[data-table=\"${tableName}\"]`);\n                        if (tableElement) {\n                            tableElement.remove();\n                            console.log(`🗑️ Table ${tableName} removed from DOM`);\n                        }\n\n                        // Remove from localStorage (cleanup)\n                        const customTables = JSON.parse(localStorage.getItem('customTables') || '[]');\n                        const filtered = customTables.filter(t => t.name !== tableName);\n                        localStorage.setItem('customTables', JSON.stringify(filtered));\n                        console.log(`🗑️ Table ${tableName} removed from localStorage`);\n\n                        // Show success message\n                        alert(`✅ {{ __('Table') }} ${tableName} {{ __('deleted successfully!') }}`);\n                        \n                        // Close and reopen modal to refresh\n                        manageTablesModal.hide();\n                        setTimeout(() => {\n                            document.getElementById('btn-clear-all-data').click();\n                        }, 300);\n                    } else {\n                        // Reset button state\n                        this.disabled = false;\n                        this.innerHTML = `\n                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="margin-right: 4px;">\n                                <path d="M2 4H14M6 4V2C6 1.44772 6.44772 1 7 1H9C9.55228 1 10 1.44772 10 2V4M8 6V12M5 6L5.5 12M11 6L10.5 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>\n                            </svg>\n                            {{ __('Delete') }}\n                        `;\n                        alert(`❌ {{ __('Error deleting table') }} ${tableName}. {{ __('Please try again.') }}`);\n                    }\n                } catch (error) {\n                    console.error('Error in delete process:', error);\n                    \n                    // Reset button state\n                    this.disabled = false;\n                    this.innerHTML = `\n                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="margin-right: 4px;">\n                            <path d="M2 4H14M6 4V2C6 1.44772 6.44772 1 7 1H9C9.55228 1 10 1.44772 10 2V4M8 6V12M5 6L5.5 12M11 6L10.5 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>\n                        </svg>\n                        {{ __('Delete') }}\n                    `;\n                    \n                    alert(`❌ {{ __('Error deleting table') }} ${tableName}: ${error.message}`);\n                }\n            }\n        });\n    });\n}\n\n// Export enhanced functions\nwindow.tableDeleteEnhancement = {\n    deleteTableWithAPI,\n    createEnhancedManageTablesModal,\n    attachEnhancedDeleteHandlers\n};\n\nconsole.log('✅ Table Delete Enhancement module loaded');