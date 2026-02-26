@extends('layouts.master')

@section('title')
    {{ __('Edit Business') }}
@endsection

@section('main_content')
<div class="erp-table-section">
    <div class="container-fluid">
        <div class="card border-0">
            <div class="card-bodys">
                <div class="table-header p-16">
                    <h4>{{ __('Edit Business') }}</h4>
                    @can('plans-read')
                        <a href="{{ route('admin.business.index') }}" class="add-order-btn rounded-2 {{ Route::is('admin.users.create') ? 'active' : '' }}"><i class="far fa-list" aria-hidden="true"></i> {{ __('Business List') }}</a>
                    @endcan
                </div>
                <div class="order-form-section p-16">
                    <form action="{{ route('admin.business.update', $business->id) }}" method="POST" class="ajaxform_instant_reload">
                        @csrf
                        @method('PUT')
                        <div class="add-suplier-modal-wrapper d-block">
                            <div class="row">

                                <div class="col-lg-6 mb-2">
                                    <label>{{ __('Business Name') }}</label>
                                    <input type="text" name="companyName" value="{{ $business->companyName }}" required class="form-control" placeholder="{{ __('Enter Company Name') }}">
                                </div>

                                <div class="col-lg-6 mb-2">
                                    <label>{{__('Business Category')}}</label>
                                    <div class="gpt-up-down-arrow position-relative">
                                        <select name="business_category_id" required
                                                class="form-control table-select w-100 role">
                                            <option value=""> {{__('Select Business Category')}}</option>
                                            @foreach ($categories as $category)
                                                <option @selected($category->id == $business->business_category_id) value="{{ $category->id }}"> {{ ucfirst($category->name) }} </option>
                                            @endforeach
                                        </select>
                                        <span></span>
                                    </div>
                                </div>

                                <div class="col-lg-6 mb-2">
                                    <label>{{ __('Subscription Plan') }}</label>
                                    <div class="gpt-up-down-arrow position-relative">
                                        <select name="plan_subscribe_id" id="plan_select_edit" class="form-control table-select w-100 role">
                                            <option value="">{{ __('Select One') }}</option>
                                            @foreach ($plans as $plan)
                                                <option value="{{ $plan->id }}" 
                                                    @selected($plan->id == optional($business->enrolled_plan)->plan_id)
                                                    data-price="{{ $plan->subscriptionPrice }}"
                                                    data-duration="{{ $plan->duration }}"
                                                    data-warehouses="{{ $plan->warehouse_limit ?? 'Unlimited' }}"
                                                    data-branches="{{ $plan->branch_limit ?? 'Unlimited' }}"
                                                    data-finance="{{ $plan->allow_finance ? 'Yes' : 'No' }}"
                                                    data-hrm="{{ $plan->allow_hrm ? 'Yes' : 'No' }}"
                                                    data-commission="{{ $plan->allow_commission ? 'Yes' : 'No' }}">
                                                    {{ ucfirst($plan->subscriptionName) }} - {!! currency($plan->subscriptionPrice) !!}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span></span>
                                    </div>
                                    <small class="text-muted" id="plan_info_edit"></small>
                                </div>

                                <div class="col-lg-6 mb-2">
                                    <label>{{ __('Phone') }}</label>
                                    <input type="text" name="phoneNumber" value="{{ $business->phoneNumber }}" required class="form-control" placeholder="{{ __('Enter Phone Number') }}">
                                </div>

                                <div class="col-lg-6 mb-2">
                                    <label>{{ __('Email') }}</label>
                                    <input type="email" name="email" value="{{ $user->email }}" class="form-control" placeholder="{{ __('Enter Email') }}">
                                </div>

                                <div class="col-lg-6 mb-2">
                                    <label>{{ __('Shop Opening Balance') }}</label>
                                    <input type="number" name="shopOpeningBalance" value="{{ $business->shopOpeningBalance }}" required class="form-control" placeholder="{{ __('Enter Balance') }}">
                                </div>

                                <div class="col-lg-6 mb-2">
                                    <label>{{ __('Address') }}</label>
                                    <input type="text" name="address" value="{{ $business->address }}" required class="form-control" placeholder="{{ __('Enter Address') }}">
                                </div>

                                <div class="col-lg-6 mb-2">
                                    <label>{{__('Password')}}</label>
                                    <div class="position-relative">
                                        <input type="password" name="password" class="form-control" placeholder="{{ __('Enter New Password') }}">
                                        <span class="hide-pass hide-show-icon">
                                            <img class="showIcon d-none" src="{{ asset('assets/images/icons/show.svg') }}" alt="Show">
                                            <img class="hideIcon" src="{{ asset('assets/images/icons/Hide.svg') }}" alt="Hide">
                                        </span>
                                    </div>
                                </div>

                                <div class="col-lg-6 mb-2">
                                    <label>{{__('Confirm password')}}</label>
                                    <div class="position-relative">
                                        <input type="password" name="password_confirmation" class="form-control" placeholder="{{ __('Enter Confirm password') }}">
                                        <span class="hide-pass hide-show-icon">
                                            <img class="hideIcon" src="{{ asset('assets/images/icons/Hide.svg') }}" alt="Hide">
                                            <img class="showIcon  d-none" src="{{ asset('assets/images/icons/show.svg') }}" alt="Show">
                                        </span>
                                    </div>
                                </div>

                                <div class="col-lg-12 mb-3 mt-4">
                                    <h6 class="text-danger border-bottom pb-2">{{ __('ZATCA / Tax Information') }} (بيانات ضريبية)</h6>
                                </div>

                                <div class="col-lg-6 mb-2">
                                    <label>{{ __('VAT Number') }} (الرقم الضريبي) <span class="text-danger">*</span></label>
                                    <input type="text" name="vat_no" value="{{ $business->vat_no }}" class="form-control" placeholder="300000000000003" maxlength="15">
                                    <small class="text-muted">15 digits required for B2B invoices</small>
                                </div>

                                <div class="col-lg-6 mb-2">
                                    <label>{{ __('Commercial Registration') }} (السجل التجاري) <span class="text-danger">*</span></label>
                                    <input type="text" name="commercial_registration" value="{{ $business->commercial_registration }}" class="form-control" placeholder="1010000001" maxlength="10">
                                    <small class="text-muted">10 digits - Required for B2B</small>
                                </div>

                                <div class="col-lg-6 mb-2">
                                    <label>{{ __('Additional ID') }} (معرف إضافي للمورد)</label>
                                    <input type="text" name="additional_id" value="{{ $business->additional_id }}" class="form-control" placeholder="OTH-12345">
                                    <small class="text-muted">Optional - Other Seller ID</small>
                                </div>

                                <div class="col-lg-6 mb-2">
                                    <label>{{ __('Country Code') }} (رمز الدولة)</label>
                                    <input type="text" name="country_code" value="{{ $business->country_code ?? 'SA' }}" class="form-control" placeholder="SA" maxlength="2">
                                    <small class="text-muted">2 letters (e.g., SA, AE, KW)</small>
                                </div>

                                <div class="col-lg-12 mb-3 mt-3">
                                    <h6 class="text-info border-bottom pb-2">{{ __('Address Details') }} (تفاصيل العنوان)</h6>
                                </div>

                                <div class="col-lg-6 mb-2">
                                    <label>{{ __('Building Number') }} (رقم المبنى) <span class="text-danger">*</span></label>
                                    <input type="text" name="building_number" value="{{ $business->building_number }}" class="form-control" placeholder="1234">
                                </div>

                                <div class="col-lg-6 mb-2">
                                    <label>{{ __('Street Name') }} (اسم الشارع) <span class="text-danger">*</span></label>
                                    <input type="text" name="street_name" value="{{ $business->street_name }}" class="form-control" placeholder="King Fahad Road">
                                </div>

                                <div class="col-lg-6 mb-2">
                                    <label>{{ __('District') }} (الحي) <span class="text-danger">*</span></label>
                                    <input type="text" name="district" value="{{ $business->district }}" class="form-control" placeholder="Al Malaz">
                                </div>

                                <div class="col-lg-6 mb-2">
                                    <label>{{ __('City') }} (المدينة) <span class="text-danger">*</span></label>
                                    <input type="text" name="city" value="{{ $business->city }}" class="form-control" placeholder="Riyadh">
                                </div>

                                <div class="col-lg-6 mb-2">
                                    <label>{{ __('Postal Code') }} (الرمز البريدي) <span class="text-danger">*</span></label>
                                    <input type="text" name="postal_code" value="{{ $business->postal_code }}" class="form-control" placeholder="12345" maxlength="5">
                                </div>

                                <div class="col-lg-6 mb-2">
                                    <label>{{ __('Additional Address') }} (عنوان إضافي)</label>
                                    <input type="text" name="additional_address" value="{{ $business->additional_address }}" class="form-control" placeholder="Near Landmark">
                                    <small class="text-muted">Optional</small>
                                </div>

                                <div class="col-lg-12 mb-3 mt-3">
                                    <h6 class="text-success border-bottom pb-2">{{ __('Bank Information') }} (معلومات البنك)</h6>
                                </div>

                                <div class="col-lg-6 mb-2">
                                    <label>{{ __('Bank Name') }} (اسم البنك)</label>
                                    <input type="text" name="bank_name" value="{{ $business->bank_name }}" class="form-control" placeholder="Al Rajhi Bank">
                                </div>

                                <div class="col-lg-6 mb-2">
                                    <label>{{ __('Bank Account Number') }} (رقم الحساب البنكي)</label>
                                    <input type="text" name="bank_account_number" value="{{ $business->bank_account_number }}" class="form-control" placeholder="SA1234567890123456789012">
                                </div>

                                <div class="col-lg-12 mb-2">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> 
                                        <strong>ملاحظة:</strong> الحقول المميزة بـ <span class="text-danger">*</span> مطلوبة لفواتير B2B حسب متطلبات هيئة الزكاة والضريبة والجمارك (ZATCA)
                                        <br>
                                        <strong>Note:</strong> Fields marked with <span class="text-danger">*</span> are required for B2B invoices according to ZATCA requirements
                                    </div>
                                </div>

                                <div class="col-lg-12 mb-3 mt-4">
                                    <h6 class="text-danger border-bottom pb-2">{{ __('Additional Info') }}</h6>
                                </div>

                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-10">
                                            <label class="img-label">{{ __('Image') }}</label>
                                            <input type="file" accept="image/*" name="pictureUrl" class="form-control file-input-change" data-id="image">
                                        </div>
                                        <div class="col-2 align-self-center mt-3">
                                            <img src="{{ asset($business->pictureUrl ?? 'assets/images/icons/upload.png') }}" id="image" class="table-img">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="button-group text-end mt-5">
                                        <button type="reset" class="theme-btn border-btn m-2">{{ __('Cancel') }}</button>
                                        <button class="theme-btn m-2 submit-btn">{{ __('Save') }}</button>
                                    </div>
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

@push('js')
    <script src="{{ asset('assets/js/custom/custom.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Show plan info on page load if plan is selected
            const selectedOption = $('#plan_select_edit').find('option:selected');
            if (selectedOption.val()) {
                updatePlanInfo(selectedOption, '#plan_info_edit');
            }
            
            $('#plan_select_edit').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                updatePlanInfo(selectedOption, '#plan_info_edit');
            });
            
            function updatePlanInfo(selectedOption, targetId) {
                const planInfo = $(targetId);
                
                if (selectedOption.val()) {
                    const warehouses = selectedOption.data('warehouses');
                    const branches = selectedOption.data('branches');
                    const finance = selectedOption.data('finance');
                    const hrm = selectedOption.data('hrm');
                    const commission = selectedOption.data('commission');
                    const duration = selectedOption.data('duration');
                    
                    let info = `Duration: ${duration} days | `;
                    info += `Warehouses: ${warehouses} | `;
                    info += `Branches: ${branches} | `;
                    info += `Finance: ${finance} | `;
                    info += `HRM: ${hrm} | `;
                    info += `Commission: ${commission}`;
                    
                    planInfo.html(info).removeClass('text-muted').addClass('text-info');
                } else {
                    planInfo.html('').removeClass('text-info').addClass('text-muted');
                }
            }
        });
    </script>
@endpush
