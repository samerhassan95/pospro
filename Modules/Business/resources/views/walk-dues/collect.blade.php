@extends('layouts.business.master')

@section('title')
    {{ __('Collect Walk-in Customer Due') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-header p-16">
                        <h4>{{ __('Collect Walk-in Customer Due') }}</h4>
                        <a href="{{ route('business.walk-dues.index') }}" class="add-order-btn rounded-2">
                            <i class="fas fa-arrow-left me-1"></i>{{ __('Back to List') }}
                        </a>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Invoice Details') }}</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('Invoice No') }}:</strong></td>
                                            <td>{{ $sale->invoiceNumber }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Date') }}:</strong></td>
                                            <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Customer Name') }}:</strong></td>
                                            <td>{{ $sale->customer_name ?? __('Walk-in Customer') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Customer Phone') }}:</strong></td>
                                            <td>{{ $sale->customer_phone ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Total Amount') }}:</strong></td>
                                            <td>{!! currency_format($sale->totalAmount, currency: business_currency()) !!}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Paid Amount') }}:</strong></td>
                                            <td class="text-success">{!! currency_format($sale->paidAmount, currency: business_currency()) !!}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Due Amount') }}:</strong></td>
                                            <td class="text-danger fw-bold">{!! currency_format($sale->dueAmount, currency: business_currency()) !!}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Collect Payment') }}</h5>
                                </div>
                                <div class="card-body">
                                    <form id="collectDueForm" action="{{ route('business.collect.walk.dues.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                                        
                                        <div class="mb-3">
                                            <label for="amount" class="form-label">{{ __('Collection Amount') }} <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="amount" name="amount" 
                                                   step="0.01" min="0" max="{{ $sale->dueAmount }}" 
                                                   placeholder="{{ __('Enter amount to collect') }}" required>
                                            <small class="text-muted">{{ __('Maximum amount') }}: {!! currency_format($sale->dueAmount, currency: business_currency()) !!}</small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="payment_type" class="form-label">{{ __('Payment Method') }} <span class="text-danger">*</span></label>
                                            <select class="form-control" id="payment_type" name="payment_type" required>
                                                <option value="">{{ __('Select payment method') }}</option>
                                                <option value="cash">{{ __('Cash') }}</option>
                                                <option value="bank">{{ __('Bank Transfer') }}</option>
                                                <option value="card">{{ __('Card Payment') }}</option>
                                                <option value="mobile">{{ __('Mobile Payment') }}</option>
                                                <option value="cheque">{{ __('Cheque') }}</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="note" class="form-label">{{ __('Note') }}</label>
                                            <textarea class="form-control" id="note" name="note" rows="3" 
                                                      placeholder="{{ __('Add any additional notes (optional)') }}"></textarea>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-money-bill me-1"></i>{{ __('Collect Payment') }}
                                            </button>
                                            <a href="{{ route('business.walk-dues.index') }}" class="btn btn-secondary">
                                                {{ __('Cancel') }}
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    $('#collectDueForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Disable submit button and show loading
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>{{ __("Processing...") }}');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                toastr.success(response.message);
                if (response.redirect) {
                    window.location.href = response.redirect;
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON;
                if (errors.message) {
                    toastr.error(errors.message);
                } else if (errors.errors) {
                    Object.values(errors.errors).forEach(function(error) {
                        toastr.error(error[0]);
                    });
                } else {
                    toastr.error('{{ __("An error occurred. Please try again.") }}');
                }
            },
            complete: function() {
                // Re-enable submit button
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Auto-fill maximum amount when clicking on due amount
    $('.text-danger.fw-bold').on('click', function() {
        const maxAmount = {{ $sale->dueAmount }};
        $('#amount').val(maxAmount);
    });
</script>
@endpush