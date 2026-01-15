<div class="responsive-table m-0">
    <table class="table" id="datatable">
        <thead>
            <tr>
                <th>{{ __('SL') }}.</th>
                <th>{{ __('Invoice No') }}</th>
                @if(auth()->user()->accessToMultiBranch())
                <th>{{ __('Branch') }}</th>
                @endif
                <th>{{ __('Date') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Total') }}</th>
                <th>{{ __('Paid') }}</th>
                <th>{{ __('Return Amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchases as $purchase)
                @php
                    $total_return_amount = $purchase->purchaseReturns->sum('total_return_amount');
                @endphp
                <tr>
                    <td>{{ ($purchases->currentPage() - 1) * $purchases->perPage() + $loop->iteration }}</td>
                    <td>
                        <a href="{{ route('business.purchases.invoice', $purchase->id) }}" target="_blank" class="text-primary">
                            {{ $purchase->invoiceNumber }}
                        </a>
                    </td>
                    @if(auth()->user()->accessToMultiBranch())
                    <td>{{ $purchase->branch->name ?? '' }}</td>
                    @endif
                    <td>{{ formatted_date($purchase->purchaseDate) }}</td>
                    <td>{{ $purchase->party->name ?? '' }}</td>
                    <td>{{ currency_format($purchase->totalAmount , currency: business_currency()) }}</td>
                    <td>{{ currency_format($purchase->paidAmount, currency: business_currency()) }}</td>
                    <td>{{ currency_format($total_return_amount ?? 0, currency: business_currency()) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="m-2">
    {{ $purchases->links('vendor.pagination.bootstrap-5') }}
</div>
