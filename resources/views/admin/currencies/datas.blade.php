<div class="responsive-table m-0">
    <table class="table" id="erp-table">
        <thead>
            <tr>
                <th>
                    <div class="d-flex align-items-center gap-1">
                        <label class="table-custom-checkbox">
                            <input type="checkbox" class="table-hidden-checkbox selectAllCheckbox">
                            <span class="table-custom-checkmark custom-checkmark"></span>
                        </label>
                        <i class="fal fa-trash-alt delete-selected"></i>
                    </div>
                </th>
                <th>{{ __('SL') }}.</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Code') }}</th>
                <th>{{ __('Rate') }}</th>
                <th>{{ __('Symbol') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Default') }}</th>
                <th class="d-print-none">{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($currencies as $currency)
                <tr>
                    <td class="w-60 checkbox text-start">
                        <label class="table-custom-checkbox">
                            <input type="checkbox" name="ids[]" class="table-hidden-checkbox checkbox-item"
                                value="{{ $currency->id }}" data-url="{{ route('admin.currencies.delete-all') }}">
                            <span class="table-custom-checkmark custom-checkmark"></span>
                        </label>
                        <i></i>
                    </td>

                    <td>{{ ($currencies->currentPage() - 1) * $currencies->perPage() + $loop->iteration }} <i class="{{ request('id') == $currency->id ? 'fas fa-bell text-red' : '' }}"></i>
                    </td>
                    <td>{{ $currency->name }}</td>
                    <td>{{ $currency->code }}</td>
                    <td>{{ $currency->rate }}</td>
                    <td>
                        @if(trim(strtoupper($currency->code)) === 'SAR')
                             <span class="currency-svg" style="display:inline-block;vertical-align:middle;">
                                <svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg"> <g clip-path="url(#clip0_price_5-1_admin)"> <path d="M6.68122 10.6309C6.48962 11.0558 6.36297 11.5168 6.31445 12.0003L10.369 11.1384C10.5606 10.7137 10.6872 10.2525 10.7358 9.76904L6.68122 10.6309Z" fill="#298000"></path> <path d="M10.3691 8.55619C10.5607 8.13144 10.6873 7.67031 10.7359 7.18683L7.57749 7.85857V6.56725L10.369 5.97403C10.5606 5.54929 10.6873 5.08815 10.7358 4.60467L7.57739 5.27584V0.631863C7.09343 0.903594 6.66363 1.2653 6.31425 1.69195V5.54441L5.05111 5.8129V0.000244141C4.56715 0.27188 4.13735 0.633678 3.78797 1.06033V6.08129L0.961685 6.68186C0.770089 7.1066 0.643345 7.56773 0.594729 8.05122L3.78797 7.3726V8.99879L0.365788 9.72601C0.174192 10.1508 0.0475433 10.6119 -0.000976562 11.0954L3.58109 10.3341C3.87269 10.2735 4.12331 10.1011 4.28625 9.86384L4.94318 8.8899V8.88971C5.01138 8.78895 5.05111 8.66746 5.05111 8.53661V7.10412L6.31425 6.83564V9.41827L10.369 8.55599L10.3691 8.55619Z" fill="#298000"></path> </g> <defs> <clipPath id="clip0_price_5-1_admin"> <rect width="10.7368" height="12" fill="white"></rect> </clipPath> </defs> </svg>
                             </span>
                        @else
                            {{ $currency->symbol }}
                        @endif
                    </td>
                    <td>
                        <div class="{{ $currency->status == 1 ? 'badge bg-success' : 'badge bg-danger' }}">
                            {{ $currency->status == 1 ? 'Active' : 'Inactive' }}
                        </div>
                    </td>
                    <td>
                        <div class="{{ $currency->is_default == 1 ? 'badge bg-success' : 'badge bg-danger' }}">
                            {{ $currency->is_default == 1 ? 'Yes' : 'No' }}
                        </div>
                    </td>
                    <td class="d-print-none">
                        <div class="dropdown table-action">
                            <button type="button" data-bs-toggle="dropdown">
                                <i class="far fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">

                                @if ($currency->is_default)
                                    @can('currencies-update')
                                        <li>
                                            <a href="{{ route('admin.currencies.edit', $currency->id) }}">
                                                <i class="fal fa-pencil-alt"></i>
                                                {{ __('Edit') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.currencies.default', ['id' => $currency->id]) }}">
                                                <i class="fas fa-adjust"></i>
                                                {{ __('Make Default') }}
                                            </a>
                                        </li>
                                    @endcan
                                @else
                                    @can('currencies-update')
                                        <li>
                                            <a href="{{ route('admin.currencies.edit', $currency->id) }}">
                                                <i class="fal fa-pencil-alt"></i>
                                                {{ __('Edit') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.currencies.default', ['id' => $currency->id]) }}">
                                                <i class="fas fa-adjust"></i>
                                                {{ __('Make Default') }}
                                            </a>
                                        </li>
                                    @endcan

                                    @can('currencies-delete')
                                        <li>
                                            <a href="{{ route('admin.currencies.destroy', $currency->id) }}"
                                                class="confirm-action" data-method="DELETE">
                                                <i class="fal fa-trash-alt"></i>
                                                {{ __('Delete') }}
                                            </a>
                                        </li>
                                    @endcan
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div>
    {{ $currencies->links('vendor.pagination.bootstrap-5') }}
</div>
