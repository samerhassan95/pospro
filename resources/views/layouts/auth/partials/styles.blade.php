<!-- Cairo Font -->
<link rel="stylesheet" href="{{ asset('fonts/cairo/cairo.css') }}">

<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome/css/fontawesome-all.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

@stack('css')

<!-- Font Awesome 5 Pro font-family overrides - HIGHEST PRIORITY -->
<style>
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
</style>
