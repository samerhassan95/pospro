@php
    $modules = product_setting()->modules ?? [];
@endphp
@if(isset($cart_contents) && count($cart_contents) > 0)
    @foreach($cart_contents as $cart)
        <div class="sidebar-cart-item cart-item-card" 
             data-row_id="{{ $cart->rowId }}" 
             data-update_route="{{ route('business.carts.update', $cart->rowId) }}" 
             data-destroy_route="{{ route('business.carts.destroy', $cart->rowId) }}"
             data-stock_id="{{ $cart->options->stock_id ?? '' }}"
             data-batch_no="{{ $cart->options->batch_no ?? '' }}">
            <div class="cart-item-content">
                <div class="cart-item-name">{{ $cart->name }}</div>
    
            </div>
            <div class="cart-item-content">
            @usercan('sales.price')
                <div class="cart-item-price">{!! currency_format($cart->price, currency: business_currency()) !!}</div>
                @endusercan    
            </div>
            <div class="cart-item-controls">
                <button type="button" class="qty-btn minus-btn">
                    <svg width="22" height="22" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="10" cy="10" r="10" fill="#E5E7EB"/>
                        <path d="M6 10H14" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
                <span class="cart-qty-display">{{ $cart->qty }}</span>
                <button type="button" class="qty-btn plus-btn">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="10" cy="10" r="10" fill="#000000"/>
                        <path d="M10 6V14M6 10H14" stroke="white" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
                <button type="button" class="remove-item-btn">
     <svg width="22" height="22" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M5.52024 4.81313C5.32499 4.61786 5.0084 4.61784 4.81313 4.81309C4.61786 5.00835 4.61784 5.32493 4.81309 5.5202L6.4596 7.16688L4.81352 8.81313C4.61827 9.0084 4.61829 9.32499 4.81356 9.52024C5.00883 9.71549 5.32541 9.71547 5.52067 9.5202L7.16667 7.87402L8.81267 9.5202C9.00792 9.71547 9.3245 9.71549 9.51977 9.52024C9.71505 9.32499 9.71506 9.0084 9.51981 8.81313L7.87374 7.16688L9.52024 5.5202C9.71549 5.32493 9.71547 5.00835 9.5202 4.81309C9.32493 4.61784 9.00835 4.61786 8.81309 4.81313L7.16667 6.45974L5.52024 4.81313Z" fill="#E53030"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M7.16667 14.3333C3.20863 14.3333 0 11.1247 0 7.16667C0 3.20863 3.20863 0 7.16667 0C11.1247 0 14.3333 3.20863 14.3333 7.16667C14.3333 11.1247 11.1247 14.3333 7.16667 14.3333ZM1 7.16667C1 10.5724 3.76091 13.3333 7.16667 13.3333C10.5724 13.3333 13.3333 10.5724 13.3333 7.16667C13.3333 3.76091 10.5724 1 7.16667 1C3.76091 1 1 3.76091 1 7.16667Z" fill="#E53030"/>
</svg>

                </button>
            </div>
            <!-- Hidden inputs for calculations and existing JavaScript compatibility -->
            <input type="hidden" value="{{ $cart->qty }}" class="cart-qty">
            <input type="hidden" class="cart-price" value="{{ $cart->price }}">
            <input type="hidden" class="cart-discount" value="0">
            <!-- Hidden inputs for batch and expire date if enabled -->
            @if (is_module_enabled($modules, 'show_product_batch_no'))
                <input type="hidden" class="batch_no" value="{{ $cart->options->batch_no ?? '' }}">
            @endif
            @if (is_module_enabled($modules, 'show_product_expire_date'))
                <input type="hidden" class="expire_date" value="{{ $cart->options->expire_date ?? '' }}">
            @endif
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
