@foreach($transactions as $transaction)
    <tr>
        <td>{{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}</td>
        <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') }}</td>
        <td>{{ $transaction->fromBank->name ?? __('N/A') }}</td>
        <td>{{ $transaction->toBank->name ?? __('N/A') }}</td>
        <td>
            @if($transaction->transaction_type == 'bank_to_bank')
                <span class="badge bg-info">{{ __('Transfer') }}</span>
            @elseif($transaction->transaction_type == 'bank_to_cash')
                <span class="badge bg-warning">{{ __('Bank to Cash') }}</span>
            @else
                <span class="badge bg-primary">{{ ucfirst($transaction->type) }}</span>
            @endif
        </td>
        <td>{!! currency_format($transaction->amount, currency: business_currency()) !!}</td>
        <td class="text-start">{{ $transaction->note }}</td>
        <td>
            @usercan('banks.delete')
            <a href="{{ route('business.bank-transactions.destroy', $transaction->id) }}" class="confirm-action" data-method="DELETE">
                <i class="fal fa-trash-alt text-danger"></i>
            </a>
            @endusercan
        </td>
    </tr>
@endforeach
