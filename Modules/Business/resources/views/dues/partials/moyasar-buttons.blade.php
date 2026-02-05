{{-- Moyasar Payment Buttons for Due Collections --}}
@if(!empty($business->moyasar_setting) && !empty($business->moyasar_setting['api_key']))
    {{-- Pay Due Collection Button --}}
    @if(isset($party) && $party->due > 0)
        <button type="button" class="btn btn-info btn-sm pay-due-collection-moyasar" 
                data-party-id="{{ $party->id }}" 
                data-due-amount="{{ $party->due }}"
                title="{{ __('Collect Due via Moyasar') }}">
            <i class="fas fa-credit-card"></i> {{ __('Collect via Moyasar') }}
        </button>
    @endif

    {{-- Bulk Due Collection (for multiple parties) --}}
    @if(isset($parties) && $parties->count() > 0)
        <div class="mb-3">
            <label class="form-label">{{ __('Collect Dues via Moyasar') }}</label>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>{{ __('Party') }}</th>
                            <th>{{ __('Due Amount') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($parties as $party)
                            @if($party->due > 0)
                                <tr>
                                    <td>{{ $party->name }}</td>
                                    <td>{{ currency_format($party->due, currency: business_currency()) }}</td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-xs pay-due-collection-moyasar" 
                                                data-party-id="{{ $party->id }}" 
                                                data-due-amount="{{ $party->due }}">
                                            <i class="fas fa-credit-card"></i> {{ __('Collect') }}
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@else
    {{-- Configuration Notice --}}
    @if(auth()->user()->can('moyasar-settings.read'))
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            {{ __('Moyasar payment gateway is not configured.') }}
            <a href="{{ route('business.moyasar.index') }}" class="btn btn-sm btn-warning ms-2">
                {{ __('Configure Now') }}
            </a>
        </div>
    @endif
@endif