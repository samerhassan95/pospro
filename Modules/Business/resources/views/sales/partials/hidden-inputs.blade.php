<!-- Hidden Configuration Inputs -->
@php 
    $currency = business_currency(); 
    $rounding_amount_option = sale_rounding(); 
@endphp

<input type="hidden" id="currency_symbol" value="{{ $currency->symbol }}">
<input type="hidden" id="currency_position" value="{{ $currency->position }}">
<input type="hidden" id="currency_code" value="{{ $currency->code }}">
<input type="hidden" id="get_product" value="{{ route('business.products.prices') }}">
<input type="hidden" value="{{ route('business.sales.cart') }}" id="get-cart">
<input type="hidden" value="{{ route('business.sales.cart-data') }}" id="get-cart-data">
<input type="hidden" value="{{ route('business.carts.remove-all') }}" id="clear-cart">
<input type="hidden" id="rounding_amount_option" value="{{ $rounding_amount_option }}">
<input type="hidden" id="get-by-category" value="{{ route('business.products.get-by-category') }}">
<input type="hidden" id="cart-store-url" value="{{ route('business.carts.store') }}">
<input type="hidden" id="selectedProductValue" name="selectedProductValue">
<input type="hidden" id="asset_base_url" value="{{ asset('') }}">
<input type="hidden" id="get_stock_prices" value="{{ route('business.products.stocks-prices') }}">
<input type="hidden" id="warehouse_module_exist" value="{{ moduleCheck('WarehouseAddon') ? 1 : 0 }}">
<input type="hidden" id="payable_amount" value="0">
<input type="hidden" id="barcode-search-route" value="{{ route('business.products.search') }}">
<input type="hidden" id="add-to-cart-route" value="{{ route('business.carts.store') }}">
