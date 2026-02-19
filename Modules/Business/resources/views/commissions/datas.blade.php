<div class="responsive-table">
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('SL') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Commission Type') }}</th>
                <th>{{ __('Commission Value') }}</th>
                <th class="d-print-none">{{ __('Action') }}</th>
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
                                {{ $user->commission_type == 'percentage' ? __('Percentage') : __('Fixed Amount') }}
                            </span>
                        @else
                            <span class="badge bg-secondary">{{ __('Not Set') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($user->commission_value)
                            {{ $user->commission_type == 'percentage' ? $user->commission_value . '%' : currency_format($user->commission_value, currency: business_currency()) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="d-print-none">
                        <div class="dropdown table-action">
                            <button type="button" data-bs-toggle="dropdown">
                                <svg width="14" height="4" viewBox="0 0 14 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2 0.75C2.69036 0.75 3.25 1.30964 3.25 2C3.25 2.69036 2.69036 3.25 2 3.25C1.30964 3.25 0.75 2.69036 0.75 2C0.75 1.30964 1.30964 0.75 2 0.75Z" fill="#5A6376"/>
                                    <path d="M7 0.75C7.69036 0.75 8.25 1.30964 8.25 2C8.25 2.69036 7.69036 3.25 7 3.25C6.30964 3.25 5.75 2.69036 5.75 2C5.75 1.30964 6.30964 0.75 7 0.75Z" fill="#5A6376"/>
                                    <path d="M12 0.75C12.6904 0.75 13.25 1.30964 13.25 2C13.25 2.69036 12.6904 3.25 12 3.25C11.3096 3.25 10.75 2.69036 10.75 2C10.75 1.30964 11.3096 0.75 12 0.75Z" fill="#5A6376"/>
                                </svg>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                @if($user->commission_type)
                                    @usercan('commissions.update')
                                    <a href="{{ route('business.commissions.edit', $user->id) }}" class="dropdown-item">
                                        <i class="fas fa-edit me-2"></i> {{ __('Edit Commission') }}
                                    </a>
                                    @endusercan
                                @else
                                    @usercan('commissions.create')
                                    <a href="{{ route('business.commissions.create') }}?user_id={{ $user->id }}" class="dropdown-item">
                                        <i class="fas fa-plus me-2"></i> {{ __('Set Commission') }}
                                    </a>
                                    @endusercan
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <div class="empty-state">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ __('No users found') }}</h5>
                            <p class="text-muted">{{ __('No users are available for commission setup.') }}</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
