@php
    $running_balance = $opening_balance ?? 0;
@endphp

@foreach($cash_flows as $transaction)
    @php
        $cash_in = $transaction->type == 'credit' ? $transaction->amount : 0;
        $cash_out = $transaction->type == 'debit' ? $transaction->amount : 0;
        $running_balance += ($cash_in - $cash_out);
        
        $reference = '';
        if ($transaction->sale) {
            $reference = __('Sale') . ' #' . $transaction->sale->invoiceNumber . ' - ' . ($transaction->sale->party->name ?? __('Walk-in Customer'));
        } elseif ($transaction->purchase) {
            $reference = __('Purchase') . ' #' . $transaction->purchase->invoiceNumber . ' - ' . ($transaction->purchase->party->name ?? __('N/A'));
        } elseif ($transaction->saleReturn) {
            $reference = __('Sale Return') . ' #' . $transaction->saleReturn->id;
        } elseif ($transaction->purchaseReturn) {
            $reference = __('Purchase Return') . ' #' . $transaction->purchaseReturn->id;
        } elseif ($transaction->dueCollect) {
            $reference = __('Due Collection') . ' - ' . ($transaction->dueCollect->party->name ?? __('N/A'));
        } else {
            $reference = $transaction->note ?? __('N/A');
        }
    @endphp
    <tr>
        <td>{{ ($cash_flows->currentPage() - 1) * $cash_flows->perPage() + $loop->iteration }}</td>
        <td class="text-start">{{ \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') }}</td>
        <td class="text-start">
            @if($transaction->type == 'credit')
                <span class="badge bg-success">{{ __('Cash In') }}</span>
            @else
                <span class="badge bg-danger">{{ __('Cash Out') }}</span>
            @endif
        </td>
        <td class="text-start">{{ $reference }}</td>
        <td class="text-start">{{ $transaction->paymentType->name ?? __('N/A') }}</td>
        <td class="text-end">{{ $cash_in > 0 ? currency_format($cash_in, currency: business_currency()) : '-' }}</td>
        <td class="text-end">{{ $cash_out > 0 ? currency_format($cash_out, currency: business_currency()) : '-' }}</td>
        <td class="text-end">{{ currency_format($running_balance, currency: business_currency()) }}</td>
    </tr>
@endforeach
