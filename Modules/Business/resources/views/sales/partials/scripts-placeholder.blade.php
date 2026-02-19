    <script>
        // Translation variables for JavaScript
        window.translations = {
            'no_products_in': '{{ __("No Products in") }}',
            'no_products_available_category': '{{ __("There are no products available in this category.") }}',
            'no_products_found': '{{ __("No products found") }}',
            'no_products_available': '{{ __("No products available") }}'
        };

        // Add missing functions for cart functionality
        window.fetchUpdatedCart = function(callback) {
            let url = "{{ route('business.carts.index') }}?layout=new";
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $("#cart-list").html(response);
                    if (typeof callback == "function") callback();
                },
                error: function() {
                    console.error("Error fetching cart");
                }
            });
        };

        // Add missing currency format function
        window.currencyFormat = function(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            }).format(amount);
        };

        // Add missing helper functions
        window.getNumericValue = function(value) {
            if (typeof value === 'string') {
                return parseFloat(value.replace(/[^0-9.-]/g, '')) || 0;
            }
            return parseFloat(value) || 0;
        };

        window.formattedAmount = function(amount, decimals = 2) {
            return parseFloat(amount).toFixed(decimals);
        };

        window.RoundingTotal = function(amount) {
            // Simple rounding logic - can be customized based on business needs
            return Math.round(amount * 100) / 100;
        };

        // Add missing addProductToCart function for compatibility
        window.addProductToCart = function(productElement) {
            console.log("addProductToCart called with", productElement);
            
            // Convert DOM element to jQuery object if needed
            const $element = $(productElement);
            
            // Get product data
            const productId = $element.data('product_id');
            const productName = $element.data('product_name');
            const defaultPrice = parseFloat($element.data('default_price')) || 0;
            const productCode = $element.data('product_code');
            const productUnitId = $element.data('product_unit_id');
            const productUnitName = $element.data('product_unit_name');
            const productImage = $element.data('product_image');
            const route = $element.data('route');
            const stocks = $element.data('stocks') || [];
            
            console.log("Product data:", {
                productId, productName, defaultPrice, productCode, 
                productUnitId, productUnitName, productImage, route, stocks
            });
            
            // Validate required data
            if (!productId || !route) {
                console.error("Missing required product data", { productId, route });
                toastr.error("Missing product information");
                return;
            }
            
            // Validate price
            if (defaultPrice < 0 || isNaN(defaultPrice)) {
                console.error("Invalid price", defaultPrice);
                toastr.error("Price can not be negative.");
                return;
            }
            
            // Use the first available stock if multiple stocks exist
            let stockId = null;
            let purchasePrice = 0;
            if (Array.isArray(stocks) && stocks.length > 0) {
                const firstStock = stocks[0];
                stockId = firstStock.id;
                purchasePrice = firstStock.productPurchasePrice || 0;
            }
            
            const requestData = {
                type: "sale",
                id: productId,
                name: productName,
                price: defaultPrice,
                quantity: 1,
                product_code: productCode,
                product_unit_id: productUnitId,
                product_unit_name: productUnitName,
                stock_id: stockId,
                purchase_price: purchasePrice,
                product_image: productImage,
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            
            console.log("Sending AJAX request to", route, "with data", requestData);
            
            // Add to cart via AJAX
            $.ajax({
                url: route,
                type: "POST",
                data: requestData,
                success: function (response) {
                    console.log("Add to cart response", response);
                    if (response.success) {
                        // Refresh cart list
                        fetchUpdatedCart(function() {
                            if (typeof calTotalAmount === 'function') {
                                calTotalAmount();
                            }
                        });
                        toastr.success("Product added to cart");
                    } else {
                        console.error("Add to cart failed", response);
                        toastr.error(response.message || "Failed to add product to cart");
                    }
                },
                error: function (xhr) {
                    console.error("Error adding product to cart:", xhr.responseText, xhr);
                    toastr.error("Error adding product to cart");
                },
            });
        };

        // Product click handlers
        $(document).ready(function() {
            console.log("Product click handlers initialized");
            
            // Handle product card click (anywhere on the card)
            $(document).on("click", ".single-product, .pos-product-card", function (e) {
                console.log("Product card clicked", this);
                
                // Prevent double triggering if clicking on add button
                if ($(e.target).closest('.add-product-btn, .pos-add-to-cart-btn').length) {
                    console.log("Clicked on add button, skipping card handler");
                    return;
                }
                
                // Add product to cart
                addProductToCart(this);
            });

            // Handle add product button click
            $(document).on("click", ".add-product-btn, .pos-add-to-cart-btn", function (e) {
                console.log("Add to cart button clicked", this);
                e.preventDefault();
                e.stopPropagation();
                
                // Find the parent product card
                const productCard = $(this).closest('.single-product, .pos-product-card');
                if (productCard.length) {
                    console.log("Found product card", productCard[0]);
                    addProductToCart(productCard[0]);
                } else {
                    console.error("Could not find product card");
                }
            });

            // Note: Quantity increase/decrease functionality is handled by existing sale.js
            // The existing handlers already support .plus-btn and .minus-btn classes
            // They work with .cart-item-card containers and .cart-qty inputs
            // We just need to sync the visible quantity span with the hidden input
            
            function syncQuantityDisplays() {
                $('.cart-item-card').each(function() {
                    const $item = $(this);
                    const hiddenQty = $item.find('.cart-qty').val();
                    const $visibleQty = $item.find('.cart-qty-display');
                    if (hiddenQty && $visibleQty.length && $visibleQty.text() !== hiddenQty) {
                        $visibleQty.text(hiddenQty);
                    }
                });
            }
            
            // Override fetchUpdatedCart to sync quantities after cart refresh
            const originalFetchUpdatedCart = window.fetchUpdatedCart;
            window.fetchUpdatedCart = function(callback) {
                originalFetchUpdatedCart(function() {
                    // Small delay to ensure DOM is updated
                    setTimeout(syncQuantityDisplays, 50);
                    if (typeof callback === 'function') callback();
                });
            };
            
            // Sync quantities after any cart operation
            $(document).on('click', '.plus-btn, .minus-btn', function() {
                const $item = $(this).closest('.cart-item-card');
                setTimeout(function() {
                    const hiddenQty = $item.find('.cart-qty').val();
                    const $visibleQty = $item.find('.cart-qty-display');
                    if (hiddenQty && $visibleQty.length) {
                        $visibleQty.text(hiddenQty);
                    }
                }, 100);
            });
            
            // Initial sync when page loads
            $(document).ready(function() {
                setTimeout(syncQuantityDisplays, 100);
            });

            // Note: Remove item functionality is handled by existing sale.js
            // The existing handler already supports .remove-item-btn class
            // No additional handler needed to avoid conflicts
        });

        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.pos-tab-btn, .pos-view-btn');
            const toggleButtons = document.querySelectorAll('.pos-toggle-btn');
            const tablesSection = document.getElementById('tables-view');
            const productsSection = document.getElementById('products-section');
            const productsGridSection = document.querySelector('.pos-products-section');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tab = this.getAttribute('data-tab') || this.getAttribute('data-view');

                    // Remove active from ALL buttons (both toggle and icon buttons)
                    tabButtons.forEach(btn => {
                        btn.classList.remove('pos-toggle-btn-active');
                        btn.classList.remove('active');
                    });
                    
                    // Add active to clicked button only
                    if (this.classList.contains('pos-toggle-btn')) {
                        this.classList.add('pos-toggle-btn-active');
                    } else {
                        this.classList.add('active');
                    }

                    // Show/hide sections
                    if (tab === 'tables') {
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
                        if (tablesSection) tablesSection.style.display = 'none';
                        if (productsSection) productsSection.style.display = 'block';
                        if (productsGridSection) productsGridSection.style.display = 'block';
                        
                        // Show appropriate view
                        const brandView = document.getElementById('brand-view');
                        const categoryView = document.getElementById('category-view');
                        const searchView = document.getElementById('search-view');
                        
                        if (tab === 'brand' && brandView) {
                            brandView.style.display = 'block';
                            if (categoryView) categoryView.style.display = 'none';
                            if (searchView) searchView.style.display = 'none';
                        } else if (tab === 'search' && searchView) {
                            searchView.style.display = 'block';
                            if (brandView) brandView.style.display = 'none';
                            if (categoryView) categoryView.style.display = 'none';
                        } else if (categoryView) {
                            categoryView.style.display = 'block';
                            if (brandView) brandView.style.display = 'none';
                            if (searchView) searchView.style.display = 'none';
                        }
                    }
                });
            });

            // Table click functionality - open order modal on click
            const tables = document.querySelectorAll('.table-item');
            let selectedTable = null;

            tables.forEach(table => {
                // Single click to open order modal
                table.addEventListener('click', function(e) {
                    // If clicking on a chair, don't open modal
                    if (e.target.classList.contains('chair')) {
                        return;
                    }

                    // Check table status
                    const tableName = this.getAttribute('data-table');

                    if (this.classList.contains('blocked')) {
                        // Table is reserved - show reservation details in modal
                        const reservations = JSON.parse(localStorage.getItem('tableReservations') || '{}');
                        let reservationInfo = null;
                        let reservationKey = null;

                        // Find reservation for this table
                        for (const [key, reservation] of Object.entries(reservations)) {
                            if (reservation.table === tableName) {
                                reservationInfo = reservation;
                                reservationKey = key;
                                break;
                            }
                        }

                        if (reservationInfo) {
                            // Show reservation details in modal
                            showReservationDetails(reservationInfo, reservationKey);
                        } else {
                            alert('{{ __("This table is blocked/reserved") }}');
                        }
                        return;
                    }

                    if (this.classList.contains('utilized')) {
                        // Table is occupied - open order modal to view/edit
                        selectedTable = this;
                        document.getElementById('order-table-name').textContent = tableName;

                        // Load existing order data if available
                        const tableOrders = JSON.parse(localStorage.getItem('tableOrders') || '{}');
                        if (tableOrders[tableName]) {
                            document.getElementById('order-customer-name').value = tableOrders[tableName].customer || '';
                            document.getElementById('order-guests').value = tableOrders[tableName].guests || '1';
                            document.getElementById('order-items').value = tableOrders[tableName].items || '';
                            document.getElementById('order-notes').value = tableOrders[tableName].notes || '';
                        }

                        const orderModal = new bootstrap.Modal(document.getElementById('tableOrderModal'));
                        orderModal.show();
                        return;
                    }

                    // Table is free - open reservation modal and pre-select it
                    const chairCount = this.querySelectorAll('.chair').length;

                    // Pre-fill the reservation form
                    document.getElementById('reservation-guests').value = Math.min(chairCount, 4);

                    // Store the clicked table as pre-selected
                    selectedTableForReservation = {
                        name: tableName,
                        chairs: chairCount,
                        element: this
                    };

                    // Open Make Reservation modal
                    const reservationModal = new bootstrap.Modal(document.getElementById('makeReservationModal'));
                    reservationModal.show();

                    // Show the clicked table as pre-selected, but allow changing
                    setTimeout(() => {
                        const container = document.getElementById('available-tables-container');
                        container.innerHTML = '';

                        // Show pre-selected table
                        const tableBtn = document.createElement('button');
                        tableBtn.className = 'btn btn-success';
                        tableBtn.textContent = `${tableName} (${chairCount} {{ __("chairs") }}) - {{ __("Selected") }}`;
                        tableBtn.onclick = function() {
                            // Already selected, do nothing
                        };
                        container.appendChild(tableBtn);

                        // Add message to search for other tables
                        const searchMsg = document.createElement('p');
                        searchMsg.className = 'mt-2 text-muted';
                        searchMsg.innerHTML = '{{ __("Click") }} <strong>{{ __("Search Available Tables") }}</strong> {{ __("to see other options") }}';
                        container.appendChild(searchMsg);

                        document.getElementById('available-tables-list').style.display = 'block';
                        document.getElementById('confirm-reservation').disabled = false;
                    }, 300);
                });
            });

            // Chair click functionality - each chair can have different status
            const chairs = document.querySelectorAll('.chair');
            chairs.forEach(chair => {
                chair.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent table click

                    // Cycle through chair statuses
                    if (this.classList.contains('chair-utilized')) {
                        this.classList.remove('chair-utilized');
                        this.classList.add('chair-free');
                    } else if (this.classList.contains('chair-free')) {
                        this.classList.remove('chair-free');
                        this.classList.add('chair-blocked');
                    } else if (this.classList.contains('chair-blocked')) {
                        this.classList.remove('chair-blocked');
                        this.classList.add('chair-utilized');
                    } else {
                        // First click - set to utilized
                        this.classList.add('chair-utilized');
                    }
                });
            });

            // Live Views & Integration toggles functionality
            const showUtilization = document.getElementById('show-utilization');
            const showOrdered = document.getElementById('show-ordered');
            const showRecommendations = document.getElementById('show-recommendations');
            const showReservations = document.getElementById('show-reservations');

            // Show Utilization - toggle visibility of table/chair colors
            if (showUtilization) {
                showUtilization.addEventListener('change', function() {
                    const allTables = document.querySelectorAll('.table-item');
                    if (this.checked) {
                        allTables.forEach(table => {
                            table.style.opacity = '1';
                        });
                        console.log('Utilization view: ON');
                    } else {
                        allTables.forEach(table => {
                            table.style.opacity = '0.3';
                        });
                        console.log('Utilization view: OFF');
                    }
                });
            }

            // Show Ordered - highlight tables with orders
            if (showOrdered) {
                showOrdered.addEventListener('change', function() {
                    const utilizedTables = document.querySelectorAll('.table-item.utilized');
                    if (this.checked) {
                        utilizedTables.forEach(table => {
                            table.style.boxShadow = '0 0 20px rgba(239, 78, 68, 0.8)';
                        });
                        console.log('Show ordered: ON');
                    } else {
                        utilizedTables.forEach(table => {
                            table.style.boxShadow = 'none';
                        });
                        console.log('Show ordered: OFF');
                    }
                });
            }

            // Show Recommendations - suggest available tables
            if (showRecommendations) {
                showRecommendations.addEventListener('change', function() {
                    const freeTables = document.querySelectorAll('.table-item.free');
                    if (this.checked) {
                        freeTables.forEach(table => {
                            table.style.boxShadow = '0 0 20px rgba(72, 240, 69, 0.8)';
                            table.style.animation = 'pulse 2s infinite';
                        });
                        console.log('Show recommendations: ON');
                    } else {
                        freeTables.forEach(table => {
                            table.style.boxShadow = 'none';
                            table.style.animation = 'none';
                        });
                        console.log('Show recommendations: OFF');
                    }
                });
            }

            // Show Reservations - highlight blocked/reserved tables
            if (showReservations) {
                showReservations.addEventListener('change', function() {
                    const blockedTables = document.querySelectorAll('.table-item.blocked');
                    if (this.checked) {
                        blockedTables.forEach(table => {
                            table.style.boxShadow = '0 0 20px rgba(255, 243, 1, 0.8)';
                            const tableName = table.getAttribute('data-table');
                            table.setAttribute('title', tableName + ' - Reserved');
                        });
                        console.log('Show reservations: ON');
                    } else {
                        blockedTables.forEach(table => {
                            table.style.boxShadow = 'none';
                            table.removeAttribute('title');
                        });
                        console.log('Show reservations: OFF');
                    }
                });
            }

            // Add Table button functionality
            const btnAddTable = document.getElementById('btn-add-table');
            if (btnAddTable) {
                btnAddTable.addEventListener('click', function() {
                    const addTableModal = new bootstrap.Modal(document.getElementById('addTableModal'));
                    addTableModal.show();
                });
            }

            // Save new table functionality
            const saveNewTableBtn = document.getElementById('save-new-table');
            if (saveNewTableBtn) {
                saveNewTableBtn.addEventListener('click', function() {
                    const tableName = document.getElementById('new-table-name').value.trim();
                    const chairCount = parseInt(document.getElementById('new-table-chairs').value);
                    const tableStatus = 'free'; // Always default to free

                    if (!tableName) {
                        alert('{{ __("Please enter a table number") }}');
                        return;
                    }

                    // Auto-determine table type based on chair count
                    let tableType = 'circle';
                    if (chairCount === 6) {
                        tableType = 'rounded';
                    } else if (chairCount === 8) {
                        tableType = 'rectangle-h';
                    } else if (chairCount === 10) {
                        tableType = 'rectangle-h10';
                    } else if (chairCount === 12) {
                        tableType = 'rectangle';
                    }

                    // Create new table element
                    const floorPlan = document.querySelector('.restaurant-floor-plan');
                    const newTable = document.createElement('div');
                    newTable.className = `table-item ${tableStatus}`;
                    newTable.setAttribute('data-table', tableName);
                    newTable.style.opacity = '0.7';
                    newTable.style.border = '3px dashed var(--clr-primary)';

                    // Add table type class based on chair count
                    if (chairCount === 12) {
                        newTable.classList.add('table-rectangle');
                    } else if (chairCount === 10) {
                        newTable.classList.add('table-rectangle-h10');
                    } else if (chairCount === 8) {
                        newTable.classList.add('table-rectangle-h');
                    } else if (chairCount === 6) {
                        newTable.classList.add('table-rounded');
                    } else {
                        newTable.classList.add('table-circle');
                    }

                    // Position new table (center of floor plan)
                    newTable.style.top = '300px';
                    newTable.style.left = '300px';

                    // Add table name
                    const nameSpan = document.createElement('span');
                    nameSpan.className = 'table-name';
                    nameSpan.textContent = tableName;
                    newTable.appendChild(nameSpan);

                    // Add chairs based on count
                    const chairWrapper = document.createElement('div');
                    chairWrapper.className = 'chair-wrapper';

                    if (chairCount === 12) {
                        // 12 chairs: 1 top, 5 right, 1 bottom, 5 left (vertical rectangle table)
                        ['top', 'right-1', 'right-2', 'right-3', 'right-4', 'right-5', 'bottom', 'left-1', 'left-2', 'left-3', 'left-4', 'left-5'].forEach(pos => {
                            const chair = document.createElement('div');
                            chair.className = `chair chair-${pos}`;
                            chairWrapper.appendChild(chair);
                        });
                    } else if (chairCount === 10) {
                        // 10 chairs: 4 top, 4 bottom, 1 left, 1 right (horizontal rectangle)
                        ['top-1', 'top-2', 'top-3', 'top-4', 'right', 'bottom-1', 'bottom-2', 'bottom-3', 'bottom-4', 'left'].forEach(pos => {
                            const chair = document.createElement('div');
                            chair.className = `chair chair-${pos}`;
                            chairWrapper.appendChild(chair);
                        });
                    } else if (chairCount === 8) {
                        // 8 chairs: 3 top, 3 bottom, 1 left, 1 right (horizontal rectangle)
                        ['top-1', 'top-2', 'top-3', 'right', 'bottom-1', 'bottom-2', 'bottom-3', 'left'].forEach(pos => {
                            const chair = document.createElement('div');
                            chair.className = `chair chair-${pos}`;
                            chairWrapper.appendChild(chair);
                        });
                    } else if (chairCount === 6) {
                        // 6 chairs: rounded table
                        ['top-left', 'top-right', 'right', 'bottom-right', 'bottom-left', 'left'].forEach(pos => {
                            const chair = document.createElement('div');
                            chair.className = `chair chair-${pos}`;
                            chairWrapper.appendChild(chair);
                        });
                    } else if (chairCount === 4) {
                        // 4 chairs: circle table
                        ['top', 'right', 'bottom', 'left'].forEach(pos => {
                            const chair = document.createElement('div');
                            chair.className = `chair chair-${pos}`;
                            chairWrapper.appendChild(chair);
                        });
                    } else if (chairCount === 2) {
                        // 2 chairs: circle table
                        ['top', 'bottom'].forEach(pos => {
                            const chair = document.createElement('div');
                            chair.className = `chair chair-${pos}`;
                            chairWrapper.appendChild(chair);
                        });
                    }

                    newTable.appendChild(chairWrapper);
                    floorPlan.appendChild(newTable);

                    // Close modal first
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addTableModal'));
                    modal.hide();

                    // Show instruction
                    const instruction = document.createElement('div');
                    instruction.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.9); color: white; padding: 30px 50px; border-radius: 12px; z-index: 10000; font-size: 18px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.3);';
                    instruction.innerHTML = `
                        <div style="font-size: 24px; font-weight: bold; margin-bottom: 15px;">ًں“چ {{ __("Position Your Table") }}</div>
                        <div style="margin-bottom: 20px;">{{ __("Click and drag the table to position it") }}</div>
                        <button id="confirm-position-btn" style="background: var(--clr-primary); color: white; border: none; padding: 12px 30px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer;">{{ __("Confirm Position") }}</button>
                        <button id="cancel-position-btn" style="background: #E6E6E6; color: white; border: none; padding: 12px 30px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; margin-left: 10px;">{{ __("Cancel") }}</button>
                    `;
                    document.body.appendChild(instruction);

                    // Enable immediate dragging
                    let isPositioning = true;
                    let positioningTable = newTable;
                    let isDraggingNew = false;
                    let dragOffsetX = 0;
                    let dragOffsetY = 0;

                    // Mouse down on table to start dragging
                    positioningTable.addEventListener('mousedown', function(e) {
                        if (!isPositioning) return;
                        isDraggingNew = true;

                        const rect = positioningTable.getBoundingClientRect();
                        const floorRect = floorPlan.getBoundingClientRect();

                        dragOffsetX = e.clientX - rect.left;
                        dragOffsetY = e.clientY - rect.top;

                        positioningTable.style.cursor = 'grabbing';
                        e.preventDefault();
                    });

                    // Mouse move to drag table
                    function positionMouseMove(e) {
                        if (!isPositioning || !isDraggingNew) return;

                        const floorRect = floorPlan.getBoundingClientRect();
                        const tableWidth = positioningTable.offsetWidth;
                        const tableHeight = positioningTable.offsetHeight;

                        let newX = e.clientX - floorRect.left - dragOffsetX;
                        let newY = e.clientY - floorRect.top - dragOffsetY;

                        // Keep within bounds
                        newX = Math.max(0, Math.min(newX, floorPlan.offsetWidth - tableWidth));
                        newY = Math.max(0, Math.min(newY, floorPlan.offsetHeight - tableHeight));

                        positioningTable.style.left = newX + 'px';
                        positioningTable.style.top = newY + 'px';
                        positioningTable.style.right = 'auto';
                        positioningTable.style.bottom = 'auto';
                    }

                    // Mouse up to stop dragging
                    function positionMouseUp() {
                        if (isDraggingNew) {
                            isDraggingNew = false;
                            positioningTable.style.cursor = 'move';
                        }
                    }

                    document.addEventListener('mousemove', positionMouseMove);
                    document.addEventListener('mouseup', positionMouseUp);

                    // Confirm position
                    document.getElementById('confirm-position-btn').addEventListener('click', function() {
                        isPositioning = false;
                        isDraggingNew = false;
                        document.removeEventListener('mousemove', positionMouseMove);
                        document.removeEventListener('mouseup', positionMouseUp);

                        // Finalize table
                        newTable.style.opacity = '1';
                        newTable.style.border = 'none';
                        newTable.style.cursor = 'move';

                        // Add event listeners
                        addTableEventListeners(newTable);
                        makeDraggable(newTable);

                        // Save to localStorage
                        saveCustomTable(newTable);

                        // Remove instruction
                        instruction.remove();

                        // Reset form
                        document.getElementById('new-table-name').value = '';
                        document.getElementById('new-table-chairs').value = '4';

                        alert('{{ __("Table added successfully!") }}');
                    });

                    // Cancel
                    document.getElementById('cancel-position-btn').addEventListener('click', function() {
                        isPositioning = false;
                        isDraggingNew = false;
                        document.removeEventListener('mousemove', positionMouseMove);
                        document.removeEventListener('mouseup', positionMouseUp);
                        newTable.remove();
                        instruction.remove();
                    });
                });
            }

            // Function to add event listeners to tables
            function addTableEventListeners(table) {
                // Single click to check status and open appropriate modal
                table.addEventListener('click', function(e) {
                    // If clicking on a chair, don't open modal
                    if (e.target.classList.contains('chair')) {
                        return;
                    }

                    // Check table status
                    const tableName = this.getAttribute('data-table');

                    if (this.classList.contains('blocked')) {
                        // Table is reserved - show reservation details in modal
                        const reservations = JSON.parse(localStorage.getItem('tableReservations') || '{}');
                        let reservationInfo = null;
                        let reservationKey = null;

                        // Find reservation for this table
                        for (const [key, reservation] of Object.entries(reservations)) {
                            if (reservation.table === tableName) {
                                reservationInfo = reservation;
                                reservationKey = key;
                                break;
                            }
                        }

                        if (reservationInfo) {
                            // Show reservation details in modal
                            showReservationDetails(reservationInfo, reservationKey);
                        } else {
                            alert('{{ __("This table is blocked/reserved") }}');
                        }
                        return;
                    }

                    if (this.classList.contains('utilized')) {
                        // Table is occupied - open order modal to view/edit
                        selectedTable = this;
                        document.getElementById('order-table-name').textContent = tableName;

                        // Load existing order data if available
                        const tableOrders = JSON.parse(localStorage.getItem('tableOrders') || '{}');
                        if (tableOrders[tableName]) {
                            document.getElementById('order-customer-name').value = tableOrders[tableName].customer || '';
                            document.getElementById('order-guests').value = tableOrders[tableName].guests || '1';
                            document.getElementById('order-items').value = tableOrders[tableName].items || '';
                            document.getElementById('order-notes').value = tableOrders[tableName].notes || '';
                        }

                        const orderModal = new bootstrap.Modal(document.getElementById('tableOrderModal'));
                        orderModal.show();
                        return;
                    }

                    // Table is free - open reservation modal and pre-select it
                    const chairCount = this.querySelectorAll('.chair').length;

                    // Pre-fill the reservation form
                    document.getElementById('reservation-guests').value = Math.min(chairCount, 4);

                    // Store the clicked table as pre-selected
                    selectedTableForReservation = {
                        name: tableName,
                        chairs: chairCount,
                        element: this
                    };

                    // Open Make Reservation modal
                    const reservationModal = new bootstrap.Modal(document.getElementById('makeReservationModal'));
                    reservationModal.show();

                    // Show the clicked table as pre-selected, but allow changing
                    setTimeout(() => {
                        const container = document.getElementById('available-tables-container');
                        container.innerHTML = '';

                        // Show pre-selected table
                        const tableBtn = document.createElement('button');
                        tableBtn.className = 'btn btn-success';
                        tableBtn.textContent = `${tableName} (${chairCount} {{ __("chairs") }}) - {{ __("Selected") }}`;
                        tableBtn.onclick = function() {
                            // Already selected, do nothing
                        };
                        container.appendChild(tableBtn);

                        // Add message to search for other tables
                        const searchMsg = document.createElement('p');
                        searchMsg.className = 'mt-2 text-muted';
                        searchMsg.innerHTML = '{{ __("Click") }} <strong>{{ __("Search Available Tables") }}</strong> {{ __("to see other options") }}';
                        container.appendChild(searchMsg);

                        document.getElementById('available-tables-list').style.display = 'block';
                        document.getElementById('confirm-reservation').disabled = false;
                    }, 300);
                });

                // Add chair event listeners
                const chairs = table.querySelectorAll('.chair');
                chairs.forEach(chair => {
                    chair.addEventListener('click', function(e) {
                        e.stopPropagation();

                        if (this.classList.contains('chair-utilized')) {
                            this.classList.remove('chair-utilized');
                            this.classList.add('chair-free');
                        } else if (this.classList.contains('chair-free')) {
                            this.classList.remove('chair-free');
                            this.classList.add('chair-blocked');
                        } else if (this.classList.contains('chair-blocked')) {
                            this.classList.remove('chair-blocked');
                            this.classList.add('chair-utilized');
                        } else {
                            this.classList.add('chair-utilized');
                        }
                    });
                });
            }

            // ========== TABLE ROTATION FEATURE ==========

            // Rotate table by 90 degrees
            function rotateTable(table) {
                const currentRotation = parseInt(table.getAttribute('data-rotation') || '0');
                const newRotation = (currentRotation + 90) % 360;

                table.setAttribute('data-rotation', newRotation);

                // Save rotation
                const tableName = table.getAttribute('data-table');
                saveTablePosition(tableName, table);

                console.log(`Table ${tableName} rotated to ${newRotation}آ°`);
            }

            // Reset table rotation to 0
            function resetTableRotation(table) {
                table.setAttribute('data-rotation', '0');

                const tableName = table.getAttribute('data-table');
                saveTablePosition(tableName, table);

                console.log(`Table ${tableName} rotation reset`);
            }

            // Add right-click context menu for rotation
            document.addEventListener('contextmenu', function(e) {
                const table = e.target.closest('.table-item');
                if (!table) return;

                e.preventDefault();

                // Remove existing context menu
                const existingMenu = document.getElementById('table-context-menu');
                if (existingMenu) existingMenu.remove();

                // Create context menu
                const menu = document.createElement('div');
                menu.id = 'table-context-menu';
                menu.style.cssText = `
                    position: fixed;
                    top: ${e.clientY}px;
                    left: ${e.clientX}px;
                    background: white;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    z-index: 10000;
                    min-width: 180px;
                    padding: 8px 0;
                `;

                const currentRotation = parseInt(table.getAttribute('data-rotation') || '0');
                const tableName = table.getAttribute('data-table');

                menu.innerHTML = `
                    <div style="padding: 8px 16px; font-weight: bold; border-bottom: 1px solid #eee; color: #666;">
                        ${tableName}
                    </div>
                    <div class="menu-item" data-action="rotate" style="padding: 10px 16px; cursor: pointer; transition: background 0.2s;">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="vertical-align: middle; margin-right: 8px;">
                            <path d="M14 8C14 11.3137 11.3137 14 8 14C4.68629 14 2 11.3137 2 8C2 4.68629 4.68629 2 8 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M8 2L10 4M8 2L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        Rotate 90آ° (Current: ${currentRotation}آ°)
                    </div>
                    <div class="menu-item" data-action="reset" style="padding: 10px 16px; cursor: pointer; transition: background 0.2s;">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="vertical-align: middle; margin-right: 8px;">
                            <path d="M2 8H14M8 2V14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        Reset Rotation
                    </div>
                `;

                document.body.appendChild(menu);

                // Add hover effects
                menu.querySelectorAll('.menu-item').forEach(item => {
                    item.addEventListener('mouseenter', function() {
                        this.style.background = '#f3f4f6';
                    });
                    item.addEventListener('mouseleave', function() {
                        this.style.background = 'transparent';
                    });
                });

                // Handle menu actions
                menu.querySelector('[data-action="rotate"]').addEventListener('click', function() {
                    rotateTable(table);
                    menu.remove();
                });

                menu.querySelector('[data-action="reset"]').addEventListener('click', function() {
                    resetTableRotation(table);
                    menu.remove();
                });

                // Close menu on click outside
                setTimeout(() => {
                    document.addEventListener('click', function closeMenu() {
                        menu.remove();
                        document.removeEventListener('click', closeMenu);
                    });
                }, 10);
            });

            // ========== END TABLE ROTATION FEATURE ==========

            // Make tables draggable function
            function makeDraggable(table) {
                table.addEventListener('mousedown', function(e) {
                    // Don't drag if clicking on chair or double-clicking
                    if (e.target.classList.contains('chair') || e.detail === 2) {
                        return;
                    }

                    isDragging = true;
                    currentTable = this;

                    const rect = this.getBoundingClientRect();
                    const parent = this.offsetParent.getBoundingClientRect();

                    offsetX = e.clientX - rect.left;
                    offsetY = e.clientY - rect.top;

                    this.style.cursor = 'grabbing';
                    e.preventDefault();
                });
            }

            // Dragging variables
            let isDragging = false;
            let currentTable = null;
            let offsetX = 0;
            let offsetY = 0;

            // Save table order functionality
            const saveTableOrderBtn = document.getElementById('save-table-order');
            if (saveTableOrderBtn) {
                saveTableOrderBtn.addEventListener('click', function() {
                    const customerName = document.getElementById('order-customer-name').value;
                    const guests = document.getElementById('order-guests').value;
                    const orderItems = document.getElementById('order-items').value;
                    const notes = document.getElementById('order-notes').value;
                    const time = document.getElementById('order-time').value;
                    const status = document.getElementById('order-table-status').value;

                    if (!customerName) {
                        alert('{{ __("Please enter customer name") }}');
                        return;
                    }

                    // Update table status based on order status
                    if (selectedTable) {
                        const tableName = selectedTable.getAttribute('data-table');

                        // Remove all status classes
                        selectedTable.classList.remove('utilized', 'free', 'blocked');

                        // Add new status
                        if (status === 'completed') {
                            // Order completed - table becomes free
                            selectedTable.classList.add('free');

                            // Remove from table orders
                            const tableOrders = JSON.parse(localStorage.getItem('tableOrders') || '{}');
                            delete tableOrders[tableName];
                            localStorage.setItem('tableOrders', JSON.stringify(tableOrders));
                        } else if (status === 'utilized') {
                            // Order in progress
                            selectedTable.classList.add('utilized');

                            // Add complete order button
                            addCompleteOrderButton(selectedTable);

                            // Store order data
                            const orderData = {
                                table: tableName,
                                customer: customerName,
                                guests: guests,
                                items: orderItems,
                                notes: notes,
                                time: time,
                                status: status,
                                timestamp: new Date().toISOString()
                            };

                            const tableOrders = JSON.parse(localStorage.getItem('tableOrders') || '{}');
                            tableOrders[tableName] = orderData;
                            localStorage.setItem('tableOrders', JSON.stringify(tableOrders));
                        } else {
                            // Free or blocked
                            selectedTable.classList.add(status);
                        }

                        console.log('Order saved:', {table: tableName, customer: customerName, status: status});
                    }

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('tableOrderModal'));
                    modal.hide();

                    // Reset form
                    document.getElementById('order-customer-name').value = '';
                    document.getElementById('order-guests').value = '1';
                    document.getElementById('order-items').value = '';
                    document.getElementById('order-notes').value = '';
                    document.getElementById('order-table-status').value = 'utilized';

                    if (status === 'completed') {
                        alert('{{ __("Order completed! Table is now free.") }}');
                    } else {
                        alert('{{ __("Order saved successfully!") }}');
                    }
                });
            }

            // Manage Tables button functionality (both buttons do the same thing)
            const btnManageTables = document.getElementById('btn-manage-tables');
            const btnManageAllTables = document.getElementById('btn-manage-all-tables');

            console.log('Manage Tables button found:', btnManageTables);
            console.log('Manage All Tables button found:', btnManageAllTables);

            function openManageReservationsModal() {
                console.log('ًں“‹ Opening Manage Reservations modal...');

                // Load and display all reservations in modal
                const reservations = JSON.parse(localStorage.getItem('tableReservations') || '{}');
                console.log('ًں“‹ Reservations from localStorage:', reservations);
                console.log('ًں“‹ Number of reservations:', Object.keys(reservations).length);
                const tbody = document.getElementById('reservations-table-body');
                const noReservationsMsg = document.getElementById('no-reservations-message');

                console.log('Reservations:', reservations);
                console.log('Table body element:', tbody);
                console.log('No reservations message element:', noReservationsMsg);

                if (!tbody) {
                    console.error('reservations-table-body element not found!');
                    alert('Error: Table body element not found. Please refresh the page.');
                    return;
                }

                tbody.innerHTML = '';

                if (Object.keys(reservations).length === 0) {
                    if (tbody.closest('.table-responsive')) {
                        tbody.closest('.table-responsive').style.display = 'none';
                    }
                    if (noReservationsMsg) {
                        noReservationsMsg.style.display = 'block';
                    }
                } else {
                    if (tbody.closest('.table-responsive')) {
                        tbody.closest('.table-responsive').style.display = 'block';
                    }
                    if (noReservationsMsg) {
                        noReservationsMsg.style.display = 'none';
                    }

                    // Get current date/time for status check
                    const now = new Date();
                    const currentDate = now.toISOString().split('T')[0];
                    const currentTime = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

                    for (const [key, reservation] of Object.entries(reservations)) {
                        const row = document.createElement('tr');

                        // Determine status
                        let status = 'ًںں، Reserved';
                        let statusClass = 'text-warning';
                        const reservationDateTime = new Date(reservation.date + ' ' + reservation.time);
                        const currentDateTime = new Date(currentDate + ' ' + currentTime);

                        if (currentDateTime >= reservationDateTime) {
                            status = 'âڈ° Time Arrived';
                            statusClass = 'text-success';
                        }

                        row.innerHTML = `
                            <td><strong>${reservation.table}</strong></td>
                            <td>${reservation.customerName}</td>
                            <td>${reservation.phone || 'N/A'}</td>
                            <td>${reservation.date}</td>
                            <td>${reservation.time}</td>
                            <td>${reservation.guests}</td>
                            <td>${reservation.notes || '-'}</td>
                            <td class="${statusClass}">${status}</td>
                            <td>
                                <button class="btn btn-sm btn-danger delete-reservation" data-key="${key}" data-table="${reservation.table}">
                                    {{ __('Cancel') }}
                                </button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    }

                    // Add delete functionality
                    document.querySelectorAll('.delete-reservation').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const key = this.getAttribute('data-key');
                            const tableName = this.getAttribute('data-table');

                            if (confirm('{{ __("Are you sure you want to cancel this reservation?") }}')) {
                                // Remove reservation
                                delete reservations[key];
                                localStorage.setItem('tableReservations', JSON.stringify(reservations));

                                // Update table status to free
                                const table = document.querySelector(`[data-table="${tableName}"]`);
                                if (table && table.classList.contains('blocked')) {
                                    table.classList.remove('blocked');
                                    table.classList.add('free');
                                }

                                // Reload modal
                                openManageReservationsModal();
                            }
                        });
                    });
                }

                // Open modal
                console.log('Opening manage reservations modal...');
                const manageModal = new bootstrap.Modal(document.getElementById('manageReservationsModal'));
                manageModal.show();
            }

            // Only add event listener to the button that exists
            if (btnManageAllTables) {
                btnManageAllTables.addEventListener('click', openManageReservationsModal);
            } else if (btnManageTables) {
                btnManageTables.addEventListener('click', openManageReservationsModal);
            } else {
                console.error('No manage reservations button found in DOM!');
            }

            // Manage Orders button functionality
            const btnManageOrders = document.getElementById('btn-manage-orders');
            console.log('Manage Orders button found:', btnManageOrders);

            function openManageOrdersModal() {
                console.log('ًں“¦ Opening Manage Orders modal...');

                // Load and display all orders in modal
                const tableOrders = JSON.parse(localStorage.getItem('tableOrders') || '{}');
                console.log('ًں“¦ Orders from localStorage:', tableOrders);
                console.log('ًں“¦ Number of orders:', Object.keys(tableOrders).length);
                const tbody = document.getElementById('orders-table-body');
                const noOrdersMsg = document.getElementById('no-orders-message');

                if (!tbody) {
                    console.error('orders-table-body element not found!');
                    alert('Error: Table body element not found. Please refresh the page.');
                    return;
                }

                tbody.innerHTML = '';

                if (Object.keys(tableOrders).length === 0) {
                    if (tbody.closest('.table-responsive')) {
                        tbody.closest('.table-responsive').style.display = 'none';
                    }
                    if (noOrdersMsg) {
                        noOrdersMsg.style.display = 'block';
                    }
                } else {
                    if (tbody.closest('.table-responsive')) {
                        tbody.closest('.table-responsive').style.display = 'block';
                    }
                    if (noOrdersMsg) {
                        noOrdersMsg.style.display = 'none';
                    }

                    for (const [tableName, order] of Object.entries(tableOrders)) {
                        const row = document.createElement('tr');

                        // Format timestamp
                        const startedAt = new Date(order.timestamp).toLocaleString();

                        row.innerHTML = `
                            <td><strong>${tableName}</strong></td>
                            <td>${order.customer || 'N/A'}</td>
                            <td>${order.guests || 'N/A'}</td>
                            <td style="max-width: 200px; white-space: pre-wrap;">${order.items || '-'}</td>
                            <td>${order.notes || '-'}</td>
                            <td>${startedAt}</td>
                            <td>
                                <button class="btn btn-sm btn-primary view-order" data-table="${tableName}">
                                    {{ __('View/Edit') }}
                                </button>
                                <button class="btn btn-sm  complete-order" data-table="${tableName}">
                                    {{ __('Complete') }}
                                </button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    }

                    // Add view/edit functionality
                    document.querySelectorAll('.view-order').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const tableName = this.getAttribute('data-table');
                            const order = tableOrders[tableName];
                            const table = document.querySelector(`[data-table="${tableName}"]`);

                            if (table && order) {
                                // Close manage orders modal
                                bootstrap.Modal.getInstance(document.getElementById('manageOrdersModal')).hide();

                                // Open order modal with existing data
                                selectedTable = table;
                                document.getElementById('order-table-name').textContent = tableName;
                                document.getElementById('order-customer-name').value = order.customer || '';
                                document.getElementById('order-guests').value = order.guests || 1;
                                document.getElementById('order-items').value = order.items || '';
                                document.getElementById('order-notes').value = order.notes || '';
                                document.getElementById('order-table-status').value = 'utilized';

                                const orderModal = new bootstrap.Modal(document.getElementById('tableOrderModal'));
                                orderModal.show();
                            }
                        });
                    });

                    // Add complete functionality
                    document.querySelectorAll('.complete-order').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const tableName = this.getAttribute('data-table');

                            if (confirm(`{{ __("Complete order for table") }} ${tableName}?`)) {
                                // Remove order from localStorage
                                delete tableOrders[tableName];
                                localStorage.setItem('tableOrders', JSON.stringify(tableOrders));

                                // Update table status to free
                                const table = document.querySelector(`[data-table="${tableName}"]`);
                                if (table) {
                                    table.classList.remove('utilized', 'blocked');
                                    table.classList.add('free');
                                }

                                // Refresh the modal
                                openManageOrdersModal();

                                alert(`{{ __("Order completed! Table") }} ${tableName} {{ __("is now free.") }}`);
                            }
                        });
                    });
                }

                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('manageOrdersModal'));
                modal.show();
            }

            if (btnManageOrders) {
                btnManageOrders.addEventListener('click', openManageOrdersModal);
            }

            // Auto-check reservations and update table status
            function checkReservationTimes() {
                console.log('âڈ° checkReservationTimes called');
                const reservations = JSON.parse(localStorage.getItem('tableReservations') || '{}');
                console.log('âڈ° Current reservations:', reservations);
                const now = new Date();
                const currentDate = now.toISOString().split('T')[0];
                const currentTime = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
                console.log('âڈ° Current date/time:', currentDate, currentTime);

                // First, remove all existing badges
                document.querySelectorAll('.reservation-badge').forEach(badge => badge.remove());

                let needsUpdate = false;
                for (const [key, reservation] of Object.entries(reservations)) {
                    console.log('âڈ° Checking reservation:', key, reservation);
                    const table = document.querySelector(`[data-table="${reservation.table}"]`);
                    if (!table) {
                        console.log('âڈ° Table not found:', reservation.table);
                        continue;
                    }

                    // Add reservation badge to table
                    if (table.classList.contains('blocked')) {
                        console.log('âڈ° Adding badge to table:', reservation.table);
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

                    // Just mark that time has arrived, but don't change status automatically
                    // Status will change when user clicks on the table
                    if (reservation.date === currentDate && reservation.time <= currentTime && !reservation.timeArrived) {
                        console.log('âڈ° Marking time arrived for:', reservation.table);
                        // Store that reservation time has arrived
                        reservation.timeArrived = true;
                        needsUpdate = true;
                    }
                }

                // Only update localStorage if needed
                if (needsUpdate) {
                    console.log('âڈ° Updating localStorage with timeArrived flags');
                    localStorage.setItem('tableReservations', JSON.stringify(reservations));
                }
                console.log('âڈ° checkReservationTimes completed');
            }

            // Show reservation details in modal
            function showReservationDetails(reservation, reservationKey) {
                console.log('ًں”چ showReservationDetails called for:', reservation.table, reservationKey);
                console.log('ًں”چ Reservation data:', reservation);

                document.getElementById('detail-table').textContent = reservation.table;
                document.getElementById('detail-customer').textContent = reservation.customerName;
                document.getElementById('detail-phone').textContent = reservation.phone || 'N/A';
                document.getElementById('detail-date').textContent = reservation.date;
                document.getElementById('detail-time').textContent = reservation.time;
                document.getElementById('detail-guests').textContent = reservation.guests;
                document.getElementById('detail-notes').textContent = reservation.notes || '-';

                // Check table current status
                const table = document.querySelector(`[data-table="${reservation.table}"]`);
                let statusText = 'ًںں، Reserved';
                let showGuestArrivedBtn = true;

                if (table && table.classList.contains('utilized')) {
                    // Table is already utilized (guest has arrived)
                    statusText = 'ï؟½ Utilized - Guest Arrived';
                    showGuestArrivedBtn = false;
                } else {
                    // Check if reservation time has arrived
                    const now = new Date();
                    const currentDate = now.toISOString().split('T')[0];
                    const currentTime = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
                    const reservationDateTime = new Date(reservation.date + ' ' + reservation.time);
                    const currentDateTime = new Date(currentDate + ' ' + currentTime);

                    if (currentDateTime >= reservationDateTime) {
                        statusText = 'âڈ° Time Arrived - Waiting for Guest';
                    }
                }

                document.getElementById('detail-status').textContent = statusText;

                // Show/hide Guest Arrived button based on status
                const guestArrivedBtn = document.getElementById('guest-arrived-btn');
                if (guestArrivedBtn) {
                    guestArrivedBtn.style.display = showGuestArrivedBtn ? 'inline-block' : 'none';
                }

                // Cancel reservation button
                document.getElementById('cancel-reservation-btn').onclick = function() {
                    if (confirm('{{ __("Are you sure you want to cancel this reservation?") }}')) {
                        const reservations = JSON.parse(localStorage.getItem('tableReservations') || '{}');
                        delete reservations[reservationKey];
                        localStorage.setItem('tableReservations', JSON.stringify(reservations));

                        // Update table status
                        const table = document.querySelector(`[data-table="${reservation.table}"]`);
                        if (table) {
                            table.classList.remove('blocked');
                            table.classList.add('free');
                        }

                        // Close modal and refresh
                        bootstrap.Modal.getInstance(document.getElementById('reservationDetailsModal')).hide();
                        checkReservationTimes();
                        alert('{{ __("Reservation cancelled successfully") }}');
                    }
                };

                // Guest arrived button
                document.getElementById('guest-arrived-btn').onclick = function() {
                    console.log('ًںڑ¨ Guest Arrived button clicked!');
                    console.log('ًںڑ¨ Reservation key:', reservationKey);
                    console.log('ًںڑ¨ Table:', reservation.table);

                    const table = document.querySelector(`[data-table="${reservation.table}"]`);
                    if (table) {
                        table.classList.remove('blocked');
                        table.classList.add('utilized');

                        // Remove reservation from localStorage (guest has arrived)
                        const reservations = JSON.parse(localStorage.getItem('tableReservations') || '{}');
                        console.log('ًںڑ¨ Before delete:', reservations);
                        delete reservations[reservationKey];
                        console.log('ًںڑ¨ After delete:', reservations);
                        localStorage.setItem('tableReservations', JSON.stringify(reservations));
                        console.log('ًںڑ¨ Saved to localStorage');

                        // Remove reservation badge
                        const badge = table.querySelector('.reservation-badge');
                        if (badge) badge.remove();

                        // Open order modal
                        selectedTable = table;
                        document.getElementById('order-table-name').textContent = reservation.table;
                        document.getElementById('order-customer-name').value = reservation.customerName;
                        document.getElementById('order-guests').value = reservation.guests;
                        document.getElementById('order-notes').value = reservation.notes || '';

                        bootstrap.Modal.getInstance(document.getElementById('reservationDetailsModal')).hide();
                        const orderModal = new bootstrap.Modal(document.getElementById('tableOrderModal'));
                        orderModal.show();
                    }
                };

                // Open modal
                const detailsModal = new bootstrap.Modal(document.getElementById('reservationDetailsModal'));
                detailsModal.show();
            }

            // Add complete order button to utilized tables
            function addCompleteOrderButton(table) {
                // Remove existing button if any
                const existingBtn = table.querySelector('.complete-order-btn');
                if (existingBtn) existingBtn.remove();

                // Create complete order button
                const completeBtn = document.createElement('div');
                completeBtn.className = 'complete-order-btn';
                completeBtn.title = 'Complete Order & Free Table';
                completeBtn.innerHTML = `
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                `;

                // Add click handler to open modal
                completeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();

                    const tableName = table.getAttribute('data-table');
                    const tableOrders = JSON.parse(localStorage.getItem('tableOrders') || '{}');
                    const orderData = tableOrders[tableName];

                    if (orderData) {
                        // Populate modal with order details
                        document.getElementById('complete-table-name').textContent = tableName;
                        document.getElementById('complete-customer-name').textContent = orderData.customer || '-';
                        document.getElementById('complete-guests').textContent = orderData.guests || '1';
                        document.getElementById('complete-order-time').textContent = orderData.time || '-';
                        document.getElementById('complete-order-items').textContent = orderData.items || '{{ __("No items") }}';

                        // Show/hide notes section
                        const notesSection = document.getElementById('complete-notes-section');
                        const notesContent = document.getElementById('complete-order-notes');
                        if (orderData.notes) {
                            notesContent.textContent = orderData.notes;
                            notesSection.style.display = 'block';
                        } else {
                            notesSection.style.display = 'none';
                        }

                        // Store table reference for completion
                        window.currentCompleteTable = table;

                        // Open modal
                        const modal = new bootstrap.Modal(document.getElementById('completeOrderModal'));
                        modal.show();
                    }
                });

                table.appendChild(completeBtn);
            }

            // Handle complete order confirmation - use setTimeout to ensure DOM is ready
            setTimeout(function() {
                const confirmCompleteBtn = document.getElementById('confirm-complete-order');
                if (confirmCompleteBtn) {
                    // Remove any existing listeners
                    const newBtn = confirmCompleteBtn.cloneNode(true);
                    confirmCompleteBtn.parentNode.replaceChild(newBtn, confirmCompleteBtn);

                    // Add new listener
                    newBtn.addEventListener('click', function() {
                        const table = window.currentCompleteTable;
                        if (!table) {
                            console.error('No table reference found');
                            return;
                        }

                        const tableName = table.getAttribute('data-table');
                        console.log('Completing order for:', tableName);

                        // Remove order from localStorage
                        const tableOrders = JSON.parse(localStorage.getItem('tableOrders') || '{}');
                        delete tableOrders[tableName];
                        localStorage.setItem('tableOrders', JSON.stringify(tableOrders));

                        // Update table status to free
                        table.classList.remove('utilized', 'blocked');
                        table.classList.add('free');

                        // Remove the complete button
                        const completeBtn = table.querySelector('.complete-order-btn');
                        if (completeBtn) completeBtn.remove();

                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('completeOrderModal'));
                        if (modal) modal.hide();

                        console.log(`Order completed for ${tableName}, table is now free`);

                        // Show success message
                        setTimeout(() => {
                            alert(`âœ… Order completed! ${tableName} is now free.`);
                        }, 300);
                    });

                    console.log('Complete order button listener attached');
                } else {
                    console.error('confirm-complete-order button not found');
                }
            }, 500);

            // Restore table statuses from localStorage on page load
            function restoreTableStatuses() {
                const reservations = JSON.parse(localStorage.getItem('tableReservations') || '{}');
                const tableOrders = JSON.parse(localStorage.getItem('tableOrders') || '{}');

                console.log('Restoring table statuses...');
                console.log('Reservations:', reservations);
                console.log('Table Orders:', tableOrders);

                // First, reset all tables to free
                document.querySelectorAll('.table-item').forEach(table => {
                    const tableName = table.getAttribute('data-table');

                    // Check if table has a reservation
                    let hasReservation = false;
                    for (const [key, reservation] of Object.entries(reservations)) {
                        if (reservation.table === tableName) {
                            hasReservation = true;
                            // Table is reserved - set to blocked
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
                        // Add complete order button
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

                console.log('Table statuses restored from localStorage');
            }

            // Restore custom tables from localStorage
            function restoreCustomTables() {
                const customTables = JSON.parse(localStorage.getItem('customTables') || '[]');
                const floorPlan = document.querySelector('.restaurant-floor-plan');

                console.log('Restoring custom tables:', customTables);

                customTables.forEach(tableData => {
                    // Create table element
                    const newTable = document.createElement('div');
                    newTable.className = `table-item ${tableData.status}`;
                    newTable.setAttribute('data-table', tableData.name);
                    newTable.style.top = tableData.top;
                    newTable.style.left = tableData.left;

                    // Add table type class
                    newTable.classList.add(tableData.tableClass);

                    // Add table name
                    const nameSpan = document.createElement('span');
                    nameSpan.className = 'table-name';
                    nameSpan.textContent = tableData.name;
                    newTable.appendChild(nameSpan);

                    // Add chairs
                    const chairWrapper = document.createElement('div');
                    chairWrapper.className = 'chair-wrapper';

                    tableData.chairs.forEach(chairClass => {
                        const chair = document.createElement('div');
                        chair.className = `chair ${chairClass}`;
                        chairWrapper.appendChild(chair);
                    });

                    newTable.appendChild(chairWrapper);
                    floorPlan.appendChild(newTable);

                    // Add event listeners
                    addTableEventListeners(newTable);
                    makeDraggable(newTable);
                });

                console.log(`Restored ${customTables.length} custom tables`);
            }

            // Save custom table to localStorage
            function saveCustomTable(tableElement) {
                const customTables = JSON.parse(localStorage.getItem('customTables') || '[]');
                const tableName = tableElement.getAttribute('data-table');

                // Get table class (type)
                let tableClass = '';
                if (tableElement.classList.contains('table-rectangle')) tableClass = 'table-rectangle';
                else if (tableElement.classList.contains('table-rectangle-h10')) tableClass = 'table-rectangle-h10';
                else if (tableElement.classList.contains('table-rectangle-h')) tableClass = 'table-rectangle-h';
                else if (tableElement.classList.contains('table-rounded')) tableClass = 'table-rounded';
                else if (tableElement.classList.contains('table-circle')) tableClass = 'table-circle';

                // Get chair classes
                const chairs = [];
                tableElement.querySelectorAll('.chair').forEach(chair => {
                    const classList = Array.from(chair.classList);
                    const chairClass = classList.find(c => c.startsWith('chair-'));
                    if (chairClass) chairs.push(chairClass);
                });

                // Get status
                let status = 'free';
                if (tableElement.classList.contains('utilized')) status = 'utilized';
                else if (tableElement.classList.contains('blocked')) status = 'blocked';

                const tableData = {
                    name: tableName,
                    tableClass: tableClass,
                    chairs: chairs,
                    top: tableElement.style.top,
                    left: tableElement.style.left,
                    status: status
                };

                // Check if table already exists
                const existingIndex = customTables.findIndex(t => t.name === tableName);
                if (existingIndex >= 0) {
                    customTables[existingIndex] = tableData;
                } else {
                    customTables.push(tableData);
                }

                localStorage.setItem('customTables', JSON.stringify(customTables));
                console.log('Custom table saved:', tableData);
            }

            // Save table position to localStorage (for all tables)
            function saveTablePosition(tableName, tableElement) {
                const tablePositions = JSON.parse(localStorage.getItem('tablePositions') || '{}');

                tablePositions[tableName] = {
                    top: tableElement.style.top,
                    left: tableElement.style.left,
                    right: tableElement.style.right,
                    bottom: tableElement.style.bottom,
                    rotation: tableElement.getAttribute('data-rotation') || '0'
                };

                localStorage.setItem('tablePositions', JSON.stringify(tablePositions));
            }

            // Restore table positions from localStorage
            function restoreTablePositions() {
                const tablePositions = JSON.parse(localStorage.getItem('tablePositions') || '{}');

                console.log('Restoring table positions:', tablePositions);

                document.querySelectorAll('.table-item').forEach(table => {
                    const tableName = table.getAttribute('data-table');

                    if (tablePositions[tableName]) {
                        const position = tablePositions[tableName];
                        if (position.top) table.style.top = position.top;
                        if (position.left) table.style.left = position.left;
                        if (position.right) table.style.right = position.right;
                        if (position.bottom) table.style.bottom = position.bottom;

                        // Restore rotation
                        if (position.rotation && position.rotation !== '0') {
                            table.setAttribute('data-rotation', position.rotation);
                        }

                        console.log(`Position restored for ${tableName}:`, position);
                    }
                });

                console.log('All table positions restored');
            }

            // Save area position to localStorage (for Bar, Toilets, Entrance)
            function saveAreaPosition(areaName, areaElement) {
                const areaPositions = JSON.parse(localStorage.getItem('areaPositions') || '{}');

                areaPositions[areaName] = {
                    top: areaElement.style.top,
                    left: areaElement.style.left,
                    right: areaElement.style.right,
                    bottom: areaElement.style.bottom
                };

                // Save entrance side if it's the entrance
                if (areaName === 'entrance') {
                    areaPositions[areaName].entranceSide = areaElement.getAttribute('data-entrance-side');
                }

                localStorage.setItem('areaPositions', JSON.stringify(areaPositions));
            }

            // Restore area positions from localStorage
            function restoreAreaPositions() {
                const areaPositions = JSON.parse(localStorage.getItem('areaPositions') || '{}');

                console.log('Restoring area positions:', areaPositions);

                document.querySelectorAll('[data-area]').forEach(area => {
                    const areaName = area.getAttribute('data-area');

                    if (areaPositions[areaName]) {
                        const position = areaPositions[areaName];
                        if (position.top) area.style.top = position.top;
                        if (position.left) area.style.left = position.left;
                        if (position.right) area.style.right = position.right;
                        if (position.bottom) area.style.bottom = position.bottom;
                        if (position.entranceSide && areaName === 'entrance') {
                            area.setAttribute('data-entrance-side', position.entranceSide);
                            // Don't call updateEntranceCutout here - will be called in setTimeout
                        }

                        console.log(`Position restored for ${areaName}:`, position);
                    }
                });

                console.log('All area positions restored');
            }

            // Update entrance cutout position based on entrance location
            function updateEntranceCutout(entranceElement, side) {
                const wrapper = entranceElement.closest('.floor-plan-wrapper');
                const floorPlan = entranceElement.closest('.restaurant-floor-plan');
                const cutoutCover = wrapper.querySelector('.entrance-cutout-cover');

                if (!cutoutCover) return;

                // Always use getBoundingClientRect for accurate positioning
                const entranceRect = entranceElement.getBoundingClientRect();
                const floorPlanRect = floorPlan.getBoundingClientRect();

                // Calculate entrance position relative to floor plan
                const entranceTop = entranceRect.top - floorPlanRect.top;
                const entranceLeft = entranceRect.left - floorPlanRect.left;
                const entranceWidth = entranceRect.width;
                const entranceHeight = entranceRect.height;

                console.log('Cutout calculation:', {
                    side,
                    entranceTop,
                    entranceLeft,
                    entranceWidth,
                    entranceHeight
                });

                if (side === 'right') {
                    // Cover the border line on right side
                    cutoutCover.style.width = '6px';
                    cutoutCover.style.height = '120px';
                    cutoutCover.style.right = '-3px';
                    cutoutCover.style.left = 'auto';
                    cutoutCover.style.top = (entranceTop + entranceHeight / 2 - 60) + 'px';
                    cutoutCover.style.bottom = 'auto';
                } else if (side === 'left') {
                    // Cover the border line on left side
                    cutoutCover.style.width = '6px';
                    cutoutCover.style.height = '120px';
                    cutoutCover.style.left = '-3px';
                    cutoutCover.style.right = 'auto';
                    cutoutCover.style.top = (entranceTop + entranceHeight / 2 - 60) + 'px';
                    cutoutCover.style.bottom = 'auto';
                } else if (side === 'top') {
                    // Cover the border line on top side
                    cutoutCover.style.width = '120px';
                    cutoutCover.style.height = '6px';
                    cutoutCover.style.top = '-3px';
                    cutoutCover.style.bottom = 'auto';
                    cutoutCover.style.left = (entranceLeft + entranceWidth / 2 - 60) + 'px';
                    cutoutCover.style.right = 'auto';
                } else if (side === 'bottom') {
                    // Cover the border line on bottom side
                    cutoutCover.style.width = '120px';
                    cutoutCover.style.height = '6px';
                    cutoutCover.style.bottom = '-3px';
                    cutoutCover.style.top = 'auto';
                    cutoutCover.style.left = (entranceLeft + entranceWidth / 2 - 60) + 'px';
                    cutoutCover.style.right = 'auto';
                }

                console.log('Cutout positioned:', {
                    top: cutoutCover.style.top,
                    left: cutoutCover.style.left,
                    right: cutoutCover.style.right,
                    bottom: cutoutCover.style.bottom
                });
            }

            // Delete custom table from localStorage
            function deleteCustomTable(tableName) {
                const customTables = JSON.parse(localStorage.getItem('customTables') || '[]');
                const filtered = customTables.filter(t => t.name !== tableName);
                localStorage.setItem('customTables', JSON.stringify(filtered));
                console.log('Custom table deleted:', tableName);
            }

            // Clear all data button - now "Manage Tables" button
            const btnClearAllData = document.getElementById('btn-clear-all-data');
            if (btnClearAllData) {
                btnClearAllData.addEventListener('click', function() {
                    // Show modal with all tables (default + custom)
                    const customTables = JSON.parse(localStorage.getItem('customTables') || '[]');
                    const allTables = document.querySelectorAll('.table-item');

                    let tablesList = '<div style="max-height: 400px; overflow-y: auto;"><table class="table table-striped"><thead><tr><th>Table Name</th><th>Chairs</th><th>Status</th><th>Type</th><th>Actions</th></tr></thead><tbody>';

                    allTables.forEach(table => {
                        const tableName = table.getAttribute('data-table');
                        const chairCount = table.querySelectorAll('.chair').length;
                        let status = 'Free';
                        if (table.classList.contains('utilized')) status = 'Utilized';
                        else if (table.classList.contains('blocked')) status = 'Reserved';

                        const isCustom = customTables.some(t => t.name === tableName);
                        const tableType = isCustom ? 'Custom' : 'Default';

                        const deleteBtn = isCustom ? `<button class="btn btn-sm btn-danger delete-table-btn" data-table="${tableName}">Delete</button>` : '<span class="text-muted">-</span>';

                        tablesList += `<tr>
                            <td><strong>${tableName}</strong></td>
                            <td>${chairCount} chairs</td>
                            <td><span class="badge bg-${status === 'Free' ? 'success' : status === 'Utilized' ? 'danger' : 'warning'}">${status}</span></td>
                            <td>${tableType}</td>
                            <td>${deleteBtn}</td>
                        </tr>`;
                    });

                    tablesList += '</tbody></table></div>';

                    // Create custom modal
                    const modalHtml = `
                        <div class="modal fade" id="manageTablesModal" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Manage Tables</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        ${tablesList}

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    // Remove old modal if exists
                    const oldModal = document.getElementById('manageTablesModal');
                    if (oldModal) oldModal.remove();

                    // Add new modal
                    document.body.insertAdjacentHTML('beforeend', modalHtml);

                    // Open modal
                    const manageTablesModal = new bootstrap.Modal(document.getElementById('manageTablesModal'));
                    manageTablesModal.show();

                    // Add delete table functionality
                    document.querySelectorAll('.delete-table-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const tableName = this.getAttribute('data-table');
                            if (confirm(`Are you sure you want to delete table ${tableName}?`)) {
                                // Remove from DOM
                                const tableElement = document.querySelector(`[data-table="${tableName}"]`);
                                if (tableElement) tableElement.remove();

                                // Remove from localStorage
                                deleteCustomTable(tableName);

                                // Close and reopen modal to refresh
                                manageTablesModal.hide();
                                setTimeout(() => btnClearAllData.click(), 300);
                            }
                        });
                    });

                    // Clear all reservations & orders
                    document.getElementById('clear-all-data-btn').addEventListener('click', function() {
                        if (confirm('Are you sure you want to clear all reservations, orders, and positions? This cannot be undone.')) {
                            localStorage.removeItem('tableReservations');
                            localStorage.removeItem('tableOrders');
                            localStorage.removeItem('tablePositions');
                            localStorage.removeItem('areaPositions');

                            document.querySelectorAll('.table-item').forEach(table => {
                                table.classList.remove('blocked', 'utilized');
                                table.classList.add('free');
                            });

                            document.querySelectorAll('.reservation-badge').forEach(badge => badge.remove());

                            alert('All reservations, orders, and positions cleared! Refresh the page to reset positions.');
                            manageTablesModal.hide();
                        }
                    });

                    // Clear all custom tables
                    document.getElementById('clear-custom-tables-btn').addEventListener('click', function() {
                        if (confirm('Are you sure you want to delete all custom tables? This cannot be undone.')) {
                            customTables.forEach(tableData => {
                                const tableElement = document.querySelector(`[data-table="${tableData.name}"]`);
                                if (tableElement) tableElement.remove();
                            });

                            localStorage.removeItem('customTables');
                            alert('All custom tables deleted!');
                            manageTablesModal.hide();
                        }
                    });
                });
            }

            // Restore custom tables first (before restoring statuses)
            restoreCustomTables();

            // Restore table statuses on page load
            restoreTableStatuses();

            // Restore table positions after tables are loaded
            restoreTablePositions();

            // Restore area positions (Bar, Toilets, Entrance)
            restoreAreaPositions();

            // Wait for DOM to be fully ready, then update entrance cutout
            // Use window.load event AND font loading to ensure all elements are fully rendered
            function initializeEntranceCutout() {
                const entranceArea = document.querySelector('.entrance-area');
                if (!entranceArea) {
                    console.error('â‌Œ Entrance area not found');
                    return;
                }

                const entranceSide = entranceArea.getAttribute('data-entrance-side') || 'right';

                // Retry logic with multiple attempts
                let retryCount = 0;
                const maxRetries = 10;

                function attemptUpdate() {
                    // Force a reflow
                    entranceArea.offsetHeight;

                    const rect = entranceArea.getBoundingClientRect();
                    const computedStyle = window.getComputedStyle(entranceArea);
                    const isVisible = computedStyle.display !== 'none' && computedStyle.visibility !== 'hidden';

                    console.log(`=== Entrance Cutout Attempt ${retryCount + 1} ===`);
                    console.log('Side:', entranceSide);
                    console.log('Rect:', rect.width, 'x', rect.height);
                    console.log('Display:', computedStyle.display, 'Visibility:', computedStyle.visibility);

                    // Check if element has dimensions OR if we've exhausted retries
                    if ((rect.width === 0 || rect.height === 0) && retryCount < maxRetries && isVisible) {
                        retryCount++;
                        console.log(`âڑ ï¸ڈ Entrance not rendered yet, retry ${retryCount}/${maxRetries} in 300ms...`);
                        setTimeout(attemptUpdate, 300);
                    } else if (rect.width > 0 && rect.height > 0) {
                        updateEntranceCutout(entranceArea, entranceSide);
                        console.log('âœ… Cutout Updated Successfully');
                    } else {
                        // Force update anyway - the cutout calculation will use the entrance position
                        console.warn('âڑ ï¸ڈ Forcing cutout update despite 0 dimensions');
                        updateEntranceCutout(entranceArea, entranceSide);
                        console.log('âœ… Cutout forced update completed');
                    }
                }

                attemptUpdate();
            }

            // Wait for fonts to load before initializing
            function startInitialization() {
                if (document.fonts && document.fonts.ready) {
                    document.fonts.ready.then(() => {
                        console.log('ًں“‌ Fonts loaded, initializing entrance cutout...');
                        setTimeout(initializeEntranceCutout, 200);
                    });
                } else {
                    // Fallback if fonts API not available
                    setTimeout(initializeEntranceCutout, 500);
                }
            }

            // Initialize on window load (ensures all resources loaded)
            if (document.readyState === 'complete') {
                startInitialization();
            } else {
                window.addEventListener('load', startInitialization);
            }

            // Also run initial checks
            setTimeout(() => {
                checkReservationTimes();
                console.log('Initial reservation check completed');
            }, 500);

            // Check reservation times every minute
            setInterval(checkReservationTimes, 60000); // Check every 60 seconds

            // Make Reservation button functionality
            const btnMakeReservation = document.getElementById('btn-make-reservation');
            if (btnMakeReservation) {
                btnMakeReservation.addEventListener('click', function() {
                    // Reset form first
                    document.getElementById('reservation-customer-name').value = '';
                    document.getElementById('reservation-phone').value = '';
                    document.getElementById('reservation-guests').value = '2';
                    document.getElementById('reservation-notes').value = '';
                    document.getElementById('available-tables-list').style.display = 'none';
                    document.getElementById('confirm-reservation').disabled = true;

                    // Set default date to today
                    const today = new Date().toISOString().split('T')[0];
                    document.getElementById('reservation-date').value = today;
                    document.getElementById('reservation-date').min = today;

                    // Set default time to current time (always fresh)
                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    document.getElementById('reservation-time').value = `${hours}:${minutes}`;

                    const reservationModal = new bootstrap.Modal(document.getElementById('makeReservationModal'));
                    reservationModal.show();
                });
            }

            // Search available tables
            let selectedTableForReservation = null;
            const searchAvailableBtn = document.getElementById('search-available-tables');
            if (searchAvailableBtn) {
                searchAvailableBtn.addEventListener('click', function() {
                    const guests = parseInt(document.getElementById('reservation-guests').value);
                    const date = document.getElementById('reservation-date').value;
                    const time = document.getElementById('reservation-time').value;
                    const customerName = document.getElementById('reservation-customer-name').value;

                    if (!customerName || !date || !time) {
                        alert('{{ __("Please fill in customer name, date and time") }}');
                        return;
                    }

                    // Get all tables
                    const allTables = document.querySelectorAll('.table-item');
                    const reservations = JSON.parse(localStorage.getItem('tableReservations') || '{}');
                    const availableTables = [];

                    allTables.forEach(table => {
                        const tableName = table.getAttribute('data-table');
                        const chairCount = table.querySelectorAll('.chair').length;

                        // Check if table has enough chairs
                        if (chairCount >= guests) {
                            // Check if table is not reserved at this time
                            const reservationKey = `${tableName}_${date}_${time}`;
                            if (!reservations[reservationKey] && !table.classList.contains('utilized')) {
                                availableTables.push({
                                    name: tableName,
                                    chairs: chairCount,
                                    element: table
                                });
                            }
                        }
                    });

                    // Display available tables
                    const container = document.getElementById('available-tables-container');
                    container.innerHTML = '';

                    if (availableTables.length === 0) {
                        container.innerHTML = '<p class="text-danger">{{ __("No available tables found for this time and guest count") }}</p>';
                        document.getElementById('available-tables-list').style.display = 'block';
                        return;
                    }

                    availableTables.forEach(table => {
                        const tableBtn = document.createElement('button');
                        tableBtn.className = 'btn btn-outline-success';
                        tableBtn.textContent = `${table.name} (${table.chairs} {{ __("chairs") }})`;
                        tableBtn.onclick = function() {
                            // Remove selection from other buttons
                            container.querySelectorAll('.btn').forEach(btn => {
                                btn.classList.remove('btn-success');
                                btn.classList.add('btn-outline-success');
                            });
                            // Select this button
                            this.classList.remove('btn-outline-success');
                            this.classList.add('btn-success');
                            selectedTableForReservation = table;
                            document.getElementById('confirm-reservation').disabled = false;
                        };
                        container.appendChild(tableBtn);
                    });

                    document.getElementById('available-tables-list').style.display = 'block';
                });
            }

            // Confirm reservation
            const confirmReservationBtn = document.getElementById('confirm-reservation');
            console.log('Confirm reservation button found:', confirmReservationBtn);
            console.log('Button exists?', confirmReservationBtn !== null);

            if (confirmReservationBtn) {
                console.log('Adding click event listener to confirm reservation button...');
                confirmReservationBtn.addEventListener('click', function() {
                    console.log('âœ… Confirm reservation clicked!');
                    console.log('Selected table:', selectedTableForReservation);

                    if (!selectedTableForReservation) {
                        alert('{{ __("Please select a table") }}');
                        return;
                    }

                    const customerName = document.getElementById('reservation-customer-name').value;
                    const phone = document.getElementById('reservation-phone').value;
                    const date = document.getElementById('reservation-date').value;
                    const time = document.getElementById('reservation-time').value;
                    const guests = parseInt(document.getElementById('reservation-guests').value);
                    const notes = document.getElementById('reservation-notes').value;

                    console.log('Reservation data:', {customerName, phone, date, time, guests, notes});

                    // Validate required fields
                    if (!customerName || !date || !time) {
                        alert('{{ __("Please fill in customer name, date and time") }}');
                        return;
                    }

                    // Validate: guests cannot exceed table chairs
                    if (guests > selectedTableForReservation.chairs) {
                        alert(`{{ __("Number of guests") }} (${guests}) {{ __("cannot exceed table capacity") }} (${selectedTableForReservation.chairs} {{ __("chairs") }}). {{ __("Please select a larger table or reduce guests.") }}`);
                        return;
                    }

                    // Save reservation
                    const reservations = JSON.parse(localStorage.getItem('tableReservations') || '{}');
                    const reservationKey = `${selectedTableForReservation.name}_${date}_${time}`;

                    console.log('Reservation key:', reservationKey);

                    reservations[reservationKey] = {
                        table: selectedTableForReservation.name,
                        customerName: customerName,
                        phone: phone,
                        date: date,
                        time: time,
                        guests: guests,
                        notes: notes,
                        timestamp: new Date().toISOString()
                    };

                    console.log('Saving to localStorage:', reservations);
                    localStorage.setItem('tableReservations', JSON.stringify(reservations));

                    // Verify save
                    const savedData = localStorage.getItem('tableReservations');
                    console.log('âœ… Saved! Verifying:', savedData);
                    console.log('âœ… Parsed saved data:', JSON.parse(savedData));

                    // Change table status to blocked (reserved)
                    selectedTableForReservation.element.classList.remove('free', 'utilized');
                    selectedTableForReservation.element.classList.add('blocked');
                    console.log('Table status changed to blocked');

                    // Immediately add reservation badge to the table
                    console.log('âœ… Calling checkReservationTimes to add badge...');
                    checkReservationTimes();
                    console.log('âœ… Badge added, now closing modal...');

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('makeReservationModal'));
                    modal.hide();

                    // Reset form
                    document.getElementById('reservation-customer-name').value = '';
                    document.getElementById('reservation-phone').value = '';
                    document.getElementById('reservation-guests').value = '2';
                    document.getElementById('reservation-guests').removeAttribute('max'); // Remove max limit
                    document.getElementById('reservation-notes').value = '';
                    document.getElementById('available-tables-list').style.display = 'none';
                    document.getElementById('confirm-reservation').disabled = true;
                    selectedTableForReservation = null;

                    alert(`{{ __("Reservation confirmed for") }} ${customerName} {{ __("at table") }} ${reservations[reservationKey].table}`);
                });
            }

            // Add draggable to all existing tables
            document.querySelectorAll('.table-item').forEach(table => {
                makeDraggable(table);
            });

            // Add draggable to static areas (Bar, Toilets, Entrance)
            document.querySelectorAll('[data-area]').forEach(area => {
                makeDraggable(area);
            });

            document.addEventListener('mousemove', function(e) {
                if (!isDragging || !currentTable) return;

                const parent = currentTable.offsetParent;
                const parentRect = parent.getBoundingClientRect();

                let newX = e.clientX - parentRect.left - offsetX;
                let newY = e.clientY - parentRect.top - offsetY;

                // Check if this is the entrance area
                const isEntrance = currentTable.getAttribute('data-area') === 'entrance';

                if (isEntrance) {
                    // Entrance can move freely, but snap to nearest edge
                    const tableWidth = currentTable.offsetWidth;
                    const tableHeight = currentTable.offsetHeight;

                    // Calculate distances to each edge
                    const distToLeft = newX;
                    const distToRight = parent.offsetWidth - (newX + tableWidth);
                    const distToTop = newY;
                    const distToBottom = parent.offsetHeight - (newY + tableHeight);

                    // Find nearest edge
                    const minDist = Math.min(distToLeft, distToRight, distToTop, distToBottom);

                    let entranceSide = 'right';

                    if (minDist === distToLeft) {
                        // Snap to left edge
                        entranceSide = 'left';
                        currentTable.style.left = '20px';
                        currentTable.style.right = 'auto';
                        newY = Math.max(0, Math.min(newY, parent.offsetHeight - tableHeight));
                        currentTable.style.top = newY + 'px';
                        currentTable.style.bottom = 'auto';
                    } else if (minDist === distToRight) {
                        // Snap to right edge
                        entranceSide = 'right';
                        currentTable.style.right = '20px';
                        currentTable.style.left = 'auto';
                        newY = Math.max(0, Math.min(newY, parent.offsetHeight - tableHeight));
                        currentTable.style.top = newY + 'px';
                        currentTable.style.bottom = 'auto';
                    } else if (minDist === distToTop) {
                        // Snap to top edge
                        entranceSide = 'top';
                        currentTable.style.top = '20px';
                        currentTable.style.bottom = 'auto';
                        newX = Math.max(0, Math.min(newX, parent.offsetWidth - tableWidth));
                        currentTable.style.left = newX + 'px';
                        currentTable.style.right = 'auto';
                    } else {
                        // Snap to bottom edge
                        entranceSide = 'bottom';
                        currentTable.style.bottom = '20px';
                        currentTable.style.top = 'auto';
                        newX = Math.max(0, Math.min(newX, parent.offsetWidth - tableWidth));
                        currentTable.style.left = newX + 'px';
                        currentTable.style.right = 'auto';
                    }

                    // Update entrance side attribute
                    currentTable.setAttribute('data-entrance-side', entranceSide);

                    // Update cutout position
                    updateEntranceCutout(currentTable, entranceSide);
                } else {
                    // Normal dragging for tables and other areas
                    const tableWidth = currentTable.offsetWidth;
                    const tableHeight = currentTable.offsetHeight;

                    newX = Math.max(0, Math.min(newX, parent.offsetWidth - tableWidth));
                    newY = Math.max(0, Math.min(newY, parent.offsetHeight - tableHeight));

                    currentTable.style.left = newX + 'px';
                    currentTable.style.top = newY + 'px';
                    currentTable.style.right = 'auto';
                    currentTable.style.bottom = 'auto';
                }
            });

            document.addEventListener('mouseup', function() {
                if (isDragging && currentTable) {
                    currentTable.style.cursor = 'move';

                    // Check if it's a table or an area
                    const tableName = currentTable.getAttribute('data-table');
                    const areaName = currentTable.getAttribute('data-area');

                    if (tableName) {
                        // Save position for tables
                        saveTablePosition(tableName, currentTable);

                        // Check if this is a custom table and save its full data
                        const customTables = JSON.parse(localStorage.getItem('customTables') || '[]');
                        const isCustomTable = customTables.some(t => t.name === tableName);

                        if (isCustomTable) {
                            saveCustomTable(currentTable);
                            console.log('Custom table position updated:', tableName);
                        } else {
                            console.log('Default table position saved:', tableName);
                        }
                    } else if (areaName) {
                        // Save position for static areas
                        saveAreaPosition(areaName, currentTable);
                        console.log('Area position saved:', areaName);
                    }

                    isDragging = false;
                    currentTable = null;
                }
            });
        });

        // Position sidebar to start from pos-main-container and stick on scroll
        function positionSidebar() {
            const mainContainer = document.querySelector('.pos-main-container');
            const sidebar = document.querySelector('.order-sidebar');

            if (mainContainer && sidebar && window.innerWidth > 992) {
                const containerTop = mainContainer.offsetTop;
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                if (scrollTop >= containerTop) {
                    // When scrolled past container, stick to top
                    sidebar.style.top = '0px';
                } else {
                    // Before scroll, align with container
                }
            }
        }

        // Run on load, scroll, and resize
        window.addEventListener('load', positionSidebar);
        window.addEventListener('scroll', positionSidebar);
        window.addEventListener('resize', positionSidebar);
    </script>
    <script src="{{ asset('assets/js/choices.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom/sale.js') . '?v=' . time() }}"></script>
    <script src="{{ asset('assets/js/custom/math.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom/calculator.js') }}"></script>
    <script src="{{ asset('assets/js/custom/pos-products.js') . '?v=' . time() }}"></script>
    <script src="{{ asset('assets/js/custom/pos-payment-modal.js') . '?v=' . time() }}"></script>

