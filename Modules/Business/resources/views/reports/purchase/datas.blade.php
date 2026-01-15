<div class="responsive-table m-0">
    <table class="table" id="datatable">
        <thead>
        <tr>
            <th>{{ __('SL') }}.</th>
            @if(auth()->user()->accessToMultiBranch())
            <th class="text-start">{{ __('Branch') }}</th>
            @endif
            <th class="text-start">{{ __('Invoice No') }}</th>
            <th class="text-start">{{ __('Party Name') }}</th>
            <th class="text-start">{{ __('Total Amount') }}</th>
            <th class="text-start">{{ __('Discount Amount') }}</th>
            <th class="text-start">{{ __('Paid Amount') }}</th>
            <th class="text-start">{{ __('Due Amount') }}</th>
            <th class="text-start">{{ __('Vat Amount') }}</th>
            <th class="text-start">{{ __('Payment Type') }}</th>
            <th class="text-start">{{ __('Purchase Date') }}</th>
        </tr>
        </thead>
        <tbody>
            @foreach($purchases as $purchase)
                <tr>
                    <td>{{ ($purchases->currentPage() - 1) * $purchases->perPage() + $loop->iteration }}</td>
                    @if(auth()->user()->accessToMultiBranch())
                    <td class="text-start">{{ $purchase->branch->name ?? '' }}</td>
                    @endif
                    <td class="text-start">{{ $purchase->invoiceNumber }}</td>
                    <td class="text-start">{{ $purchase->party?->name }}</td>
                    <td class="text-start">{{ currency_format($purchase->totalAmount, currency: business_currency()) }}</td>
                    <td class="text-start">{{ currency_format($purchase->discountAmount, currency: business_currency()) }}</td>
                    <td class="text-start">{{ currency_format($purchase->paidAmount, currency: business_currency()) }}</td>
                    <td class="text-start">{{ currency_format($purchase->dueAmount, currency: business_currency()) }}</td>
                    <td class="text-start">{{ currency_format($purchase->vat_amount, currency: business_currency()) }}</td>
                    <td class="text-start">{{ $purchase->payment_type_id != null ? $purchase->payment_type->name ?? '' : $purchase->paymentType }}</td>
                    <td class="text-start">{{ formatted_date($purchase->purchaseDate) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="m-2">
    {{ $purchases->links('vendor.pagination.bootstrap-5') }}
</div>
