@extends('layouts.master')

@section('title', __('ZATCA Subscription Settings'))

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-header p-16">
                        <h4>{{ __('ZATCA Subscription Settings (Global) / إعدادات فواتير الاشتراكات') }}</h4>
                    </div>
                    
                    <div class="order-form-section p-16">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        {{-- Status Display --}}
                        <div class="alert alert-{{ !empty($zatcaSettings['csid']) ? 'success' : 'warning' }}">
                            <strong>{{ __('System Onboarding Status:') }}</strong>
                            @if (!empty($zatcaSettings['csid']))
                                {{ __('Connected') }} ({{ $zatcaSettings['environment'] ?? 'Sandbox' }})
                            @else
                                {{ __('Not Connected') }}
                            @endif
                        </div>

                        <form action="{{ route('admin.zatca.subscription.update') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <h5>{{ __('Step 1: System Owner Details / بيانات مالك النظام') }}</h5>
                                    <hr>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('Environment Mode') }}</label>
                                    <select name="environment" class="form-control">
                                        <option value="sandbox" @selected(($zatcaSettings['environment'] ?? 'sandbox') == 'sandbox')>Sandbox (Test)</option>
                                        <option value="simulation" @selected(($zatcaSettings['environment'] ?? '') == 'simulation')>Simulation (Pre-Prod)</option>
                                        <option value="production" @selected(($zatcaSettings['environment'] ?? '') == 'production')>Production (Live)</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('Common Name (Company Name)') }}</label>
                                    <input type="text" name="common_name" class="form-control" 
                                           value="{{ $zatcaSettings['csr_config']['common_name'] ?? config('app.name') }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('VAT Registration Number (Organization Identifier)') }}</label>
                                    <input type="text" name="csr_organization_identifier" class="form-control" 
                                           value="{{ $zatcaSettings['csr_config']['organization_identifier'] ?? '300000000000003' }}" required>
                                    <small class="text-muted">15 digits</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('Commercial Registration Number') }} (رقم السجل التجاري)</label>
                                    <input type="text" name="commercial_registration" class="form-control" 
                                           value="{{ $zatcaSettings['commercial_registration'] ?? '1234567890' }}">
                                    <small class="text-muted">10 digits</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('Additional ID') }} (معرف إضافي)</label>
                                    <input type="text" name="additional_id" class="form-control" 
                                           value="{{ $zatcaSettings['additional_id'] ?? '152034' }}">
                                    <small class="text-muted">Optional - 6 digits</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('Location (City)') }}</label>
                                    <input type="text" name="csr_location" class="form-control" 
                                           value="{{ $zatcaSettings['csr_config']['location'] ?? 'Riyadh' }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('Registered Address (Street)') }}</label>
                                    <input type="text" name="csr_street" class="form-control" 
                                           value="{{ $zatcaSettings['csr_config']['registered_address'] ?? 'King Fahad Road' }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('Organization Unit Name (District)') }}</label>
                                    <input type="text" name="csr_organization_unit_name" class="form-control" 
                                           value="{{ $zatcaSettings['csr_config']['organization_unit_name'] ?? 'HQ' }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('Building Number') }}</label>
                                    <input type="text" name="building_number" class="form-control" 
                                           value="{{ $zatcaSettings['building_number'] ?? '1234' }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('Postal Code') }}</label>
                                    <input type="text" name="postal_code" class="form-control" 
                                           value="{{ $zatcaSettings['postal_code'] ?? '12345' }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('Country Code') }} (رمز الدولة)</label>
                                    <input type="text" name="country_code" class="form-control" 
                                           value="{{ $zatcaSettings['country_code'] ?? 'SA' }}" maxlength="2">
                                    <small class="text-muted">2 letters (e.g., SA)</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('Industry') }}</label>
                                    <input type="text" name="csr_industry" class="form-control" 
                                           value="{{ $zatcaSettings['csr_config']['business_category'] ?? 'Software' }}" required>
                                </div>

                                <div class="col-md-12 mb-3 mt-3">
                                    <h6 class="text-primary border-bottom pb-2">{{ __('Bank Information') }} (معلومات البنك)</h6>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('Bank Name') }} (اسم البنك)</label>
                                    <input type="text" name="bank_name" class="form-control" 
                                           value="{{ $zatcaSettings['bank_name'] ?? 'البنك الأهلي السعودي' }}">
                                    <small class="text-muted">Optional</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('Bank Account Number') }} (رقم الحساب البنكي)</label>
                                    <input type="text" name="bank_account_number" class="form-control" 
                                           value="{{ $zatcaSettings['bank_account_number'] ?? 'SA1234567890123456789012' }}">
                                    <small class="text-muted">IBAN format (Optional)</small>
                                </div>

                                <div class="col-md-12 mb-3 mt-4">
                                    <h5>{{ __('Step 2: Authenticate (OTP) / رمز التحقق') }}</h5>
                                    <hr>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>{{ __('OTP Code') }}</label>
                                    <input type="text" name="otp" class="form-control" placeholder="123456">
                                    <small class="text-muted">Required only for initial connection or re-connection.</small>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    {{ !empty($zatcaSettings['csid']) ? __('Update & Re-Connect') : __('Connect System to ZATCA') }}
                                </button>
                            </div>
                        </form>

                        @if (!empty($zatcaSettings['csid']))
                            <div class="row mt-5">
                                <div class="col-md-12 mb-3">
                                    <h5>{{ __('Step 3: Recent Subscription Invoices / فواتير الاشتراكات الأخيرة') }}</h5>
                                    <hr>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Plan ID') }}</th>
                                                    <th>{{ __('Business') }}</th>
                                                    <th>{{ __('Plan') }}</th>
                                                    <th>{{ __('Price') }}</th>
                                                    <th>{{ __('ZATCA Status') }}</th>
                                                    <th>{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($recentSubscriptions as $sub)
                                                    <tr>
                                                        <td>{{ $sub->invoice_number }}</td>
                                                        <td>{{ $sub->business->companyName ?? 'N/A' }}</td>
                                                        <td>{{ $sub->plan->subscriptionName ?? 'N/A' }}</td>
                                                        <td>
                                                            <svg class="sar-symbol-svg" width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_price_5-1)"><path d="M6.68122 10.6309C6.48962 11.0558 6.36297 11.5168 6.31445 12.0003L10.369 11.1384C10.5606 10.7137 10.6872 10.2525 10.7358 9.76904L6.68122 10.6309Z" fill="#298000"></path><path d="M10.3691 8.55619C10.5607 8.13144 10.6873 7.67031 10.7359 7.18683L7.57749 7.85857V6.56725L10.369 5.97403C10.5606 5.54929 10.6873 5.08815 10.7358 4.60467L7.57739 5.27584V0.631863C7.09343 0.903594 6.66363 1.2653 6.31425 1.69195V5.54441L5.05111 5.8129V0.000244141C4.56715 0.27188 4.13735 0.633678 3.78797 1.06033V6.08129L0.961685 6.68186C0.770089 7.1066 0.643345 7.56773 0.594729 8.05122L3.78797 7.3726V8.99879L0.365788 9.72601C0.174192 10.1508 0.0475433 10.6119 -0.000976562 11.0954L3.58109 10.3341C3.87269 10.2735 4.12331 10.1011 4.28625 9.86384L4.94318 8.8899V8.88971C5.01138 8.78895 5.05111 8.66746 5.05111 8.53661V7.10412L6.31425 6.83564V9.41827L10.369 8.55599L10.3691 8.55619Z" fill="#298000"></path></g><defs><clipPath id="clip0_price_5-1"><rect width="10.7368" height="12" fill="white"></rect></clipPath></defs></svg>
                                                            {{ number_format($sub->price, 2) }}
                                                        </td>
                                                        <td>
                                                            @if($sub->zatca_status == 'COMPLIANT' || $sub->zatca_status == 'REPORTED')
                                                                <span class="badge bg-success">{{ $sub->zatca_status }}</span>
                                                            @elseif($sub->zatca_status == 'FAILED')
                                                                <span class="badge bg-danger">{{ $sub->zatca_status }}</span>
                                                                <button class="btn btn-sm btn-info ms-2" onclick="showZatcaResponse('{{ $sub->id }}')">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                <div id="response-{{ $sub->id }}" style="display:none;">{{ json_encode($sub->zatca_response) }}</div>
                                                            @else
                                                                <span class="badge bg-secondary">{{ __('Pending') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-warning btn-sm" onclick="testCompliance('{{ $sub->id }}', this)">
                                                                <i class="fas fa-flask"></i> {{ __('Test Compliance') }}
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3 mt-5 text-center">
                                    <hr>
                                    <form action="{{ route('admin.zatca.subscription.production-csid') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-lg" 
                                            {{ ($zatcaSettings['environment'] ?? '') == 'production' ? 'disabled' : '' }}>
                                            <i class="fas fa-rocket"></i> {{ __('Request Platform Production CSID & Go Live') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Response -->
    <div class="modal fade" id="zatcaResponseModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('ZATCA Response Details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <pre id="responseContent" style="background: #f8f9fa; padding: 15px; border-radius: 5px;"></pre>
                </div>
            </div>
        </div>
    </div>

    @push('js')
    <script>
        function testCompliance(subId, btn) {
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("Testing...") }}';

            fetch(`{{ url('admin/zatca-subscription-test') }}/${subId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                
                if (data.success) {
                    toastr.success('{{ __("Subscription Invoice is compliant!") }}');
                } else {
                    let msg = data.message || '{{ __("Compliance check failed.") }}';
                    if (data.body && data.body.validationResults) {
                        const errors = data.body.validationResults.errorMessages.map(e => e.message).join('\n');
                        msg += '\n\nErrors:\n' + errors;
                    }
                    alert(msg);
                }
                location.reload();
            })
            .catch(error => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert('{{ __("An error occurred:") }} ' + error.message);
            });
        }

        function showZatcaResponse(id) {
            const content = document.getElementById('response-' + id).innerText;
            try {
                const json = JSON.parse(content);
                document.getElementById('responseContent').innerText = JSON.stringify(json, null, 4);
            } catch (e) {
                document.getElementById('responseContent').innerText = content;
            }
            new bootstrap.Modal(document.getElementById('zatcaResponseModal')).show();
        }
    </script>
    @endpush
@endsection
