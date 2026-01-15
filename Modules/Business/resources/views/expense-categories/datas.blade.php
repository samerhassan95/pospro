<div class="responsive-table m-0">
    <table class="table" id="datatable">
        <thead>
            <tr>
                @usercan('expense-categories.delete')
                <th class="w-60">
                    <div class="d-flex align-items-center gap-3">
                        <input type="checkbox" class="select-all-delete multi-delete">
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
            @foreach($expense_categories as $expense_category)
                <tr>
                    @usercan('expense-categories.delete')
                    <td class="w-60 checkbox">
                        <input type="checkbox" name="ids[]" class="delete-checkbox-item multi-delete" value="{{ $expense_category->id }}">
                    </td>
                    @endusercan
                    <td>{{ ($expense_categories->currentPage() - 1) * $expense_categories->perPage() + $loop->iteration }}</td>
                    <td class="text-start">{{ $expense_category->categoryName }}</td>
                    <td class="text-start">{{ $expense_category->categoryDescription }}</td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" {{ $expense_category->status == 1 ? 'checked' : '' }} class="status" data-url="{{ route('business.expense-categories.status', $expense_category->id) }}">
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
                                    @usercan('expense-categories.update')
                                    <a href="#expense-categories-edit-modal" data-bs-toggle="modal" class="expense-categories-edit-btn"
                                    data-url="{{ route('business.expense-categories.update', $expense_category->id) }}"
                                    data-expense-categories-name="{{ $expense_category->categoryName }}" data-expense-categories-description="{{ $expense_category->categoryDescription }}"><i class="fal fa-pencil-alt"></i>{{__('Edit')}}</a>
                                    @endusercan
                                </li>
                                <li>
                                    @usercan('expense-categories.delete')
                                    <a href="{{ route('business.expense-categories.destroy', $expense_category->id) }}" class="confirm-action" data-method="DELETE">
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
    {{ $expense_categories->links('vendor.pagination.bootstrap-5') }}
</div>
