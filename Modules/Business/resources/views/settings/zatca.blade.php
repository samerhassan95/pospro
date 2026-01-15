@extends('layouts.business.master')

@section('title', __('ZATCA Settings'))

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-header p-16">
                        <h4>{{ __('ZATCA Integration Settings / إعدادات الفوترة الإلكترونية') }}</h4>
                    </div>
                    
                    <div class="order-form-section p-16">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        {{-- Status Display --}}
                        <div class="alert alert-{{ $business->zatca_setting && !empty($business->zatca_setting['csid']) ? 'success' : 'warning' }}">
                            <strong>{{ __('Integration Status:') }}</strong>
                            @if ($business->zatca_setting && !empty($business->zatca_setting['csid']))
                                {{ __('Connected') }} ({{ $business->zatca_setting['environment'] ?? 'Sandbox' }})
                            @else
                                {{ __('Not Connected') }}
                            @endif
                        </div>

                        <form action="{{ route('business.zatca.update') }}" method="POST" class="ajaxform_instant_reload">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <h5>{{ __('Step 1: Organization Details / بيانات المنشأة') }}</h5>
                                    <hr>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('Environment Mode') }}</label>
                                    <select name="environment" class="form-control">
                                        <option value="sandbox" @selected(($business->zatca_setting['environment'] ?? 'sandbox') == 'sandbox')>Sandbox (Test)</option>
                                        <option value="simulation" @selected(($business->zatca_setting['environment'] ?? '') == 'simulation')>Simulation (Pre-Prod)</option>
                                        <option value="production" @selected(($business->zatca_setting['environment'] ?? '') == 'production')>Production (Live)</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('Organization Name') }}</label>
                                    <input type="text" name="csr_common_name" class="form-control" 
                                           value="{{ $business->companyName }}" readonly 
                                           placeholder="Matches Tax Name">
                                    <small class="text-muted">Must match exact VAT Registration Name</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('VAT Registration Number') }}</label>
                                    <input type="text" name="vat_number" class="form-control" 
                                           value="{{ $business->vat_no }}" readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('Organization Unit Name') }} (Branch Name)</label>
                                    <input type="text" name="csr_organization_unit_name" class="form-control" 
                                           value="{{ $business->zatca_setting['csr_config']['organization_unit_name'] ?? 'Riyadh Branch' }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('Organization Identifier') }} (Group VAT if exists)</label>
                                    <input type="text" name="csr_organization_identifier" class="form-control" 
                                           value="{{ $business->zatca_setting['csr_config']['organization_identifier'] ?? $business->vat_no }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('Address: City') }}</label>
                                    <input type="text" name="csr_location" class="form-control" 
                                           value="{{ $business->zatca_setting['csr_config']['location'] ?? 'Riyadh' }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('Address: Street') }}</label>
                                    <input type="text" name="csr_street" class="form-control" 
                                           value="{{ $business->zatca_setting['csr_config']['registered_address'] ?? 'King Fahad Road' }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('Industry (Category)') }}</label>
                                    <input type="text" name="csr_industry" class="form-control" 
                                           value="{{ $business->zatca_setting['csr_config']['business_category'] ?? 'Retail' }}" required>
                                </div>

                                <div class="col-md-12 mb-3 mt-4">
                                    <h5>{{ __('Step 2: Authenticate (OTP) / رمز التحقق') }}</h5>
                                    <small>{{ __('Get this from Fatoora Portal (https://fatoora.zatca.gov.sa/)') }}</small>
                                    <hr>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('OTP Code') }}</label>
                                    <input type="text" name="otp" class="form-control" placeholder="123456" 
                                           {{ !empty($business->zatca_setting['csid']) ? '' : 'required' }}>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    {{ !empty($business->zatca_setting['csid']) ? __('Update & Re-Connect') : __('Connect to ZATCA') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
