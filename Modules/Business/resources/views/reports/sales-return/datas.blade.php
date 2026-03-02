<div class="responsive-table m-0">
    <table class="table" id="datatable">
        <thead>
            <tr>
                <th>{{ __('SL') }}.</th>
                @if(auth()->user()->accessToMultiBranch())
                <th>{{ __('Branch') }}</th>
                @endif
                <th>{{ __('Invoice No') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Total') }}</th>
                <th>{{ __('Paid') }}</th>
                <th>{{ __('Return Amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)

                @php
                    $total_return_amount = $sale->saleReturns->sum('total_return_amount');
                @endphp

                <tr>
                    <td>{{ ($sales->currentPage() - 1) * $sales->perPage() + $loop->iteration }}</td>
                    @if(auth()->user()->accessToMultiBranch())
                    <td>{{ $sale->branch->name ?? '' }}</td>
                    @endif
                    <td>
                        <a href="{{ route('business.sales.invoice', $sale->id) }}" target="_blank" class="text-primary">
                            {{ $sale->invoiceNumber }}
                        </a>
                    </td>
                    <td>{{ formatted_date($sale->saleDate) }}</td>
                    <td>{{ $sale->party->name ?? 'Guest' }}</td>

                    <td>{{ $sale->totalAmount }}</td>
                    <td>{{ $sale->paidAmount }}</td>
                    <td>{!! currency_format($total_return_amount ?? 0, currency: business_currency()) !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="m-2">
    {{ $sales->links('vendor.pagination.bootstrap-5') }}
</div>
