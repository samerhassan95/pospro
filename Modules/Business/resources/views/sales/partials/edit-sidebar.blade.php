<!-- Order Sidebar for Edit Mode -->
<div class="order-sidebar">
    <!-- Request Number Display - First Element -->
    <div class="request-number-section">
        <div class="request-number-row">
            <span class="request-label">{{ __('Edit Sale #') }}</span>
            <span class="request-value" id="request-number-display">{{ $sale->invoiceNumber }}</span>
        </div>
    </div>

    <!-- Search Customer Section -->
    <div class="sidebar-search-customer">
        <div class="search-customer-wrapper">
            <select required name="party_id" id="party_id" class="sidebar-customer-select choices-select">
                <option value="">{{ __('Search Customer') }}</option>
                <option class="guest-option" value="guest" @selected($sale->party_id === null || $sale->party_id === 'guest')>{{ __('Guest') }}</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" data-type="{{ $customer->type }}" data-phone="{{ $customer->phone }}" data-zatca-type="{{ $customer->zatca_type ?? 'b2c' }}" @selected($sale->party_id == $customer->id)>
                        {{ $customer->name }}({{ $customer->type }}{{ $customer->due ? ' ' . currency_format($customer->due, currency: business_currency()) : '' }}) {{ $customer->phone }}
                    </option>
                @endforeach
            </select>
            <a href="#customer-create-modal" data-bs-toggle="modal" class="sidebar-add-customer-btn">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 4V16M4 10H16" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
        <div class="guest-phone-field {{ $sale->party_id === null || $sale->party_id === 'guest' ? '' : 'd-none' }} guest_phone">
            <input type="text" name="customer_phone" class="form-control" placeholder="{{ __('Enter Customer Phone Number') }}" value="{{ $sale->meta['customer_phone'] ?? '' }}">
        </div>
        
        <!-- B2B Additional Fields Button -->
        <div class="mt-3 {{ $sale->party && $sale->party->zatca_type === 'b2b' ? '' : 'd-none' }}" id="b2b-fields-wrapper">
            <button type="button" class="btn btn-outline-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#b2bAdditionalFieldsModal">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 5px;">
                    <path d="M9 11L12 14L22 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M21 12V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V5C3.89543 5 3 5.89543 3 7V19C3 19.5523 3.44772 20 4 20H19C19.5523 20 20 19.5523 20 19V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                {{ __('B2B Additional Fields') }}
            </button>
        </div>
    </div>

    <!-- Order Details Section -->
    <div class="order-details-section">
        <h3 class="section-title">{{ __('Order Details') }}</h3>
        <div class="customer-name" id="selected-customer-name">{{ $sale->party ? $sale->party->name : __('Guest') }}</div>
        <div class="order-info-line">
            <span class="order-date">{{ formatted_date($sale->saleDate, 'D, M\TH\LY Y') }}</span>
            <svg class="clock-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="10" cy="10" r="8" stroke="#6B7280" stroke-width="1.5"/>
                <path d="M10 5V10L13 13" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <span class="order-time" id="current-time">{{ formatted_date($sale->saleDate, 'h:i A') }}</span>
            <span class="phone-number" id="selected-customer-phone">{{ $sale->party ? $sale->party->phone : ($sale->meta['customer_phone'] ?? '') }}</span>
        </div>
        <input type="hidden" name="invoiceNumber" value="{{ $sale->invoiceNumber }}">
        <input type="hidden" name="saleDate" value="{{ formatted_date($sale->saleDate, 'Y-m-d') }}">
    </div>

    <!-- Delivery Type Tabs -->
    <div class="sidebar-delivery-tabs">
        <button type="button" class="delivery-tab-btn" data-delivery-type="delivery">{{ __('Delivery') }}</button>
        <button type="button" class="delivery-tab-btn" data-delivery-type="pre-order">{{ __('Pre-order') }}</button>
        <button type="button" class="delivery-tab-btn active" data-delivery-type="takeaway">{{ __('Takeaway') }}</button>
    </div>
    
    <!-- Hidden input to store selected delivery type -->
    <input type="hidden" name="delivery_type" id="delivery_type" value="takeaway">

    <!-- Products Section -->
    <div class="products-section">
        <div class="products-header">
            <h3 class="section-title">{{ __('Products') }}</h3>
            <button type="button" class="clear-all-btn cancel-sale-btn" data-route="{{ route('business.carts.remove-all') }}">
                {{ __('Clear All') }} 
                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5.52024 4.81313C5.32499 4.61786 5.0084 4.61784 4.81313 4.81309C4.61786 5.00835 4.61784 5.32493 4.81309 5.5202L6.4596 7.16688L4.81352 8.81313C4.61827 9.0084 4.61829 9.32499 4.81356 9.52024C5.00883 9.71549 5.32541 9.71547 5.52067 9.5202L7.16667 7.87402L8.81267 9.5202C9.00792 9.71547 9.3245 9.71549 9.51977 9.52024C9.71505 9.32499 9.71506 9.0084 9.51981 8.81313L7.87374 7.16688L9.52024 5.5202C9.71549 5.32493 9.71547 5.00835 9.5202 4.81309C9.32493 4.61784 9.00835 4.61786 8.81309 4.81313L7.16667 6.45974L5.52024 4.81313Z" fill="#E53030"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.16667 14.3333C3.20863 14.3333 0 11.1247 0 7.16667C0 3.20863 3.20863 0 7.16667 0C11.1247 0 14.3333 3.20863 14.3333 7.16667C14.3333 11.1247 11.1247 14.3333 7.16667 14.3333ZM1 7.16667C1 10.5724 3.76091 13.3333 7.16667 13.3333C10.5724 13.3333 13.3333 10.5724 13.3333 7.16667C13.3333 3.76091 10.5724 1 7.16667 1C3.76091 1 1 3.76091 1 7.16667Z" fill="#E53030"/>
                </svg>
            </button>
        </div>
        <div class="products-list" id="cart-list">
            @include('business::sales.cart-list-new')
        </div>
    </div>

    <!-- Order Summary -->
    <div class="order-summary-section">
        <div class="summary-row">
            <span class="summary-label">{{ __('Items') }}</span>
            <span class="summary-value" id="items_count">{{ $cart_contents->count() }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">{{ __('Subtotal') }}</span>
            <span class="summary-value" id="sub_total">{{ currency_format($sale->subTotal, currency: business_currency()) }}</span>
        </div>
        <div class="summary-row discount-row">
            <span class="summary-label">{{ __('Discount') }}</span>
            <div class="discount-controls">
                <span class="summary-value" id="discount_display">{{ $sale->discount_type == 'percent' ? $sale->discount_percent . '%' : currency_format($sale->discountAmount, currency: business_currency()) }}</span>
                <button type="button" class="add-discount-btn" id="add-discount-btn">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 3V13M3 8H13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="discount-input-section d-none" id="discount-input-section">
            <div class="discount-input-wrapper">
                <input type="number" step="0.01" min="0" id="discount_amount_input" class="form-control discount-input" placeholder="0.00" value="{{ $sale->discount_type == 'percent' ? $sale->discount_percent : $sale->discountAmount }}">
                <select id="discount_type_select" class="form-select discount-type-select">
                    <option value="flat" @selected($sale->discount_type == 'flat')>{{ __('Flat') }}</option>
                    <option value="percent" @selected($sale->discount_type == 'percent')>{{ __('Percent') }}</option>
                </select>
                <button type="button" class="apply-discount-btn" id="apply-discount-btn">{{ __('Apply') }}</button>
                <button type="button" class="cancel-discount-btn" id="cancel-discount-btn">{{ __('Cancel') }}</button>
            </div>
        </div>
        <div class="summary-row vat-row">
            <span class="summary-label">{{ __('VAT') }}</span>
            <div class="vat-controls">
                <span class="summary-value" id="vat_display">{{ currency_format($sale->vat_amount ?? 0, currency: business_currency()) }}</span>
                <button type="button" class="add-vat-btn" id="add-vat-btn">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 3V13M3 8H13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="vat-input-section d-none" id="vat-input-section">
            <div class="vat-input-wrapper">
                <select id="vat_select_input" class="form-select vat-select-input">
                    <option value="">{{ __('Select VAT') }}</option>
                    @foreach ($vats as $vat)
                        <option value="{{ $vat->id }}" data-rate="{{ $vat->rate }}" @selected($sale->vat_id == $vat->id)>{{ $vat->name }} ({{ $vat->rate }}%)</option>
                    @endforeach
                </select>
                <button type="button" class="apply-vat-btn" id="apply-vat-btn">{{ __('Apply') }}</button>
                <button type="button" class="cancel-vat-btn" id="cancel-vat-btn">{{ __('Cancel') }}</button>
            </div>
        </div>
        <div class="summary-row shipping-row">
            <span class="summary-label">{{ __('Shipping') }}</span>
            <div class="shipping-controls">
                <span class="summary-value" id="shipping_display">{{ currency_format($sale->shipping_charge ?? 0, currency: business_currency()) }}</span>
                <button type="button" class="add-shipping-btn" id="add-shipping-btn">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 3V13M3 8H13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="shipping-input-section d-none" id="shipping-input-section">
            <div class="shipping-input-wrapper">
                <input type="number" step="0.01" min="0" id="shipping_charge_input" class="form-control shipping-input" placeholder="0.00" value="{{ $sale->shipping_charge ?? 0 }}">
                <button type="button" class="apply-shipping-btn" id="apply-shipping-btn">{{ __('Apply') }}</button>
                <button type="button" class="cancel-shipping-btn" id="cancel-shipping-btn">{{ __('Cancel') }}</button>
            </div>
        </div>
        <div class="summary-row summary-total">
            <span class="summary-label ">{{ __('Total') }}</span>
            <span class="summary-value" id="total_amount">{{ currency_format($sale->totalAmount, currency: business_currency()) }}</span>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons-section">
        @usercan('sales.update')
            <button type="button" class="pay-bill-btn" id="open-payment-modal">{{ __('Update Sale') }}</button>
        @endusercan
        <button type="button" class="cancel-order-btn cancel-sale-btn" data-route="{{ route('business.carts.remove-all') }}">
            {{ __('Cancel Order') }}
        </button>
    </div>

    <!-- Hidden Form Fields -->
    <div style="display: none;">
        <input type="hidden" name="receive_amount" id="receive_amount" value="{{ $sale->change_amount + $sale->paidAmount }}">
        <input type="hidden" id="change_amount" value="{{ $sale->change_amount }}">
        <input type="hidden" id="due_amount" value="0">
        <select name="payment_type_id" id="payment_type_id">
            @foreach ($payment_types as $type)
                <option value="{{ $type->id }}" @selected($sale->payment_type_id == $type->id || ($sale->payment_type_id === null && $sale->paymentType == $type->name))>{{ $type->name }}</option>
            @endforeach
        </select>
        <input type="hidden" name="note" id="payment_note" value="{{ $sale->meta['note'] ?? '' }}">
        <select name="vat_id" class="vat_select">
            <option value="">{{ __('Select') }}</option>
            @foreach ($vats as $vat)
                <option value="{{ $vat->id }}" data-rate="{{ $vat->rate }}" @selected($sale->vat_id == $vat->id)>{{ $vat->name }} ({{ $vat->rate }}%)</option>
            @endforeach
        </select>
        <input type="hidden" name="vat_amount" id="vat_amount" value="{{ ($sale->vat_amount ?? 0) != 0 ? $sale->vat_amount : (($sale->vat_percent ?? 0) != 0 ? $sale->vat_percent : 0) }}">
        <select name="discount_type" class="discount_type">
            <option value="flat" @selected($sale->discount_type == 'flat')>{{ __('Flat') }}</option>
            <option value="percent" @selected($sale->discount_type == 'percent')>{{ __('Percent') }}</option>
        </select>
        <input type="hidden" name="discountAmount" id="discount_amount" value="{{ $sale->discount_type == 'percent' ? $sale->discount_percent : $sale->discountAmount }}">
        <input type="hidden" name="shipping_charge" id="shipping_charge" value="{{ $sale->shipping_charge }}">
        <input type="hidden" id="payable_amount" value="{{ $sale->totalAmount }}">
    </div>
</div>


<!-- Payment Modal for Edit Mode -->
<div class="payment-modal-overlay" id="payment-modal-overlay">
    <div class="payment-modal">
        <div class="payment-modal-header">
            <h3 class="payment-modal-title">{{ __('Update Payment') }}</h3>
            <div class="payment-modal-order-info">
                <div class="payment-order-number">{{ __('Sale') }} #<span id="modal-order-number">{{ $sale->invoiceNumber }}</span></div>
                <div class="payment-order-total" id="modal-order-total">{{ currency_format($sale->totalAmount, currency: business_currency()) }}</div>
            </div>
        </div>

        <div class="payment-modal-tabs">
            <button type="button" class="payment-tab-btn active" data-tab="full">{{ __('Full Payment') }}</button>
            <button type="button" class="payment-tab-btn" data-tab="split">{{ __('Split Bill') }}</button>
        </div>

        <div class="payment-methods">
            <button type="button" class="payment-method-btn active" data-method="cash">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M28 8H4C2.89543 8 2 8.89543 2 10V22C2 23.1046 2.89543 24 4 24H28C29.1046 24 30 23.1046 30 22V10C30 8.89543 29.1046 8 28 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 20C18.2091 20 20 18.2091 20 16C20 13.7909 18.2091 12 16 12C13.7909 12 12 13.7909 12 16C12 18.2091 13.7909 20 16 20Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>{{ __('Cash') }}</span>
            </button>
            <button type="button" class="payment-method-btn" data-method="card">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="6" width="28" height="20" rx="2" stroke="currentColor" stroke-width="2"/>
                    <path d="M2 12H30" stroke="currentColor" stroke-width="2"/>
                    <path d="M6 20H12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span>{{ __('Card') }}</span>
            </button>
            <button type="button" class="payment-method-btn" data-method="upi">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 28C22.6274 28 28 22.6274 28 16C28 9.37258 22.6274 4 16 4C9.37258 4 4 9.37258 4 16C4 22.6274 9.37258 28 16 28Z" stroke="currentColor" stroke-width="2"/>
                    <path d="M16 10V22M10 16H22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span>{{ __('UPI') }}</span>
            </button>
            <button type="button" class="payment-method-btn" data-method="due">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 28C22.6274 28 28 22.6274 28 16C28 9.37258 22.6274 4 16 4C9.37258 4 4 9.37258 4 16C4 22.6274 9.37258 28 16 28Z" stroke="currentColor" stroke-width="2"/>
                    <path d="M16 8V16L20 20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span>{{ __('DUE') }}</span>
            </button>
        </div>

        <div class="payment-amounts">
            <div class="payment-amount-field">
                <label>{{ __('Due Amount') }}</label>
                <input type="text" id="modal-due-amount" readonly value="{{ $sale->totalAmount - $sale->paidAmount }}">
            </div>
            <div class="payment-amount-field">
                <label>{{ __('Receive Amount') }}</label>
                <input type="text" id="modal-receive-amount" value="{{ $sale->change_amount + $sale->paidAmount }}">
            </div>
        </div>

        <div class="payment-summary">
            <div class="payment-summary-row">
                <span>{{ __('Total Bill') }}</span>
                <span id="modal-total-bill">{{ currency_format($sale->totalAmount, currency: business_currency()) }}</span>
            </div>
            <div class="payment-summary-row">
                <span>{{ __('Amount Paid') }}</span>
                <span id="modal-amount-paid">{{ currency_format($sale->paidAmount, currency: business_currency()) }}</span>
            </div>
            <div class="payment-summary-row">
                <span>{{ __('Due Amount') }}</span>
                <span id="modal-due-summary">{{ currency_format($sale->totalAmount - $sale->paidAmount, currency: business_currency()) }}</span>
            </div>
        </div>

        <div class="payment-numpad">
            <button type="button" class="numpad-btn" data-value="7">7</button>
            <button type="button" class="numpad-btn" data-value="8">8</button>
            <button type="button" class="numpad-btn" data-value="9">9</button>
            <button type="button" class="numpad-btn" data-value="4">4</button>
            <button type="button" class="numpad-btn" data-value="5">5</button>
            <button type="button" class="numpad-btn" data-value="6">6</button>
            <button type="button" class="numpad-btn" data-value="1">1</button>
            <button type="button" class="numpad-btn" data-value="2">2</button>
            <button type="button" class="numpad-btn" data-value="3">3</button>
            <button type="button" class="numpad-btn" data-value="0">0</button>
            <button type="button" class="numpad-btn" data-value=".">.</button>
            <button type="button" class="numpad-btn numpad-clear" data-value="clear">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>

        <div class="payment-modal-actions">
            <button type="button" class="payment-cancel-btn" id="cancel-payment-btn">{{ __('Cancel') }}</button>
            <button type="submit" form="sale-form" class="payment-complete-btn" id="complete-payment-btn">{{ __('Update Sale') }}</button>
        </div>
    </div>
</div>