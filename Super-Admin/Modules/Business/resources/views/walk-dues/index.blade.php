@extends('layouts.business.master')

@section('title')
    {{ __('Walk-in Customer Due') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-header p-16">
                        <h4>{{ __('Walk-in Customer Due') }}</h4>
                    </div>

                    <div class="responsive-table m-0" id="walk-due-table">
                        @include('business::walk-dues.datas')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
