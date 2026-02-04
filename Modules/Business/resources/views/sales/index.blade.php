@extends('layouts.business.master')

@section('title')
{{ __('Sales List') }}
@endsection

@section('main_content')
<div class="erp-table-section">
    <div class="container-fluid">
        <div class="card">
            <div class="card-bodys">
                <div class="table-header p-16">
                    <h4>{{ __('Sales List') }}</h4>
                </div>
                <div class="table-top-form p-16-0">
                    <div class="d-flex align-items-center gap-3 flex-wrap margin-lr-16">
                        <form action="{{ route('business.sales.filter') }}" method="post" class="report-filter-form" table="#sales-data">
                            @csrf
                            <div class="table-top-left d-flex gap-3 flex-wrap">

                                <div class="gpt-up-down-arrow position-relative">
                                    <select name="per_page" class="form-control">
                                        <option value="5" selected>{{ __('Show- 5') }}</option>
                                        <option value="10">{{ __('Show- 10') }}</option>
                                        <option value="25">{{ __('Show- 25') }}</option>
                                        <option value="50">{{ __('Show- 50') }}</option>
                                        <option value="100">{{ __('Show- 100') }}</option>
                                    </select>
                                    <span></span>
                                </div>

                                @if(auth()->user()->accessToMultiBranch())
                                <div class="table-search position-relative">
                                    <div class="gpt-up-down-arrow position-relative">
                                        <select name="branch_id" class="form-control">
                                            <option value="">{{ __('Select Branch') }}</option>
                                            @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                        <span></span>
                                    </div>
                                </div>
                                @endif

                                <div class="table-search position-relative">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="{{ __('Search...') }}">
                                    <span class="position-absolute">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14.582 14.582L18.332 18.332" stroke="#4D4D4D" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M16.668 9.16797C16.668 5.02584 13.3101 1.66797 9.16797 1.66797C5.02584 1.66797 1.66797 5.02584 1.66797 9.16797C1.66797 13.3101 5.02584 16.668 9.16797 16.668C13.3101 16.668 16.668 13.3101 16.668 9.16797Z" stroke="#4D4D4D" stroke-width="1.25" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </div>

                                <div class="custom-from-to align-items-center date-filters d-none">
                                    <label class="header-label">{{ __('From Date') }}</label>
                                    <input type="date" name="from_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" class="form-control">
                                </div>
                                <div class="custom-from-to align-items-center date-filters d-none">
                                    <label class="header-label">{{ __('To Date') }}</label>
                                    <input type="date" name="to_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" class="form-control">
                                </div>
                                <div class="gpt-up-down-arrow position-relative d-print-none custom-date-filter">
                                    <select name="custom_days" class="form-control custom-days">
                                        <option value="today">{{__('Today')}}</option>
                                        <option value="yesterday">{{__('Yesterday')}}</option>
                                        <option value="last_seven_days">{{__('Last 7 Days')}}</option>
                                        <option value="last_thirty_days">{{__('Last 30 Days')}}</option>
                                        <option value="current_month">{{__('Current Month')}}</option>
                                        <option value="last_month">{{__('Last Month')}}</option>
                                        <option value="current_year">{{__('Current Year')}}</option>
                                        <option value="custom_date">{{__('Custom Date')}}</option>
                                    </select>
                                    <span></span>
                                    <div class="calendar-icon">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12.6667 2.66797H3.33333C2.59695 2.66797 2 3.26492 2 4.0013V13.3346C2 14.071 2.59695 14.668 3.33333 14.668H12.6667C13.403 14.668 14 14.071 14 13.3346V4.0013C14 3.26492 13.403 2.66797 12.6667 2.66797Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M10.666 1.33203V3.9987" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M5.33398 1.33203V3.9987" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M2 6.66797H14" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div class="delete-item delete-show d-none">
                <div class="delete-item-show">
                    <p class="fw-bold"><span class="selected-count"></span> {{ __('items show') }}</p>
                    <button data-bs-toggle="modal" class="trigger-modal" data-bs-target="#multi-delete-modal" data-url="{{ route('business.sales.delete-all') }}">{{ __('Delete') }}</button>
                </div>
            </div>
            <div class="responsive-table m-0">
                <table class="table" id="datatable">
                    <thead>
                        <tr>
                            @usercan('sales.delete')
                            <th class="w-60">
                                <div class="d-flex align-items-center gap-3">
                                    <input type="checkbox" class="select-all-delete multi-delete ">
                                </div>
                            </th>
                            @endusercan
                            <th>{{ __('SL') }}.</th>
                            <th class="text-start">{{ __('Date') }}</th>
                            @if(auth()->user()->accessToMultiBranch())
                            <th class="text-start">{{ __('Branch') }}</th>
                            @endif
                            <th class="text-start">{{ __('Invoice No') }}</th>
                            <th class="text-start">{{ __('Party Name') }}</th>
                            <th class="text-start">{{ __('Total') }}</th>
                            <th class="text-start">{{ __('Discount') }}</th>
                            <th class="text-start">{{ __('Paid') }}</th>
                            <th class="text-start">{{ __('Due') }}</th>
                            <th class="text-start">{{ __('Payment') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('ZATCA') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody id="sales-data">
                        @include('business::sales.datas')
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $sales->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('modal')
@include('business::component.delete-modal')

<!-- ZATCA Issues Modal -->
<div class="modal fade" id="zatcaIssuesModal" tabindex="-1" aria-labelledby="zatcaIssuesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="zatcaIssuesModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    {{ __('ZATCA Compliance Issues') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="zatcaIssuesContent">
                    {{-- <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">{{ __('Loading...') }}</span>
                        </div>
                    </div> --}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('script')
<script>
    $(document).on('click', '.zatca-issues-btn', function(e) {
        e.preventDefault();
        const saleId = $(this).data('sale-id');
        const modal = $('#zatcaIssuesModal');
        const content = $('#zatcaIssuesContent');
        const btn = $(this);

        // Disable button and show loading
        btn.prop('disabled', true);
        // content.html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">{{ __("Loading issues...") }}</p></div>');

        // Show modal immediately
        if (!modal.hasClass('show')) {
            modal.modal('show');
        }

        // Fetch issues with timeout
        const url = '{{ route("business.sales.zatca-issues", ":id") }}'.replace(':id', saleId);
        console.log('Fetching ZATCA issues for sale:', saleId);
        console.log('URL:', url);

        $.ajax({
            url: url,
            method: 'GET',
            timeout: 10000, // 10 seconds timeout
            beforeSend: function() {
                console.log('AJAX request started');
            },
            success: function(response) {
                console.log('AJAX success response:', response);
                console.log('Response type:', typeof response);
                console.log('Response success:', response.success);
                console.log('Response has_issues:', response.has_issues);
                console.log('Response issues:', response.issues);

                btn.prop('disabled', false);

                // Handle string response (if JSON not parsed)
                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                        console.log('Parsed response:', response);
                    } catch (e) {
                        console.error('Failed to parse response:', e);
                        content.html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>{{ __("Invalid response format") }}<br><pre>' + response.substring(0, 500) + '</pre></div>');
                        return;
                    }
                }

                // Check if response exists
                if (!response) {
                    console.error('Response is null or undefined');
                    content.html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>{{ __("No response received from server") }}</div>');
                    return;
                }

                if (response && response.success !== false) {
                    let html = '';

                    if (response.has_issues && response.issues && response.issues.length > 0) {
                        html += '<div class="alert alert-warning">';
                        html += '<h6><i class="fas fa-exclamation-triangle me-2"></i>{{ __("The following issues prevent this invoice from being ZATCA compliant:") }}</h6>';
                        html += '<ul class="mb-0 mt-3">';
                        response.issues.forEach(function(issue) {
                            html += '<li class="mb-2"><i class="fas fa-times-circle text-danger me-2"></i>' + issue + '</li>';
                        });
                        html += '</ul>';
                        html += '</div>';

                        if (response.zatca_status) {
                            html += '<div class="alert alert-info mt-3">';
                            html += '<strong>{{ __("Current Status:") }}</strong> <span class="badge bg-info">' + (response.zatca_status || '{{ __("Not Reported") }}') + '</span>';
                            html += '</div>';
                        }

                        if (response.invoice_number) {
                            html += '<div class="alert alert-secondary mt-2">';
                            html += '<strong>{{ __("Invoice Number:") }}</strong> ' + response.invoice_number;
                            html += '</div>';
                        }
                    } else {
                        html += '<div class="alert alert-success">';
                        html += '<i class="fas fa-check-circle me-2"></i>';
                        html += '{{ __("No compliance issues found. Invoice is ready for ZATCA reporting.") }}';
                        html += '</div>';

                        if (response.zatca_status) {
                            html += '<div class="alert alert-info mt-3">';
                            html += '<strong>{{ __("Current Status:") }}</strong> <span class="badge bg-info">' + response.zatca_status + '</span>';
                            html += '</div>';
                        }
                    }

                    content.html(html);
                } else {
                    console.error('Response success is false or missing:', response);
                    let errorHtml = '<div class="alert alert-danger">';
                    errorHtml += '<i class="fas fa-exclamation-circle me-2"></i>';
                    errorHtml += response.error || response.message || '{{ __("Error loading issues") }}';
                    if (response.issues && response.issues.length > 0) {
                        errorHtml += '<ul class="mt-2">';
                        response.issues.forEach(function(issue) {
                            errorHtml += '<li>' + issue + '</li>';
                        });
                        errorHtml += '</ul>';
                    }
                    errorHtml += '</div>';
                    content.html(errorHtml);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    xhr: xhr,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });

                btn.prop('disabled', false);
                let errorMsg = '{{ __("Error loading issues. Please try again.") }}';

                if (status === 'timeout') {
                    errorMsg = '{{ __("Request timeout. Please try again.") }}';
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                } else if (xhr.status === 404) {
                    errorMsg = '{{ __("Invoice not found or route not found.") }}';
                } else if (xhr.status === 500) {
                    errorMsg = '{{ __("Server error. Please contact support.") }}';
                } else if (xhr.status === 0) {
                    errorMsg = '{{ __("Network error. Please check your connection.") }}';
                }

                content.html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>' + errorMsg + '<br><small>Status: ' + xhr.status + ' | ' + status + '</small></div>');
            }
        });
    });
</script>
@endpush
