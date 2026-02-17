@foreach($cashes as $cash)
    <tr>
        @usercan('banks.delete')
        <td class="w-60 checkbox">
            <input type="checkbox" name="ids[]" class="delete-checkbox-item multi-delete" value="{{ $cash->id }}">
        </td>
        @endusercan
        <td>{{ ($cashes->currentPage() - 1) * $cashes->perPage() + $loop->iteration }}</td>

        <td class="text-start">{{ $cash->name }}</td>
        <td>{{ currency_format($cash->balance, currency: business_currency()) }}</td>
        <td>
            <label class="switch">
                <input type="checkbox" {{ $cash->status == 1 ? 'checked' : '' }} class="status" data-url="{{ route('business.cashes.status', $cash->id) }}">
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
                        <a href="#cashes-edit-modal" data-bs-toggle="modal" class="cashes-edit-btn"
                        data-url="{{ route('business.cashes.update', $cash->id) }}"
                        data-cashes-status="{{ $cash->status }}"
                        data-cashes-opening-balance="{{ $cash->opening_balance }}"
                        ><i class="fal fa-pencil-alt"></i>{{__('Edit')}}</a>
                        @endusercan
                    </li>
                    <li>
                        @usercan('banks.delete')
                        <a href="{{ route('business.cashes.destroy', $cash->id) }}" class="confirm-action" data-method="DELETE">
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
