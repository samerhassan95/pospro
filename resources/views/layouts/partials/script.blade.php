<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/theme.js') }}"></script>
{{-- Sidebar Icon Color --}}
<script src="{{ asset('assets/js/sidebar-icon-color.js') }}?v={{ time() }}"></script>
{{-- jquery confirm --}}
<script src="{{asset('assets/plugins/jquery-confirm/jquery-confirm.min.js')}}"></script>
{{-- jquery validation --}}
<script src="{{asset('assets/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
{{-- Custom --}}
<script src="{{ asset('assets/plugins/validation-setup/validation-setup.js') }}"></script>
<script src="{{ asset('assets/plugins/custom/notification.js') }}"></script>
<script src="{{ asset('assets/plugins/custom/form.js') }}"></script>
{{-- Status --}}
<script src="{{ asset('assets/js/custom-ajax.js') }}"></script>
{{-- Toaster --}}
<script src="{{ asset('assets/js/toastr.min.js') }}"></script>

{{-- Translation variables for JavaScript --}}
<script>
    window.translations = window.translations || {};
    Object.assign(window.translations, {
        'no_products_in': '{{ __("No Products in") }}',
        'no_products_available_category': '{{ __("There are no products available in this category.") }}',
        'no_products_found': '{{ __("No products found") }}',
        'no_products_available': '{{ __("No products available") }}'
    });
</script>

<script src="{{ asset('assets/js/custom/custom.js') }}"></script>
<!-- Dropdown Fix for Mobile -->
<script src="{{ asset('assets/js/dropdown-fix.js') }}?v={{ time() }}"></script>

{{-- SAR Symbol Replacement --}}
<script src="{{ asset('assets/js/custom/replace-sar-symbol.js') }}?v={{ time() }}"></script>
{{-- Clean SAR SVG Display --}}
<script src="{{ asset('assets/js/custom/clean-sar-svg.js') }}?v={{ time() }}"></script>

@stack('js')

@stack('modal-view')

{{-- Toaster Message --}}
@if(Session::has('message'))
    <script>
        toastr.success( "{{ Session::get('message') }}");
    </script>
@endif
@if(Session::has('error'))
    <script>
        toastr.error( "{{ Session::get('error') }}");
    </script>
@endif
@if(Session::has('warning'))
    <script>
        toastr.warning( "{{ Session::get('warning') }}");
    </script>
@endif
@if($errors->any())
<script>
    toastr.warning('Error some occurs!');
</script>
@endif
