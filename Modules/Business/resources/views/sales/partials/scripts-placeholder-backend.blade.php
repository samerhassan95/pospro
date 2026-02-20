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
        
        // Note: Product click handling is done in sale.js
        // We don't need duplicate handlers here to avoid double-adding items
        
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
    });

    // ============================================
    // TABLE SYSTEM - BACKEND INTEGRATION
    // ============================================
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🔄 Initializing table system with backend integration...');
        
        // Save table order functionality
        const saveTableOrderBtn = document.getElementById('save-table-order');
        if (saveTableOrderBtn) {
            saveTableOrderBtn.addEventListener('click', async function() {
                const customerName = document.getElementById('order-customer-name').value;
                const guests = document.getElementById('order-guests').value;
                const orderItems = document.getElementById('order-items').value;
                const notes = document.getElementById('order-notes').value;
                const time = document.getElementById('order-time').value;
                const status = document.getElementById('order-table-status').value;

                if (!customerName) {
                    console.log('{{ __("Please enter customer name") }}');
                    return;
                }

                // Get table element and name
                const selectedTable = document.querySelector('.table-item.selected');
                if (!selectedTable) {
                    console.log('{{ __("No table selected") }}');
                    return;
                }

                const tableName = selectedTable.getAttribute('data-table');
                const tableId = selectedTable.getAttribute('data-table-id');

                // Prepare order data for backend
                const orderData = {
                    table_id: tableId,
                    table_name: tableName,
                    customer_name: customerName,
                    guest_count: guests,
                    items: orderItems,
                    notes: notes,
                    order_time: time,
                    status: status
                };

                try {
                    if (status === 'completed') {
                        // Complete the order
                        await completeOrderInBackend(orderData.id);
                        
                        // Update table status to free
                        selectedTable.classList.remove('utilized', 'blocked');
                        selectedTable.classList.add('free');
                        
                        console.log('{{ __("Order completed! Table is now free.") }}');
                    } else {
                        // Save order to backend
                        await saveOrderToBackend(orderData);
                        
                        // Update table status
                        selectedTable.classList.remove('free', 'blocked');
                        selectedTable.classList.add('utilized');
                        
                        console.log('{{ __("Order saved successfully!") }}');
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
                } catch (error) {
                    console.error('❌ Error saving order:', error);
                    console.log('{{ __("Failed to save order") }}');
                }
            });
        }

        // Manage Reservations button functionality
        const btnManageAllTables = document.getElementById('btn-manage-all-tables');
        if (btnManageAllTables) {
            btnManageAllTables.addEventListener('click', async function() {
                console.log('🔄 Opening Manage Reservations modal...');

                try {
                    // Load reservations from backend
                    const reservations = await getReservationsFromBackend();
                    console.log('📥 Loaded reservations:', reservations);

                    const tbody = document.getElementById('reservations-table-body');
                    const noReservationsMsg = document.getElementById('no-reservations-message');

                    if (!tbody) {
                        console.error('❌ reservations-table-body element not found!');
                        return;
                    }

                    tbody.innerHTML = '';

                    if (reservations.length === 0) {
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

                        reservations.forEach(reservation => {
                            const row = document.createElement('tr');

                            // Determine status
                            let status = '⏰ Reserved';
                            let statusClass = 'text-warning';
                            const reservationDateTime = new Date(reservation.reservation_date + ' ' + reservation.reservation_time);

                            if (now >= reservationDateTime) {
                                status = '✓ Time Arrived';
                                statusClass = 'text-success';
                            }

                            row.innerHTML = `
                                <td><strong>${reservation.table_name}</strong></td>
                                <td>${reservation.customer_name}</td>
                                <td>${reservation.phone || 'N/A'}</td>
                                <td>${reservation.reservation_date}</td>
                                <td>${reservation.reservation_time}</td>
                                <td>${reservation.guest_count}</td>
                                <td>${reservation.notes || '-'}</td>
                                <td class="${statusClass}">${status}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger cancel-reservation" data-id="${reservation.id}" data-table="${reservation.table_name}">
                                        {{ __('Cancel') }}
                                    </button>
                                </td>
                            `;
                            tbody.appendChild(row);
                        });

                        // Add cancel functionality
                        document.querySelectorAll('.cancel-reservation').forEach(btn => {
                            btn.addEventListener('click', async function() {
                                const reservationId = this.getAttribute('data-id');
                                const tableName = this.getAttribute('data-table');

                                if (confirm('{{ __("Are you sure you want to cancel this reservation?") }}')) {
                                    try {
                                        await cancelReservationInBackend(reservationId);
                                        console.log('✅ Reservation cancelled');

                                        // Update table status to free
                                        const table = document.querySelector(`[data-table="${tableName}"]`);
                                        if (table && table.classList.contains('blocked')) {
                                            table.classList.remove('blocked');
                                            table.classList.add('free');
                                        }

                                        // Reload modal
                                        btnManageAllTables.click();
                                    } catch (error) {
                                        console.error('❌ Error cancelling reservation:', error);
                                    }
                                }
                            });
                        });
                    }

                    // Open modal
                    const manageModal = new bootstrap.Modal(document.getElementById('manageReservationsModal'));
                    manageModal.show();
                } catch (error) {
                    console.error('❌ Error loading reservations:', error);
                }
            });
        }

        // Manage Orders button functionality
        const btnManageOrders = document.getElementById('btn-manage-orders');
        if (btnManageOrders) {
            btnManageOrders.addEventListener('click', async function() {
                console.log('🔄 Opening Manage Orders modal...');

                try {
                    // Load orders from backend
                    const orders = await getOrdersFromBackend();
                    console.log('📥 Loaded orders:', orders);

                    const tbody = document.getElementById('orders-table-body');
                    const noOrdersMsg = document.getElementById('no-orders-message');

                    if (!tbody) {
                        console.error('❌ orders-table-body element not found!');
                        return;
                    }

                    tbody.innerHTML = '';

                    if (orders.length === 0) {
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

                        orders.forEach(order => {
                            const row = document.createElement('tr');

                            row.innerHTML = `
                                <td><strong>${order.table_name}</strong></td>
                                <td>${order.customer_name}</td>
                                <td>${order.guest_count}</td>
                                <td>${order.items || '-'}</td>
                                <td>${order.notes || '-'}</td>
                                <td>${order.order_time || '-'}</td>
                                <td>
                                    <button class="btn btn-sm btn-success complete-order" data-id="${order.id}" data-table="${order.table_name}">
                                        {{ __('Complete') }}
                                    </button>
                                </td>
                            `;
                            tbody.appendChild(row);
                        });

                        // Add complete functionality
                        document.querySelectorAll('.complete-order').forEach(btn => {
                            btn.addEventListener('click', async function() {
                                const orderId = this.getAttribute('data-id');
                                const tableName = this.getAttribute('data-table');

                                if (confirm('{{ __("Mark this order as completed?") }}')) {
                                    try {
                                        await completeOrderInBackend(orderId);
                                        console.log('✅ Order completed');

                                        // Update table status to free
                                        const table = document.querySelector(`[data-table="${tableName}"]`);
                                        if (table) {
                                            table.classList.remove('utilized', 'blocked');
                                            table.classList.add('free');
                                        }

                                        // Reload modal
                                        btnManageOrders.click();
                                    } catch (error) {
                                        console.error('❌ Error completing order:', error);
                                    }
                                }
                            });
                        });
                    }

                    // Open modal
                    const manageModal = new bootstrap.Modal(document.getElementById('manageOrdersModal'));
                    manageModal.show();
                } catch (error) {
                    console.error('❌ Error loading orders:', error);
                }
            });
        }

        console.log('✅ Table system initialized with backend integration');
    });
</script>
