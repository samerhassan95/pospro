@extends('layouts.business.master')

@section('title')
    {{ __('Supplier Ledger') }} - {{ $party->name }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-bodys">
                    <div class="table-header p-16">
                        <h4>{{ $party->name }} {{ __('Ledger') }}</h4>
                        <div class="d-flex gap-2">
                            <button onclick="window.print()" class="add-order-btn rounded-2">
                                <i class="fas fa-print me-1"></i> {{ __('Print') }}
                            </button>
                            <a href="{{ route('business.supplier-ledger.index') }}" class="add-order-btn rounded-2">
                                <i class="fas fa-arrow-left me-1"></i> {{ __('Back') }}
                            </a>
                        </div>
                    </div>
                   
                    <div class="row mb-3 p-16">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __('Supplier Information') }}</h6>
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
                                            <td>{{ $party->address }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __('Financial Summary') }}</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <th width="50%">{{ __('Total Purchases') }}:</th>
                                            <td class="text-primary fw-bold">{!! currency_format($purchases->sum('totalAmount')) !!}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Total Paid') }}:</th>
                                            <td class="text-success fw-bold">{!! currency_format($purchases->sum('paidAmount')) !!}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Total Due') }}:</th>
                                            <td class="text-danger fw-bold">{!! currency_format($purchases->sum('dueAmount')) !!}</td>
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
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchases as $purchase)
                                    <tr>
                                        <td>{{ $purchase->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="fw-bold text-primary">{{ $purchase->invoiceNumber }}</span>
                                        </td>
                                        <td>{!! currency_format($purchase->totalAmount) !!}</td>
                                        <td class="text-success fw-bold">{!! currency_format($purchase->paidAmount) !!}</td>
                                        <td class="text-danger fw-bold">{!! currency_format($purchase->dueAmount) !!}</td>
                                        <td>
                                            @if($purchase->dueAmount == 0)
                                                <span class="badge bg-success">{{ __('Paid') }}</span>
                                            @elseif($purchase->paidAmount == 0)
                                                <span class="badge bg-danger">{{ __('Unpaid') }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ __('Partial') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="{{ route('business.purchases.edit', $purchase->id) }}">
                                                        <i class="fas fa-edit me-2"></i>{{ __('Edit') }}
                                                    </a></li>
                                                    @if($purchase->dueAmount > 0)
                                                        <li><a class="dropdown-item" href="{{ route('business.collect.dues', $purchase->id) }}">
                                                            <i class="fas fa-money-bill me-2"></i>{{ __('Pay Due') }}
                                                        </a></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">{{ __('No purchases found') }}</h5>
                                                <p class="text-muted">{{ __('This supplier has no purchase transactions yet.') }}</p>
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
