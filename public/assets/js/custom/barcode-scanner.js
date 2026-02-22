// Barcode Scanner JavaScript
(function($) {
    'use strict';

    let scanner = null;
    let isScanning = false;
    let searchTimeout = null;
    let lastScanTime = 0;
    let scanCooldown = 1000; // 1 second cooldown between scans
    let barcodeBuffer = ''; // Buffer for external scanner input
    let barcodeTimeout = null;

    // Initialize barcode scanner functionality
    function initBarcodeScanner() {
        console.log('🔧 Initializing barcode scanner...');
        
        // Check if elements exist
        const scanBtn = $('#scan-barcode-btn');
        const searchView = $('#search-view');
        const categoryView = $('#category-view');
        
        console.log('🔧 Scan button found:', scanBtn.length);
        console.log('🔧 Search view found:', searchView.length);
        console.log('🔧 Category view found:', categoryView.length);
        
        if (scanBtn.length === 0) {
            console.error('❌ Scan button not found!');
            return;
        }
        
        if (searchView.length === 0) {
            console.error('❌ Search view not found!');
            return;
        }
        
        // Ensure proper initial state
        $('#search-view').hide().css('display', 'none');
        $('#category-view').show().css('display', 'block');
        $('#brand-view').hide().css('display', 'none');
        $('#tables-view').hide().css('display', 'none');
        
        // Set initial class
        $('.products-section').removeClass('view-active-category view-active-brand view-active-search view-active-tables');
        $('.products-section').addClass('view-active-category');
        
        console.log('🔧 Initial view states set');
        
        // Add direct click handler (not using override)
        scanBtn.off('click.barcode').on('click.barcode', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('🔍 DIRECT: Scan button clicked');
            console.log('🔍 DIRECT: Before - Search view display:', $('#search-view').css('display'));
            console.log('🔍 DIRECT: Before - Category view display:', $('#category-view').css('display'));
            
            // Simple direct approach
            $('.view-section').hide();
            $('#search-view').show();
            
            console.log('🔍 DIRECT: After - Search view display:', $('#search-view').css('display'));
            console.log('🔍 DIRECT: After - Category view display:', $('#category-view').css('display'));
            
            // Verify the element is actually visible
            const searchViewElement = document.getElementById('search-view');
            if (searchViewElement) {
                console.log('🔍 DIRECT: Search view element style:', searchViewElement.style.display);
                console.log('🔍 DIRECT: Search view computed style:', window.getComputedStyle(searchViewElement).display);
            }
            
            // Update button states
            $('.pos-view-btn, .pos-toggle-btn').removeClass('pos-toggle-btn-active active');
            $(this).addClass('pos-toggle-btn-active active');
            
            // Focus on search input
            setTimeout(() => {
                $('#product-search-input').focus();
            }, 100);
            
            return false;
        });
        
        console.log('🔧 Direct scan button handler attached');
        
        // Switch to search view when scan button is clicked (no modal)
        $('#scan-barcode-btn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('🔍 Scan button clicked - switching to search view');
            
            // Hide ALL views explicitly
            $('#category-view').hide();
            $('#brand-view').hide();
            $('#tables-view').hide();
            $('#search-view').hide();
            
            // Force hide with CSS
            $('#category-view').css('display', 'none');
            $('#brand-view').css('display', 'none');
            $('#tables-view').css('display', 'none');
            
            // Show search view
            $('#search-view').show().css('display', 'block');
            
            console.log('🔍 Search view should now be visible');
            console.log('Category view display:', $('#category-view').css('display'));
            console.log('Search view display:', $('#search-view').css('display'));
            
            // Remove active class from all view buttons
            $('.pos-view-btn, .pos-toggle-btn').removeClass('pos-toggle-btn-active active');
            
            // Add active class to scan button
            $(this).addClass('pos-toggle-btn-active active');
            
            // Focus on search input
            setTimeout(() => {
                $('#product-search-input').focus();
            }, 100);
        });

        // Search functionality - use the product-search-input in the view
        $('#product-search-input').on('input', function() {
            const query = $(this).val().trim();
            
            // Clear previous timeout
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            // Debounce search - only show results, don't auto-add
            searchTimeout = setTimeout(() => {
                if (query.length >= 2) {
                    searchProducts(query, false); // false = don't auto-add on typing
                } else {
                    clearResults();
                }
            }, 300);
        });

        // Clear search - handle both inputs
        $('#clear-search-btn, #clear-product-search-btn').on('click', function() {
            $('#product-search-input').val('').focus();
            clearResults();
        });

        // Camera controls
        $('#start-camera-scan').on('click', function() {
            startCameraScanning();
        });
        $('#stop-camera-scan').on('click', function() {
            stopCameraScanning();
        });

        // Handle Enter key in search input - auto add if single result (for external scanners)
        $('#product-search-input').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault(); // Prevent form submission
                const query = $(this).val().trim();
                if (query.length >= 2) {
                    // Auto-add when Enter is pressed (external scanner behavior)
                    searchProducts(query, true); // true = auto-add if single result
                }
            }
        });

        // Cleanup when switching away from search view
        $('.pos-view-btn').on('click', function() {
            const view = $(this).data('view');
            console.log('🔄 View button clicked:', view);
            
            if (view !== 'search' && this.id !== 'scan-barcode-btn') {
                console.log('🔄 Switching away from search view to:', view);
                
                stopCameraScanning();
                clearResults();
                $('#product-search-input').val('');
                
                // Hide search view explicitly
                $('#search-view').hide().css('display', 'none');
                
                // Show appropriate view
                if (view === 'category') {
                    $('#category-view').show().css('display', 'block');
                    $('#brand-view').hide().css('display', 'none');
                } else if (view === 'brand') {
                    $('#brand-view').show().css('display', 'block');
                    $('#category-view').hide().css('display', 'none');
                } else if (view === 'tables') {
                    $('#tables-view').show().css('display', 'block');
                    $('#category-view').hide().css('display', 'none');
                    $('#brand-view').hide().css('display', 'none');
                }
                
                console.log('🔄 After switch - Category view:', $('#category-view').css('display'));
                console.log('🔄 After switch - Search view:', $('#search-view').css('display'));
            }
        });
    }

    // Search products via AJAX
    function searchProducts(query, autoAdd = false) {
        showLoading();
        
        // Clear previous results
        $('#search-products-table').empty();
        
        $.ajax({
            url: $('#barcode-search-route').val() || '/business/products/search',
            method: 'POST',
            data: {
                query: query,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            timeout: 10000,
            success: function(response) {
                hideLoading();
                
                if (response.success && response.products && response.products.length > 0) {
                    // Always show products in table first
                    displayResults(response.products);
                    
                    // If autoAdd is true and only one product found, add it automatically
                    if (autoAdd && response.products.length === 1) {
                        const product = response.products[0];
                        
                        const productData = {
                            id: product.id,
                            name: product.productName || product.name,
                            code: product.productCode || product.code || '',
                            price: product.sales_price || product.price || 0,
                            image: product.image || '/assets/images/products/box.svg',
                            stock_quantity: product.stock_quantity || 0,
                            stock_id: product.stock_id || '',
                            quantity: 1
                        };
                        
                        // Auto add to cart
                        addProductToCart(productData, null);
                        
                        // Clear search input for next scan (external scanner support)
                        setTimeout(() => {
                            $('#product-search-input').val('').focus();
                        }, 500);
                        
                        // Show success message
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Product scanned and added to cart!');
                        }
                    }
                } else {
                    showNoResults();
                    
                    if (autoAdd && typeof toastr !== 'undefined') {
                        toastr.warning('No product found with code: ' + query);
                    }
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                
                let errorMessage = 'Error searching products. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (status === 'timeout') {
                    errorMessage = 'Search request timed out. Please try again.';
                } else if (status === 'abort') {
                    errorMessage = 'Search request was cancelled.';
                }
                
                showNoResults();
                
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage);
                }
            }
        });
    }

    // Display search results in table
    function displayResults(products) {
        const tbody = $('#search-products-table');
        tbody.empty();
        
        if (products.length === 0) {
            showNoResults();
            return;
        }

        products.forEach(function(product) {
            const row = createProductRow(product);
            tbody.append(row);
        });
    }

    // Create product row HTML for search table
    function createProductRow(product) {
        // Image URL comes as full URL from backend
        const imageUrl = product.image || '/assets/images/products/box.svg';
        
        const price = product.sales_price || product.price || 0;
        const stock = product.stock_quantity || 0;
        const stockClass = stock > 0 ? 'text-success' : 'text-danger';
        const batchNo = product.batch_no || '-';
        
        return `
            <tr data-product-id="${product.id}" 
                data-product-name="${product.productName || product.name}"
                data-product-code="${product.productCode || product.code || ''}"
                data-product-price="${price}"
                data-product-image="${imageUrl}"
                data-stock-quantity="${stock}"
                data-stock-id="${product.stock_id || ''}"
                style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px; width: 80px;">
                    <img src="${imageUrl}" alt="${product.productName || product.name}" 
                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; display: block;">
                </td>
                <td style="padding: 12px;">
                    <div style="font-weight: 500; color: #111827;">${product.productName || product.name}</div>
                    ${product.category ? `<small style="color: #6b7280;">${product.category.categoryName || product.category.name}</small>` : ''}
                </td>
                <td style="padding: 12px;">
                    <code style="background: #f3f4f6; padding: 4px 8px; border-radius: 4px; font-size: 12px;">${product.productCode || product.code || '-'}</code>
                </td>
                <td style="padding: 12px; color: #6b7280;">
                    ${batchNo}
                </td>
                <td style="padding: 12px;">
                    <span class="${stockClass}" style="font-weight: 500;">${stock}</span>
                </td>
                <td style="padding: 12px; font-weight: 600; color: #111827;">
                    ${formatCurrency(price)}
                </td>
                <td style="padding: 12px;">
                    <input type="number" class="qty-input" data-product-id="${product.id}" 
                           value="1" min="1" max="${stock}" 
                           style="width: 70px; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center;"
                           ${stock <= 0 ? 'disabled' : ''}>
                </td>
                <td style="padding: 12px; font-weight: 600; color: #111827;" class="subtotal-cell" data-price="${price}">
                    ${formatCurrency(price)}
                </td>
                <td style="padding: 12px; width: 120px;">
                    <button type="button" class="btn btn-primary btn-sm add-to-cart-from-scan" 
                            data-product-id="${product.id}"
                            style="padding: 8px 16px; border-radius: 6px; font-size: 13px; white-space: nowrap; width: 100%; min-height: 38px;"
                            ${stock <= 0 ? 'disabled' : ''}>
                        <svg class="btn-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align: middle; margin-right: 4px;">
                            <path d="M8 3V13M3 8H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span class="btn-spinner spinner-border spinner-border-sm" role="status" style="display: none; width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                        <span class="btn-text">Add</span>
                    </button>
                </td>
            </tr>
        `;
    }

    // Handle add to cart from search table rows
    $(document).on('click', '.add-to-cart-from-scan', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const $row = $btn.closest('tr');
        const $qtyInput = $row.find('.qty-input');
        const quantity = parseInt($qtyInput.val()) || 1;
        
        const productData = {
            id: $row.data('product-id'),
            name: $row.data('product-name'),
            code: $row.data('product-code'),
            price: $row.data('product-price'),
            image: $row.data('product-image'),
            stock_quantity: $row.data('stock-quantity'),
            stock_id: $row.data('stock-id'),
            quantity: quantity
        };

        // Add to cart
        addProductToCart(productData, $btn);
    });

    // Handle quantity input changes - update subtotal
    $(document).on('input', '.qty-input', function() {
        const $input = $(this);
        const qty = parseInt($input.val()) || 1;
        const $row = $input.closest('tr');
        const $subtotalCell = $row.find('.subtotal-cell');
        const price = parseFloat($subtotalCell.data('price')) || 0;
        const subtotal = price * qty;
        
        $subtotalCell.text(formatCurrency(subtotal));
    });

    // Add product to cart (integrate with existing cart system)
    function addProductToCart(product, $btn) {
        const cartData = {
            type: 'sale',
            id: product.id,
            name: product.name,
            quantity: 1, // Always add 1 at a time when scanning
            price: product.price,
            product_code: product.code,
            product_image: product.image,
            sales_price: product.price,
            stock_id: product.stock_id || null, // Important for duplicate detection
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        // Show loading state on button - simple approach
        if ($btn && $btn.length) {
            $btn.prop('disabled', true);
            $btn.find('.btn-icon').hide();
            $btn.find('.btn-spinner').css('display', 'inline-block');
            $btn.find('.btn-text').text('Adding...');
        }

        $.ajax({
            url: $('#add-to-cart-route').val() || '/business/carts',
            method: 'POST',
            data: cartData,
            success: function(response) {
                // Reset button state
                if ($btn && $btn.length) {
                    $btn.prop('disabled', false);
                    $btn.find('.btn-icon').show();
                    $btn.find('.btn-spinner').hide();
                    $btn.find('.btn-text').text('Add');
                }
                
                if (response.success) {
                    // Refresh cart display
                    if (typeof fetchUpdatedCart === 'function') {
                        fetchUpdatedCart(function() {
                            // Recalculate totals after cart is updated
                            if (typeof calTotalAmount === 'function') {
                                calTotalAmount();
                            }
                        });
                    }
                    
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Product added to cart!');
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || 'Failed to add product to cart');
                    }
                }
            },
            error: function(xhr, status, error) {
                // Reset button state
                if ($btn && $btn.length) {
                    $btn.prop('disabled', false);
                    $btn.find('.btn-icon').show();
                    $btn.find('.btn-spinner').hide();
                    $btn.find('.btn-text').text('Add');
                }
                
                let errorMessage = 'Error adding product to cart. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage);
                }
            }
        });
    }

    // Camera scanning functionality
    function startCameraScanning() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert('Camera access is not supported in this browser. Please use manual input.');
            return;
        }

        // Show loading state
        $('#start-camera-scan').html(`
            <div class="spinner-border spinner-border-sm me-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            Starting Camera...
        `).prop('disabled', true);

        // Show camera section
        $('#camera-section').show();
        $('#start-camera-scan').hide();
        isScanning = true;

        // Initialize barcode detection
        initBarcodeDetection();
    }

    function stopCameraScanning() {
        // Stop Html5Qrcode if it's being used and is running
        if (scanner && typeof scanner.stop === 'function' && scanner.isScanning) {
            scanner.stop().catch(err => {});
        }
        
        // Stop video stream
        const video = document.getElementById('barcode-scanner-video');
        if (video && video.srcObject) {
            const tracks = video.srcObject.getTracks();
            tracks.forEach(track => track.stop());
            video.srcObject = null;
        }
        
        $('#camera-section').hide();
        $('#start-camera-scan').show().html(`
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                <path d="M2 6C2 4.89543 2.89543 4 4 4H6L7 2H13L14 4H16C17.1046 4 18 4.89543 18 6V14C18 15.1046 17.1046 16 16 16H4C2.89543 16 2 15.1046 2 14V6Z" stroke="currentColor" stroke-width="2"/>
                <circle cx="10" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
            </svg>
            Start Camera
        `).prop('disabled', false);
        isScanning = false;
        
        if (typeof Quagga !== 'undefined') {
            Quagga.stop();
        }
        scanner = null;
    }

    // Initialize barcode detection (if supported)
    function initBarcodeDetection() {
        // Check if Html5Qrcode is available
        if (typeof Html5Qrcode !== 'undefined') {
            startHtml5QrcodeScanner();
        } else {
            alert('Barcode scanner library not loaded. Please refresh the page.\n\nYou can still type barcodes manually in the search box.');
            stopCameraScanning();
        }
    }

    // Use Html5Qrcode library (best compatibility)
    function startHtml5QrcodeScanner() {
        try {
            const html5QrCode = new Html5Qrcode("barcode-scanner-video");
            
            // Optimized config for faster scanning
            const config = { 
                fps: 30, // Increased from 20 for faster detection
                qrbox: { width: 300, height: 150 }, // Wider box for barcodes
                aspectRatio: 2.0, // Better for horizontal barcodes
                disableFlip: false,
                formatsToSupport: [
                    Html5QrcodeSupportedFormats.EAN_13,
                    Html5QrcodeSupportedFormats.EAN_8,
                    Html5QrcodeSupportedFormats.UPC_A,
                    Html5QrcodeSupportedFormats.UPC_E,
                    Html5QrcodeSupportedFormats.CODE_128,
                    Html5QrcodeSupportedFormats.CODE_39,
                    Html5QrcodeSupportedFormats.QR_CODE
                ]
            };
            
            html5QrCode.start(
                { facingMode: "environment" },
                config,
                (decodedText, decodedResult) => {
                    // Prevent duplicate scans within cooldown period
                    const now = Date.now();
                    if (now - lastScanTime < scanCooldown) {
                        return; // Ignore scan
                    }
                    lastScanTime = now;
                    
                    // Visual feedback - flash green
                    $('#barcode-scanner-video').css('border', '5px solid #10b981');
                    
                    // The scanner will auto-stop, just clean up UI
                    setTimeout(() => {
                        stopCameraScanning();
                        
                        // Show scanned code in search input
                        $('#product-search-input').val(decodedText);
                        
                        // Search and auto-add product
                        searchProducts(decodedText, true);
                    }, 100);
                },
                (errorMessage) => {
                    // Ignore scanning errors
                }
            ).catch(err => {
                alert('Camera error: ' + (err.message || err));
                stopCameraScanning();
            });
            
            scanner = html5QrCode;
        } catch (err) {
            alert('Scanner error: ' + (err.message || err));
            stopCameraScanning();
        }
    }

    // Fallback barcode detection using QuaggaJS
    function loadQuaggaJS(video) {
        // Check if QuaggaJS is already loaded
        if (typeof Quagga !== 'undefined') {
            initQuaggaScanner(video);
            return;
        }

        // Load QuaggaJS dynamically
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js';
        script.onload = function() {
            initQuaggaScanner(video);
        };
        script.onerror = function() {
            showManualInputMessage();
        };
        document.head.appendChild(script);
    }

    // Initialize Quagga scanner
    function initQuaggaScanner(video) {
        if (typeof Quagga === 'undefined') return;

        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        video.parentNode.appendChild(canvas);

        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: canvas,
                constraints: {
                    width: 640,
                    height: 480,
                    facingMode: "environment"
                }
            },
            decoder: {
                readers: [
                    "code_128_reader",
                    "ean_reader",
                    "ean_8_reader",
                    "code_39_reader",
                    "code_39_vin_reader",
                    "codabar_reader",
                    "upc_reader",
                    "upc_e_reader",
                    "i2of5_reader"
                ]
            }
        }, function(err) {
            if (err) {
                showManualInputMessage();
                return;
            }
            Quagga.start();
        });

        Quagga.onDetected(function(data) {
            if (isScanning && data.codeResult) {
                const barcode = data.codeResult.code;
                $('#product-search-input').val(barcode);
                searchProducts(barcode, true); // true = auto-add when scanned
                Quagga.stop();
                canvas.remove();
                stopCameraScanning();
            }
        });
    }

    // Show message when camera detection is not available
    function showManualInputMessage() {
        const message = `
            <div class="alert alert-info mt-3">
                <strong>Camera scanning not fully supported in this browser.</strong><br>
                Please type or paste your barcode/QR code manually in the search box above.
            </div>
        `;
        $('#camera-section').append(message);
    }

    // Utility functions
    function showLoading() {
        const tbody = $('#search-products-table');
        tbody.html(`
            <tr>
                <td colspan="9" style="padding: 40px; text-align: center;">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p style="margin-top: 16px; color: #6b7280;">Searching products...</p>
                </td>
            </tr>
        `);
    }

    function hideLoading() {
        // Loading is cleared when results are displayed
    }

    function showNoResults() {
        const tbody = $('#search-products-table');
        tbody.html(`
            <tr>
                <td colspan="9" style="padding: 40px; text-align: center;">
                    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin: 0 auto 16px; display: block;">
                        <circle cx="32" cy="32" r="30" stroke="#E5E7EB" stroke-width="4"/>
                        <path d="M22 22L42 42M22 42L42 22" stroke="#E5E7EB" stroke-width="4" stroke-linecap="round"/>
                    </svg>
                    <h6 style="color: #9ca3af; margin-bottom: 8px;">${'No products found'}</h6>
                    <p style="color: #9ca3af; font-size: 14px;">${'Try searching with a different code or product name'}</p>
                </td>
            </tr>
        `);
    }

    function clearResults() {
        $('#search-products-table').empty();
    }

    function formatCurrency(amount) {
        // Use existing currency formatting if available
        if (typeof currency_format === 'function') {
            return currency_format(amount);
        }
        
        // Fallback formatting
        return '$' + parseFloat(amount).toFixed(2);
    }

    // Global barcode listener for external scanners (works on all views)
    function initGlobalBarcodeListener() {
        $(document).on('keypress', function(e) {
            // Ignore if user is typing in an input field (except product-search-input)
            const $target = $(e.target);
            if ($target.is('input, textarea, select') && !$target.is('#product-search-input')) {
                return;
            }
            
            // Ignore if modal is open or user is in a form
            if ($('.modal.show').length > 0) {
                return;
            }
            
            const char = String.fromCharCode(e.which);
            
            // Clear timeout
            if (barcodeTimeout) {
                clearTimeout(barcodeTimeout);
            }
            
            // Add character to buffer
            if (e.which === 13) { // Enter key - barcode complete
                if (barcodeBuffer.length >= 3) { // Minimum 3 characters for a barcode
                    e.preventDefault();
                    processExternalScan(barcodeBuffer);
                    barcodeBuffer = '';
                }
            } else {
                // Add character to buffer
                barcodeBuffer += char;
                
                // Auto-reset buffer after 100ms of no input (scanner types fast)
                barcodeTimeout = setTimeout(() => {
                    barcodeBuffer = '';
                }, 100);
            }
        });
    }
    
    // Process barcode from external scanner
    function processExternalScan(barcode) {
        // Prevent duplicate scans within cooldown period
        const now = Date.now();
        if (now - lastScanTime < scanCooldown) {
            return; // Ignore scan
        }
        lastScanTime = now;
        
        // Visual feedback - show toast notification
        if (typeof toastr !== 'undefined') {
            toastr.info('Scanning: ' + barcode, '', {timeOut: 1000});
        }
        
        // Search and auto-add product (works from any view)
        searchProducts(barcode, true);
    }

    // Initialize when document is ready
    $(document).ready(function() {
        initBarcodeScanner();
        initGlobalBarcodeListener(); // Listen for external scanner on all views
        
        // Add additional view switching handler to override any conflicts
        setTimeout(function() {
            console.log('🔧 Setting up view switching override...');
            
            // Force proper view switching for scan button
            $(document).off('click', '#scan-barcode-btn').on('click', '#scan-barcode-btn', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                
                console.log('🔍 OVERRIDE: Scan button clicked');
                
                // Remove all view-active classes from products section
                $('.products-section').removeClass('view-active-category view-active-brand view-active-search view-active-tables');
                
                // Force hide all other views
                $('.view-section').each(function() {
                    if (this.id !== 'search-view') {
                        $(this).hide().css('display', 'none');
                    }
                });
                
                // Force show search view
                $('#search-view').show().css('display', 'block');
                
                // Add search view class
                $('.products-section').addClass('view-active-search');
                
                // Update button states
                $('.pos-view-btn, .pos-toggle-btn').removeClass('pos-toggle-btn-active active');
                $(this).addClass('pos-toggle-btn-active active');
                
                console.log('🔍 OVERRIDE: Views switched');
                
                return false;
            });
            
            // Force proper view switching for other buttons
            $(document).off('click', '.pos-view-btn:not(#scan-barcode-btn)').on('click', '.pos-view-btn:not(#scan-barcode-btn)', function(e) {
                const view = $(this).data('view');
                
                if (view && view !== 'search') {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    
                    console.log('🔄 OVERRIDE: Switching to view:', view);
                    
                    // Remove all view-active classes from products section
                    $('.products-section').removeClass('view-active-category view-active-brand view-active-search view-active-tables');
                    
                    // Hide ALL views first
                    $('.view-section').each(function() {
                        $(this).hide().css('display', 'none');
                    });
                    
                    // Show only the target view
                    const targetView = $('#' + view + '-view');
                    if (targetView.length) {
                        targetView.show().css('display', 'block');
                        console.log('🔄 OVERRIDE: Showing view:', view + '-view');
                        
                        // Add corresponding class to products section
                        $('.products-section').addClass('view-active-' + view);
                    }
                    
                    // Update button states
                    $('.pos-view-btn, .pos-toggle-btn').removeClass('pos-toggle-btn-active active');
                    $(this).addClass('pos-toggle-btn-active active');
                    
                    // Special handling for toggle buttons (brand/category)
                    if ($(this).hasClass('pos-toggle-btn')) {
                        $('.pos-toggle-btn').removeClass('pos-toggle-btn-active');
                        $(this).addClass('pos-toggle-btn-active');
                    }
                    
                    console.log('🔄 OVERRIDE: View switch complete for:', view);
                    return false;
                }
            });
            
        }, 500); // Delay to ensure all other handlers are loaded
    });

})(jQuery);