@extends('layouts.business.master')

@section('title', __('Moyasar Settings'))

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-header p-16">
                        <h4>{{ __('Moyasar Payment Integration / تكامل ميسر للدفع الإلكتروني') }}</h4>
                    </div>
                    
                    <div class="order-form-section p-16">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="alert alert-info">
                            {{ __('Configure your Moyasar API keys to accept payments from customers.') }}
                        </div>

                        <form action="{{ route('business.moyasar.update') }}" method="POST" class="ajaxform_instant_reload">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>{{ __('API Secret Key') }} (sk_...)</label>
                                    <input type="text" name="api_key" class="form-control" 
                                           value="{{ $moyasar_setting['api_key'] ?? '' }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('Publishable Key') }} (pk_...)</label>
                                    <input type="text" name="publishable_key" class="form-control" 
                                           value="{{ $moyasar_setting['publishable_key'] ?? '' }}" required>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Save Moyasar Settings') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
