<table class="table table-striped table-bordered">
    <thead class="bg-primary text-white">
        <tr>
            <th>{{ __('SL') }}</th>
            <th>{{ __('Invoice No') }}</th>
            <th>{{ __('Date') }}</th>
            <th>{{ __('Customer Name') }}</th>
            <th>{{ __('Customer Phone') }}</th>
            <th>{{ __('Total Amount') }}</th>
            <th>{{ __('Paid') }}</th>
            <th>{{ __('Due') }}</th>
            <th>{{ __('Action') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($sales as $key => $sale)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $sale->invoiceNumber }}</td>
                <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                <td>{{ $sale->customer_name ?? 'Walk-in Customer' }}</td>
                <td>{{ $sale->customer_phone ?? '-' }}</td>
                <td>{{ number_format($sale->totalAmount, 2) }}</td>
                <td class="text-success">{{ number_format($sale->paidAmount, 2) }}</td>
                <td class="text-danger fw-bold">{{ number_format($sale->dueAmount, 2) }}</td>
                <td>
                    <a href="{{ route('business.walk-dues.collect', $sale->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-money-bill"></i> {{ __('Collect') }}
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center">{{ __('No walk-in customer dues found') }}</td>
            </tr>
        @endforelse
    </tbody>
    @if($sales->count() > 0)
        <tfoot>
            <tr class="bg-light">
                <th colspan="7" class="text-end">{{ __('Total Due') }}:</th>
                <th class="text-danger" colspan="2">{{ number_format($sales->sum('dueAmount'), 2) }}</th>
            </tr>
        </tfoot>
    @endif
</table>
