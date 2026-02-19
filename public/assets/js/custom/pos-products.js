// POS Products Section JavaScript

(function($) {
    'use strict';

    // Category Scroll Functionality
    function initCategoryScroll() {
        console.log('=== initCategoryScroll called ===');
        
        // Support both sales and purchases pages
        // Try to find the category list element
        let categoryList = $('.pos-category-list');
        if (categoryList.length === 0) {
            console.log('No .pos-category-list found, trying #purchase-category-list');
            categoryList = $('#purchase-category-list');
        }
        if (categoryList.length === 0) {
            console.log('No #purchase-category-list found, trying #category-list');
            categoryList = $('#category-list');
        }
        
        const prevBtn = $('.pos-category-scroll-btn.prev');
        const nextBtn = $('.pos-category-scroll-btn.next');
        
        console.log('Category list found:', categoryList.length, categoryList);
        console.log('Prev button found:', prevBtn.length, prevBtn);
        console.log('Next button found:', nextBtn.length, nextBtn);
        
        if (categoryList.length === 0) {
            console.error('❌ Category list not found! Cannot initialize scroll.');
            return false;
        }
        
        if (prevBtn.length === 0 || nextBtn.length === 0) {
            console.error('❌ Scroll buttons not found! Cannot initialize scroll.');
            return false;
        }
        
        console.log('✅ All elements found, initializing category scroll...');
        
        // Log element details for debugging
        console.log('Category list element:', categoryList[0]);
        console.log('Category list scrollWidth:', categoryList[0].scrollWidth);
        console.log('Category list clientWidth:', categoryList[0].clientWidth);
        console.log('Is scrollable?', categoryList[0].scrollWidth > categoryList[0].clientWidth);
        
        // Scroll on button click
        prevBtn.off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('⬅️ Prev button clicked');
            const element = categoryList[0];
            const currentScroll = element.scrollLeft;
            const targetScroll = Math.max(0, currentScroll - 200);
            console.log('Current scroll:', currentScroll, 'Target:', targetScroll);
            
            // Use native smooth scroll
            element.scrollTo({
                left: targetScroll,
                behavior: 'smooth'
            });
        });
        
        nextBtn.off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('➡️ Next button clicked');
            const element = categoryList[0];
            const currentScroll = element.scrollLeft;
            const maxScroll = element.scrollWidth - element.clientWidth;
            const targetScroll = Math.min(maxScroll, currentScroll + 200);
            console.log('Current scroll:', currentScroll, 'Target:', targetScroll, 'Max:', maxScroll);
            
            // Use native smooth scroll
            element.scrollTo({
                left: targetScroll,
                behavior: 'smooth'
            });
        });
        
        // Update button visibility and active state based on scroll position
        function updateScrollButtons() {
            const scrollLeft = categoryList.scrollLeft();
            const scrollWidth = categoryList[0].scrollWidth;
            const clientWidth = categoryList[0].clientWidth;
            const maxScroll = scrollWidth - clientWidth;
            
            console.log('📊 Scroll info:', {scrollLeft, scrollWidth, clientWidth, maxScroll});
            
            // Update prev button
            if (scrollLeft <= 0) {
                prevBtn.addClass('disabled').removeClass('active');
                console.log('⬅️ Prev button disabled');
            } else {
                prevBtn.removeClass('disabled').addClass('active');
                console.log('⬅️ Prev button enabled');
            }
            
            // Update next button
            if (scrollLeft >= maxScroll - 5) {
                nextBtn.addClass('disabled').removeClass('active');
                console.log('➡️ Next button disabled');
            } else {
                nextBtn.removeClass('disabled').addClass('active');
                console.log('➡️ Next button enabled');
            }
        }
        
        // Remove old scroll listener and add new one
        categoryList.off('scroll').on('scroll', updateScrollButtons);
        
        // Initial update with delay to ensure DOM is ready
        setTimeout(updateScrollButtons, 100);
        setTimeout(updateScrollButtons, 500);
        
        console.log('✅ Category scroll initialized successfully');
        return true;
    }
    
    // Category Filter
    function initCategoryFilter() {
        $(document).on('click', '.pos-category-item', function() {
            const categoryId = $(this).data('category');
            const categoryName = $(this).find('.pos-category-name').text();
            
            // Update active state
            $('.pos-category-item').removeClass('active');
            $(this).addClass('active');
            
            // Remove any existing no products message
            $('.no-products-message').remove();
            
            // Filter products
            if (categoryId === 'all') {
                // Show all products
                $('.pos-product-card').show();
            } else {
                // Hide all products first
                $('.pos-product-card').hide();
                
                // Show only products matching the selected category
                let hasProducts = false;
                $('.pos-product-card').each(function() {
                    const productCategoryId = $(this).data('category_id');
                    if (productCategoryId == categoryId) {
                        $(this).show();
                        hasProducts = true;
                    }
                });
                
                // If no products found, show message
                if (!hasProducts) {
                    const noProductsTitle = window.translations?.no_products_in || 'No Products in';
                    const noProductsMessage = window.translations?.no_products_available_category || 'There are no products available in this category.';
                    
                    const noProductsHtml = `
                        <div class="no-products-message" style="grid-column: 1 / -1; text-align: center; padding: 40px 20px;">
                            <div style="background: #f9f9f9; border: 2px dashed #e8e8e8; border-radius: 16px; padding: 40px;">
                                <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin: 0 auto 20px; display: block; opacity: 0.3;">
                                    <path d="M32 8L8 20V44C8 52 16 56 32 56C48 56 56 52 56 44V20L32 8Z" stroke="#666" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M32 32V56" stroke="#666" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8 20L32 32L56 20" stroke="#666" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <h3 style="font-size: 18px; font-weight: 600; color: #1a1a1a; margin: 0 0 8px 0;">${noProductsTitle} ${categoryName}</h3>
                                <p style="font-size: 14px; color: #666; margin: 0;">${noProductsMessage}</p>
                            </div>
                        </div>
                    `;
                    $('.pos-products-grid').append(noProductsHtml);
                }
            }
        });
    }
    
    // Product Option Buttons (Mood & Size)
    function initProductOptions() {
        // Mood buttons
        $(document).on('click', '.mood-btn', function(e) {
            e.stopPropagation();
            const card = $(this).closest('.pos-product-card');
            card.find('.mood-btn').removeClass('active');
            $(this).addClass('active');
        });
        
        // Size buttons
        $(document).on('click', '.size-btn', function(e) {
            e.stopPropagation();
            const card = $(this).closest('.pos-product-card');
            card.find('.size-btn').removeClass('active');
            $(this).addClass('active');
        });
    }
    
    // Tab Switching
    function initTabs() {
        $('.pos-tab-btn').on('click', function() {
            $('.pos-tab-btn').removeClass('active');
            $(this).addClass('active');
            
            // You can add tab content switching logic here
            const tabText = $(this).text().trim();
            if (tabText === 'Tables') {
                // Show tables view
                console.log('Switch to Tables view');
            } else {
                // Show products view
                console.log('Switch to Products view');
            }
        });
    }
    
    // Initialize all functions
    $(document).ready(function() {
        console.log('DOM Ready - Initializing POS Products...');
        initCategoryScroll();
        initCategoryFilter();
        initProductOptions();
        initTabs();
        
        // Re-initialize after a delay to handle dynamic content
        setTimeout(function() {
            console.log('Re-initializing category scroll after delay...');
            initCategoryScroll();
        }, 1000);
    });
    
    // Also initialize on window load as a fallback
    $(window).on('load', function() {
        console.log('Window Load - Re-initializing category scroll...');
        setTimeout(function() {
            initCategoryScroll();
        }, 500);
    });
    
})(jQuery);
