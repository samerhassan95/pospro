<table class="table table-striped table-bordered">
    <thead class="bg-primary text-white">
        <tr>
            <th>{{ __('SL') }}</th>
            <th>{{ __('Invoice No') }}</th>
            <th>{{ __('Date') }}</th>
            <th>{{ __('Sales Person') }}</th>
            <th>{{ __('Total Amount') }}</th>
            <th>{{ __('Commission Type') }}</th>
            <th>{{ __('Commission Value') }}</th>
            <th>{{ __('Commission Amount') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($sales as $key => $sale)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $sale->invoiceNumber }}</td>
                <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                <td>{{ $sale->user->name ?? '-' }}</td>
                <td>{{ number_format($sale->totalAmount, 2) }}</td>
                <td>
                    <span class="badge bg-{{ $sale->user->commission_type == 'percentage' ? 'success' : 'info' }}">
                        {{ ucfirst($sale->user->commission_type ?? 'N/A') }}
                    </span>
                </td>
                <td>
                    {{ $sale->user->commission_type == 'percentage' ? $sale->user->commission_value . '%' : number_format($sale->user->commission_value ?? 0, 2) }}
                </td>
                <td class="fw-bold text-success">{{ number_format($sale->commission ?? 0, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center">{{ __('No sales with commission found') }}</td>
            </tr>
        @endforelse
    </tbody>
    @if($sales->count() > 0)
        <tfoot>
            <tr class="bg-light">
                <th colspan="7" class="text-end">{{ __('Total Commission') }}:</th>
                <th class="text-success">{{ number_format($sales->sum('commission'), 2) }}</th>
            </tr>
        </tfoot>
    @endif
</table>
