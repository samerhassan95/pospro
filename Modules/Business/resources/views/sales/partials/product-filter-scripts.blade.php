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
    // DISABLED - Search functionality is now handled by barcode-scanner.js
    // This prevents conflicts with the barcode scanner search table
    /*
    const searchInput = document.getElementById('product-search-input');
    const searchProductsTable = document.getElementById('search-products-table');
    let searchTimeout = null;
    
    function performSearch() {
        // Search is now handled by barcode-scanner.js
    }
    
    if (searchInput) {
        // Event listeners disabled - handled by barcode-scanner.js
    }
    */
    
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
