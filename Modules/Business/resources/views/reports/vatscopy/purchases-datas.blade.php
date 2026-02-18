<div class="table-container">
    <table class="table dashboard-table-content">
        <thead class="thead-light">
            <tr>
                <th class="text-start" scope="col">{{ __('Date') }}</th>
                <th class="text-center" scope="col">{{ __('Invoice') }}</th>
                <th class="text-center" scope="col">{{ __('Supplier') }}</th>
                <th class="text-center" scope="col">{{ __('Total Amount') }}</th>
                <th class="text-center" scope="col">{{ __('Payment Method') }}</th>
                <th class="text-center" scope="col">{{ __('Discount') }}</th>
                @foreach ($vats as $vat)
                    <th class="text-center">{{ $vat->name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($purchases as $purchase)
                <tr>
                    <td class="text-start">{{ formatted_date($purchase->created_at) }}</td>
                    <td class="text-center">{{ $purchase->invoiceNumber }}</td>
                    <td class="text-center">{{ $purchase->party->name ?? '' }}</td>
                    <td class="text-center">
                        {{ currency_format($purchase->totalAmount, currency: business_currency()) }}</td>
                    <td class="text-center">{{ $purchase->payment_type->name ?? '' }}</td>
                    <td class="text-center">
                        {{ currency_format($purchase->discountAmount, currency: business_currency()) }}</td>
                    @foreach ($vats as $vat)
                        <td class="text-center">
                            {{ $purchase->vat_id == $vat->id ? currency_format($purchase->vat_amount, currency: business_currency()) : '0' }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="m-2">
    {{ $purchases->links('vendor.pagination.bootstrap-5') }}
</div>
