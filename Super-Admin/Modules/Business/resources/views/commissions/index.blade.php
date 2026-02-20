@extends('layouts.business.master')

@section('title')
    {{ __('Commission Management') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-header p-16">
                        <h4>{{ __('Commission Management') }}</h4>
                        <div>
                            <a href="{{ route('business.commissions.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('Set Commission') }}
                            </a>
                        </div>
                    </div>

                    <div class="responsive-table m-0" id="commission-table">
                        @include('business::commissions.datas')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
