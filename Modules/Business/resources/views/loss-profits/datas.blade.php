<div class="responsive-table m-0">
    <table class="table" id="datatable">
        <thead>
            <tr>
                <th>{{ __('SL') }}.</th>
                <th class="text-start">{{ __('Invoice') }}</th>
                <th class="text-start">{{ __('Name') }}</th>
                <th class="text-start">{{ __('Total') }}</th>
                <th class="text-start">{{ __('Loss/Profit') }}</th>
                <th class="text-start">{{ __('Date') }}</th>
                <th class="text-start">{{ __('Status') }}</th>
                <th class="d-print-none">{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($loss_profits as $loss_profit)
                <tr>
                    <td>{{ ($loss_profits->currentPage() - 1) * $loss_profits->perPage() + $loop->iteration }}</td>
                    <td class="text-start">{{ $loss_profit->invoiceNumber }}</td>
                    <td class="text-start">{{ $loss_profit->party?->name }}</td>
                    <td class="text-start">{!! currency_format($loss_profit->totalAmount, 'icon', 2, business_currency()) !!}</td>
                    <td class="text-start">
                        @php
                            $amount = abs($loss_profit->lossProfit);
                        @endphp
                        <span
                            class="{{ $loss_profit->lossProfit < 0 ? 'text-danger' : 'text-success' }} px-2 py-1 rounded d-inline-block">
                            {!! currency_format($amount, 'icon', 2, business_currency()) !!}
                        </span>
                    </td>
                    <td class="text-start">{{ formatted_date($loss_profit->created_at) }}</td>
                    <td class="text-start">
                        <span
                            class="{{ $loss_profit->dueAmount == 0 ? 'text-success px-2 py-1 rounded' : ($loss_profit->dueAmount > 0 && $loss_profit->dueAmount < $loss_profit->totalAmount ? 'text-warning px-2 py-1 rounded' : 'text-danger px-2 py-1 rounded') }}">
                            {{ $loss_profit->dueAmount == 0 ? 'Paid' : ($loss_profit->dueAmount > 0 && $loss_profit->dueAmount < $loss_profit->totalAmount ? 'Partial Paid' : 'Unpaid') }}
                        </span>
                    </td>

                    <td class="d-print-none">
                        <div class="dropdown table-action">
                            <button type="button" data-bs-toggle="dropdown">
                                <i class="far fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                 <li>
                                    @usercan('loss-profits.read')
                                    <a href="#loss-profit-view" class="loss-profit-view" data-id="{{ $loss_profit->id }}" data-bs-toggle="modal">
                                        <i class="fal fa-eye"></i>
                                        {{ __('View') }}
                                    </a>
                                    @endusercan
                                </li>
                                <input type="hidden" value="{{ route('business.loss-profits.show', ['loss_profit' => ':id']) }}" id="loss-profit-id">

                            </ul>
                        </div>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="m-2">
    {{ $loss_profits->links('vendor.pagination.bootstrap-5') }}
</div>

