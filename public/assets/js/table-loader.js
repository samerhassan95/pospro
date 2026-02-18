// Load and display tables from backend API
async function loadAndDisplayTables() {
    console.log('🔄 Loading tables from backend API...');
    
    try {
        const response = await fetch('/api/business/tables', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        console.log('📋 API Response:', result);
        
        if (result.success && result.data) {
            const tables = result.data;
            console.log(`✅ Loaded ${tables.length} tables from database`);
            
            // Update existing tables with database data
            tables.forEach(tableData => {
                const existingTable = document.querySelector(`[data-table="${tableData.table_name}"]`);
                if (existingTable) {
                    // Update status
                    existingTable.className = `table-item ${tableData.status}`;
                    existingTable.classList.add(`table-${tableData.table_type}`);
                    
                    // Update position if saved
                    if (tableData.position_top) existingTable.style.top = tableData.position_top;
                    if (tableData.position_left) existingTable.style.left = tableData.position_left;
                    if (tableData.position_right) existingTable.style.right = tableData.position_right;
                    if (tableData.position_bottom) existingTable.style.bottom = tableData.position_bottom;
                    
                    console.log(`✅ Updated table: ${tableData.table_name}`);
                }
            });
            
            console.log('✅ All tables loaded and displayed');
            
        } else {
            console.error('❌ API returned error:', result);
            alert('خطأ في تحميل الطاولات من قاعدة البيانات');
        }
        
    } catch (error) {
        console.error('❌ Error loading tables:', error);
        alert('خطأ في الاتصال بالخادم: ' + error.message);
    }
}

// Initialize tables when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM loaded, initializing tables...');
    
    // Load tables when Tables tab is clicked
    const tablesTab = document.querySelector('[data-tab="tables"]');
    if (tablesTab) {
        tablesTab.addEventListener('click', function() {
            console.log('📋 Tables tab clicked, loading tables...');
            setTimeout(() => {
                loadAndDisplayTables();
            }, 100);
        });
    }
});

// Export functions for global use
window.loadAndDisplayTables = loadAndDisplayTables;