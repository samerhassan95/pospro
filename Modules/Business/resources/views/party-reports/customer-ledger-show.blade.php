@extends('layouts.business.master')

@section('title')
    {{ __('Customer Ledger') }} - {{ $party->name }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-header p-16">
                        <h4>{{ $party->name }} {{ __('Ledger') }}</h4>
                        <a href="{{ route('business.customer-ledger.index') }}" class="add-order-btn rounded-2">
                            <i class="fas fa-arrow-left me-1"></i>{{ __('Back') }}
                        </a>
                    </div>
                   
                    <div class="row mb-4 p-16">
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">{{ __('Customer Information') }}</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <th width="40%">{{ __('Name') }}:</th>
                                            <td>{{ $party->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Phone') }}:</th>
                                            <td>{{ $party->phone }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Email') }}:</th>
                                            <td>{{ $party->email ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Address') }}:</th>
                                            <td>{{ $party->address ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">{{ __('Financial Summary') }}</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <th width="40%">{{ __('Total Sales') }}:</th>
                                            <td class="text-primary fw-bold">{{ currency_format($sales->sum('totalAmount'), currency: business_currency()) }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Total Paid') }}:</th>
                                            <td class="text-success fw-bold">{{ currency_format($sales->sum('paidAmount'), currency: business_currency()) }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Total Due') }}:</th>
                                            <td class="text-danger fw-bold">{{ currency_format($sales->sum('dueAmount'), currency: business_currency()) }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Total Transactions') }}:</th>
                                            <td class="fw-bold">{{ $sales->count() }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="responsive-table m-0">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Invoice No') }}</th>
                                    <th>{{ __('Grand Total') }}</th>
                                    <th>{{ __('Paid') }}</th>
                                    <th>{{ __('Due') }}</th>
                                    <th>{{ __('Payment Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sales as $sale)
                                    <tr>
                                        <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $sale->invoiceNumber }}</td>
                                        <td>{{ currency_format($sale->totalAmount, currency: business_currency()) }}</td>
                                        <td class="text-success">{{ currency_format($sale->paidAmount, currency: business_currency()) }}</td>
                                        <td class="text-danger">{{ currency_format($sale->dueAmount, currency: business_currency()) }}</td>
                                        <td>
                                            @if($sale->dueAmount == 0)
                                                <span class="badge bg-success">{{ __('Paid') }}</span>
                                            @elseif($sale->paidAmount == 0)
                                                <span class="badge bg-danger">{{ __('Unpaid') }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ __('Partial') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">{{ __('No sales found') }}</h5>
                                                <p class="text-muted">{{ __('No sales transactions have been recorded for this customer.') }}</p>
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
    </div>
@endsection
