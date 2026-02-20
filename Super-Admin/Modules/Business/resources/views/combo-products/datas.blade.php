<table class="table table-striped table-bordered">
    <thead class="bg-primary text-white">
        <tr>
            <th>{{ __('SL') }}</th>
            <th>{{ __('Product Name') }}</th>
            <th>{{ __('SKU') }}</th>
            <th>{{ __('Purchase Price') }}</th>
            <th>{{ __('Quantity') }}</th>
            <th>{{ __('Stock') }}</th>
            <th>{{ __('Action') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($combos as $key => $combo)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ optional($combo->product)->productName ?? '-' }}</td>
                <td>{{ optional($combo->product)->productCode ?? '-' }}</td>
                <td>{{ number_format($combo->purchase_price, 2) }}</td>
                <td>{{ $combo->quantity }}</td>
                <td>{{ optional($combo->stock)->productStock ?? 0 }}</td>
                <td>
                    <div class="dropdown table-action">
                        <button type="button" data-bs-toggle="dropdown">
                            <i class="far fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('business.combo-products.edit', $combo->id) }}">
                                    <i class="fal fa-pencil-alt"></i>
                                    {{ __('Edit') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('business.combo-products.destroy', $combo->id) }}" class="confirm-action" data-method="DELETE">
                                    <i class="fal fa-trash-alt"></i>
                                    {{ __('Delete') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">{{ __('No combo products found') }}</td>
            </tr>
        @endforelse
    </tbody>
</table>
