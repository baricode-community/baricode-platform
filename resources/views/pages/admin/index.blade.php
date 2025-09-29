<x-layouts.app :title="__('Admin')">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold mb-2 flex items-center gap-2 dark:text-white">
                    <x-heroicon-o-cog-6-tooth class="w-8 h-8 text-indigo-500" />
                    Admin Panel
                </h1>
                <div class="flex flex-col md:flex-row md:items-center gap-2 text-gray-600 dark:text-gray-300">
                    <span class="flex items-center gap-1"><x-heroicon-o-user-circle class="w-5 h-5" />
                        {{ auth()->user()->name }} ({{ auth()->user()->email }})</span>
                    <span class="hidden md:inline-block">|</span>
                    <span class="flex items-center gap-1"><x-heroicon-o-calendar class="w-5 h-5 text-gray-600 dark:text-red-400" /> Terdaftar sejak:
                        {{ auth()->user()->created_at->format('d M Y') }}</span>
                </div>
            </div>
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=4f46e5&color=fff&size=80"
                alt="Avatar"
                class="rounded-full shadow-lg border-4 border-indigo-200 dark:border-indigo-400 bg-white w-20 h-20 object-cover hidden md:block">
        </div>

        <!-- Statistik Platform -->
        <h2 class="text-2xl font-bold mb-6 flex items-center gap-2 dark:text-white">
            <x-heroicon-o-chart-bar class="w-7 h-7 text-indigo-500 dark:text-indigo-400" /> Statistik Platform
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="p-8 rounded-xl shadow-xl text-center border bg-white dark:bg-gray-800 dark:border-gray-700 hover:scale-105 transition-transform duration-200">
                <h2 class="text-4xl font-extrabold text-indigo-600 dark:text-indigo-400 flex items-center justify-center gap-2">
                    <x-heroicon-o-users class="w-8 h-8" /> {{ $users_count ?? 0 }}
                </h2>
                <p class="mt-2 text-lg font-medium text-gray-700 dark:text-gray-300">Total Pengguna</p>
            </div>
            <div class="p-8 rounded-xl shadow-xl text-center border bg-white dark:bg-gray-800 dark:border-gray-700 hover:scale-105 transition-transform duration-200">
                <h2 class="text-4xl font-extrabold text-green-600 dark:text-green-400 flex items-center justify-center gap-2">
                    <x-heroicon-o-book-open class="w-8 h-8" /> {{ $courses_count ?? 0 }}
                </h2>
                <p class="mt-2 text-lg font-medium text-gray-700 dark:text-gray-300">Total Kursus</p>
            </div>
            <div class="p-8 rounded-xl shadow-xl text-center border bg-white dark:bg-gray-800 dark:border-gray-700 hover:scale-105 transition-transform duration-200">
                <h2 class="text-4xl font-extrabold text-yellow-600 dark:text-yellow-400 flex items-center justify-center gap-2">
                    <x-heroicon-o-document-text class="w-8 h-8" /> {{ $articlesCount ?? 0 }}
                </h2>
                <p class="mt-2 text-lg font-medium text-gray-700 dark:text-gray-300">Total Artikel</p>
            </div>
        </div>

        <!-- Menu Navigasi Admin -->
        <h2 class="text-2xl font-bold mb-6 flex items-center gap-2 dark:text-white">
            <x-heroicon-o-wrench-screwdriver class="w-7 h-7 text-indigo-500 dark:text-indigo-400" /> Menu Admin & Konfigurasi
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            <a href="{{ route('admin.users') }}" class="block p-6 rounded-xl shadow-lg border bg-white dark:bg-gray-800 dark:border-gray-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition">
                <div class="flex items-center gap-3 mb-2">
                    <x-heroicon-o-users class="w-7 h-7 text-indigo-500" />
                    <span class="font-semibold text-lg dark:text-white">Manajemen Pengguna</span>
                </div>
                <p class="text-gray-600 dark:text-gray-300">Kelola data pengguna, peran, dan akses.</p>
            </a>
            <a href="{{ route('admin.course-categories.index') }}" class="block p-6 rounded-xl shadow-lg border bg-white dark:bg-gray-800 dark:border-gray-700 hover:bg-green-50 dark:hover:bg-green-900/30 transition">
                <div class="flex items-center gap-3 mb-2">
                    <x-heroicon-o-academic-cap class="w-7 h-7 text-green-500" />
                    <span class="font-semibold text-lg dark:text-white">Kelola Kursus Lengkap</span>
                </div>
                <p class="text-gray-600 dark:text-gray-300">Kelola kategori → kursus → modul → pelajaran secara hierarkis.</p>
            </a>
            <a href="{{ route('admin.meets') }}" class="block p-6 rounded-xl shadow-lg border bg-white dark:bg-gray-800 dark:border-gray-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition">
                <div class="flex items-center gap-3 mb-2">
                    <x-heroicon-o-video-camera class="w-7 h-7 text-purple-500" />
                    <span class="font-semibold text-lg dark:text-white">Manajemen Meet</span>
                </div>
                <p class="text-gray-600 dark:text-gray-300">Kelola meet online dan peserta.</p>
            </a>
            <a href="{{ route('admin') }}" class="block p-6 rounded-xl shadow-lg border bg-white dark:bg-gray-800 dark:border-gray-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition">
                <div class="flex items-center gap-3 mb-2">
                    <x-heroicon-o-document-text class="w-7 h-7 text-yellow-500" />
                    <span class="font-semibold text-lg dark:text-white">Manajemen Artikel</span>
                </div>
                <p class="text-gray-600 dark:text-gray-300">Kelola artikel dan konten edukasi.</p>
            </a>
            <a href="{{ route('admin') }}" class="block p-6 rounded-xl shadow-lg border bg-white dark:bg-gray-800 dark:border-gray-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition">
                <div class="flex items-center gap-3 mb-2">
                    <x-heroicon-o-cog-6-tooth class="w-7 h-7 text-gray-500" />
                    <span class="font-semibold text-lg dark:text-white">Konfigurasi Platform</span>
                </div>
                <p class="text-gray-600 dark:text-gray-300">Atur pengaturan umum platform.</p>
            </a>
            <a href="{{ route('admin') }}" class="block p-6 rounded-xl shadow-lg border bg-white dark:bg-gray-800 dark:border-gray-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition">
                <div class="flex items-center gap-3 mb-2">
                    <x-heroicon-o-clipboard-document-list class="w-7 h-7 text-red-500" />
                    <span class="font-semibold text-lg dark:text-white">Log Aktivitas</span>
                </div>
                <p class="text-gray-600 dark:text-gray-300">Pantau aktivitas dan log sistem.</p>
            </a>
            <a href="{{ route('admin') }}" class="block p-6 rounded-xl shadow-lg border bg-white dark:bg-gray-800 dark:border-gray-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition">
                <div class="flex items-center gap-3 mb-2">
                    <x-heroicon-o-lifebuoy class="w-7 h-7 text-blue-500" />
                    <span class="font-semibold text-lg dark:text-white">Bantuan & Dukungan</span>
                </div>
                <p class="text-gray-600 dark:text-gray-300">Kelola tiket bantuan dan FAQ.</p>
            </a>
        </div>

        <!-- Motivasi Admin -->
        <div class="mt-12 bg-gradient-to-r from-indigo-500 to-indigo-700 dark:from-indigo-600 dark:to-indigo-800 p-8 rounded-xl shadow-xl text-center relative overflow-hidden">
            <x-heroicon-o-light-bulb class="w-12 h-12 text-yellow-300 dark:text-yellow-200 mx-auto mb-2 animate-pulse" />
            <h2 class="text-2xl font-bold text-white">💡 Selalu Pantau & Tingkatkan Platform!</h2>
            <p class="mt-2 text-indigo-100 dark:text-indigo-200 text-lg italic">"Admin yang baik adalah kunci sukses komunitas yang berkembang."</p>
            <div class="absolute -bottom-8 -right-8 opacity-20">
                <x-heroicon-o-sparkles class="w-32 h-32 text-white" />
            </div>
        </div>
    </div>
</x-layouts.app>
