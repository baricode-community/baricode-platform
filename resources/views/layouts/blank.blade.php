<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Komunitas Ngoding - 100% Gratis')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
</head>

<body>
    @yield('content')
    @if ($slot)
        {{ $slot }}
    @endif
    @stack('scripts')
</body>

</html>
