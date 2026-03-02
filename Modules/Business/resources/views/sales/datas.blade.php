@foreach ($sales as $sale)
<tr>
    @usercan('sales.delete')
    <td class="w-60 checkbox">
        @if(!in_array($sale->id, $salesWithReturns))
        <input type="checkbox" name="ids[]" class="delete-checkbox-item  multi-delete" value="{{ $sale->id }}">
        @endif
    </td>
    @endusercan
    <td>{{ ($sales->currentPage() - 1) * $sales->perPage() + $loop->iteration }}</td>
    <td class="text-start">{{ formatted_date($sale->saleDate) }}</td>
    @if(auth()->user()->accessToMultiBranch())
    <td class="text-start">{{ $sale->branch->name ?? '' }}</td>
    @endif
    <td class="text-start">{{ $sale->invoiceNumber }}</td>
    <td class="text-start">{{ $sale->party->name ?? 'Guest' }}</td>
    <td class="text-start">{!! currency_format($sale->totalAmount, currency: business_currency()) !!}</td>
    <td class="text-start">{!! currency_format($sale->discountAmount, currency: business_currency()) !!}</td>
    <td class="text-start">{!! currency_format($sale->paidAmount, currency: business_currency()) !!}</td>
    <td class="text-start">{!! currency_format($sale->dueAmount, currency: business_currency()) !!}</td>
    <td class="text-start">{{ $sale->payment_type_id != null ? $sale->payment_type->name ?? '' : $sale->paymentType }}</td>
    <td>
        @if($sale->details->sum('quantities') == 0)
        <div class="paid-badge">{{ __('Returned') }}</div>
        @elseif($sale->dueAmount == 0)
        <div class="paid-badge">{{ __('Paid') }}</div>
        @elseif($sale->dueAmount > 0 && $sale->dueAmount < $sale->totalAmount)
            <div class="unpaid-badge">{{ __('Partial Paid') }}</div>
            @else
            <div class="unpaid-badge-2">{{ __('Unpaid') }}</div>
            @endif
    </td>
    <td>
        <div class="d-flex flex-column gap-1 align-items-center">
            @if($sale->zatca_status === 'REPORTED')
            <div class="paid-badge" style="background: #28a745; color: white;">{{ __('Reported') }}</div>
            @elseif($sale->zatca_status === 'REPORTING')
            <div class="unpaid-badge" style="background: #ffc107; color: black;">{{ __('Sending...') }}</div>
            @elseif($sale->zatca_status === 'FAILED' || $sale->zatca_status === 'ERROR')
            <div class="unpaid-badge-2" style="background: #dc3545; color: white;" title="{{ $sale->zatca_response['error'] ?? 'Error' }}">{{ __('Failed') }}</div>
            @else
            <span class="text-muted small">-</span>
            @endif

            <button type="button" class="btn btn-sm btn-outline-warning zatca-issues-btn"
                data-sale-id="{{ $sale->id }}"
                data-bs-toggle="modal"
                data-bs-target="#zatcaIssuesModal"
                title="{{ __('Check ZATCA Compliance Issues') }}"
                style="font-size: 11px; padding: 2px 8px; white-space: nowrap;">
                <i class="fas fa-exclamation-triangle"></i> {{ __('Issues') }}
            </button>
        </div>
    </td>

    <td class="d-print-none text-center">
        <div class="dropdown table-action">
            <button type="button" data-bs-toggle="dropdown">
                <i class="far fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu">
                @usercan('sales.read')
                <li>
                    <a target="_blank" href="{{ route('business.sales.invoice', $sale->id) }}">
                        <i class="fal fa-file-invoice"></i>
                        {{ __('Invoice') }}
                    </a>
                </li>
                @endusercan
                <li>
                    <a href="#" class="zatca-issues-btn"
                        data-sale-id="{{ $sale->id }}"
                        data-bs-toggle="modal"
                        data-bs-target="#zatcaIssuesModal">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        {{ __('ZATCA Issues') }}
                    </a>
                </li>
                @if($sale->details->sum('quantities') != 0)
                @usercan('sale-returns.read')
                <li>
                    <a href="{{ route('business.sale-returns.create', ['sale_id' => $sale->id]) }}">
                        <i class="fal fa-undo-alt"></i>
                        {{ __('Sales Return') }}
                    </a>
                </li>
                @endusercan
                @endif
                @if(!in_array($sale->id, $salesWithReturns))
                @usercan('sales.update')
                <li>
                    <a href="{{ route('business.sales.edit', $sale->id) }}">
                        <i class="fal fa-edit"></i>
                        {{ __('Edit') }}
                    </a>
                </li>
                @endusercan
                @usercan('sales.delete')
                <li>
                    <a href="{{ route('business.sales.destroy', $sale->id) }}" class="confirm-action"
                        data-method="DELETE">
                        <i class="fal fa-trash-alt"></i>
                        {{ __('Delete') }}
                    </a>
                </li>
                @endusercan
                @endif
            </ul>
        </div>
    </td>
</tr>
@endforeach
