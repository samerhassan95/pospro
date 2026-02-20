@extends('layouts.business.pos')

@section('title')
    {{ __('Pos Sale') }}
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/choices.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/calculator.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pos-products.css') . '?v=' . time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/barcode-scanner.css') . '?v=' . time() }}">
    @include('business::sales.partials.styles')
@endpush

@section('main_content')
    <form id="sale-form" action="{{ route('business.sales.store') }}" method="post" enctype="multipart/form-data" class="ajaxform pos-fullscreen-form">
        @csrf

        {{-- Main Content Area --}}
        <div class="pos-main-container">
            {{-- Left Column: Header + Products/Tables --}}
            <div class="pos-left-column">
                {{-- Top Header Navigation --}}
                @include('business::sales.partials.header')
                
                {{-- Products & Tables Section --}}
                @include('business::sales.partials.products')
            </div>

            {{-- Right Column: Order Sidebar --}}
            @include('business::sales.partials.sidebar')
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
    
    {{-- JavaScript functionality --}}
    @include('business::sales.partials.scripts-placeholder')
    @include('business::sales.partials.product-filter-scripts')
@endpush
