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
                                                        <td>{{ number_format($sub->price, 2) }} SAR</td>
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
