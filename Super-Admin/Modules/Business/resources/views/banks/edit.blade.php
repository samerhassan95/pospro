<div class="modal fade" id="banks-edit-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ __('Edit Bank Account') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post" class="ajaxform_instant_reload" id="banks-edit-form">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Bank Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="banks-name" class="form-control" placeholder="{{ __('Enter Bank Name') }}" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Opening Balance') }}</label>
                                <input type="number" step="any" name="opening_balance" id="banks-opening-balance" class="form-control" placeholder="{{ __('0.00') }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Opening Date') }}</label>
                                <input type="date" name="opening_date" id="banks-opening-date" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Status') }} <span class="text-danger">*</span></label>
                                <select name="status" id="banks-status" class="form-control">
                                    <option value="1">{{ __('Active') }}</option>
                                    <option value="0">{{ __('Inactive') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary submit-btn">{{ __('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(document).on('click', '.banks-edit-btn', function() {
            var url = $(this).data('url');
            var name = $(this).data('banks-name');
            var status = $(this).data('banks-status');
            var opening_balance = $(this).data('banks-opening-balance');
            var opening_date = $(this).data('banks-opening-date');

            $('#banks-edit-form').attr('action', url);
            $('#banks-name').val(name);
            $('#banks-status').val(status);
            $('#banks-opening-balance').val(opening_balance);
            $('#banks-opening-date').val(opening_date);
        });
    </script>
@endpush
