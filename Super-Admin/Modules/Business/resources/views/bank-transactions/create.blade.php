<div class="modal fade" id="bank-transactions-create-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ __('Add Bank Transaction') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('business.bank-transactions.store') }}" method="post" class="ajaxform_instant_reload">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Transaction Type') }} <span class="text-danger">*</span></label>
                                <select name="transaction_type" id="transaction_type" class="form-control" required>
                                    <option value="bank_to_bank">{{ __('Bank to Bank Transfer') }}</option>
                                    <option value="bank_to_cash">{{ __('Bank to Cash') }}</option>
                                    <option value="adjust_bank">{{ __('Adjustment (Deposit/Withdraw)') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('From Account') }} <span class="text-danger">*</span></label>
                                <select name="from" class="form-control" required>
                                    @foreach($all_accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }} ({{ currency_format($account->balance, currency: business_currency()) }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6" id="to_account_div">
                            <div class="mb-3">
                                <label class="form-label">{{ __('To Account') }} <span class="text-danger">*</span></label>
                                <select name="to" class="form-control">
                                    @foreach($all_accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }} ({{ currency_format($account->balance, currency: business_currency()) }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6 d-none" id="adjust_type_div">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Adjustment Type') }} <span class="text-danger">*</span></label>
                                <select name="type" class="form-control">
                                    <option value="credit">{{ __('Deposit (Credit)') }}</option>
                                    <option value="debit">{{ __('Withdraw (Debit)') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Amount') }} <span class="text-danger">*</span></label>
                                <input type="number" step="any" name="amount" class="form-control" placeholder="{{ __('0.00') }}" required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Date') }}</label>
                                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Note') }}</label>
                                <textarea name="note" class="form-control" rows="2" placeholder="{{ __('Transfer note...') }}"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary submit-btn">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
    <script>
        $('#transaction_type').on('change', function() {
            var type = $(this).val();
            if (type === 'bank_to_bank') {
                $('#to_account_div').removeClass('d-none');
                $('#adjust_type_div').addClass('d-none');
            } else if (type === 'adjust_bank') {
                $('#to_account_div').addClass('d-none');
                $('#adjust_type_div').removeClass('d-none');
            } else {
                $('#to_account_div').addClass('d-none');
                $('#adjust_type_div').addClass('d-none');
            }
        });
    </script>
@endpush
