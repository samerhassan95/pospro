@foreach($banks as $bank)
    <tr>
        @usercan('banks.delete')
        <td class="w-60 checkbox">
            <input type="checkbox" name="ids[]" class="delete-checkbox-item multi-delete" value="{{ $bank->id }}">
        </td>
        @endusercan
        <td>{{ ($banks->currentPage() - 1) * $banks->perPage() + $loop->iteration }}</td>

        <td class="text-start">{{ $bank->name }}</td>
        <td>{!! currency_format($bank->balance, currency: business_currency()) !!}</td>
        <td>
            <label class="switch">
                <input type="checkbox" {{ $bank->status == 1 ? 'checked' : '' }} class="status" data-url="{{ route('business.banks.status', $bank->id) }}">
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
                        <a href="#banks-edit-modal" data-bs-toggle="modal" class="banks-edit-btn"
                        data-url="{{ route('business.banks.update', $bank->id) }}"
                        data-banks-name="{{ $bank->name }}"
                        data-banks-status="{{ $bank->status }}"
                        data-banks-balance="{{ $bank->balance }}"
                        data-banks-opening-balance="{{ $bank->opening_balance }}"
                        data-banks-opening-date="{{ $bank->opening_date }}"
                        ><i class="fal fa-pencil-alt"></i>{{__('Edit')}}</a>
                        @endusercan
                    </li>
                    <li>
                        @usercan('banks.delete')
                        <a href="{{ route('business.banks.destroy', $bank->id) }}" class="confirm-action" data-method="DELETE">
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
