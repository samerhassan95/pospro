@extends('layouts.business.master')

@section('title')
    {{ __('Supplier Ledger') }} - {{ $party->name }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-header p-16">
                        <h4>{{ $party->name }} {{ __('Ledger') }}</h4>
                        <a href="{{ route('business.supplier-ledger.index') }}" class="btn btn-secondary rounded-2">
                            <i class="fas fa-arrow-left me-1"></i> {{ __('Back') }}
                        </a>
                    </div>
                   
                    <div class="row mb-3 p-16">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <td>{{ $party->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Phone') }}</th>
                                    <td>{{ $party->phone }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Email') }}</th>
                                    <td>{{ $party->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Address') }}</th>
                                    <td>{{ $party->address }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>{{ __('Total Purchases') }}</th>
                                    <td>{{ number_format($purchases->sum('totalAmount'), 2) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Total Paid') }}</th>
                                    <td>{{ number_format($purchases->sum('paidAmount'), 2) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Total Due') }}</th>
                                    <td class="text-danger fw-bold">{{ number_format($purchases->sum('dueAmount'), 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="responsive-table m-0">
                        <table class="table table-striped table-bordered">
                            <thead class="bg-primary text-white">
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
                                @forelse ($purchases as $purchase)
                                    <tr>
                                        <td>{{ $purchase->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $purchase->invoiceNumber }}</td>
                                        <td>{{ number_format($purchase->totalAmount, 2) }}</td>
                                        <td class="text-success">{{ number_format($purchase->paidAmount, 2) }}</td>
                                        <td class="text-danger">{{ number_format($purchase->dueAmount, 2) }}</td>
                                        <td>
                                            @if($purchase->dueAmount == 0)
                                                <span class="badge bg-success">{{ __('Paid') }}</span>
                                            @elseif($purchase->paidAmount == 0)
                                                <span class="badge bg-danger">{{ __('Unpaid') }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ __('Partial') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">{{ __('No purchases found') }}</td>
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
