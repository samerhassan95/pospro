<!DOCTYPE html>
@if (in_array(app()->getLocale(), ['ar', 'arbh', 'eg-ar', 'fa', 'prs', 'ps', 'ur']))
<html lang="{{ app()->getLocale() }}" dir="rtl">
@else
<html lang="{{ app()->getLocale() }}" dir="auto">
@endif

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="{{__('IE=edge')}}">
    <meta name="viewport" content="{{__('width=device-width, initial-scale=1.0')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('title') @yield('title') | @endif {{ get_option('general')['title'] ?? config('app.name') }}</title>
    @include('layouts.business.partials.css')
    @stack('css')
</head>

<body class="pos-fullscreen-body">
    <div class="pos-fullscreen-wrapper">
        @yield('main_content')
    </div>
    @stack('modal')
    @include('layouts.business.partials.script')
</body>

</html>
