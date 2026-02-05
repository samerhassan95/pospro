
<link rel="icon" type="image/svg+xml" href="{{ asset(get_favicon()) }}">
<link rel="icon" type="image/svg+xml" sizes="32x32" href="{{ asset(get_favicon()) }}">
<link rel="icon" type="image/svg+xml" sizes="96x96" href="{{ asset(get_favicon()) }}">
<link rel="apple-touch-icon" href="{{ asset(get_favicon()) }}">

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

<!-- Dynamic Color Variables - Must be AFTER all CSS files to override -->
<style id="dynamic-colors">
:root {
    --clr-primary: {{ get_primary_color() }} !important;
    --clr-secondary: {{ get_secondary_color() }} !important;
    --clr-white: #fff;
    --clr-black: #000;
    --clr-dark: #101828;
    --clr-light: #344054;
    --clr-light-sm: #dee2e6;
}

/* Apply primary color to sidebar SVG icons */
.side-bar-manu li a .sidebar-icon svg,
.side-bar-manu li a .sidebar-icon svg path {
    /* fill: var(--clr-primary) !important; */
    color: var(--clr-primary) !important;
}

/* White color on hover and active */
.side-bar-manu li.active > a .sidebar-icon svg,
.side-bar-manu li.active > a .sidebar-icon svg path,
.side-bar-manu li:hover > a .sidebar-icon svg,
.side-bar-manu li:hover > a .sidebar-icon svg path {
    /* fill: #fff !important; */
    color: #fff !important;
}

/* Apply primary color to sidebar IMG icons (fallback for any remaining img icons) */
.side-bar-manu li a .sidebar-icon img {
    @php
        $primaryColor = get_primary_color();
        $hex = str_replace('#', '', $primaryColor);
        if (strtolower($hex) === '011646') {
            echo 'filter: brightness(0) saturate(100%) invert(7%) sepia(98%) saturate(4299%) hue-rotate(211deg) brightness(94%) contrast(105%) !important;';
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            $brightness = ($r + $g + $b) / 3 / 255;
            echo 'filter: brightness(0) saturate(100%) brightness(' . number_format($brightness, 2) . ') !important;';
        }
    @endphp
}

.side-bar-manu li.active > a .sidebar-icon img,
.side-bar-manu li:hover > a .sidebar-icon img {
    filter: brightness(0) invert(1) !important;
}

/* Generated at: {{ now() }} */
</style>
<!-- Debug: Primary={{ get_primary_color() }}, Secondary={{ get_secondary_color() }} -->
