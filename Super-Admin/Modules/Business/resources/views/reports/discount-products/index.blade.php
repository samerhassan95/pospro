@extends('layouts.business.master')

@section('title')
    {{ __('Discount Product') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-header p-16">
                        <h4>{{ __('Discount Product') }}</h4>
                        <div>
                            <span class="badge bg-info">{{ __('Total Discount') }}: {{ number_format($totalDiscount, 2) }}</span>
                        </div>
                    </div>

                    <div class="responsive-table m-0">
                        <table class="table table-striped table-bordered">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>{{ __('SL') }}</th>
                                    <th>{{ __('Invoice No') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Total Amount') }}</th>
                                    <th>{{ __('Discount') }}</th>
                                    <th>{{ __('Grand Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sales as $key => $sale)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $sale->invoiceNumber }}</td>
                                        <td>{{ $sale->party->name ?? 'Walk-in Customer' }}</td>
                                        <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                                        <td>{{ number_format($sale->totalAmount + $sale->discountAmount, 2) }}</td>
                                        <td class="text-danger fw-bold">{{ number_format($sale->discountAmount, 2) }}</td>
                                        <td>{{ number_format($sale->totalAmount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">{{ __('No discount products found') }}</td>
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
