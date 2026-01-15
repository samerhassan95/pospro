<div class="responsive-table vatlist-body mt-3">
    <table class="table" id="datatable">
        <thead>
            <tr>
                @usercan('vats.delete')
                <th class="w-60">
                    <div class="d-flex align-items-center gap-3">
                        <input type="checkbox" class="select-all-delete multi-delete">
                    </div>
                </th>
                @endusercan
                <th class="w-60">{{ __('SL') }}.</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Tax Rate') }}</th>
                <th>{{ __('Status') }}</th>
                <th class="text-center">{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($vats as $vat)
                <tr>
                    @usercan('vats.delete')
                    <td class="w-60 checkbox">
                        <input type="checkbox" name="ids[]" class="delete-checkbox-item multi-delete" value="{{ $vat->id }}">
                    </td>
                    @endusercan
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $vat->name }}</td>
                    <td class="text-dark fw-bold">{{ $vat->rate }}%</td>
                    <td class="text-center w-150">
                        <label class="switch">
                            <input type="checkbox" {{ $vat->status == 1 ? 'checked' : '' }} class="status"
                                data-url="{{ route('business.vats.status', $vat->id) }}">
                            <span class="slider round"></span>
                        </label>
                    </td>
                    <td>
                        <div class="dropdown table-action">
                            <button type="button" data-bs-toggle="dropdown"><i class="far fa-ellipsis-v"></i></button>
                            <ul class="dropdown-menu">
                                <li>
                                    @usercan('vats.update')
                                    <a href="#vat-edit-modal" class="vat-edit-btn" data-bs-toggle="modal"
                                        data-url="{{ route('business.vats.update', $vat->id) }}"
                                        data-vat-name="{{ $vat->name }}" data-vat-code="{{ $vat->code }}"
                                        data-vat-rate="{{ $vat->vat_rate }}" data-new-vat-rate="{{ $vat->rate }}">
                                        <i class="fal fa-edit"></i>
                                        {{ __('Edit') }}
                                    </a>
                                    @endusercan
                                </li>

                                <li>
                                    @usercan('vats.delete')
                                    <a href="{{ route('business.vats.destroy', $vat->id) }}" class="confirm-action"
                                        data-method="DELETE">
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
        </tbody>
    </table>
</div>
<div class="m-2">
    {{ $vats->links('vendor.pagination.bootstrap-5') }}
</div>
