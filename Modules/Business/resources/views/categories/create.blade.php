<div class="modal fade common-validation-modal" id="category-create-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">{{ __('Add New Category') }}</h1>
                <button type="button" class="btn-close modal-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="personal-info">
                    <form action="{{ route('business.categories.store') }}" method="post" enctype="multipart/form-data" class="ajaxform_instant_reload">
                        @csrf

                        <div class="row">

                            <div class="mt-3 col-lg-12">
                                <label class="custom-top-label">{{ __('Name') }}</label>
                                <input type="text" name="categoryName" placeholder="{{ __('Enter Category Name') }}" required class="form-control"/>
                            </div>

                            <div class="mt-3 col-lg-12">
                                <label>{{ __('Icon') }}</label>
                                <div class="border rounded upload-img-container">
                                    <label class="upload-v4">
                                        <div class="img-wrp">
                                            <img src="{{ asset('assets/images/icons/upload-icon.svg') }}" alt="Brand" id="brand-img">
                                        </div>
                                        <input type="file" name="icon" class="d-none" onchange="document.getElementById('brand-img').src = window.URL.createObjectURL(this.files[0])" accept="image/*">
                                    </label>
                                </div>
                            </div>

                            <div class="m-2">
                                <h2 class='option-title'>{{ __('Select Variations') }}:</h2>
                                <div class="select-variations-container" id="variations-checkboxes-container-create">
                                    <!-- Variations will be loaded dynamically here -->
                                </div>
                            </div>
                        </div>
                        <div class="offcanvas-footer mt-3">
                            <div class="button-group text-end mt-5">
                                <button type="reset" class="theme-btn border-btn m-2">{{ __('Reset') }}</button>
                                @usercan('categories.create')
                                <button class="theme-btn m-2 submit-btn">{{ __('Save') }}</button>
                                @endusercan
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
