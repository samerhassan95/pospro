{{-- B2B Additional Fields Modal --}}
<div class="modal fade" id="b2bAdditionalFieldsModal" tabindex="-1" aria-labelledby="b2bAdditionalFieldsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-id" id="b2bAdditionalFieldsModalLabel">
                    <i class="fas fa-file-invoice"></i> {{ __('B2B Invoice Additional Information') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    {{-- Supply Date --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label">{{ __('Supply Date') }} / تاريخ التوريد</label>
                        <input type="date" name="supply_date" id="supply_date" class="form-control" value="{{ date('Y-m-d') }}">
                        <small class="text-muted">{{ __('Date when goods/services were supplied') }}</small>
                    </div>

                    {{-- PO Number --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label">{{ __('Purchase Order Number') }} / رقم أمر الشراء</label>
                        <input type="text" name="po_number" id="po_number" class="form-control" placeholder="{{ __('e.g., PO-2024-001') }}">
                        <small class="text-muted">{{ __('Customer purchase order reference') }}</small>
                    </div>

                    {{-- Contract Number --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label">{{ __('Contract Number') }} / رقم العقد</label>
                        <input type="text" name="contract_number" id="contract_number" class="form-control" placeholder="{{ __('e.g., CNT-2024-001') }}">
                        <small class="text-muted">{{ __('Optional - Contract reference number') }}</small>
                    </div>

                    {{-- Payment Terms --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label">{{ __('Payment Terms') }} / شروط الدفع</label>
                        <select name="payment_terms" id="payment_terms" class="form-control">
                            <option value="">{{ __('Select Payment Terms') }}</option>
                            <option value="Immediate">{{ __('Immediate Payment') }} / فوري</option>
                            <option value="Net 7">{{ __('Net 7 Days') }} / 7 أيام</option>
                            <option value="Net 15">{{ __('Net 15 Days') }} / 15 يوم</option>
                            <option value="Net 30">{{ __('Net 30 Days') }} / 30 يوم</option>
                            <option value="Net 45">{{ __('Net 45 Days') }} / 45 يوم</option>
                            <option value="Net 60">{{ __('Net 60 Days') }} / 60 يوم</option>
                            <option value="Net 90">{{ __('Net 90 Days') }} / 90 يوم</option>
                            <option value="Custom">{{ __('Custom Terms') }} / شروط مخصصة</option>
                        </select>
                        <small class="text-muted">{{ __('Payment due period') }}</small>
                    </div>

                    {{-- Payment Means --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label">{{ __('Payment Means') }} / طريقة الدفع</label>
                        <select name="payment_means" id="payment_means" class="form-control">
                            <option value="">{{ __('Select Payment Means') }}</option>
                            <option value="Cash">{{ __('Cash') }} / نقدي</option>
                            <option value="Bank Transfer">{{ __('Bank Transfer') }} / تحويل بنكي</option>
                            <option value="Credit Card">{{ __('Credit Card') }} / بطاقة ائتمان</option>
                            <option value="Debit Card">{{ __('Debit Card') }} / بطاقة مدين</option>
                            <option value="Cheque">{{ __('Cheque') }} / شيك</option>
                            <option value="Credit">{{ __('On Credit') }} / آجل</option>
                            <option value="Other">{{ __('Other') }} / أخرى</option>
                        </select>
                        <small class="text-muted">{{ __('Method of payment') }}</small>
                    </div>

                    {{-- Shipping Address Section --}}
                    <div class="col-lg-12 mt-3">
                        <h6 class="text-primary border-bottom pb-2">
                            <i class="fas fa-shipping-fast"></i> {{ __('Shipping Address') }} / عنوان الشحن
                        </h6>
                        <p class="text-muted small">{{ __('Fill if different from billing address') }}</p>
                    </div>

                    {{-- Shipping Address Line 1 --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label">{{ __('Address Line 1') }}</label>
                        <input type="text" name="shipping_address_line1" id="shipping_address_line1" class="form-control" placeholder="{{ __('Street, Building') }}">
                    </div>

                    {{-- Shipping Address Line 2 --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label">{{ __('Address Line 2') }}</label>
                        <input type="text" name="shipping_address_line2" id="shipping_address_line2" class="form-control" placeholder="{{ __('District, Additional Info') }}">
                    </div>

                    {{-- Shipping City --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label">{{ __('City') }} / المدينة</label>
                        <input type="text" name="shipping_city" id="shipping_city" class="form-control" placeholder="{{ __('Enter City') }}">
                    </div>

                    {{-- Shipping Postal Code --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label">{{ __('Postal Code') }} / الرمز البريدي</label>
                        <input type="text" name="shipping_postal_code" id="shipping_postal_code" class="form-control" placeholder="{{ __('Enter Postal Code') }}">
                    </div>

                    {{-- Info Alert --}}
                    <div class="col-lg-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>{{ __('Note') }}:</strong> {{ __('These fields are optional but recommended for B2B invoices to ensure ZATCA compliance.') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> {{ __('Close') }}
                </button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="fas fa-check"></i> {{ __('Save') }}
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Button to open modal (add this near the submit button in your sale form) --}}
<button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#b2bAdditionalFieldsModal" id="b2bAdditionalFieldsBtn" style="display: none;">
    <i class="fas fa-plus-circle"></i> {{ __('B2B Additional Info') }}
</button>

<script>
// Show/hide B2B additional fields button based on party type
document.addEventListener('DOMContentLoaded', function() {
    const partySelect = document.querySelector('select[name="party_id"]');
    const b2bBtn = document.getElementById('b2bAdditionalFieldsBtn');
    
    if (partySelect && b2bBtn) {
        partySelect.addEventListener('change', function() {
            // Check if selected party is B2B type
            const selectedOption = this.options[this.selectedIndex];
            const partyType = selectedOption.getAttribute('data-zatca-type');
            
            if (partyType === 'b2b') {
                b2bBtn.style.display = 'inline-block';
            } else {
                b2bBtn.style.display = 'none';
            }
        });
    }
});
</script>
