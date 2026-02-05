@extends('layouts.master')

@section('title')
    {{ __('General Settings') }}
@endsection

@section('main_content')
    <div class="erp-table-section">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-bodys">
                    <div class="table-header p-16">
                        <h4>{{ __('General Settings') }}</h4>
                    </div>
                    <div class="order-form-section p-16">
                        <form action="{{ route('admin.settings.update', $general->id) }}" method="post" enctype="multipart/form-data" class="ajaxform_instant_reload">
                            @csrf
                            @method('put')
                            <div class="add-suplier-modal-wrapper d-block">
                                <div class="row">
                                    <div class="col-lg-12 mt-2">
                                        <label>{{ __('Title') }}</label>
                                        <input type="text" name="title" value="{{ $general->value['title'] ?? '' }}" required class="form-control" placeholder="{{ __('Enter Title') }}">
                                    </div>
                                    <div class="col-lg-12 mt-2">
                                        <label>{{ __('Copy Right') }}</label>
                                        <input type="text" name="copy_right" value="{{ $general->value['copy_right'] ?? '' }}" required class="form-control" placeholder="{{ __('Enter Title') }}">
                                    </div>

                                    <div class="col-lg-6 mt-2">
                                        <label>{{ __('Dynamic Text') }}</label>
                                        <input type="text" name="admin_footer_text" value="{{ $general->value['admin_footer_text'] ?? '' }}" required class="form-control" placeholder="{{ __('Enter Text') }}">
                                    </div>

                                    <div class="col-lg-6 mt-2">
                                        <label>{{ __('Dynamic Link Text') }}</label>
                                        <input type="text" name="admin_footer_link_text" value="{{ $general->value['admin_footer_link_text'] ?? '' }}" required class="form-control" placeholder="{{ __('Enter Text') }}">
                                    </div>

                                    <div class="col-lg-6 mt-2">
                                        <label>{{ __('Dynamic Link') }}</label>
                                        <input type="text" name="admin_footer_link" value="{{ $general->value['admin_footer_link'] ?? '' }}" required class="form-control" placeholder="{{ __('Enter Link') }}">
                                    </div>

                                    <div class="col-lg-6 mt-2">
                                        <label>{{ __('App Link') }}</label>
                                        <input type="url" name="app_link" value="{{ $general->value['app_link'] ?? '' }}" class="form-control" placeholder="{{ __('Enter Link') }}">
                                    </div>

                                    <div class="col-lg-6">
                                        <label class="custom-top-label">{{ __('Language') }}</label>
                                        <div class="gpt-up-down-arrow position-relative">
                                            <select class="form-control form-selected" name="default_lang" required>
                                                <option value="">{{ __('Select one') }}</option>
                                                @foreach ($languages as $language)
                                                    <option value="{{ $language['code'] }}" @selected($language['code'] == ($general->value['default_lang'] ?? ''))>
                                                        {{ $language['name'] }} ({{ $language['code'] }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span></span>
                                        </div>
                                    </div>

                                    {{-- Color Settings --}}
                                    <div class="col-lg-12 mt-4">
                                        <h5 class="mb-3">{{ __('Color Settings') }}</h5>
                                    </div>

                                    <div class="col-lg-6 mt-2">
                                        <label>{{ __('Primary Color') }}</label>
                                        <div class="input-group">
                                            <input type="text" name="primary_color" id="primary_color_input" value="{{ $general->value['primary_color'] ?? '#011646' }}" class="form-control" placeholder="#011646" onkeyup="syncColorPicker('primary')">
                                            <input type="color" id="primary_color_picker" value="{{ $general->value['primary_color'] ?? '#011646' }}" class="form-control" style="max-width: 60px; cursor: pointer;" onchange="syncColorInput('primary')" oninput="syncColorInput('primary')">
                                        </div>
                                        <small class="text-muted">{{ __('Default: #011646') }}</small>
                                    </div>

                                    <div class="col-lg-6 mt-2">
                                        <label>{{ __('Secondary Color') }}</label>
                                        <div class="input-group">
                                            <input type="text" name="secondary_color" id="secondary_color_input" value="{{ $general->value['secondary_color'] ?? '#0071bc' }}" class="form-control" placeholder="#0071bc" onkeyup="syncColorPicker('secondary')">
                                            <input type="color" id="secondary_color_picker" value="{{ $general->value['secondary_color'] ?? '#0071bc' }}" class="form-control" style="max-width: 60px; cursor: pointer;" onchange="syncColorInput('secondary')" oninput="syncColorInput('secondary')">
                                        </div>
                                        <small class="text-muted">{{ __('Default: #0071bc') }}</small>
                                    </div>

                                    <script>
                                    function syncColorInput(type) {
                                        const picker = document.getElementById(type + '_color_picker');
                                        const input = document.getElementById(type + '_color_input');
                                        if (picker && input) {
                                            input.value = picker.value.toUpperCase();
                                        }
                                    }
                                    
                                    function syncColorPicker(type) {
                                        const picker = document.getElementById(type + '_color_picker');
                                        const input = document.getElementById(type + '_color_input');
                                        if (picker && input) {
                                            let value = input.value.trim();
                                            if (value && !value.startsWith('#')) {
                                                value = '#' + value;
                                                input.value = value;
                                            }
                                            if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
                                                picker.value = value.toLowerCase();
                                            }
                                        }
                                    }
                                    </script>

                                    <div class="col-lg-12 mt-2">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> 
                                            <strong>{{ __('Note:') }}</strong> {{ __('After saving, press Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac) to hard refresh and see the new colors applied.') }}
                                        </div>
                                    </div>

                                    <div class="col-lg-6 settings-image-upload">
                                        <label class="title">{{ __('Main Header Logo') }}</label>
                                        <div class="upload-img-v2">
                                            <label class="upload-v4 settings-upload-v4">
                                                <div class="img-wrp">
                                                    <img src="{{ asset($general->value['logo'] ?? 'assets/images/icons/upload-icon.svg') }}" alt="user" id="logo">
                                                </div>
                                                <input type="file" name="logo" class="d-none" accept="image/*" onchange="document.getElementById('logo').src = window.URL.createObjectURL(this.files[0])" class="form-control">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 settings-image-upload">
                                        <label class="title">{{ __('Common Header Logo') }}</label>
                                        <div class="upload-img-v2">
                                            <label class="upload-v4 settings-upload-v4">
                                                <div class="img-wrp">
                                                    <img src="{{ asset($general->value['common_header_logo'] ?? 'assets/images/icons/upload-icon.svg') }}" alt="user" id="common_header_logo">
                                                </div>
                                                <input type="file" name="common_header_logo" class="d-none" accept="image/*" onchange="document.getElementById('common_header_logo').src = window.URL.createObjectURL(this.files[0])" class="form-control">
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 settings-image-upload">
                                        <label class="title">{{ __('Footer Logo') }}</label>
                                        <div class="upload-img-v2">
                                            <label class="upload-v4 settings-upload-v4">
                                                <div class="img-wrp">
                                                    <img src="{{ asset($general->value['footer_logo'] ?? 'assets/images/icons/upload-icon.svg') }}" alt="user" id="footer_logo">
                                                </div>
                                                <input type="file" name="footer_logo" class="d-none" accept="image/*" onchange="document.getElementById('footer_logo').src = window.URL.createObjectURL(this.files[0])" class="form-control">
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 settings-image-upload">
                                        <label class="title">{{ __('Admin Logo') }}</label>
                                        <div class="upload-img-v2">
                                            <label class="upload-v4 settings-upload-v4">
                                                <div class="img-wrp">
                                                    <img src="{{ asset($general->value['admin_logo'] ?? 'assets/images/icons/upload-icon.svg') }}" alt="user" id="admin_logo">
                                                </div>
                                                <input type="file" name="admin_logo" class="d-none" accept="image/*" onchange="document.getElementById('admin_logo').src = window.URL.createObjectURL(this.files[0])" class="form-control">
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 settings-image-upload">
                                        <label class="title">{{ __('Favicon') }}</label>
                                        <div class="upload-img-v2">
                                            <label class="upload-v4 settings-upload-v4">
                                                <div class="img-wrp">
                                                    <img src="{{ asset($general->value['favicon'] ?? 'assets/images/icons/upload-icon.svg') }}" alt="user" id="favicon">
                                                </div>
                                                <input type="file" name="favicon" class="d-none" accept="image/*" onchange="document.getElementById('favicon').src = window.URL.createObjectURL(this.files[0])" class="form-control">
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 settings-image-upload">
                                        <label class="title">{{ __('Login Page Logo') }}</label>
                                        <div class="upload-img-v2">
                                            <label class="upload-v4 settings-upload-v4">
                                                <div class="img-wrp">
                                                    <img src="{{ asset($general->value['login_page_logo'] ?? 'assets/images/icons/upload-icon.svg') }}" alt="user" id="login_page_logo">
                                                </div>
                                                <input type="file" name="login_page_logo" class="d-none" accept="image/*" onchange="document.getElementById('login_page_logo').src = window.URL.createObjectURL(this.files[0])" class="form-control">
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 settings-image-upload">
                                        <label class="title">{{ __('Login Page Image') }}</label>
                                        <div class="upload-img-v2">
                                            <label class="upload-v4 settings-upload-v4">
                                                <div class="img-wrp">
                                                    <img src="{{ asset($general->value['login_page_image'] ?? 'assets/images/icons/upload-icon.svg') }}" alt="user" id="login_page_image">
                                                </div>
                                                <input type="file" name="login_page_image" class="d-none" accept="image/*" onchange="document.getElementById('login_page_image').src = window.URL.createObjectURL(this.files[0])" class="form-control">
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Dashboard Banner Settings --}}
                                    <div class="col-lg-12 mt-4">
                                        <h5 class="mb-3">{{ __('Dashboard Banner Settings') }}</h5>
                                    </div>

                                    <div class="col-lg-6 settings-image-upload">
                                        <label class="title">{{ __('Dashboard Banner Image') }}</label>
                                        <div class="upload-img-v2">
                                            <label class="upload-v4 settings-upload-v4">
                                                <div class="img-wrp">
                                                    <img src="{{ asset($general->value['dashboard_banner_image'] ?? 'assets/images/icons/upload-icon.svg') }}" alt="banner" id="dashboard_banner_image">
                                                </div>
                                                <input type="file" name="dashboard_banner_image" class="d-none" accept="image/*" onchange="document.getElementById('dashboard_banner_image').src = window.URL.createObjectURL(this.files[0])" class="form-control">
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 mt-2">
                                        <label>{{ __('Dashboard Banner Title') }}</label>
                                        <input type="text" name="dashboard_banner_title" value="{{ $general->value['dashboard_banner_title'] ?? '' }}" class="form-control" placeholder="{{ __('Enter Banner Title') }}">
                                    </div>

                                    <div class="col-lg-12 mt-2">
                                        <label>{{ __('Dashboard Banner Description') }}</label>
                                        <textarea name="dashboard_banner_description" class="form-control" rows="3" placeholder="{{ __('Enter Banner Description') }}">{{ $general->value['dashboard_banner_description'] ?? '' }}</textarea>
                                    </div>

                                    <div class="col-lg-6 mt-2">
                                        <label>{{ __('Dashboard Banner Button Text') }}</label>
                                        <input type="text" name="dashboard_banner_button_text" value="{{ $general->value['dashboard_banner_button_text'] ?? '' }}" class="form-control" placeholder="{{ __('Enter Button Text') }}">
                                    </div>

                                    @can('settings-update')
                                        <div class="col-lg-12">
                                            <div class="text-end mt-5">
                                                <button type="submit" class="theme-btn m-2 submit-btn">{{ __('Update') }}</button>
                                            </div>
                                        </div>
                                    @endcan
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
(function() {
    'use strict';
    
    // Primary color sync
    const primaryPicker = document.getElementById('primary_color_picker');
    const primaryInput = document.getElementById('primary_color_input');
    
    if (primaryPicker && primaryInput) {
        // Picker changes input
        primaryPicker.addEventListener('input', function() {
            primaryInput.value = this.value.toUpperCase();
            console.log('Primary picker changed to:', this.value);
        });
        
        primaryPicker.addEventListener('change', function() {
            primaryInput.value = this.value.toUpperCase();
            console.log('Primary picker changed to:', this.value);
        });
        
        // Input changes picker
        primaryInput.addEventListener('input', function() {
            let value = this.value.trim();
            if (value && !value.startsWith('#')) {
                value = '#' + value;
                this.value = value;
            }
            if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
                primaryPicker.value = value.toLowerCase();
                console.log('Primary input changed to:', value);
            }
        });
        
        primaryInput.addEventListener('keyup', function() {
            let value = this.value.trim();
            if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
                primaryPicker.value = value.toLowerCase();
            }
        });
    }
    
    // Secondary color sync
    const secondaryPicker = document.getElementById('secondary_color_picker');
    const secondaryInput = document.getElementById('secondary_color_input');
    
    if (secondaryPicker && secondaryInput) {
        // Picker changes input
        secondaryPicker.addEventListener('input', function() {
            secondaryInput.value = this.value.toUpperCase();
            console.log('Secondary picker changed to:', this.value);
        });
        
        secondaryPicker.addEventListener('change', function() {
            secondaryInput.value = this.value.toUpperCase();
            console.log('Secondary picker changed to:', this.value);
        });
        
        // Input changes picker
        secondaryInput.addEventListener('input', function() {
            let value = this.value.trim();
            if (value && !value.startsWith('#')) {
                value = '#' + value;
                this.value = value;
            }
            if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
                secondaryPicker.value = value.toLowerCase();
                console.log('Secondary input changed to:', value);
            }
        });
        
        secondaryInput.addEventListener('keyup', function() {
            let value = this.value.trim();
            if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
                secondaryPicker.value = value.toLowerCase();
            }
        });
    }
    
    console.log('Color sync initialized');
})();

// Force hard reload after form submission
$(document).ready(function() {
    $('form.ajaxform_instant_reload').on('submit', function() {
        setTimeout(function() {
            location.reload(true);
        }, 1000);
    });
});
</script>
@endpush
