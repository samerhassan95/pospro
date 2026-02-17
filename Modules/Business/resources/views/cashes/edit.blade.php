<div class="modal fade" id="cashes-edit-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ __('Edit Cash Account') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post" class="ajaxform_instant_reload" id="cashes-edit-form">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Opening Balance') }}</label>
                                <input type="number" step="any" name="opening_balance" id="cashes-opening-balance" class="form-control" placeholder="{{ __('0.00') }}">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Status') }} <span class="text-danger">*</span></label>
                                <select name="status" id="cashes-status" class="form-control">
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
        $(document).on('click', '.cashes-edit-btn', function() {
            var url = $(this).data('url');
            var status = $(this).data('cashes-status');
            var opening_balance = $(this).data('cashes-opening-balance');

            $('#cashes-edit-form').attr('action', url);
            $('#cashes-status').val(status);
            $('#cashes-opening-balance').val(opening_balance);
        });
    </script>
@endpush
