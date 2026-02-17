<table class="table table-striped table-bordered">
    <thead class="bg-primary text-white">
        <tr>
            <th>{{ __('SL') }}</th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Email') }}</th>
            <th>{{ __('Commission Type') }}</th>
            <th>{{ __('Commission Value') }}</th>
            <th>{{ __('Action') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($users as $key => $user)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->commission_type)
                        <span class="badge bg-{{ $user->commission_type == 'percentage' ? 'success' : 'info' }}">
                            {{ ucfirst($user->commission_type) }}
                        </span>
                    @else
                        <span class="badge bg-secondary">{{ __('Not Set') }}</span>
                    @endif
                </td>
                <td>
                    @if($user->commission_value)
                        {{ $user->commission_type == 'percentage' ? $user->commission_value . '%' : number_format($user->commission_value, 2) }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($user->commission_type)
                        <a href="{{ route('business.commissions.edit', $user->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                    @else
                        <a href="{{ route('business.commissions.create') }}?user_id={{ $user->id }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> {{ __('Set') }}
                        </a>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">{{ __('No users found') }}</td>
            </tr>
        @endforelse
    </tbody>
</table>
