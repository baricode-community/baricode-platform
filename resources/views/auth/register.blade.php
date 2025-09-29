<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Daftar</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                <div
                    class="w-16 h-16 bg-gradient-to-br from-green-500 to-blue-600 rounded-2xl flex items-center justify-center">
                    <span class="text-white font-bold text-xl">BC</span>
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Buat akun Anda
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                    Masuk di sini
                </a>
            </p>
            <div class="mt-6">
                <a href="{{ route('auth.google') }}"
                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 48 48">
                        <g>
                            <path fill="#4285F4"
                                d="M24 9.5c3.54 0 6.7 1.22 9.18 3.23l6.85-6.85C36.68 2.09 30.74 0 24 0 14.82 0 6.71 5.08 2.69 12.44l7.98 6.2C13.13 13.13 18.17 9.5 24 9.5z" />
                            <path fill="#34A853"
                                d="M46.1 24.55c0-1.64-.15-3.22-.42-4.74H24v9.01h12.43c-.54 2.91-2.17 5.38-4.62 7.04l7.18 5.59C43.98 37.61 46.1 31.62 46.1 24.55z" />
                            <path fill="#FBBC05"
                                d="M10.67 28.64A14.5 14.5 0 019.5 24c0-1.61.28-3.17.77-4.64l-7.98-6.2A23.94 23.94 0 000 24c0 3.77.9 7.34 2.49 10.48l8.18-5.84z" />
                            <path fill="#EA4335"
                                d="M24 48c6.48 0 11.92-2.14 15.89-5.82l-7.18-5.59c-2 1.34-4.56 2.13-8.71 2.13-5.83 0-10.87-3.63-12.83-8.79l-8.18 5.84C6.71 42.92 14.82 48 24 48z" />
                            <path fill="none" d="M0 0h48v48H0z" />
                        </g>
                    </svg>
                    Daftar dengan Google
                </a>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-xl sm:rounded-lg sm:px-10">
                <form class="space-y-6" action="{{ route('auth.store') }}" method="POST">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Nama Lengkap
                        </label>
                        <div class="mt-1 relative">
                            <input id="name" name="name" type="text" autocomplete="name" required
                                value="{{ old('name') }}"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-300 @enderror">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Alamat Email
                        </label>
                        <div class="mt-1 relative">
                            <input id="email" name="email" type="email" autocomplete="email" required
                                value="{{ old('email') }}"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('email') border-red-300 @enderror">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="whatsapp" class="block text-sm font-medium text-gray-700">
                            Nomor WhatsApp
                        </label>
                        <div class="mt-1 relative">
                            <input id="whatsapp" name="whatsapp" type="text" autocomplete="tel"
                                value="{{ old('whatsapp') }}" placeholder="08xxxxxxxxxx"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('whatsapp') border-red-300 @enderror">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H5a2 2 0 01-2-2V5zm0 10a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2zm10-10a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zm0 10a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                            </div>
                        </div>
                        @error('whatsapp')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="about" class="block text-sm font-medium text-gray-700">
                            Tentang Diri Anda
                        </label>
                        <div class="mt-1">
                            <textarea id="about" name="about" rows="3" required maxlength="50000"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('about') border-red-300 @enderror"
                                placeholder="Ceritakan sedikit tentang diri Anda...">{{ old('about') }}</textarea>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Maksimal 50.000 huruf</p>
                        @error('about')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Kata Sandi
                        </label>
                        <div class="mt-1 relative">
                            <input id="password" name="password" type="password" autocomplete="new-password"
                                required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('password') border-red-300 @enderror">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <span id="password-toggle" class="cursor-pointer">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM3 19v-6a9 9 0 0118 0v6a2 2 0 01-2 2H5a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                            Konfirmasi Kata Sandi
                        </label>
                        <div class="mt-1 relative">
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                autocomplete="new-password" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <span id="password-confirm-toggle" class="cursor-pointer">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM3 19v-6a9 9 0 0118 0v6a2 2 0 01-2 2H5a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input id="terms" name="terms" type="checkbox" required
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="terms" class="ml-2 block text-sm text-gray-900">
                            Saya setuju dengan
                            <a href="{{ route('tos') }}" class="text-blue-600 hover:text-blue-500">Syarat dan
                                Ketentuan serta Kebijakan Privasi</a>
                        </label>
                    </div>

                    <div>
                        <button type="submit"
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-green-500 to-blue-600 hover:from-green-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-green-200 group-hover:text-green-100" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                    </path>
                                </svg>
                            </span>
                            Buat Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('password-toggle');
        const passwordConfirmInput = document.getElementById('password_confirmation');
        const passwordConfirmToggle = document.getElementById('password-confirm-toggle');

        passwordToggle.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        });

        passwordConfirmToggle.addEventListener('click', function() {
            if (passwordConfirmInput.type === 'password') {
                passwordConfirmInput.type = 'text';
            } else {
                passwordConfirmInput.type = 'password';
            }
        });
    </script>
</body>

</html>
