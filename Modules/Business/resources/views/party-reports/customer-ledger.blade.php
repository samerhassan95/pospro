@extends('layouts.business.master')

@section('title')
    {{ __('Customer Ledger') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-bodys">
                    <div class="table-header p-16 d-print-none">
                        <h4>{{ __('Customer Ledger') }}</h4>
                    </div>

                    <div class="table-header justify-content-center border-0 text-center d-none d-block d-print-block">
                        @include('business::print.header')
                        <h4 class="mt-2">{{ __('Customer Ledger') }}</h4>
                    </div>

                    <div class="table-top-form p-16">
                        <div class="table-top-left d-flex gap-3">
                            <div class="table-search position-relative d-print-none">
                                <input class="form-control searchInput" type="text" name="search"
                                    placeholder="{{ __('Search by name, phone or email...') }}" id="customerSearch">
                                <span class="position-absolute">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14.582 14.582L18.332 18.332" stroke="#4D4D4D" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M16.668 9.16797C16.668 5.02584 13.3101 1.66797 9.16797 1.66797C5.02584 1.66797 1.66797 5.02584 1.66797 9.16797C1.66797 13.3101 5.02584 16.668 9.16797 16.668C13.3101 16.668 16.668 13.3101 16.668 9.16797Z" stroke="#4D4D4D" stroke-width="1.25" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="table-top-btn-group d-print-none">
                            <ul>
                                <li>
                                    <a onclick="window.print()" class="print-window">
                                        <img src="{{ asset('assets/images/logo/printer.svg') }}" alt="{{ __('Print') }}">
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="responsive-table m-0">
                    <table class="table" id="customerLedgerTable">
                        <thead>
                            <tr>
                                <th>{{ __('SL') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Phone') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Total Sales') }}</th>
                                <th>{{ __('Total Due') }}</th>
                                <th class="d-print-none">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($parties as $key => $party)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $party->name }}</td>
                                    <td>{{ $party->phone }}</td>
                                    <td>{{ $party->email ?? '-' }}</td>
                                    <td>{{ currency_format($party->sales->sum('totalAmount'), currency: business_currency()) }}</td>
                                    <td class="text-danger fw-bold">{{ currency_format($party->sales->sum('dueAmount'), currency: business_currency()) }}</td>
                                    <td class="d-print-none">
                                        <div class="dropdown table-action">
                                            <button type="button" data-bs-toggle="dropdown">
                                                <svg width="14" height="4" viewBox="0 0 14 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M2 0.75C2.69036 0.75 3.25 1.30964 3.25 2C3.25 2.69036 2.69036 3.25 2 3.25C1.30964 3.25 0.75 2.69036 0.75 2C0.75 1.30964 1.30964 0.75 2 0.75Z" fill="#5A6376"/>
                                                    <path d="M7 0.75C7.69036 0.75 8.25 1.30964 8.25 2C8.25 2.69036 7.69036 3.25 7 3.25C6.30964 3.25 5.75 2.69036 5.75 2C5.75 1.30964 6.30964 0.75 7 0.75Z" fill="#5A6376"/>
                                                    <path d="M12 0.75C12.6904 0.75 13.25 1.30964 13.25 2C13.25 2.69036 12.6904 3.25 12 3.25C11.3096 3.25 10.75 2.69036 10.75 2C10.75 1.30964 11.3096 0.75 12 0.75Z" fill="#5A6376"/>
                                                </svg>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="{{ route('business.customer-ledger.show', $party->id) }}" class="dropdown-item">
                                                    <i class="fas fa-eye me-2"></i> {{ __('View Ledger') }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">{{ __('No customers found') }}</h5>
                                            <p class="text-muted">{{ __('No customer transactions have been recorded yet.') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    // Simple search functionality
    $('#customerSearch').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#customerLedgerTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
</script>
@endpush
