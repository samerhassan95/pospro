<div class="responsive-table m-0">
    <table class="table" id="erp-table">
        <thead>
            <tr>
                <th>@lang('SL.')</th>
                <th>@lang('Message')</th>
                <th>@lang('Created At')</th>
                <th>@lang('Read At')</th>
                <th>@lang('Action')</th>
            </tr>
        </thead>
        <tbody class="searchResults">
            @foreach ($notifications as $notification)
                <tr>
                    <td>{{ $loop->index+1 }}</td>
                    <td>{{ $notification->data['message'] ?? '' }}</td>
                    <td>{{ formatted_date($notification->created_at, 'd M Y - H:i A') }}</td>
                    <td>{{ formatted_date($notification->read_at, 'd M Y - H:i A') }}</td>
                    <td>
                        <div class="dropdown table-action">
                            <button type="button" data-bs-toggle="dropdown">
                                <i class="far fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('admin.notifications.mtView', $notification->id) }}"><i class="fas fa-eye"></i> @lang('View')</a>
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
    {{ $notifications->links('vendor.pagination.bootstrap-5') }}
</div>
