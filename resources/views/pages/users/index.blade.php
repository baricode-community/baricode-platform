<x-layouts.app :title="__('Daftar Pengguna')">
    {{-- Background: Gradien Lembut --}}
    <div class="bg-gray-50 dark:bg-gray-900 py-10 px-4 sm:px-6 lg:px-8 transition-colors duration-500">
        <div class="max-w-7xl mx-auto">

            {{-- HEADER MODERN DENGAN GLASSMORPHISM RINGAN --}}
            <div class="mb-10">
                <div class="relative overflow-hidden bg-white/20 dark:bg-gray-800/50 backdrop-blur-lg border border-white/30 dark:border-gray-700/50 rounded-3xl shadow-xl transition-all duration-300 p-6 md:p-8">
                    
                    {{-- Efek Dekoratif (Ringan) --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-400/10 to-indigo-600/10 opacity-70 rounded-3xl"></div>
                    <div class="absolute top-4 left-4 w-20 h-20 bg-blue-300/30 rounded-full blur-3xl animate-blob-slow"></div>
                    <div class="absolute bottom-4 right-4 w-32 h-32 bg-indigo-300/30 rounded-full blur-3xl animate-blob-delay"></div>
                    
                    <div class="relative z-10 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            {{-- Icon Besar --}}
                            <div class="w-14 h-14 bg-white/50 dark:bg-gray-900/50 border border-white/50 dark:border-gray-700/50 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform duration-300">
                                <svg class="w-8 h-8 text-indigo-700 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white leading-tight">
                                    Daftar Pengguna
                                </h1>
                                <p class="text-gray-700 dark:text-gray-300 text-sm mt-1">
                                    Manajemen semua akun pengguna pada platform.
                                </p>
                            </div>
                        </div>

                        {{-- Statistik Ringkas --}}
                        <div class="hidden sm:block text-right">
                            <span class="px-4 py-2 bg-indigo-500/80 backdrop-blur-md rounded-full text-white text-sm font-semibold shadow-md transform hover:scale-105 transition-transform duration-300">
                                <span class="text-lg font-bold">{{ $users->count() }}</span> Total Pengguna
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Tambahkan Style Animasi CSS di sini atau di file CSS Anda --}}
            <style>
                @keyframes blob-slow {
                    0%, 100% { transform: translate(0, 0) scale(1); }
                    30% { transform: translate(30px, -20px) scale(1.1); }
                    60% { transform: translate(-20px, 40px) scale(0.9); }
                }
                @keyframes blob-delay {
                    0%, 100% { transform: translate(0, 0) scale(1); }
                    40% { transform: translate(-30px, 10px) scale(1.05); }
                    70% { transform: translate(20px, -30px) scale(0.95); }
                }
                .animate-blob-slow { animation: blob-slow 18s infinite alternate; }
                .animate-blob-delay { animation: blob-delay 15s infinite alternate; }
            </style>


            {{-- TABEL PENGGUNA MODERN --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700 transform transition-all duration-300 hover:shadow-3xl">
                
                {{-- Table Header --}}
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                            <i class="fas fa-list-ul mr-2 text-indigo-500"></i> Daftar Detail
                        </h2>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700/50">
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Pengguna
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider hidden sm:table-cell">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-3 8a8.657 8.657 0 006.364-2.636l-.637-.623C16.59 20.02 14.401 21 12 21s-4.59-.98-5.727-4.259l-.637.623A8.657 8.657 0 0012 20z"></path>
                                        </svg>
                                        Username
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider hidden md:table-cell">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Bergabung
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($users as $user)
                                <tr class="hover:bg-indigo-50/50 dark:hover:bg-gray-700/70 transition-colors duration-200 ease-in-out group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-4">
                                            {{-- Avatar dengan Animasi dan Border --}}
                                            <div class="relative flex-shrink-0">
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm shadow-md group-hover:ring-4 group-hover:ring-indigo-300/50 dark:group-hover:ring-indigo-700/50 transition-all duration-300">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                {{-- Status Online Indicator --}}
                                                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 ring-2 ring-white dark:ring-gray-800 rounded-full transform group-hover:scale-125 transition-transform duration-300"></span>
                                            </div>
                                            <div>
                                                <a href="{{ route('users.show', $user->id) }}" class="text-sm font-semibold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200 group-hover:underline">
                                                    {{ $user->name }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 transition-all duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $user->username }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 transition-all duration-200">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $user->created_at->format('d M Y') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center bg-gray-50 dark:bg-gray-800/70">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900/50 border-4 border-indigo-200 dark:border-indigo-800 rounded-full flex items-center justify-center animate-bounce-slow">
                                                <svg class="w-8 h-8 text-indigo-500 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </div>
                                            <p class="text-gray-600 dark:text-gray-400 font-semibold text-lg">Belum ada pengguna yang terdaftar</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-500">Silakan tambahkan pengguna baru untuk memulai.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>