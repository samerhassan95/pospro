// POS Products Section JavaScript

(function($) {
    'use strict';

    // Category Scroll Functionality
    function initCategoryScroll() {
        const categoryList = $('.pos-category-list');
        const prevBtn = $('.pos-category-scroll-btn.prev');
        const nextBtn = $('.pos-category-scroll-btn.next');
        
        if (categoryList.length && prevBtn.length && nextBtn.length) {
            // Scroll on button click
            prevBtn.on('click', function(e) {
                e.preventDefault();
                categoryList.animate({
                    scrollLeft: categoryList.scrollLeft() - 200
                }, 300);
            });
            
            nextBtn.on('click', function(e) {
                e.preventDefault();
                categoryList.animate({
                    scrollLeft: categoryList.scrollLeft() + 200
                }, 300);
            });
            
            // Update button visibility and active state based on scroll position
            function updateScrollButtons() {
                const scrollLeft = categoryList.scrollLeft();
                const maxScroll = categoryList[0].scrollWidth - categoryList[0].clientWidth;
                
                // Update prev button
                if (scrollLeft <= 0) {
                    prevBtn.removeClass('active').css('opacity', '0.5').css('pointer-events', 'none');
                } else {
                    prevBtn.addClass('active').css('opacity', '1').css('pointer-events', 'auto');
                }
                
                // Update next button
                if (scrollLeft >= maxScroll - 5) {
                    nextBtn.removeClass('active').css('opacity', '0.5').css('pointer-events', 'none');
                } else {
                    nextBtn.addClass('active').css('opacity', '1').css('pointer-events', 'auto');
                }
            }
            
            categoryList.on('scroll', updateScrollButtons);
            updateScrollButtons();
        }
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
                    const noProductsHtml = `
                        <div class="no-products-message" style="grid-column: 1 / -1; text-align: center; padding: 40px 20px;">
                            <div style="background: #f9f9f9; border: 2px dashed #e8e8e8; border-radius: 16px; padding: 40px;">
                                <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin: 0 auto 20px; display: block; opacity: 0.3;">
                                    <path d="M32 8L8 20V44C8 52 16 56 32 56C48 56 56 52 56 44V20L32 8Z" stroke="#666" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M32 32V56" stroke="#666" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8 20L32 32L56 20" stroke="#666" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <h3 style="font-size: 18px; font-weight: 600; color: #1a1a1a; margin: 0 0 8px 0;">No Products in ${categoryName}</h3>
                                <p style="font-size: 14px; color: #666; margin: 0;">There are no products available in this category.</p>
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
        initCategoryScroll();
        initCategoryFilter();
        initProductOptions();
        initTabs();
    });
    
})(jQuery);
