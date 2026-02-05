{{-- Moyasar Payment Buttons for Purchases --}}
@if(!empty($business->moyasar_setting) && !empty($business->moyasar_setting['api_key']))
    {{-- Pay Purchase Due Button --}}
    @if(isset($purchase) && $purchase->dueAmount > 0 && !$purchase->isPaid)
        <button type="button" class="btn btn-warning btn-sm pay-purchase-due-moyasar" 
                data-purchase-id="{{ $purchase->id }}" 
                data-due-amount="{{ $purchase->dueAmount }}"
                title="{{ __('Pay via Moyasar') }}">
            <i class="fas fa-credit-card"></i> {{ __('Pay via Moyasar') }}
        </button>
    @endif

    {{-- Direct Purchase Payment Button (for POS) --}}
    @if(isset($showDirectPayment) && $showDirectPayment)
        <button type="button" class="btn btn-primary process-purchase-moyasar" 
                title="{{ __('Pay with Moyasar') }}">
            <i class="fas fa-credit-card"></i> {{ __('Pay with Moyasar') }}
        </button>
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