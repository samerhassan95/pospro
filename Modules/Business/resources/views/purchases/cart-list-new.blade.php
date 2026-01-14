@php
    $modules = product_setting()->modules ?? [];
@endphp
@if(isset($cart_contents) && count($cart_contents) > 0)
    @foreach($cart_contents as $cart)
        <div class="cart-item-card" data-row_id="{{ $cart->rowId }}" data-update_route="{{ route('business.carts.update', $cart->rowId) }}" data-destroy_route="{{ route('business.carts.destroy', $cart->rowId) }}">
            <div class="cart-item-image-wrapper">
                <img class="cart-item-image" src="{{ asset($cart->options->product_image ?? 'assets/images/products/box.svg') }}" alt="{{ $cart->name }}">
            </div>
            <div class="cart-item-details">
                <h6 class="cart-item-name">{{ $cart->name }}</h6>
                @usercan('purchases.price')
                <p class="cart-item-price">{{ currency_format($cart->price, currency: business_currency()) }}</p>
                @endusercan
            </div>
            <div class="cart-item-actions">
                <button type="button" class="remove-item-btn remove-btn">
                    <i class="fas fa-times"></i>
                </button>
                <div class="qty-control-wrapper">
                    <button type="button" class="qty-btn minus-btn">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" step="any" value="{{ $cart->qty }}" class="cart-item-qty cart-qty" readonly>
                    <button type="button" class="qty-btn plus-btn">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <!-- Hidden inputs for batch and expire date if enabled -->
            @if (is_module_enabled($modules, 'show_product_batch_no'))
                <input type="hidden" class="batch_no" value="{{ $cart->options->batch_no ?? '' }}">
            @endif
            @if (is_module_enabled($modules, 'show_product_expire_date'))
                <input type="hidden" class="expire_date" value="{{ $cart->options->expire_date ?? '' }}">
            @endif
            <input type="hidden" class="cart-price price" value="{{ $cart->price }}">
        </div>
    @endforeach
@else
    <div class="empty-cart">
        <div class="empty-cart-icon">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <p>{{ __('No items in cart') }}</p>
    </div>
@endif