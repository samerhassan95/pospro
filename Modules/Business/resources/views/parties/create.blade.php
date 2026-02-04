@extends('layouts.business.master')

@section('title')
    {{ request('type') !== 'Supplier' ? __('Create Customer') : __('Create Supplier') }}
@endsection

@php
    $file = base_path('lang/countrylist.json');
    $countries = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    $type = request('type') !== 'Supplier' ? 'Customer' : 'Supplier';
@endphp

@section('main_content')
<div class="erp-table-section">
    <div class="container-fluid">
        <div class="card border-0">
            <div class="card-bodys">
                <div class="table-header p-16">
                    <h4>{{ __('Add new ') . ucfirst(request('type')) }}</h4>
                    @usercan('parties.read')
                    <a href="{{ route('business.parties.index', ['type' => request('type')]) }}"
                       class="add-order-btn rounded-2 {{ Route::is('business.parties.create') ? 'active' : '' }}">
                        <i class="far fa-list" aria-hidden="true"></i>
                        {{ ucfirst(request('type')) . __(' List') }}
                    </a>
                    @endusercan
                </div>

                <div class="order-form-section p-16">
                    <form action="{{ route('business.parties.store') }}" method="POST" class="ajaxform_instant_reload" enctype="multipart/form-data">
                        @csrf
                        <div class="add-suplier-modal-wrapper d-block">
                            <div class="row">
                                <div class="row col-lg-9">

                                    {{-- Name --}}
                                    <div class="col-lg-6 mb-2">
                                        <label>{{ __($type . ' Name') }}</label>
                                        <input type="text" name="name" required class="form-control" placeholder="{{ __('Enter '.$type.' Name') }}">
                                    </div>

                                    {{-- Phone --}}
                                    <div class="col-lg-6 mb-2">
                                        <label>{{ __('Phone Number') }}</label>
                                        <input type="number" name="phone" class="form-control" placeholder="{{ __('Enter Phone Number') }}">
                                    </div>

                                    {{-- Party Type --}}
                                    @if (request('type') !== 'Supplier')
                                    <div class="col-lg-6 mb-2">
                                        <label>{{ __('Party Type') }}</label>
                                        <div class="gpt-up-down-arrow position-relative">
                                            <select name="type" class="form-control table-select w-100" required>
                                                <option value=""> {{ __('Select one') }}</option>
                                                <option value="Retailer">{{ __('Customer') }}</option>
                                                <option value="Dealer">{{ __('Dealer') }}</option>
                                                <option value="Wholesaler">{{ __('Wholesaler') }}</option>
                                            </select>
                                            <span></span>
                                        </div>
                                    </div>
                                    @else
                                    <input type="hidden" name="type" value="Supplier">
                                    @endif

                                    {{-- Balance --}}
                                    <div class="col-lg-6 mb-2">
                                        <div class="form-group">
                                            <label>{{ __('Balance') }}</label>
                                            <div class="input-select-wrapper">
                                                <input type="number" step="any" name="opening_balance" placeholder="Ex: 500">
                                                <select name="opening_balance_type">
                                                    <option value="due">Due</option>
                                                    <option value="advance">Advance</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Email --}}
                                    <div class="col-lg-6 mb-2">
                                        <label>{{ __('Email') }}</label>
                                        <input type="email" name="email" class="form-control" placeholder="{{ __('Enter Email') }}">
                                    </div>

                                    {{-- Credit Limit (for customers only) --}}
                                    @if (request('type') !== 'Supplier')
                                    <div class="col-lg-6 mb-2">
                                        <label>{{ __('Party Credit Limit') }}</label>
                                        <input type="number" name="credit_limit" step="any" class="form-control" placeholder="{{ __('Ex: 800') }}">
                                    </div>
                                    @endif

                                    {{-- ZATCA Type --}}
                                    <div class="col-lg-6 mb-2">
                                        <label>{{ __('Invoice Type') }}</label>
                                        <select name="zatca_type" id="zatca_type" class="form-control" onchange="toggleB2BFields(this.value)">
                                            <option value="b2c">{{ __('B2C - Simplified Invoice') }}</option>
                                            <option value="b2b">{{ __('B2B - Tax Invoice') }}</option>
                                        </select>
                                    </div>

                                    <script>
                                    function toggleB2BFields(type) {
                                        const b2bFields = document.querySelectorAll('.b2b-field');
                                        const vatField = document.getElementById('vat_number_field');
                                        const vatInput = document.getElementById('vat_number');
                                        
                                        if (type === 'b2b') {
                                            // Show fields
                                            b2bFields.forEach(function(field) {
                                                field.style.display = 'block';
                                                const input = field.querySelector('input, select');
                                                if (input) input.required = true;
                                            });
                                            if (vatField) {
                                                vatField.style.display = 'block';
                                                if (vatInput) vatInput.required = true;
                                            }
                                        } else {
                                            // Hide fields
                                            b2bFields.forEach(function(field) {
                                                field.style.display = 'none';
                                                const input = field.querySelector('input, select');
                                                if (input) input.required = false;
                                            });
                                            if (vatField) {
                                                vatField.style.display = 'none';
                                                if (vatInput) vatInput.required = false;
                                            }
                                        }
                                    }
                                    </script>

                                    {{-- VAT Number (required for B2B) --}}
                                    <div class="col-lg-6 mb-2" id="vat_number_field" style="display: none;">
                                        <label>{{ __('VAT Number') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="vat_number" id="vat_number" class="form-control" placeholder="300XXXXXXXXXXXX" maxlength="15">
                                        <small class="text-muted">{{ __('15 digits - Required for B2B invoices') }}</small>
                                    </div>

                                    {{-- Commercial Registration (B2B) --}}
                                    <div class="col-lg-6 mb-2 b2b-field" style="display: none;">
                                        <label>{{ __('Commercial Registration') }} / رقم السجل التجاري</label>
                                        <input type="text" name="commercial_registration" class="form-control" placeholder="{{ __('Enter CR Number') }}">
                                        <small class="text-muted">{{ __('Optional - Company registration number') }}</small>
                                    </div>

                                    {{-- Additional ID (B2B) --}}
                                    <div class="col-lg-6 mb-2 b2b-field" style="display: none;">
                                        <label>{{ __('Additional ID') }} / معرف إضافي</label>
                                        <input type="text" name="additional_id" class="form-control" placeholder="{{ __('Enter Additional ID') }}">
                                        <small class="text-muted">{{ __('Optional - Additional identification') }}</small>
                                    </div>

                                    {{-- Address --}}
                                    <div class="col-lg-6 mb-2">
                                        <label>{{ __('Address') }}</label>
                                        <input type="text" name="address" class="form-control" placeholder="{{ __('Enter Address') }}">
                                    </div>

                                    {{-- Building Number --}}
                                    <div class="col-lg-6 mb-2 b2b-field" style="display: none;">
                                        <label>{{ __('Building Number') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="building_number" class="form-control" placeholder="{{ __('Enter Building Number') }}">
                                    </div>

                                    {{-- Street Name --}}
                                    <div class="col-lg-6 mb-2 b2b-field" style="display: none;">
                                        <label>{{ __('Street Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="street_name" class="form-control" placeholder="{{ __('Enter Street Name') }}">
                                    </div>

                                    {{-- District --}}
                                    <div class="col-lg-6 mb-2 b2b-field" style="display: none;">
                                        <label>{{ __('District') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="district" class="form-control" placeholder="{{ __('Enter District') }}">
                                    </div>

                                    {{-- City --}}
                                    <div class="col-lg-6 mb-2 b2b-field" style="display: none;">
                                        <label>{{ __('City') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="city" class="form-control" placeholder="{{ __('Enter City') }}">
                                    </div>

                                    {{-- Postal Code --}}
                                    <div class="col-lg-6 mb-2 b2b-field" style="display: none;">
                                        <label>{{ __('Postal Code') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="postal_code" class="form-control" placeholder="{{ __('Enter Postal Code') }}" maxlength="10">
                                    </div>

                                    {{-- Country Code --}}
                                    <div class="col-lg-6 mb-2 b2b-field" style="display: none;">
                                        <label>{{ __('Country Code') }} <span class="text-danger">*</span></label>
                                        <select name="country_code" class="form-control">
                                            <option value="SA" selected>{{ __('Saudi Arabia (SA)') }}</option>
                                            <option value="AE">{{ __('United Arab Emirates (AE)') }}</option>
                                            <option value="BH">{{ __('Bahrain (BH)') }}</option>
                                            <option value="KW">{{ __('Kuwait (KW)') }}</option>
                                            <option value="OM">{{ __('Oman (OM)') }}</option>
                                            <option value="QA">{{ __('Qatar (QA)') }}</option>
                                        </select>
                                    </div>

                                    {{-- Billing & Shipping Address --}}
                                    <div class="accordion" id="customAccordion">

                                        {{-- Billing --}}
                                        <div class="accordion-item border-0">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed text-primary fw-medium bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                                    <span class="icon me-2">+</span> {{ __('Billing Address') }}
                                                </button>
                                            </h2>
                                            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#customAccordion">
                                                <div class="accordion-body fst-italic text-secondary p-0">
                                                    <div class="row">
                                                        <div class="col-lg-6 mb-2">
                                                            <label>{{ __('Address line 1') }}</label>
                                                            <input type="text" name="billing_address[address]" class="form-control" placeholder="{{ __('Enter address') }}">
                                                        </div>
                                                        <div class="col-lg-6 mb-2">
                                                            <label>{{ __('City') }}</label>
                                                            <input type="text" name="billing_address[city]" class="form-control" placeholder="{{ __('Enter city') }}">
                                                        </div>
                                                        <div class="col-lg-6 mb-2">
                                                            <label>{{ __('State') }}</label>
                                                            <input type="text" name="billing_address[state]" class="form-control" placeholder="{{ __('Enter state') }}">
                                                        </div>
                                                        <div class="col-lg-6 mb-2">
                                                            <label>{{ __('Zip Code') }}</label>
                                                            <input type="text" name="billing_address[zip_code]" class="form-control" placeholder="{{ __('Enter zip code') }}">
                                                        </div>
                                                        <div class="col-lg-6 mb-2">
                                                            <label>{{ __('Country') }}</label>
                                                            <select name="billing_address[country]" class="form-control">
                                                                <option value="">{{ __('Select a country') }}</option>
                                                                @foreach ($countries as $country)
                                                                    <option value="{{ $country['name'] }}">{{ __($country['name']) }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Shipping --}}
                                        <div class="accordion-item border-0">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button fw-medium text-dark bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                                    <span class="icon me-2">−</span> {{ __('Shipping Address') }}
                                                </button>
                                            </h2>
                                            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#customAccordion">
                                                <div class="accordion-body fst-italic text-secondary">
                                                    <div class="row">
                                                        <div class="col-lg-6 mb-2">
                                                            <label>{{ __('Address line 1') }}</label>
                                                            <input type="text" name="shipping_address[address]" class="form-control" placeholder="{{ __('Enter address') }}">
                                                        </div>
                                                        <div class="col-lg-6 mb-2">
                                                            <label>{{ __('City') }}</label>
                                                            <input type="text" name="shipping_address[city]" class="form-control" placeholder="{{ __('Enter city') }}">
                                                        </div>
                                                        <div class="col-lg-6 mb-2">
                                                            <label>{{ __('State') }}</label>
                                                            <input type="text" name="shipping_address[state]" class="form-control" placeholder="{{ __('Enter state') }}">
                                                        </div>
                                                        <div class="col-lg-6 mb-2">
                                                            <label>{{ __('Zip Code') }}</label>
                                                            <input type="text" name="shipping_address[zip_code]" class="form-control" placeholder="{{ __('Enter zip code') }}">
                                                        </div>
                                                        <div class="col-lg-6 mb-2">
                                                            <label>{{ __('Country') }}</label>
                                                            <select name="shipping_address[country]" class="form-control">
                                                                <option value="">{{ __('Select a country') }}</option>
                                                                @foreach ($countries as $country)
                                                                    <option value="{{ $country['name'] }}">{{ __($country['name']) }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                {{-- Image --}}
                                <div class="col-lg-3">
                                    <h6 class="img-title">Image <span>(PNG & JPG)</span></h6>
                                    <div id="uploadBox">
                                        <div id="previewArea">
                                            <div id="iconArea">
                                                <img src="{{ asset('assets/images/icons/img.png') }}" alt="icon" />
                                            </div>
                                            <p>{{ __('Drag & drop your Image') }}</p>
                                            <p>{{ __('or') }} <span class="browse-text">{{ __('Browse') }}</span></p>
                                        </div>
                                    </div>
                                    <input type="file" name="image" id="fileInput" accept="image/*">
                                </div> {{-- closes col-lg-3 --}}
                            </div> {{-- closes row --}}
                        </div> {{-- closes wrapper --}}

                            {{-- Buttons --}}
                            <div class="col-lg-12">
                                <div class="button-group text-end mt-5">
                                    <button type="reset" class="theme-btn border-btn m-2">{{ __('Reset') }}</button>
                                    @usercan('parties.create')
                                    <button class="theme-btn m-2 submit-btn">{{ __('Save') }}</button>
                                    @endusercan
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
    // Use vanilla JavaScript if jQuery not available
    document.addEventListener('DOMContentLoaded', function() {
        const zatcaTypeSelect = document.getElementById('zatca_type');
        const b2bFields = document.querySelectorAll('.b2b-field');
        const vatNumberField = document.getElementById('vat_number_field');
        const vatNumberInput = document.getElementById('vat_number');

        function toggleB2BFields() {
            const type = zatcaTypeSelect.value;
            
            if (type === 'b2b') {
                // Show B2B fields
                b2bFields.forEach(field => {
                    field.style.display = 'block';
                    const input = field.querySelector('input, select');
                    if (input) input.required = true;
                });
                
                if (vatNumberField) {
                    vatNumberField.style.display = 'block';
                    if (vatNumberInput) vatNumberInput.required = true;
                }
            } else {
                // Hide B2B fields
                b2bFields.forEach(field => {
                    field.style.display = 'none';
                    const input = field.querySelector('input, select');
                    if (input) input.required = false;
                });
                
                if (vatNumberField) {
                    vatNumberField.style.display = 'none';
                    if (vatNumberInput) vatNumberInput.required = false;
                }
            }
        }

        // Listen for changes
        if (zatcaTypeSelect) {
            zatcaTypeSelect.addEventListener('change', toggleB2BFields);
            // Trigger on page load
            toggleB2BFields();
        }
    });
</script>
@endpush
