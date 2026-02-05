@extends('layouts.business.blank')

@section('title')
    {{ __('Invoice') }}
@endsection

@section('main_content')
    @if (invoice_setting() == '3_inch_80mm' && moduleCheck('ThermalPrinterAddon'))
        @include('thermalprinteraddon::sales.3_inch_80mm')
    @elseif($sale->invoice_type === 'b2b')
        @include('business::sales.invoices.b2b-unified')
    @else
        @include('business::sales.invoices.a4-size')
    @endif
@endsection
