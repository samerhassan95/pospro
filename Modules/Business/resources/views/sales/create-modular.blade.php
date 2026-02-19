@extends('layouts.business.pos')

@section('title')
    {{ __('Pos Sale') }}
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/choices.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/calculator.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pos-products.css') . '?v=' . time() }}">
    @include('business::sales.partials.styles')
@endpush

@section('main_content')
    <form id="sale-form" action="{{ route('business.sales.store') }}" method="post" enctype="multipart/form-data" class="ajaxform pos-fullscreen-form">
        @csrf

        {{-- Top Header Navigation --}}
        @include('business::sales.partials.header')

        {{-- Main Content Area --}}
        <div class="pos-main-container">
            {{-- Products & Tables Section (Left Side) --}}
            @include('business::sales.partials.products')

            {{-- Order Sidebar (Right Side) --}}
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
    {{-- JavaScript functionality --}}
    {{-- Note: Full scripts are still in original file. Use scripts-placeholder.blade.php as template --}}
    @include('business::sales.partials.scripts-placeholder')
@endpush
