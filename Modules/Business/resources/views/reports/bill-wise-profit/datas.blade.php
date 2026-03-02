@foreach($bills as $bill)
    <tr>
        <td>{{ ($bills->currentPage() - 1) * $bills->perPage() + $loop->iteration }}</td>
        <td class="text-start">{{ $bill->invoiceNumber }}</td>
        <td class="text-start">{{ $bill->party->name ?? __('Walk-in Customer') }}</td>
        <td class="text-start">{{ \Carbon\Carbon::parse($bill->saleDate)->format('d-m-Y') }}</td>
        <td class="text-end">{!! currency_format($bill->totalAmount, currency: business_currency()) !!}</td>
        <td class="text-end">
            @if($bill->lossProfit >= 0)
                <span class="badge bg-success">{!! currency_format($bill->lossProfit, currency: business_currency()) !!}</span>
            @else
                <span class="badge bg-danger">{!! currency_format(abs($bill->lossProfit), currency: business_currency()) !!}</span>
            @endif
        </td>
        <td class="text-center">
            <a href="{{ route('business.bill-wise-profit-reports.show', $bill->id) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-eye"></i> {{ __('Details') }}
            </a>
        </td>
    </tr>
@endforeach
