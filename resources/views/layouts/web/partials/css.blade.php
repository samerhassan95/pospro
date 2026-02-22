<link rel="icon" type="image/svg+xml" href="{{ asset(get_favicon()) }}">
<link rel="icon" type="image/svg+xml" sizes="32x32" href="{{ asset(get_favicon()) }}">
<link rel="icon" type="image/svg+xml" sizes="96x96" href="{{ asset(get_favicon()) }}">
<link rel="apple-touch-icon" href="{{ asset(get_favicon()) }}">

<!-- Cairo Font -->
<link rel="stylesheet" href="{{ asset('fonts/cairo/cairo.css') }}">

<link rel="stylesheet" href="{{ asset('assets/web/css/bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/web/css/swiper-bundle.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/web/fonts/fontawesome/css/all.min.css') }}" />
<!-- Slick Slider -->
<link rel="stylesheet" href="{{ asset('assets/web/css/slick.css') }}" />
{{-- jquery-confirm --}}
<link rel="stylesheet" href="{{ asset('assets/plugins/jquery-confirm/jquery-confirm.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/web/css/slick-theme.css') }}" />
<!-- Custom Css -->
<link rel="stylesheet" href="{{ asset('assets/web/css/styles.css') }}?v={{ time() }}" />
<link rel="stylesheet" href="{{ asset('assets/web/css/responsive.css') }}?v={{ time() }}" />
<!-- Dropdown Fix for Mobile -->
<link rel="stylesheet" href="{{ asset('assets/css/dropdown-fix.css') }}?v={{ time() }}" />

<!-- Toaster -->
<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">

@if (app()->getLocale() == 'ar')
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.rtl.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/web/css/arabic.css') }}?v={{ time() }}">
@endif

@stack('css')

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

/* Apply primary color to sidebar icons */
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

/* Font Awesome 5 Pro font-family overrides - HIGHEST PRIORITY */
/* Font Awesome 5 Pro font-family overrides - ALL ICONS */
.fa, .fas, .far, .fal, .fab, .fad, .fat, .fass, .fasr, .fasl {
    font-family: "Font Awesome 5 Pro", "Font Awesome 6 Free", "FontAwesome" !important;
}

/* All FontAwesome icon pseudo-elements */
[class^="fa-"]:before, [class*=" fa-"]:before,
.fa:before, .fas:before, .far:before, .fal:before, .fab:before, .fad:before, .fat:before, .fass:before, .fasr:before, .fasl:before {
    font-family: "Font Awesome 5 Pro", "Font Awesome 6 Free", "FontAwesome" !important;
}

/* Specific icon overrides */
.fa-navicon:before, .fa-reorder:before, .fa-bars:before,
.fa-remove:before, .fa-close:before, .fa-times:before {
    font-family: "Font Awesome 5 Pro", "Font Awesome 6 Free", "FontAwesome" !important;
}

/* Override FontAwesome shorthand for all icons */
[class^="fa-"], [class*=" fa-"] {
    font-family: "Font Awesome 5 Pro", "Font Awesome 6 Free", "FontAwesome" !important;
}

/* Specific override for fa-check-circle */
.fa-check-circle, .fas.fa-check-circle {
    font-family: "Font Awesome 6 Free" !important;
}

/* Specific override for fa-times-circle */
.fa-times-circle, .fas.fa-times-circle {
    font-family: "Font Awesome 6 Free" !important;
}
</style>
<!-- Debug: Primary={{ get_primary_color() }}, Secondary={{ get_secondary_color() }} -->
