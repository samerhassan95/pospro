<div class="responsive-table">
    <table class="table">
        <thead>
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
                    <td>{{ currency_format($sale->totalAmount, currency: business_currency()) }}</td>
                    <td>
                        @if($sale->user && $sale->user->commission_type)
                            <span class="badge bg-{{ $sale->user->commission_type == 'percentage' ? 'success' : 'info' }}">
                                {{ $sale->user->commission_type == 'percentage' ? __('Percentage') : __('Fixed Amount') }}
                            </span>
                        @else
                            <span class="badge bg-secondary">{{ __('N/A') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($sale->user && $sale->user->commission_value)
                            {{ $sale->user->commission_type == 'percentage' ? $sale->user->commission_value . '%' : currency_format($sale->user->commission_value, currency: business_currency()) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="fw-bold text-success">{{ currency_format($sale->commission ?? 0, currency: business_currency()) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="empty-state">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ __('No sales with commission found') }}</h5>
                            <p class="text-muted">{{ __('No sales have been made with commission setup or no commissions have been earned yet.') }}</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($sales->count() > 0)
            <tfoot>
                <tr class="table-total">
                    <th colspan="7" class="text-end">{{ __('Total Commission') }}:</th>
                    <th class="text-success">{{ currency_format($sales->sum('commission'), currency: business_currency()) }}</th>
                </tr>
            </tfoot>
        @endif
    </table>
</div>
