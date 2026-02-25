<div class="modal fade common-validation-modal" id="category-edit-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">{{ __('Edit Category') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="personal-info">
                    <form action="" method="post" enctype="multipart/form-data"
                          class="ajaxform_instant_reload categoryEditForm">
                        @csrf
                        @method('put')

                        <div class="row">
                            <div class="mt-3 col-lg-12">
                                <label class="custom-top-label">{{ __('Name') }}</label>
                                <input type="text" name="categoryName" id="category_name" required placeholder="{{ __('Enter Category Name') }}" class="form-control"/>
                            </div>
                            <div class="col-lg-12 mb-2">
                                <label>{{ __("Icon") }}</label>
                                <div class="border rounded upload-img-container">
                                    <label class="upload-v4">
                                        <div class="img-wrp">
                                            <img src="" alt="user" id="category_icon">
                                        </div>
                                        <input type="file" name="icon" class="d-none" onchange="document.getElementById('category_icon').src = window.URL.createObjectURL(this.files[0])" accept="image/*">
                                    </label>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h2 class='option-title'>{{ __('Select Variations') }}:</h2>
                                <div class="select-variations-container" id="variations-checkboxes-container">
                                    <!-- Variations will be loaded dynamically here -->
                                </div>
                            </div>

                        </div>
                        <div class="offcanvas-footer mt-3">
                    <div class="button-group text-end mt-5">
                        <a href="{{ route('business.categories.index') }}"
                           class="theme-btn border-btn m-2">{{ __('Cancel') }}</a>
                           @usercan('categories.update')
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
