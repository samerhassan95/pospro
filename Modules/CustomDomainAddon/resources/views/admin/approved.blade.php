<div class="modal fade" id="approve-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 border-0">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">{{ __('Are you sure?') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="" method="POST" class="ajaxform_instant_reload modalApproveForm">
                @csrf
                <div class="modal-body">
                    <div class="">
                        <label for="reason" >{{ __('Enter Reason') }}</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="{{ __('Enter reason...') }}"></textarea>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="button-group text-center mt-3">
                        <button type="reset" class="theme-btn border-btn m-2">{{__('Cancel')}}</button>
                        <button class="theme-btn m-2 submit-btn">{{__('Submit')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
