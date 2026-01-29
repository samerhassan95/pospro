<div class="modal fade" id="create-domain">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">{{ __('Add New') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body order-form-section">
                <form action="{{ route('business.domains.store') }}" method="post" class="ajaxform_instant_reload">
                    @csrf
                    <div class="add-suplier-modal-wrapper">
                        <div class="row">

                            <div class="col-lg-12 mt-2">
                                <label>{{ __('Domain Type') }}</label>
                                <div class="gpt-up-down-arrow position-relative">
                                    <select name="domain_type" id="domain_type" class="form-control table-select w-100" required>
                                        <option value="addon">{{ __('Custom Domain') }}</option>
                                        <option value="subdomain">{{ __('Subdomain') }}</option>
                                    </select>
                                    <span></span>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-2">
                                <label>{{ __('Domain Name') }}</label>
                                <div class="input-group">
                                    <input type="text" id="domain" name="domain" required class="form-control"
                                        placeholder="{{ __('Enter your domain name') }}">
                                    <span class="input-group-text" id="subdomain-suffix"
                                        style="display: none;">.{{ get_root_domain() }}</span>
                                </div>
                            </div>


                            <div class="col-lg-12">
                                <div class="button-group text-center mt-3">
                                    <button type="reset" class="theme-btn border-btn m-2">{{ __('Reset') }}</button>
                                    @usercan('domains.create')
                                    <button class="theme-btn m-2 submit-btn">{{ __('Save') }}</button>
                                    @endusercan
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function() {
            $("#domain_type").change(function() {
                if ($(this).val() === "subdomain") {
                    $("#subdomain-suffix").show();
                } else {
                    $("#subdomain-suffix").hide();
                }
            });
        });
    </script>
@endpush
