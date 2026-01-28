@forelse ($products as $product)
    @php
        $firstStock = $product->stocks->first();
        $purchasePrice = $firstStock->productPurchasePrice ?? 0;
        $salePrice = $firstStock->productSalePrice ?? 0;
    @endphp
    <div class="pos-product-card single-product {{ $product->id }}"
         data-product_id="{{ $product->id }}"
         data-category_id="{{ $product->category_id ?? '' }}"
         data-default_price="{{ $salePrice }}"
         data-product_code="{{ $product->productCode }}"
         data-product_unit_id="{{ $product->unit->id ?? null }}"
         data-product_unit_name="{{ $product->unit->unitName ?? null }}"
         data-product_image="{{ $product->productPicture }}"
         data-product_name="{{ $product->productName }}"
         data-purchase_price="{{ $purchasePrice }}"
         data-batch_count="{{ $product->stocks->count() }}"
         data-stocks='@json($product->stocks)'
         data-route="{{ route('business.carts.store') }}"
    >
        <div class="pos-product-top">
            <div class="pos-product-image-wrapper">
                <img class="pos-product-image" src="{{ asset($product->productPicture ?? 'assets/images/products/box.svg') }}" alt="{{ $product->productName }}">
            </div>
            <div class="pos-product-info">
                <div class="pos-product-header">
                    <h6 class="pos-product-name product_name">{{ $product->productName }}</h6>
                    <p class="pos-product-desc">{{ Str::limit($product->productDescription ?? $product->productDetails ?? 'Caramel syrup with coffee, milk, and whipped cream', 60) }}</p>
                </div>
                <span class="pos-product-price product_price">{{ currency_format($salePrice, currency: business_currency()) }}</span>
            </div>
        </div>
        
        <div class="pos-product-options">
            <div class="pos-option-group">
                <p class="pos-option-label">{{ __('Mood') }}</p>
                <div class="pos-option-buttons">
                    <button type="button" class="pos-option-btn mood-btn active" data-mood="hot" title="{{ __('Hot') }}">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 3V6M12 18V21M21 12H18M6 12H3M17.25 17.25L15 15M9 9L6.75 6.75M17.25 6.75L15 9M9 15L6.75 17.25" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                    <button type="button" class="pos-option-btn mood-btn" data-mood="cold" title="{{ __('Cold') }}">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 3V21M12 3L9 6M12 3L15 6M12 21L9 18M12 21L15 18M3 12H21M3 12L6 9M3 12L6 15M21 12L18 9M21 12L18 15M6.75 6.75L9.75 9.75M6.75 17.25L9.75 14.25M17.25 6.75L14.25 9.75M17.25 17.25L14.25 14.25" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="pos-option-group">
                <p class="pos-option-label">{{ __('Size') }}</p>
                <div class="pos-option-buttons">
                    <button type="button" class="pos-option-btn size-btn" data-size="S">S</button>
                    <button type="button" class="pos-option-btn size-btn active" data-size="M">M</button>
                    <button type="button" class="pos-option-btn size-btn" data-size="L">L</button>
                </div>
            </div>
        </div>
        
        <button type="button" class="pos-add-to-cart-btn add-product-btn">
            {{ __('Add to Bailing') }}
        </button>
    </div>
@empty
    <div class="alert alert-info w-100" role="alert">
        <i class="fas fa-info-circle me-2"></i>{{ __('No product found') }}
    </div>
@endforelse
