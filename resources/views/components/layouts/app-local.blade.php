<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? "خانه" }}</title>
    @include('components.layouts.header-local')
    @stack('styles')
</head>

<body>
@include('components.layouts.menu')

{{ $slot }}

@yield('footer')
@include('components.layouts.footer-local')

@stack('scripts')
@livewireScripts
</body>

</html>
