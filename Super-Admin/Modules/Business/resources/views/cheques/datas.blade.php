@foreach($cheques as $cheque)
    <tr>
        @usercan('banks.delete')
        <td class="w-60 checkbox">
            <input type="checkbox" name="ids[]" class="delete-checkbox-item multi-delete" value="{{ $cheque->id }}">
        </td>
        @endusercan
        <td>{{ ($cheques->currentPage() - 1) * $cheques->perPage() + $loop->iteration }}</td>

        <td class="text-start">{{ $cheque->name }}</td>
        <td>{{ currency_format($cheque->balance, currency: business_currency()) }}</td>
        <td>
            <label class="switch">
                <input type="checkbox" {{ $cheque->status == 1 ? 'checked' : '' }} class="status" data-url="{{ route('business.cheques.status', $cheque->id) }}">
                <span class="slider round"></span>
            </label>
        </td>
        <td class="d-print-none">
            <div class="dropdown table-action">
                <button type="button" data-bs-toggle="dropdown">
                    <i class="far fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        @usercan('banks.update')
                        <a href="#cheques-edit-modal" data-bs-toggle="modal" class="cheques-edit-btn"
                        data-url="{{ route('business.cheques.update', $cheque->id) }}"
                        data-cheques-status="{{ $cheque->status }}"
                        data-cheques-opening-balance="{{ $cheque->opening_balance }}"
                        ><i class="fal fa-pencil-alt"></i>{{__('Edit')}}</a>
                        @endusercan
                    </li>
                    <li>
                        @usercan('banks.delete')
                        <a href="{{ route('business.cheques.destroy', $cheque->id) }}" class="confirm-action" data-method="DELETE">
                            <i class="fal fa-trash-alt"></i>
                            {{ __('Delete') }}
                        </a>
                        @endusercan
                    </li>
                </ul>
            </div>
        </td>
    </tr>
@endforeach
