@forelse ($products as $product)
    @php
        $firstStock = $product->stocks->first();
        $purchasePrice = $firstStock->productPurchasePrice ?? 0;
        $salePrice = $firstStock->productSalePrice ?? 0;
        $wholeSalePrice = $firstStock->productWholeSalePrice ?? 0;
        $dealerPrice = $firstStock->productDealerPrice ?? 0;
    @endphp
    <div id="single-product" class="product-card-new single-product {{ $product->id }}"
         data-product_id="{{ $product->id }}"
         data-product_code="{{ $product->productCode }}"
         data-product_unit_id="{{ $product->unit->id ?? null }}"
         data-product_unit_name="{{ $product->unit->unitName ?? null }}"
         data-product_image="{{ $product->productPicture }}"
         data-product_name="{{ $product->productName }}"
         data-brand="{{ $product->brand->brandName ?? '' }}"
         data-stock="{{ $product->stocks_sum_product_stock ?? 0 }}"
         data-purchase_price="{{ $purchasePrice }}"
         data-sales_price="{{ $salePrice }}"
         data-whole_sale_price="{{ $wholeSalePrice }}"
         data-dealer_price="{{ $dealerPrice }}"
    >
        <img class="product-card-image" src="{{ asset($product->productPicture ?? 'assets/images/products/box.svg') }}" alt="{{ $product->productName }}">
        <div class="product-card-body">
            <div class="product-card-row">
                <div class="product-card-info">
                    <h6 class="product-card-name product_name">{{ $product->productName }}</h6>
                    <p class="product-card-code">{{ $product->productCode }}</p>
                </div>
                @usercan('purchases.price')
                <span class="product-card-price product_price">{{ currency_format($purchasePrice, currency: business_currency()) }}</span>
                @endusercan
            </div>
            <button type="button" class="add-product-btn">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
@empty
    <div class="alert alert-info w-100" role="alert">
        <i class="fas fa-info-circle me-2"></i>{{ __('No product found') }}
    </div>
@endforelse