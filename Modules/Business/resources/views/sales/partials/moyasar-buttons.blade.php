{{-- Moyasar Payment Buttons for Sales --}}
@if(!empty($business->moyasar_setting) && !empty($business->moyasar_setting['api_key']))
    {{-- Pay Sale Due Button --}}
    @if(isset($sale) && $sale->dueAmount > 0 && !$sale->isPaid)
        <button type="button" class="btn btn-success btn-sm pay-sale-due-moyasar" 
                data-sale-id="{{ $sale->id }}" 
                data-due-amount="{{ $sale->dueAmount }}"
                title="{{ __('Pay via Moyasar') }}">
            <i class="fas fa-credit-card"></i> {{ __('Pay via Moyasar') }}
        </button>
    @endif

    {{-- Direct Sale Payment Button (for POS) --}}
    @if(isset($showDirectPayment) && $showDirectPayment)
        <button type="button" class="btn btn-primary process-sale-moyasar" 
                title="{{ __('Pay with Moyasar') }}">
            <i class="fas fa-credit-card"></i> {{ __('Pay with Moyasar') }}
        </button>
    @endif

    {{-- Invoice Payment Link --}}
    @if(isset($sale) && $sale->dueAmount > 0 && !$sale->isPaid && isset($showInvoiceLink) && $showInvoiceLink)
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle"></i>
            {{ __('Customer can pay online using this link:') }}
            <br>
            <a href="{{ route('invoice.show', $sale->uuid) }}" target="_blank" class="btn btn-link p-0">
                {{ route('invoice.show', $sale->uuid) }}
            </a>
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