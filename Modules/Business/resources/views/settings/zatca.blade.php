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

                        @if (!empty($business->zatca_setting['csid']))
                            <div class="row mt-5">
                                <div class="col-md-12 mb-3">
                                    <h5>{{ __('Step 3: Compliance Testing / اختبار التوافق') }}</h5>
                                    <p class="text-muted">{{ __('To move to Production, you must test at least 3-5 invoices successfully in Sandbox/Simulation.') }}</p>
                                    <hr>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Invoice #') }}</th>
                                                    <th>{{ __('Date') }}</th>
                                                    <th>{{ __('Total') }}</th>
                                                    <th>{{ __('ZATCA Status') }}</th>
                                                    <th>{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($sales as $sale)
                                                    <tr>
                                                        <td>{{ $sale->invoiceNumber }}</td>
                                                        <td>{{ $sale->saleDate }}</td>
                                                        <td>{!! currency_format($sale->totalAmount) !!}</td>
                                                        <td>
                                                            @if($sale->zatca_status == 'REPORTED' || $sale->zatca_status == 'COMPLIANT')
                                                                <span class="badge bg-success">{{ $sale->zatca_status }}</span>
                                                            @elseif($sale->zatca_status == 'FAILED')
                                                                <span class="badge bg-danger">{{ $sale->zatca_status }}</span>
                                                                <button class="btn btn-sm btn-info ms-2" onclick="showZatcaResponse('{{ $sale->id }}')">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                <div id="response-{{ $sale->id }}" style="display:none;">{{ $sale->zatca_response }}</div>
                                                            @else
                                                                <span class="badge bg-secondary">{{ __('Pending') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-warning btn-sm" onclick="testCompliance('{{ $sale->id }}', this)">
                                                                <i class="fas fa-flask"></i> {{ __('Test Compliance') }}
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3 mt-5">
                                    <h5>{{ __('Step 4: Go Live / تفعيل الإنتاج') }}</h5>
                                    <p class="text-muted">{{ __('Only click this after you have successfully tested invoices above.') }}</p>
                                    <hr>
                                </div>
                                
                                <div class="col-md-12">
                                    <form action="{{ route('business.zatca.production-csid') }}" method="POST">
                                        @csrf
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> {{ __('Action: This will request a Production CSID from ZATCA. This action is permanent for this device.') }}
                                        </div>
                                        <button type="submit" class="btn btn-success btn-lg w-100" 
                                            {{ ($business->zatca_setting['environment'] ?? '') == 'production' ? 'disabled' : '' }}>
                                            <i class="fas fa-rocket"></i> {{ __('Request Production CSID & Go Live') }}
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
        function testCompliance(saleId, btn) {
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';

            fetch(`{{ url('business/zatca-test-invoice') }}/${saleId}`, {
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
                    toastr.success('Invoice is compliant with ZATCA!');
                } else {
                    let msg = data.message || 'Compliance check failed.';
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
                alert('An error occurred during testing: ' + error.message);
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
