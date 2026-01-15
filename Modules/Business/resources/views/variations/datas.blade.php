<div class="responsive-table m-0">
    <table class="table" id="datatable">
        <thead>
            <tr>
                <th class="w-60 d-print-none">
                    <div class="d-flex align-items-center gap-3">
                        <input type="checkbox" class="select-all-delete multi-delete">
                    </div>
                </th>
                <th> {{ __('SL') }}. </th>
                <th> {{ __('Variation') }} </th>
                <th> {{ __('Values') }} </th>
                <th> {{ __('Status') }}</th>
                <th> {{ __('Action') }} </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($variations as $variation)
                <tr>
                    <td class="w-60 checkbox d-print-none">
                        <input type="checkbox" name="ids[]" class="delete-checkbox-item  multi-delete" value="{{ $variation->id }}">
                    </td>
                    <td>{{ ($variations->currentPage() - 1) * $variations->perPage() + $loop->iteration }}</td>

                    <td>{{ $variation->name }}</td>
                    <td>{{ implode(', ', $variation->values) }}</td>

                    <td>
                        <label class="switch">
                            <input type="checkbox" {{ $variation->status == 1 ? 'checked' : '' }} class="status"
                                data-url="{{ route('business.variations.status', $variation->id) }}">
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
                                    <a href="#variations-edit-modal" data-bs-toggle="modal" class="variations-edit-btn"
                                        data-url="{{ route('business.variations.update', $variation->id) }}"
                                        data-variation-name="{{ $variation->name }}"
                                        data-variation-values='@json($variation->values)'>
                                        <i class="fal fa-edit"></i>{{ __('Edit') }}
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('business.variations.destroy', $variation->id) }}" class="confirm-action"
                                        data-method="DELETE">
                                        <i class="fal fa-trash-alt"></i>
                                        {{ __('Delete') }}
                                    </a>
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
    {{ $variations->links('vendor.pagination.bootstrap-5') }}
</div>
