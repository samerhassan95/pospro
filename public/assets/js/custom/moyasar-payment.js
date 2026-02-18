/**
 * Moyasar Payment Integration for POS System
 */

class MoyasarPayment {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Pay Sale Due
        $(document).on('click', '.pay-sale-due-moyasar', (e) => {
            e.preventDefault();
            const saleId = $(e.target).data('sale-id');
            const dueAmount = $(e.target).data('due-amount');
            this.showPaymentModal('sale', saleId, dueAmount);
        });

        // Pay Purchase Due
        $(document).on('click', '.pay-purchase-due-moyasar', (e) => {
            e.preventDefault();
            const purchaseId = $(e.target).data('purchase-id');
            const dueAmount = $(e.target).data('due-amount');
            this.showPaymentModal('purchase', purchaseId, dueAmount);
        });

        // Pay Due Collection
        $(document).on('click', '.pay-due-collection-moyasar', (e) => {
            e.preventDefault();
            const partyId = $(e.target).data('party-id');
            const dueAmount = $(e.target).data('due-amount');
            this.showDueCollectionModal(partyId, dueAmount);
        });

        // Process Sale with Moyasar
        $(document).on('click', '.process-sale-moyasar', (e) => {
            e.preventDefault();
            this.processSaleWithMoyasar();
        });

        // Process Purchase with Moyasar
        $(document).on('click', '.process-purchase-moyasar', (e) => {
            e.preventDefault();
            this.processPurchaseWithMoyasar();
        });
    }

    showPaymentModal(type, id, dueAmount) {
        const modalHtml = `
            <div class="modal fade" id="moyasarPaymentModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${__('Pay via Moyasar')}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="moyasarPaymentForm">
                                <div class="mb-3">
                                    <label class="form-label">${__('Due Amount')}</label>
                                    <input type="text" class="form-control" value="${dueAmount} SAR" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">${__('Payment Amount')}</label>
                                    <input type="number" name="amount" class="form-control" 
                                           max="${dueAmount}" min="0.01" step="0.01" value="${dueAmount}" required>
                                </div>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    ${__('You will be redirected to Moyasar secure payment page')}
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">${__('Cancel')}</button>
                            <button type="button" class="btn btn-primary" onclick="moyasarPayment.processPayment('${type}', ${id})">${__('Pay Now')}</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal
        $('#moyasarPaymentModal').remove();
        
        // Add new modal
        $('body').append(modalHtml);
        $('#moyasarPaymentModal').modal('show');
    }

    showDueCollectionModal(partyId, dueAmount) {
        const modalHtml = `
            <div class="modal fade" id="moyasarDueCollectionModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${__('Collect Due via Moyasar')}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="moyasarDueCollectionForm">
                                <div class="mb-3">
                                    <label class="form-label">${__('Total Due Amount')}</label>
                                    <input type="text" class="form-control" value="${dueAmount} SAR" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">${__('Collection Amount')}</label>
                                    <input type="number" name="amount" class="form-control" 
                                           max="${dueAmount}" min="0.01" step="0.01" value="${dueAmount}" required>
                                </div>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    ${__('You will be redirected to Moyasar secure payment page')}
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">${__('Cancel')}</button>
                            <button type="button" class="btn btn-primary" onclick="moyasarPayment.processDueCollection(${partyId})">${__('Collect Now')}</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal
        $('#moyasarDueCollectionModal').remove();
        
        // Add new modal
        $('body').append(modalHtml);
        $('#moyasarDueCollectionModal').modal('show');
    }

    processPayment(type, id) {
        const amount = $(`#moyasarPaymentModal input[name="amount"]`).val();
        
        if (!amount || amount <= 0) {
            this.showError(__('Please enter a valid amount'));
            return;
        }

        const url = type === 'sale' 
            ? `/business/moyasar/pay-sale-due/${id}`
            : `/business/moyasar/pay-purchase-due/${id}`;

        this.makePaymentRequest(url, { amount: amount });
    }

    processDueCollection(partyId) {
        const amount = $(`#moyasarDueCollectionModal input[name="amount"]`).val();
        
        if (!amount || amount <= 0) {
            this.showError(__('Please enter a valid amount'));
            return;
        }

        this.makePaymentRequest('/business/moyasar/pay-due-collection', {
            party_id: partyId,
            amount: amount
        });
    }

    processSaleWithMoyasar() {
        // Get sale form data
        const saleForm = $('#saleForm');
        if (!saleForm.length) {
            this.showError(__('Sale form not found'));
            return;
        }

        const formData = new FormData(saleForm[0]);
        const saleData = {};
        
        for (let [key, value] of formData.entries()) {
            saleData[key] = value;
        }

        // Get total amount
        const totalAmount = parseFloat($('#totalAmount').val() || 0);
        
        if (totalAmount <= 0) {
            this.showError(__('Invalid sale amount'));
            return;
        }

        this.makePaymentRequest('/business/moyasar/process-sale-payment', {
            sale_data: saleData,
            payment_amount: totalAmount
        });
    }

    processPurchaseWithMoyasar() {
        // Get purchase form data
        const purchaseForm = $('#purchaseForm');
        if (!purchaseForm.length) {
            this.showError(__('Purchase form not found'));
            return;
        }

        const formData = new FormData(purchaseForm[0]);
        const purchaseData = {};
        
        for (let [key, value] of formData.entries()) {
            purchaseData[key] = value;
        }

        // Get total amount
        const totalAmount = parseFloat($('#totalAmount').val() || 0);
        
        if (totalAmount <= 0) {
            this.showError(__('Invalid purchase amount'));
            return;
        }

        this.makePaymentRequest('/business/moyasar/process-purchase-payment', {
            purchase_data: purchaseData,
            payment_amount: totalAmount
        });
    }

    makePaymentRequest(url, data) {
        // Show loading
        this.showLoading();

        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                this.hideLoading();
                
                // Close any open modals
                $('.modal').modal('hide');
                
                // Redirect to Moyasar payment page
                if (response.url) {
                    window.location.href = response.url;
                } else {
                    this.showSuccess(__('Payment initiated successfully'));
                }
            },
            error: (xhr) => {
                this.hideLoading();
                
                let message = __('Payment failed');
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                this.showError(message);
            }
        });
    }

    showLoading() {
        if (!$('#moyasarLoadingModal').length) {
            const loadingHtml = `
                <div class="modal fade" id="moyasarLoadingModal" tabindex="-1" data-bs-backdrop="static">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <div class="modal-body text-center p-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">${__('Loading...')}</span>
                                </div>
                                <div class="mt-2">${__('Processing payment...')}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('body').append(loadingHtml);
        }
        $('#moyasarLoadingModal').modal('show');
    }

    hideLoading() {
        $('#moyasarLoadingModal').modal('hide');
    }

    showSuccess(message) {
        if (typeof toastr !== 'undefined') {
            toastr.success(message);
        } else {
            alert(message);
        }
    }

    showError(message) {
        if (typeof toastr !== 'undefined') {
            toastr.error(message);
        } else {
            alert(message);
        }
    }
}

// Initialize Moyasar Payment
const moyasarPayment = new MoyasarPayment();

// Helper function for translations
function __(key) {
    // This should be replaced with your actual translation function
    const translations = {
        'Pay via Moyasar': 'الدفع عبر ميسر',
        'Due Amount': 'المبلغ المستحق',
        'Payment Amount': 'مبلغ الدفع',
        'You will be redirected to Moyasar secure payment page': 'سيتم توجيهك إلى صفحة الدفع الآمنة لميسر',
        'Cancel': 'إلغاء',
        'Pay Now': 'ادفع الآن',
        'Collect Due via Moyasar': 'تحصيل المستحقات عبر ميسر',
        'Total Due Amount': 'إجمالي المبلغ المستحق',
        'Collection Amount': 'مبلغ التحصيل',
        'Collect Now': 'حصل الآن',
        'Please enter a valid amount': 'يرجى إدخال مبلغ صحيح',
        'Sale form not found': 'نموذج البيع غير موجود',
        'Invalid sale amount': 'مبلغ البيع غير صحيح',
        'Purchase form not found': 'نموذج الشراء غير موجود',
        'Invalid purchase amount': 'مبلغ الشراء غير صحيح',
        'Payment initiated successfully': 'تم بدء عملية الدفع بنجاح',
        'Payment failed': 'فشل في الدفع',
        'Loading...': 'جاري التحميل...',
        'Processing payment...': 'جاري معالجة الدفع...'
    };
    
    return translations[key] || key;
}