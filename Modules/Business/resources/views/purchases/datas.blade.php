<div class="responsive-table m-0">
    <table class="table" id="datatable">
        <thead>
            <tr>
                @usercan('purchases.delete')
                <th class="w-60">
                    <div class="d-flex align-items-center gap-3">
                        <input type="checkbox" class="select-all-delete multi-delete ">
                    </div>
                </th>
                @endusercan
                <th>{{ __('SL') }}.</th>
                <th class="text-start">{{ __('Date') }}</th>
                @if(auth()->user()->accessToMultiBranch())
                <th class="text-start">{{ __('Branch') }}</th>
                @endif
                <th class="text-start">{{ __('Invoice No') }}</th>
                <th class="text-start">{{ __('Party Name') }}</th>
                <th class="text-start">{{ __('Total') }}</th>
                <th class="text-start">{{ __('Discount') }}</th>
                <th class="text-start">{{ __('Paid') }}</th>
                <th class="text-start">{{ __('Due') }}</th>
                <th class="text-start">{{ __('Payment') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchases as $purchase)
                <tr>
                    @usercan('purchases.delete')
                    <td class="w-60 checkbox">
                        <input type="checkbox" name="ids[]" class="delete-checkbox-item  multi-delete" value="{{ $purchase->id }}">
                    </td>
                    @endusercan
                    <td>{{ ($purchases->currentPage() - 1) * $purchases->perPage() + $loop->iteration }}</td>
                    <td class="text-start">{{ formatted_date($purchase->purchaseDate) }}</td>
                    @if(auth()->user()->accessToMultiBranch())
                    <td class="text-start">{{ $purchase->branch->name ?? '' }}</td>
                    @endif
                    <td class="text-start">{{ $purchase->invoiceNumber }}</td>
                    <td class="text-start">{{ $purchase->party->name }}</td>
                    <td class="text-start">{{ currency_format($purchase->totalAmount, currency: business_currency()) }}</td>
                    <td class="text-start">{{ currency_format($purchase->discountAmount, currency: business_currency()) }}</td>
                    <td class="text-start">{{ currency_format($purchase->paidAmount, currency: business_currency()) }}</td>
                    <td class="text-start">{{ currency_format($purchase->dueAmount, currency: business_currency()) }}</td>
                    <td class="text-start">{{ $purchase->payment_type_id != null ? $purchase->payment_type->name ?? '' : $purchase->paymentType }}</td>
                    <td>
                        @if($purchase->details->sum('quantities') == 0)
                            <div class="paid-badge">{{ __('Returned') }}</div>
                        @elseif($purchase->dueAmount == 0)
                            <div class="paid-badge">{{ __('Paid') }}</div>
                        @elseif($purchase->dueAmount > 0 && $purchase->dueAmount < $purchase->totalAmount)
                            <div class="unpaid-badge">{{ __('Partial Paid') }}</div>
                        @else
                            <div class="unpaid-badge-2">{{ __('Unpaid') }}</div>
                        @endif
                    </td>
                    <td class="d-print-none">
                        <div class="dropdown table-action">
                            <button type="button" data-bs-toggle="dropdown">
                                <i class="far fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                @usercan('purchases.read')
                                <li>
                                    <a target="_blank" href="{{ route('business.purchases.invoice', $purchase->id) }}">
                                        <img src="{{ asset('assets/images/icons/Invoic.svg') }}" alt="" >
                                        {{ __('Invoice') }}
                                    </a>
                                </li>
                                @endusercan

                                @usercan('purchase-returns.read')
                                <li>
                                    <a href="{{ route('business.purchase-returns.create', ['purchase_id' => $purchase->id]) }}">
                                        <i class="fal fa-undo-alt"></i>
                                        {{ __('Purchase Return') }}
                                    </a>
                                </li>
                                @endusercan

                                @if(!in_array($purchase->id, $purchasesWithReturns))
                                  @usercan('purchases.read')
                                    <li>
                                        <a href="{{ route('business.purchases.edit', $purchase->id) }}">
                                            <i class="fal fa-edit"></i>
                                            {{ __('Edit') }}
                                        </a>
                                    </li>
                                    @endusercan

                                    @usercan('purchases.delete')
                                    <li>
                                        <a href="{{ route('business.purchases.destroy', $purchase->id) }}" class="confirm-action"
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
        </tbody>
    </table>
</div>
<div class="m-2">
    {{ $purchases->links('vendor.pagination.bootstrap-5') }}
</div>
