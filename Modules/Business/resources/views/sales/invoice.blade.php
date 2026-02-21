@extends('layouts.business.blank')

@section('title')
    {{ __('Invoice') }}
@endsection

@section('main_content')
    @if (invoice_setting() == '3_inch_80mm' && moduleCheck('ThermalPrinterAddon'))
        @if($sale->invoice_type === 'b2b')
            {{-- B2B Thermal Printer (detailed) --}}
            @include('thermalprinteraddon::sales.3_inch_80mm')
        @else
            {{-- B2C Thermal Printer (simple) --}}
            @include('thermalprinteraddon::sales.3_inch_80mm_simple')
        @endif
    @elseif($sale->invoice_type === 'b2b')
        {{-- B2B Full Invoice Template --}}
        @include('business::sales.invoices.b2b-unified')
    @else
        {{-- B2C Invoice Template (A4 Size) --}}
        @include('business::sales.invoices.a4-size')
    @endif
@endsection
