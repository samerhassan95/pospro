<div class="responsive-table m-0">
    <table class="table" id="datatable">
        <thead>
            <tr>
                <th> {{ __('SL') }}. </th>
                <th> {{ __('Date & Time') }} </th>
                <th> {{ __('Name') }} </th>
                <th> {{ __('Subscription Plan') }} </th>
                <th> {{ __('Duration') }} </th>
                <th> {{ __('Expired Date') }} </th>
                <th> {{ __('Total Earning') }} </th>
                <th> {{ __('Status') }} </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $business)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ formatted_date($business->created_at) }}</td>
                    <td>{{ $business->companyName }}</td>
                    <td class="text-center">
                    @if ($business->enrolled_plan?->plan?->subscriptionName == 'Free')
                    <span
                    class="free-badge">{{ $business->enrolled_plan?->plan?->subscriptionName }}</span>
                    @elseif($business->enrolled_plan?->plan?->subscriptionName == 'Premium')
                    <span
                        class="premium-badge">{{ $business->enrolled_plan?->plan?->subscriptionName }}</span>
                            @elseif($business->enrolled_plan?->plan?->subscriptionName == 'Standard')
                    <span
                        class="standard-badge">{{ $business->enrolled_plan?->plan?->subscriptionName }}</span>
                            @else
                    @endif
                    </td>
                    <td>{{ remaining_days($business->will_expire) }}</td>
                    <td>{{ formatted_date($business->will_expire) }}</td>
                    <td>550$</td>
                    <td class="text-center">
                        <div class="{{ $business->status == 1 ? 'active-status' : 'dective-status' }}">
                            {{ $business->status == 1 ? 'Active' : 'Inactive' }}
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="m-2">
    {{ $items->links('vendor.pagination.bootstrap-5') }}
</div>
