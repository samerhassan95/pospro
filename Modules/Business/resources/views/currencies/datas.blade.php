<div class="responsive-table m-0">
    <table class="table" id="erp-table">
        <thead>
            <tr>
                <th>{{ __('SL') }}.</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Country Name') }}</th>
                <th>{{ __('Code') }}</th>
                <th>{{ __('Symbol') }}</th>
                <th>{{ __('Default') }}</th>
                <th class="d-print-none">{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($currencies as $currency)
                <tr>
                    <td>{{ ($currencies->currentPage() - 1) * $currencies->perPage() + $loop->iteration }}</td>
                    <td>{{ $currency->name }}</td>
                    <td>{{ $currency->country_name }}</td>
                    <td>{{ $currency->code }}</td>
                    <td>{{ $currency->symbol }}</td>
                    <td>
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="{{ ($user_currency && $currency->name == $user_currency->name) || (!$user_currency && $currency->is_default == 1) ? 'yes-badge' : 'no-badge'  }}">
                                {{ ($user_currency && $currency->name == $user_currency->name) || (!$user_currency && $currency->is_default == 1) ? 'Yes' : 'No' }}
                            </div>
                        </div>
                    </td>
                    <td class="d-print-none">
                        <div class="dropdown table-action">
                            <button type="button" data-bs-toggle="dropdown">
                                <i class="far fa-ellipsis-v"></i>
                            </button>
                            @if(!$user_currency || $user_currency->name != $currency->name)
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('business.currencies.default', ['id' => $currency->id]) }}">
                                        <i class="fas fa-adjust"></i>
                                        {{ __('Make Default') }}
                                    </a>
                                </li>
                            </ul>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="m-2">
    {{ $currencies->links('vendor.pagination.bootstrap-5') }}
</div>
