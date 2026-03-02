<div class="table-container">
    <table class="table dashboard-table-content">
        <thead class="thead-light">
            <tr>
                <th class="text-start" scope="col">{{ __('Date') }}</th>
                <th class="text-center" scope="col">{{ __('Invoice') }}</th>
                <th class="text-center" scope="col">{{ __('Customer') }}</th>
                <th class="text-center" scope="col">{{ __('Total Amount') }}</th>
                <th class="text-center" scope="col">{{ __('Payment Method') }}</th>
                <th class="text-center" scope="col">{{ __('Discount') }}</th>
                @foreach ($vats as $vat)
                    <th class="text-center">{{ $vat->name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
                <tr>
                    <td class="text-start">{{ formatted_date($sale->created_at) }}</td>
                    <td class="text-center">{{ $sale->invoiceNumber }}</td>
                    <td class="text-center">{{ $sale->party->name ?? '' }}</td>
                    <td class="text-center">
                        {!! currency_format($sale->totalAmount, currency: business_currency()) !!}
                    </td>
                    <td class="text-center">{{ $sale->payment_type->name ?? '' }}</td>
                    <td class="text-center">
                        {!! currency_format($sale->discountAmount, currency: business_currency()) !!}</td>
                    @foreach ($vats as $vat)
                        <td class="text-center">
                            {{ $sale->vat_id == $vat->id ? currency_format($sale->vat_amount, currency: business_currency()) : '0' }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="m-2">
    {{ $sales->links('vendor.pagination.bootstrap-5') }}
</div>
