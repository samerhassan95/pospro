
<link rel="icon" type="image/svg+xml" href="{{ asset('assets/images/logo/favicon.svg') }}">
<link rel="icon" type="image/svg+xml" sizes="32x32" href="{{ asset('assets/images/logo/favicon.svg') }}">
<link rel="icon" type="image/svg+xml" sizes="96x96" href="{{ asset('assets/images/logo/favicon.svg') }}">
<link rel="apple-touch-icon" href="{{ asset('assets/images/logo/favicon.svg') }}">
<!-- Bootstrap -->
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
<!-- Fontawesome -->
<link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome/css/fontawesome-all.min.css') }}">
{{-- jquery-confirm --}}
<link rel="stylesheet" href="{{ asset('assets/plugins/jquery-confirm/jquery-confirm.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/summernote-lite.css') }}">
<!-- Lily -->
<link rel="stylesheet" href="{{ asset('assets/css/lity.css') }}">
<!-- Style -->
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ time() }}">
<link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}?v={{ time() }}">
<!-- Dropdown Fix for Mobile -->
<link rel="stylesheet" href="{{ asset('assets/css/dropdown-fix.css') }}?v={{ time() }}">
<!-- Banner -->
<link rel="stylesheet" href="{{ asset('resources/css/banner.css') }}?v={{ time() }}">
<!-- Toaster -->
<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
@stack('css')

@if (app()->getLocale() == 'ar')
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.rtl.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/arabic.css') }}?v={{ time() }}">
@endif
