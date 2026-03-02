@extends('layouts.business.master')

@section('title')
{{ __('Bill Details') }} - {{ $bill->invoiceNumber }}
@endsection

@section('main_content')
<div class="erp-table-section">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>{{ __('Invoice') }}: {{ $bill->invoiceNumber }}</h4>
                    <a href="{{ route('business.bill-wise-profit-reports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                    </a>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>{{ __('Customer') }}:</strong> {{ $bill->party->name ?? __('Walk-in Customer') }}</p>
                        <p><strong>{{ __('Date') }}:</strong> {{ \Carbon\Carbon::parse($bill->saleDate)->format('d-m-Y') }}</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <p><strong>{{ __('Total Amount') }}:</strong> {!! currency_format($bill->totalAmount, currency: business_currency()) !!}</p>
                        <p><strong>{{ __('Profit/Loss') }}:</strong> 
                            @if($bill->lossProfit >= 0)
                                <span class="badge bg-success">{!! currency_format($bill->lossProfit, currency: business_currency()) !!}</span>
                            @else
                                <span class="badge bg-danger">{!! currency_format(abs($bill->lossProfit), currency: business_currency()) !!}</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('SL') }}</th>
                                <th>{{ __('Product Name') }}</th>
                                <th class="text-end">{{ __('Quantity') }}</th>
                                <th class="text-end">{{ __('Sale Price') }}</th>
                                <th class="text-end">{{ __('Purchase Price') }}</th>
                                <th class="text-end">{{ __('Subtotal') }}</th>
                                <th class="text-end">{{ __('Profit/Loss') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bill->details as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $detail->product->productName ?? __('N/A') }}</td>
                                    <td class="text-end">{{ $detail->quantities }}</td>
                                    <td class="text-end">{!! currency_format($detail->price, currency: business_currency()) !!}</td>
                                    <td class="text-end">{!! currency_format($detail->productPurchasePrice, currency: business_currency()) !!}</td>
                                    <td class="text-end">{!! currency_format($detail->price * $detail->quantities, currency: business_currency()) !!}</td>
                                    <td class="text-end">
                                        @if($detail->lossProfit >= 0)
                                            <span class="text-success">{!! currency_format($detail->lossProfit, currency: business_currency()) !!}</span>
                                        @else
                                            <span class="text-danger">{!! currency_format(abs($detail->lossProfit), currency: business_currency()) !!}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="5" class="text-end"><strong>{{ __('Total') }}:</strong></td>
                                <td class="text-end"><strong>{!! currency_format($bill->totalAmount, currency: business_currency()) !!}</strong></td>
                                <td class="text-end">
                                    <strong>
                                        @if($bill->lossProfit >= 0)
                                            <span class="text-success">{!! currency_format($bill->lossProfit, currency: business_currency()) !!}</span>
                                        @else
                                            <span class="text-danger">{!! currency_format(abs($bill->lossProfit), currency: business_currency()) !!}</span>
                                        @endif
                                    </strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
