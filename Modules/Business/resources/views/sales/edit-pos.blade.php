@extends('layouts.business.pos')

@section('title')
    {{ __('Edit Sale') }}
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/choices.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/calculator.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pos-products.css') . '?v=' . time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/barcode-scanner.css') . '?v=' . time() }}">
    @include('business::sales.partials.styles')
@endpush

@section('main_content')
    <form id="sale-form" action="{{ route('business.sales.update', $sale->id) }}" method="post" enctype="multipart/form-data" class="ajaxform pos-fullscreen-form">
        @csrf
        @method('put')

        {{-- Main Content Area --}}
        <div class="pos-main-container">
            {{-- Left Column: Header + Products/Tables --}}
            <div class="pos-left-column">
                {{-- Top Header Navigation --}}
                @include('business::sales.partials.edit-header')
                
                {{-- Products & Tables Section --}}
                @include('business::sales.partials.products')
            </div>

            {{-- Right Column: Order Sidebar --}}
            @include('business::sales.partials.edit-sidebar')
        </div>

        {{-- Hidden Configuration Inputs --}}
        @include('business::sales.partials.hidden-inputs')
    </form>
@endsection

@push('modal')
    {{-- All Modals (Payment, Calculator, Customer, etc.) --}}
    @include('business::sales.partials.modals')
@endpush

@push('js')
    <script src="{{ asset('assets/js/choices.min.js') }}"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="{{ asset('assets/js/custom/sale.js') . '?v=' . time() }}"></script>
    <script src="{{ asset('assets/js/custom/math.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom/calculator.js') }}"></script>
    <script src="{{ asset('assets/js/custom/pos-products.js') . '?v=' . time() }}"></script>
    <script src="{{ asset('assets/js/custom/pos-payment-modal.js') . '?v=' . time() }}"></script>
    <script src="{{ asset('assets/js/custom/pos-sidebar.js') . '?v=' . time() }}"></script>
    <script src="{{ asset('assets/js/custom/barcode-scanner.js') . '?v=' . time() }}"></script>
    <script src="{{ asset('assets/js/custom/table-backend.js') . '?v=' . time() }}"></script>
    
    <script>
        // Scan view height management
        document.addEventListener('DOMContentLoaded', function() {
            // Function to toggle full height for scan view
            function toggleScanViewHeight() {
                const productsSection = document.querySelector('.products-section');
                const scanView = document.getElementById('search-view');
                
                if (productsSection && scanView) {
                    if (scanView.style.display !== 'none' && !scanView.style.display.includes('none')) {
                        productsSection.classList.add('scan-active');
                    } else {
                        productsSection.classList.remove('scan-active');
                    }
                }
            }
            
            // Watch for view changes
            const viewBtns = document.querySelectorAll('.pos-view-btn, .pos-nav-btn');
            viewBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    setTimeout(toggleScanViewHeight, 100); // Small delay to ensure view has switched
                });
            });
            
            // Also watch for scan button specifically
            const scanBtn = document.getElementById('scan-barcode-btn');
            if (scanBtn) {
                scanBtn.addEventListener('click', function() {
                    setTimeout(toggleScanViewHeight, 100);
                });
            }
            
            // Initial check
            toggleScanViewHeight();
        });
    </script>
    
    {{-- JavaScript functionality --}}
    @include('business::sales.partials.scripts-placeholder') {{-- Original file with all functionality --}}
    @include('business::sales.partials.product-filter-scripts')
    
    <script>
        // Show/Hide B2B Additional Fields button based on customer type
        document.addEventListener('DOMContentLoaded', function() {
            const partySelect = document.getElementById('party_id');
            const b2bFieldsWrapper = document.getElementById('b2b-fields-wrapper');
            const guestPhoneField = document.querySelector('.guest-phone-field');
            
            if (partySelect && b2bFieldsWrapper) {
                partySelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const zatcaType = selectedOption.getAttribute('data-zatca-type');
                    const isGuest = this.value === 'guest' || this.value === '';
                    
                    if (zatcaType === 'b2b') {
                        b2bFieldsWrapper.classList.remove('d-none');
                    } else {
                        b2bFieldsWrapper.classList.add('d-none');
                    }
                    
                    if (isGuest) {
                        guestPhoneField.classList.remove('d-none');
                    } else {
                        guestPhoneField.classList.add('d-none');
                    }
                });
            }
            
            // Pre-fill B2B fields if editing existing sale
            @if($sale->supply_date || $sale->po_number || $sale->contract_number)
                document.getElementById('supply_date').value = '{{ $sale->supply_date }}';
                document.getElementById('po_number').value = '{{ $sale->po_number }}';
                document.getElementById('contract_number').value = '{{ $sale->contract_number }}';
                document.getElementById('payment_terms').value = '{{ $sale->payment_terms }}';
                document.getElementById('payment_means').value = '{{ $sale->payment_means }}';
                document.getElementById('shipping_address_line1').value = '{{ $sale->shipping_address_line1 }}';
                document.getElementById('shipping_address_line2').value = '{{ $sale->shipping_address_line2 }}';
                document.getElementById('shipping_city').value = '{{ $sale->shipping_city }}';
                document.getElementById('shipping_postal_code').value = '{{ $sale->shipping_postal_code }}';
                document.getElementById('shipping_country_code').value = '{{ $sale->shipping_country_code }}';
            @endif
        });
    </script>
@endpush