@foreach ($domains as $domain )
<tr>
    <td class="w-60 checkbox text-start">
        <label class="table-custom-checkbox">
            <input type="checkbox" class="table-hidden-checkbox checkbox-item" name="ids[]" value="{{ $domain->id }}" data-url="{{ route('admin.domains.delete-all') }}">
            <span class="table-custom-checkmark custom-checkmark"></span>
        </label>
    </td>
    <td>{{ $loop->index + 1 }}</td>
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

                @can('domains-update')
                <li>
                    <a href="{{ route('admin.domains.edit',$domain->id) }}">
                        <i class="fal fa-edit"></i>
                        {{ __('Edit') }}
                    </a>
                </li>
                @endcan

                @can('domains-delete')
                <li>
                    <a href="{{ route('admin.domains.destroy', $domain->id) }}" class="confirm-action" data-method="DELETE">
                        <i class="fal fa-trash-alt"></i>
                        {{ __('Delete') }}
                    </a>
                </li>
                @endcan

                @if($domain->status == 0 || $domain->status == 2)
                    <li>
                        <a href="#approve-modal" class="modal-approve" data-bs-toggle="modal"
                        data-bs-target="#approve-modal"
                        data-url="{{ route('admin.domains.approved', $domain->id) }}">
                            <i class="fal fa-check"></i> {{ __('Accept') }}
                        </a>
                    </li>
                @endif

                @if($domain->status == 0 || $domain->status == 1)
                    <li>
                        <a href="#reject-modal" class="modal-reject" data-bs-toggle="modal"
                        data-bs-target="#reject-modal"
                        data-url="{{ route('admin.domains.reject', $domain->id) }}">
                            <i class="fal fa-times"></i> {{ __('Reject') }}
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </td>
</tr>
@endforeach


