// Fix for tab switching in POS system
document.addEventListener('DOMContentLoaded', function() {
    console.log('Tab fix script loaded');
    
    // Force show tables section and hide products
    setTimeout(function() {
        const tablesSection = document.getElementById('tables-section');
        const productsSection = document.getElementById('products-section');
        const productsGridSection = document.querySelector('.pos-products-section');
        
        console.log('Elements found:', {
            tablesSection: !!tablesSection,
            productsSection: !!productsSection,
            productsGridSection: !!productsGridSection
        });
        
        // Force display tables
        if (tablesSection) {
            tablesSection.style.display = 'block';
            tablesSection.style.visibility = 'visible';
        }
        
        // Force hide products
        if (productsSection) {
            productsSection.style.display = 'none';
        }
        if (productsGridSection) {
            productsGridSection.style.display = 'none';
        }
        
        // Setup tab buttons
        const tabButtons = document.querySelectorAll('.pos-tab-btn');
        console.log('Tab buttons found:', tabButtons.length);
        
        tabButtons.forEach(function(button, index) {
            console.log('Setting up button', index, button.getAttribute('data-tab'));
            
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const tab = this.getAttribute('data-tab');
                console.log('Tab clicked:', tab);
                
                // Update button states
                tabButtons.forEach(function(btn) {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                
                // Show/hide sections
                if (tab === 'tables') {
                    if (tablesSection) {
                        tablesSection.style.display = 'block';
                        tablesSection.style.visibility = 'visible';
                    }
                    if (productsSection) {
                        productsSection.style.display = 'none';
                    }
                    if (productsGridSection) {
                        productsGridSection.style.display = 'none';
                    }
                    console.log('Switched to tables');
                } else if (tab === 'products') {
                    if (tablesSection) {
                        tablesSection.style.display = 'none';
                    }
                    if (productsSection) {
                        productsSection.style.display = 'block';
                        productsSection.style.visibility = 'visible';
                    }
                    if (productsGridSection) {
                        productsGridSection.style.display = 'block';
                        productsGridSection.style.visibility = 'visible';
                    }
                    console.log('Switched to products');
                }
            });
        });
        
        // Ensure tables tab is active
        const tablesBtn = document.querySelector('.pos-tab-btn[data-tab="tables"]');
        if (tablesBtn) {
            tablesBtn.classList.add('active');
        }
        const productsBtn = document.querySelector('.pos-tab-btn[data-tab="products"]');
        if (productsBtn) {
            productsBtn.classList.remove('active');
        }
        
        // Add button event listeners
        const btnAddTable = document.getElementById('btn-add-table');
        const btnMakeReservation = document.getElementById('btn-make-reservation');
        const btnManageAllTables = document.getElementById('btn-manage-all-tables');
        const btnManageOrders = document.getElementById('btn-manage-orders');
        const btnClearAllData = document.getElementById('btn-clear-all-data');
        
        if (btnAddTable) {
            btnAddTable.addEventListener('click', function() {
                const addTableModal = new bootstrap.Modal(document.getElementById('addTableModal'));
                addTableModal.show();
            });
        }
        
        if (btnMakeReservation) {
            btnMakeReservation.addEventListener('click', function() {
                const reservationModal = new bootstrap.Modal(document.getElementById('makeReservationModal'));
                reservationModal.show();
            });
        }
        
        if (btnManageAllTables) {
            btnManageAllTables.addEventListener('click', function() {
                const manageModal = new bootstrap.Modal(document.getElementById('manageReservationsModal'));
                manageModal.show();
            });
        }
        
        if (btnManageOrders) {
            btnManageOrders.addEventListener('click', function() {
                const ordersModal = new bootstrap.Modal(document.getElementById('manageOrdersModal'));
                ordersModal.show();
            });
        }
        
        if (btnClearAllData) {
            btnClearAllData.addEventListener('click', function() {
                const manageModal = new bootstrap.Modal(document.getElementById('manageAllTablesModal'));
                manageModal.show();
            });
        }
        
    }, 100);
});