@foreach ($domains as $domain)
    <tr>
        @usercan('domains.delete')
        <td class="w-60 checkbox d-print-none text-start">
            <input type="checkbox" name="ids[]" class="delete-checkbox-item multi-delete" value="{{ $domain->id }}">
        </td>
        @endusercan
        <td >{{ $loop->index + 1 }}</td>
        <td>{{ $domain->domain }}</td>
        <td>
            <div class="d-flex align-items-center justify-content-center">
                <div class="badge {{ $domain->is_verified == 1 ? ' bg-success' : ' bg-danger'  }}">
                    {{ $domain->is_verified == 1 ? 'Yes' : 'No' }}
                </div>
            </div>
        </td>
        <td>
            <div class="d-flex align-items-center justify-content-center">
                <div class="badge {{ $domain->is_ssl_enabled == 1 ? ' bg-success' : ' bg-danger'  }}">
                    {{ $domain->is_ssl_enabled == 1 ? 'Yes' : 'No' }}
                </div>
            </div>
        </td>
        <td>{{ formatted_date($domain->created_at) }}</td>

        <td>
            <div class="d-flex align-items-center justify-content-center">
                @if($domain->status == 1)
                    <div class="badge bg-success">{{__('Active')}}</div>
                @elseif($domain->status == 2)
                    <div class="badge bg-danger">{{__('Inactive')}}</div>
                @else
                    <div class="badge bg-warning">{{__('Pending')}}</div>
                @endif
            </div>
        </td>

        <td class="print-d-none">
            <div class="dropdown table-action">
                <button type="button" data-bs-toggle="dropdown">
                    <i class="far fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu">
                    @usercan('domains.delete')
                    <li>
                        <a href="{{ route('business.domains.destroy', $domain->id) }}" class="confirm-action"
                            data-method="DELETE">
                            <i class="fal fa-trash-alt"></i>
                            {{ __('Delete') }}
                        </a>
                    </li>
                    @endusercan
                </ul>
            </div>
        </td>
    </tr>
@endforeach
