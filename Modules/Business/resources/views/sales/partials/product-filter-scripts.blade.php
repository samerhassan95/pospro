<script>
// Product filtering functionality for Brand, Category, and Search views
document.addEventListener('DOMContentLoaded', function() {
    
    // ========== BRAND FUNCTIONALITY ==========
    const brandItems = document.querySelectorAll('.pos-brand-item');
    const brandProductsList = document.getElementById('brand-products-list');
    
    // Brand scroll buttons
    const brandList = document.getElementById('brand-list');
    const brandPrevBtn = document.querySelector('.pos-brand-scroll-btn.prev');
    const brandNextBtn = document.querySelector('.pos-brand-scroll-btn.next');
    
    if (brandPrevBtn && brandNextBtn && brandList) {
        brandPrevBtn.addEventListener('click', () => {
            brandList.scrollBy({ left: -200, behavior: 'smooth' });
        });
        
        brandNextBtn.addEventListener('click', () => {
            brandList.scrollBy({ left: 200, behavior: 'smooth' });
        });
    }
    
    // Clone all products to brand view
    if (brandProductsList) {
        const allProducts = document.querySelectorAll('#products-list .pos-product-card');
        console.log('Cloning products to brand view. Total products:', allProducts.length);
        allProducts.forEach(product => {
            const brandId = product.getAttribute('data-brand_id');
            console.log('Product brand_id:', brandId, 'Product name:', product.getAttribute('data-product_name'));
            const clone = product.cloneNode(true);
            // Re-attach event listeners to cloned elements
            attachProductEventListeners(clone);
            brandProductsList.appendChild(clone);
        });
    }
    
    // Brand click handler
    brandItems.forEach(item => {
        item.addEventListener('click', function() {
            brandItems.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const brandId = this.getAttribute('data-brand');
            console.log('Brand clicked:', brandId);
            filterProductsByBrand(brandId);
        });
    });
    
    function filterProductsByBrand(brandId) {
        if (!brandProductsList) return;
        
        const products = brandProductsList.querySelectorAll('.pos-product-card');
        let visibleCount = 0;
        
        products.forEach(product => {
            const productBrandId = product.getAttribute('data-brand_id');
            
            // Convert both to strings for comparison
            const brandIdStr = String(brandId);
            const productBrandIdStr = String(productBrandId);
            
            // Show if: "all" selected, OR brand matches, OR product has no brand and "all" is selected
            if (brandId === 'all' || brandIdStr === productBrandIdStr) {
                product.style.display = '';
                visibleCount++;
            } else {
                product.style.display = 'none';
            }
        });
        
        console.log('Brand filter:', brandId, 'Visible products:', visibleCount);
        showNoProductsMessage(brandProductsList, visibleCount, '{{ __("No products found for this brand") }}');
    }
    
    // ========== CATEGORY FUNCTIONALITY ==========
    const categoryItems = document.querySelectorAll('.pos-category-item');
    const categoryProductsList = document.getElementById('products-list');
    
    categoryItems.forEach(item => {
        item.addEventListener('click', function() {
            categoryItems.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            
            const categoryId = this.getAttribute('data-category');
            filterProductsByCategory(categoryId);
        });
    });
    
    function filterProductsByCategory(categoryId) {
        if (!categoryProductsList) return;
        
        const products = categoryProductsList.querySelectorAll('.pos-product-card');
        let visibleCount = 0;
        
        products.forEach(product => {
            const productCategoryId = product.getAttribute('data-category_id');
            
            if (categoryId === 'all' || productCategoryId === categoryId) {
                product.style.display = '';
                visibleCount++;
            } else {
                product.style.display = 'none';
            }
        });
        
        showNoProductsMessage(categoryProductsList, visibleCount, '{{ __("No products found for this category") }}');
    }
    
    // ========== SEARCH FUNCTIONALITY ==========
    const searchInput = document.getElementById('product-search-input');
    const searchProductsTable = document.getElementById('search-products-table');
    const searchProductsData = document.getElementById('search-products-data');
    
    function performSearch() {
        if (!searchInput || !searchProductsTable || !searchProductsData) return;
        
        const query = searchInput.value.trim().toLowerCase();
        
        // Clear table
        searchProductsTable.innerHTML = '';
        
        if (!query) {
            return; // Keep table empty if no search query
        }
        
        const productDataElements = searchProductsData.querySelectorAll('.product-data');
        let foundCount = 0;
        
        productDataElements.forEach(productData => {
            const productName = productData.getAttribute('data-product-name');
            const productCode = productData.getAttribute('data-product-code');
            
            if (productName.includes(query) || productCode.includes(query)) {
                foundCount++;
                
                // Create table row
                const row = document.createElement('tr');
                row.className = 'search-product-row';
                row.style.cssText = 'border-bottom: 1px solid #f3f4f6; transition: background 0.2s;';
                
                const price = parseFloat(productData.getAttribute('data-product-price'));
                
                row.innerHTML = `
                    <td style="padding: 12px 16px;">
                        <input type="checkbox" class="product-checkbox" style="width: 18px; height: 18px; cursor: pointer;">
                    </td>
                    <td style="padding: 12px 16px;">
                        <img src="${productData.getAttribute('data-product-image')}" alt="${productData.getAttribute('data-product-display-name')}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                    </td>
                    <td style="padding: 12px 16px; font-size: 14px; color: #1f2937; font-weight: 500;">${productData.getAttribute('data-product-display-name')}</td>
                    <td style="padding: 12px 16px; font-size: 14px; color: #6b7280;">${productData.getAttribute('data-product-display-code')}</td>
                    <td style="padding: 12px 16px; font-size: 14px; color: #6b7280;">-</td>
                    <td style="padding: 12px 16px; font-size: 14px; color: #6b7280;">${productData.getAttribute('data-product-unit')}</td>
                    <td style="padding: 12px 16px; font-size: 14px; color: #1f2937; font-weight: 600;">${price.toFixed(2)}</td>
                    <td style="padding: 12px 16px;">
                        <input type="number" value="1" min="1" class="qty-input" style="width: 70px; padding: 6px 8px; border: 1px solid #e5e7eb; border-radius: 6px; text-align: center; font-size: 14px;">
                    </td>
                    <td style="padding: 12px 16px; font-size: 14px; color: #1f2937; font-weight: 600;" class="subtotal">${price.toFixed(2)}</td>
                `;
                
                searchProductsTable.appendChild(row);
                
                // Add qty input listener for subtotal calculation
                const qtyInput = row.querySelector('.qty-input');
                const subtotalCell = row.querySelector('.subtotal');
                qtyInput.addEventListener('input', function() {
                    const qty = parseInt(this.value) || 1;
                    const subtotal = price * qty;
                    subtotalCell.textContent = subtotal.toFixed(2);
                });
            }
        });
        
        // Show no results message if nothing found
        if (foundCount === 0) {
            const noResultsRow = document.createElement('tr');
            noResultsRow.innerHTML = '<td colspan="9" style="padding: 40px; text-align: center; color: #9ca3af; font-size: 14px;">{{ __("No products found matching your search") }}</td>';
            searchProductsTable.appendChild(noResultsRow);
        }
    }
    
    if (searchInput) {
        searchInput.addEventListener('input', performSearch);
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }
    
    // ========== HELPER FUNCTIONS ==========
    function showNoProductsMessage(container, visibleCount, message) {
        const existingMsg = container.querySelector('.no-products-message');
        if (existingMsg) existingMsg.remove();
        
        if (visibleCount === 0) {
            const noProductsMsg = document.createElement('div');
            noProductsMsg.className = 'no-products-message';
            noProductsMsg.style.cssText = 'text-align: center; padding: 40px; color: #999; grid-column: 1 / -1;';
            noProductsMsg.textContent = message;
            container.appendChild(noProductsMsg);
        }
    }
    
    function attachProductEventListeners(productElement) {
        // Re-attach add to cart button listener
        const addBtn = productElement.querySelector('.add-product-btn');
        if (addBtn && typeof window.addProductToCart === 'function') {
            addBtn.addEventListener('click', function() {
                window.addProductToCart(productElement);
            });
        }
        
        // Re-attach mood and size button listeners
        const moodBtns = productElement.querySelectorAll('.mood-btn');
        moodBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                moodBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
        
        const sizeBtns = productElement.querySelectorAll('.size-btn');
        sizeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                sizeBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }
});
</script>
