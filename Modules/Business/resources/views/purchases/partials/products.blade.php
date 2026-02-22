<!-- Products Section -->
<div class="products-section">
    </style>
    <!-- Brand View (hidden by default) -->
    <div id="brand-view" class="view-section" style="display: none;">
        <div class="pos-category-section">
            <div class="pos-category-scroll-wrapper">
                <div class="pos-brand-list" id="brand-list">
                    <!-- All Brands Option -->
                    <button type="button" class="pos-brand-item active" data-brand="all">
                        <div class="pos-brand-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19 9C19 10.45 18.57 11.78 17.83 12.89C16.75 14.49 15.04 15.62 13.05 15.91C12.71 15.97 12.36 16 12 16C11.64 16 11.29 15.97 10.95 15.91C8.96 15.62 7.25 14.49 6.17 12.89C5.43 11.78 5 10.45 5 9C5 5.13 8.13 2 12 2C15.87 2 19 5.13 19 9Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M21.2491 18.4699L19.5991 18.8599C19.2291 18.9499 18.9391 19.2299 18.8591 19.5999L18.5091 21.0699C18.3191 21.8699 17.2991 22.1099 16.7691 21.4799L11.9991 15.9999L7.2291 21.4899C6.6991 22.1199 5.6791 21.8799 5.4891 21.0799L5.1391 19.6099C5.0491 19.2399 4.7591 18.9499 4.3991 18.8699L2.7491 18.4799C1.9891 18.2999 1.7191 17.3499 2.2691 16.7999L6.1691 12.8999C7.2491 14.4999 8.9591 15.6299 10.9491 15.9199C11.2891 15.9799 11.6391 16.0099 11.9991 16.0099C12.3591 16.0099 12.7091 15.9799 13.0491 15.9199C15.0391 15.6299 16.7491 14.4999 17.8291 12.8999L21.7291 16.7999C22.2791 17.3399 22.0091 18.2899 21.2491 18.4699Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12.58 5.98L13.17 7.15999C13.25 7.31999 13.46 7.48 13.65 7.51L14.72 7.68999C15.4 7.79999 15.56 8.3 15.07 8.79L14.24 9.61998C14.1 9.75998 14.02 10.03 14.07 10.23L14.31 11.26C14.5 12.07 14.07 12.39 13.35 11.96L12.35 11.37C12.17 11.26 11.87 11.26 11.69 11.37L10.69 11.96C9.96997 12.38 9.53997 12.07 9.72997 11.26L9.96997 10.23C10.01 10.04 9.93997 9.75998 9.79997 9.61998L8.96997 8.79C8.47997 8.3 8.63997 7.80999 9.31997 7.68999L10.39 7.51C10.57 7.48 10.78 7.31999 10.86 7.15999L11.45 5.98C11.74 5.34 12.26 5.34 12.58 5.98Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span class="pos-brand-name">{{ __('All') }}</span>
                    </button>
                    @foreach($brands ?? [] as $brand)
                    <button type="button" class="pos-brand-item" data-brand="{{ $brand->id }}">
                        <div class="pos-brand-icon">
                            @if($brand->icon)
                            <img src="{{ asset($brand->icon) }}" alt="{{ $brand->brandName }}">
                            @else
                            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M24 44C35.0457 44 44 35.0457 44 24C44 12.9543 35.0457 4 24 4C12.9543 4 4 12.9543 4 24C4 35.0457 12.9543 44 24 44Z" stroke="currentColor" stroke-width="2.5"/>
                            </svg>
                            @endif
                        </div>
                        <span class="pos-brand-name">{{ $brand->brandName }}</span>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="pos-products-section">
            <h3 class="pos-menu-title">{{ __('Special Menu') }}</h3>
            <div class="pos-products-grid" id="brand-products-list"></div>
        </div>
    </div>

    <!-- Category View (default view) -->
    <div id="category-view" class="view-section">
        <div class="pos-category-section">
            <div class="pos-category-scroll-wrapper">
                <div class="pos-category-list" id="category-list">
                    <button type="button" class="pos-category-item active" data-category="all">
                        <div class="pos-category-icon">
                            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="6" y="6" width="14" height="14" rx="2" stroke="currentColor" stroke-width="2.5"/>
                                <rect x="28" y="6" width="14" height="14" rx="2" stroke="currentColor" stroke-width="2.5"/>
                                <rect x="6" y="28" width="14" height="14" rx="2" stroke="currentColor" stroke-width="2.5"/>
                                <rect x="28" y="28" width="14" height="14" rx="2" stroke="currentColor" stroke-width="2.5"/>
                            </svg>
                        </div>
                        <span class="pos-category-name">{{ __('All') }}</span>
                    </button>
                    @foreach($categories as $category)
                    <button type="button" class="pos-category-item" data-category="{{ $category->id }}">
                        <div class="pos-category-icon">
                            @if($category->icon)
                            <img src="{{ asset($category->icon) }}" alt="{{ $category->categoryName }}">
                            @else
                            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 18L24 8L40 18V38C40 39.0609 39.5786 40.0783 38.8284 40.8284C38.0783 41.5786 37.0609 42 36 42H12C10.9391 42 9.92172 41.5786 9.17157 40.8284C8.42143 40.0783 8 39.0609 8 38V18Z" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18 42V24H30V42" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            @endif
                        </div>
                        <span class="pos-category-name">{{ $category->categoryName }}</span>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="pos-products-section">
            <h3 class="pos-menu-title">{{ __('Special Menu') }}</h3>
            <div class="pos-products-grid" id="products-list">
                @include('business::purchases.product-list-new')
            </div>
        </div>
    </div>

    <!-- Search View (hidden by default) -->
    <div id="search-view" class="view-section scan-view-full-height" style="display: none; margin-top:10px">
        <div class="pos-search-section" style="margin-bottom: 16px;">
            <div class="pos-search-wrapper" style="display: flex; gap: 12px; align-items: center; width: 100%;">
                <div style="position: relative; flex: 1;">
                    <svg style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: #9ca3af;" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <input type="text" id="product-search-input" class="pos-search-input" placeholder="{{ __('Scan or type product code, name, barcode, or QR code...') }}" style="width: 100%; padding: 12px 16px 12px 44px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; background: #fff; outline: none;">
                </div>
                <button type="button" class="btn btn-primary" id="start-camera-scan" style="padding: 12px 20px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 6C2 4.89543 2.89543 4 4 4H6L7 2H13L14 4H16C17.1046 4 18 4.89543 18 6V14C18 15.1046 17.1046 16 16 16H4C2.89543 16 2 15.1046 2 14V6Z" stroke="currentColor" stroke-width="2"/>
                        <circle cx="10" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    {{ __('Start Camera') }}
                </button>
            </div>
            
            <!-- Camera Section (Hidden by default) -->
            <div id="camera-section" class="mt-3" style="display: none;">
                <div class="camera-container text-center">
                    <div id="barcode-scanner-video" style="width: 100%; max-width: 500px; margin: 0 auto;"></div>
                    <div class="camera-controls mt-2">
                        <button type="button" class="btn btn-danger" id="stop-camera-scan">
                            {{ __('Stop Camera') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="pos-products-section scan-table-container">
            <div class="responsive-table scan-table-wrapper" style="background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <table class="table scan-table" style="width: 100%; margin: 0;">
                    <thead style="background: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                        <tr>
                            <th style="padding: 16px; text-align: left; font-size: 14px; font-weight: 600; color: #374151;">{{ __('Image') }}</th>
                            <th style="padding: 16px; text-align: left; font-size: 14px; font-weight: 600; color: #374151;">{{ __('Items') }}</th>
                            <th style="padding: 16px; text-align: left; font-size: 14px; font-weight: 600; color: #374151;">{{ __('Code') }}</th>
                            <th style="padding: 16px; text-align: left; font-size: 14px; font-weight: 600; color: #374151;">{{ __('Batch') }}</th>
                            <th style="padding: 16px; text-align: left; font-size: 14px; font-weight: 600; color: #374151;">{{ __('Stock') }}</th>
                            <th style="padding: 16px; text-align: left; font-size: 14px; font-weight: 600; color: #374151;">{{ __('Purchase Price') }}</th>
                            <th style="padding: 16px; text-align: left; font-size: 14px; font-weight: 600; color: #374151;">{{ __('Qty') }}</th>
                            <th style="padding: 16px; text-align: left; font-size: 14px; font-weight: 600; color: #374151;">{{ __('Sub Total') }}</th>
                            <th style="padding: 16px; text-align: left; font-size: 14px; font-weight: 600; color: #374151;">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody id="search-products-table">
                        <!-- Products will appear here when scanned/searched -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tables View (hidden by default) -->
    <div id="tables-view" class="view-section" style="display: none;">
        <div class="pos-tables-section">
            <h3 class="pos-menu-title">{{ __('Tables') }}</h3>
            <div class="pos-tables-grid">
                <p class="text-center text-muted">{{ __('Tables feature coming soon') }}</p>
            </div>
        </div>
    </div>
</div>