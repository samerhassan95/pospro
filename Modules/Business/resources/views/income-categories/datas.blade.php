<div class="responsive-table m-0">
    <table class="table" id="datatable">
        <thead>
            <tr>
                @usercan('income-categories.delete')
                <th class="w-60">
                    <div class="d-flex align-items-center gap-3">
                        <input type="checkbox" class="select-all-delete  multi-delete">
                    </div>
                </th>
                @endusercan
                <th>{{ __('SL') }}.</th>
                <th class="text-start">{{ __('Category Name') }}</th>
                <th class="text-start">{{ __('Description') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($income_categories as $income_category)
                <tr>
                    @usercan('income-categories.delete')
                    <td class="w-60 checkbox">
                        <input type="checkbox" name="ids[]" class="delete-checkbox-item  multi-delete" value="{{ $income_category->id }}">
                    </td>
                    @endusercan
                    <td>{{ ($income_categories->currentPage() - 1) * $income_categories->perPage() + $loop->iteration }}</td>
                    <td class="text-start">{{ $income_category->categoryName }}</td>
                    <td class="text-start">{{ $income_category->categoryDescription }}</td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" {{ $income_category->status == 1 ? 'checked' : '' }} class="status" data-url="{{ route('business.income-categories.status', $income_category->id) }}">
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
                                    @usercan('income-categories.update')
                                    <a  href="#income-categories-edit-modal" data-bs-toggle="modal" class="income-categories-edit-btn"
                                    data-url="{{ route('business.income-categories.update', $income_category->id) }}"
                                    data-income-categories-name="{{ $income_category->categoryName }}" data-income-categories-description="{{ $income_category->categoryDescription }}"><i class="fal fa-pencil-alt"></i>{{__('Edit')}}</a>
                                    @endusercan
                                </li>
                                <li>
                                    @usercan('income-categories.delete')
                                    <a href="{{ route('business.income-categories.destroy', $income_category->id) }}" class="confirm-action" data-method="DELETE">
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
    {{ $income_categories->links('vendor.pagination.bootstrap-5') }}
</div>
