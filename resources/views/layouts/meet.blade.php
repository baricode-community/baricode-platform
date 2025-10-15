<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Komunitas Ngoding - 100% Gratis')</title>
    
    <meta name="description" content="{{ $meet->description ?? 'Bergabunglah dengan komunitas ngoding kami untuk belajar, berbagi, dan berkembang bersama. 100% gratis!' }}">
    <meta name="keywords" content="komunitas ngoding, belajar ngoding, coding gratis, pemrograman, developer, web development, programming community">

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

    <a href="https://chat.whatsapp.com/Fb2ZFMIKDz7JJZyBVpzXws" target="_blank"
        class="fixed bottom-8 right-8 z-50 bg-gradient-to-r from-green-400 to-green-700 text-white px-5 py-3 rounded-full shadow-lg font-poppins font-bold no-underline flex items-center gap-2 transition-all duration-300 hover:scale-105 hover:shadow-2xl dark:from-green-600 dark:to-green-900"
        aria-label="Whatsapp Komunitas">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 24 24" class="flex-shrink-0">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.028-.967-.271-.099-.468-.148-.667.15-.197.297-.767.967-.94 1.166-.173.199-.347.223-.644.075-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.298-.018-.458.13-.606.134-.133.298-.347.447-.52.149-.174.198-.298.298-.497.099-.198.05-.372-.025-.521-.075-.149-.667-1.611-.916-2.206-.242-.579-.487-.5-.667-.51-.173-.008-.372-.01-.571-.01-.198 0-.52.074-.792.372-.271.298-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.099 3.205 5.077 4.381.709.306 1.262.489 1.694.626.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.413-.074-.124-.271-.198-.568-.347z"/>
            <path d="M12.004 2c-5.514 0-9.997 4.486-9.997 10 0 1.768.464 3.484 1.347 4.995l-1.419 5.188a1 1 0 0 0 1.212 1.212l5.188-1.419c1.511.883 3.227 1.347 4.995 1.347 5.514 0 9.997-4.486 9.997-10s-4.483-10-9.997-10zm0 18c-1.613 0-3.188-.437-4.541-1.264a1 1 0 0 0-.779-.095l-3.093.847.847-3.093a1 1 0 0 0-.095-.779c-.827-1.353-1.264-2.928-1.264-4.541 0-4.411 3.589-8 8-8s8 3.589 8 8-3.589 8-8 8z"/>
        </svg>
        <span class="text-sm tracking-wide">Whatsapp Komunitas</span>
    </a>

    @stack('scripts')
</body>

</html>
