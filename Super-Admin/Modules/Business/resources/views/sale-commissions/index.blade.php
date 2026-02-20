@extends('layouts.business.master')

@section('title')
    {{ __('Sale Commission') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-header p-16">
                        <h4>{{ __('Sale Commission') }}</h4>
                    </div>

                    <div class="responsive-table m-0" id="sale-commission-table">
                        @include('business::sale-commissions.datas')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
