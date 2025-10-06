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
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .hero {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        }

        /* Custom styles for mobile menu animation */
        #menu {
            transition: all 0.3s ease-in-out;
        }

        #menu.hidden {
            opacity: 0;
            transform: translateY(-10px);
        }

        #menu:not(.hidden) {
            opacity: 1;
            transform: translateY(0);
        }

        /* Hamburger menu animation */
        .hamburger-line {
            transition: all 0.3s ease;
        }

        .menu-open .line1 {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .menu-open .line2 {
            opacity: 0;
        }

        .menu-open .line3 {
            transform: rotate(-45deg) translate(7px, -6px);
        }
    </style>
</head>

<body class="bg-gray-900 text-white">

    <header class="py-4 px-6 fixed w-full z-50 bg-gray-900 shadow-lg">
        <nav class="flex justify-between items-center max-w-7xl mx-auto">
            <a href="/" wire:navigate class="flex items-center space-x-2 text-xl font-bold text-indigo-400 hover:text-indigo-300 transition duration-300">
                <span>Baricode Community</span>
            </a>

            <!-- Hamburger Menu Button -->
            <button id="menu-toggle" class="lg:hidden text-gray-300 focus:outline-none focus:text-white">
                <div class="w-6 h-6 flex flex-col justify-center items-center">
                    <div class="hamburger-line line1 w-6 h-0.5 bg-current mb-1"></div>
                    <div class="hamburger-line line2 w-6 h-0.5 bg-current mb-1"></div>
                    <div class="hamburger-line line3 w-6 h-0.5 bg-current"></div>
                </div>
            </button>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex lg:items-center lg:space-x-4">
                <a href="{{ route('cara_belajar') }}" wire:navigate class="text-gray-300 hover:text-white px-4 py-2 transition duration-300">Cara Belajar</a>
                <a href="https://chat.whatsapp.com/Fb2ZFMIKDz7JJZyBVpzXws" class="text-gray-300 hover:text-white px-4 py-2 transition duration-300">Komunitas</a>
                
                @if(auth()->user())
                    <a href="{{ route('dashboard') }}" wire:navigate
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md ml-4 transition duration-300">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" wire:navigate
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md ml-4 transition duration-300">Masuk
                        Sekarang</a>
                @endif
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div id="menu"
            class="lg:hidden hidden absolute top-full left-0 w-full bg-gray-900 border-t border-gray-700 shadow-lg">
            <div class="px-6 py-4 space-y-3">
                <a href="{{ route('cara_belajar') }}" wire:navigate
                    class="block text-gray-300 hover:text-white hover:bg-gray-800 px-4 py-2 rounded-md transition duration-300">Cara Belajar</a>
                <a href="https://chat.whatsapp.com/Fb2ZFMIKDz7JJZyBVpzXws"
                    class="block text-gray-300 hover:text-white hover:bg-gray-800 px-4 py-2 rounded-md transition duration-300">Komunitas</a>
                <div class="pt-2 border-t border-gray-700">
                    @if(auth()->user())
                        <a href="{{ route('dashboard') }}" wire:navigate
                            class="block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md transition duration-300 text-center">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate
                            class="block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md transition duration-300 text-center">Masuk
                            Sekarang</a>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <main>
        @if (!isset($slot))
            @yield('content')
        @else
            {{ $slot }}
        @endif
    </main>

    <footer class="bg-gray-800 text-center text-gray-400 py-8">
        <p>&copy; {{ date('Y') }} Baricode Community. All Rights Reserved.</p>
    </footer>

    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const menu = document.getElementById('menu');

        menuToggle.addEventListener('click', () => {
            menu.classList.toggle('hidden');
            menuToggle.classList.toggle('menu-open');
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!menuToggle.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
                menuToggle.classList.remove('menu-open');
            }
        });

        // Close menu when window is resized to desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                menu.classList.add('hidden');
                menuToggle.classList.remove('menu-open');
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
