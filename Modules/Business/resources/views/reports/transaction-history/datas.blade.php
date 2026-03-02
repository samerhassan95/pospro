<div class="responsive-table m-0">
    <table class="table" id="datatable">
        <thead>
        <tr>
            <th>{{ __('SL') }}.</th>
            <th class="text-start">{{ __('Date') }}</th>
            <th class="text-start">{{ __('Reference') }}</th>
            <th class="text-start">{{ __('Party Name') }}</th>
            <th class="text-start">{{ __('Party Type') }}</th>
            <th class="text-start">{{ __('Total Due') }}</th>
            <th class="text-start">{{ __('Pay Due Amount') }}</th>
            <th class="text-start">{{ __('Payment Type') }}</th>
        </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transcation)
                <tr>
                    <td>{{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}</td>
                    <td class="text-start">{{ formatted_date($transcation->paymentDate) }}</td>
                    <td class="text-start"><a href="{{ route('business.collect.dues.invoice', $transcation->party_id) }}" class="text-primary" target="_blank">{{ $transcation->invoiceNumber }}</a></td>
                    <td class="text-start">{{ $transcation->party?->name }}</td>
                    <td class="text-start">{{ $transcation->party?->type }}</td>
                    <td class="text-start">{!! currency_format($transcation->totalDue, currency: business_currency()) !!}</td>
                    <td class="text-start">{!! currency_format($transcation->payDueAmount, currency: business_currency()) !!}</td>
                    <td class="text-start">{{ $transcation->payment_type_id != null ? $transcation->payment_type->name ?? '' : $transcation->paymentType }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="m-2">
    {{ $transactions->links('vendor.pagination.bootstrap-5') }}
</div>
