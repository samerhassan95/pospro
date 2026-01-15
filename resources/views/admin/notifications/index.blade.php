@extends('layouts.master')

@section('title')
    {{ __('Notifications List') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-bodys ">
                    <div class="table-header p-16">
                        <h4>{{ __('Notifications List') }}</h4>
                    </div>
                    <div class="table-top-form p-16-0">
                    </div>
                </div>

                <div id="notifications-data">
                    @include('admin.notifications.datas')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modal')
    @include('admin.components.multi-delete-modal')
@endpush
