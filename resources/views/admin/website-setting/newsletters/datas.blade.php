<div class="responsive-table m-0">
    <table class="table" id="datatable">
        <thead>
            <tr>
                @can('newsletters-delete')
                    <th class="w-60">
                        <div class="d-flex align-items-center gap-3" >
                            <input type="checkbox" class="select-all-delete multi-delete">
                        </div>
                    </th>
                @endcan
                <th>{{ __('SL') }}.</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Create At') }}</th>
                <th>{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($newsletters as $newsletter )
                <tr>
                    @can('newsletters-delete')
                        <td class="checkbox text-start">
                            <input type="checkbox" name="ids[]" class="delete-checkbox-item multi-delete" value="{{ $newsletter->id }}" data-url="{{ route('admin.newsletters.delete-all') }}">
                        </td>
                    @endcan
                    <td>{{ ($newsletters->currentPage() - 1) * $newsletters->perPage() + $loop->iteration }}</td>
                    <td>{{ $newsletter->email }}</td>
                    <td>{{ formatted_date($newsletter->created_at) }}</td>
                    <td class="d-print-none">
                        <div class="dropdown table-action">
                            <button type="button" data-bs-toggle="dropdown">
                                <i class="far fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                @can('newsletters-delete')
                                    <li>
                                        <a href="{{ route('admin.newsletters.destroy', $newsletter->id) }}" class="confirm-action" data-method="DELETE">
                                            <i class="fal fa-trash-alt"></i>
                                            {{ __('Delete') }}
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="m-2">
    {{ $newsletters->links('vendor.pagination.bootstrap-5') }}
</div>
