<div class="responsive-table">
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('SL') }}</th>
                <th>{{ __('Invoice No') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Customer Name') }}</th>
                <th>{{ __('Customer Phone') }}</th>
                <th>{{ __('Total Amount') }}</th>
                <th>{{ __('Paid') }}</th>
                <th>{{ __('Due') }}</th>
                <th class="d-print-none">{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sales as $key => $sale)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $sale->invoiceNumber }}</td>
                    <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                    <td>{{ $sale->customer_name ?? __('Walk-in Customer') }}</td>
                    <td>{{ $sale->customer_phone ?? '-' }}</td>
                    <td>{!! currency_format($sale->totalAmount, currency: business_currency()) !!}</td>
                    <td class="text-success">{!! currency_format($sale->paidAmount, currency: business_currency()) !!}</td>
                    <td class="text-danger fw-bold">{!! currency_format($sale->dueAmount, currency: business_currency()) !!}</td>
                    <td class="d-print-none">
                        <div class="dropdown table-action">
                            <button type="button" data-bs-toggle="dropdown">
                                <svg width="14" height="4" viewBox="0 0 14 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2 0.75C2.69036 0.75 3.25 1.30964 3.25 2C3.25 2.69036 2.69036 3.25 2 3.25C1.30964 3.25 0.75 2.69036 0.75 2C0.75 1.30964 1.30964 0.75 2 0.75Z" fill="#5A6376"/>
                                    <path d="M7 0.75C7.69036 0.75 8.25 1.30964 8.25 2C8.25 2.69036 7.69036 3.25 7 3.25C6.30964 3.25 5.75 2.69036 5.75 2C5.75 1.30964 6.30964 0.75 7 0.75Z" fill="#5A6376"/>
                                    <path d="M12 0.75C12.6904 0.75 13.25 1.30964 13.25 2C13.25 2.69036 12.6904 3.25 12 3.25C11.3096 3.25 10.75 2.69036 10.75 2C10.75 1.30964 11.3096 0.75 12 0.75Z" fill="#5A6376"/>
                                </svg>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('business.walk-dues.collect', $sale->id) }}" class="dropdown-item">
                                    <i class="fas fa-money-bill me-2"></i> {{ __('Collect Due') }}
                                </a>
                                <a href="{{ route('business.sales.show', $sale->id) }}" class="dropdown-item">
                                    <i class="fas fa-eye me-2"></i> {{ __('View Invoice') }}
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <div class="empty-state">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ __('No walk-in customer dues found') }}</h5>
                            <p class="text-muted">{{ __('All walk-in customers have cleared their dues or no sales have been made.') }}</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($sales->count() > 0)
            <tfoot>
                <tr class="table-total">
                    <th colspan="7" class="text-end">{{ __('Total Due') }}:</th>
                    <th class="text-danger" colspan="2">{!! currency_format($sales->sum('dueAmount'), currency: business_currency()) !!}</th>
                </tr>
            </tfoot>
        @endif
    </table>
</div>
