<x-layouts.app :title="__('Dashboard')">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold mb-2 flex items-center gap-2 dark:text-white">
                    <span class="animate-bounce">👋</span>
                    Selamat Datang
                </h1>
                <div class="flex flex-col md:flex-row md:items-center gap-2 text-gray-600 dark:text-gray-300">
                    <span class="flex items-center gap-1">
                        {{ Str::limit(auth()->user()->name, 15) }}
                    </span>
                    <span class="hidden md:inline-block">|</span>
                    <span class="flex items-center gap-1"><x-heroicon-o-calendar class="w-5 h-5 text-gray-600 dark:text-red-400" /> Bergabung sejak:
                        {{ auth()->user()->created_at->format('d M Y') }}</span>
                </div>
            </div>
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=4f46e5&color=fff&size=80"
                alt="Avatar"
                class="rounded-full shadow-lg border-4 border-indigo-200 dark:border-indigo-400 bg-white w-20 h-20 object-cover hidden md:block">
        </div>

        <!-- Statistik -->
        <h2 class="text-2xl font-bold mb-6 flex items-center gap-2 dark:text-white">
            <x-heroicon-o-chart-bar class="w-7 h-7 text-indigo-500 dark:text-indigo-400" /> Statistik
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
            <div
                class="p-8 rounded-xl shadow-xl text-center border bg-white dark:bg-gray-800 dark:border-gray-700 hover:scale-105 transition-transform duration-200">
                <h2 class="text-5xl font-extrabold text-indigo-600 dark:text-indigo-400 flex items-center justify-center gap-2">
                    <x-heroicon-o-book-open class="w-10 h-10" /> {{ $courseRecords->count() }}
                </h2>
                <p class="mt-2 text-lg font-medium text-gray-700 dark:text-gray-300">📚 Kursus Sedang Berjalan</p>
            </div>
            <div
                class="p-8 rounded-xl shadow-xl text-center border bg-white dark:bg-gray-800 dark:border-gray-700 hover:scale-105 transition-transform duration-200">
                <h2 class="text-5xl font-extrabold text-green-600 dark:text-green-400 flex items-center justify-center gap-2">
                    <x-heroicon-o-check-circle class="w-10 h-10" />
                    {{ $courseRecords->where('is_finished', true)->count() }}
                </h2>
                <p class="mt-2 text-lg font-medium text-gray-700 dark:text-gray-300">✅ Kursus Selesai</p>
            </div>
        </div>

        <!-- Kursus yang Sedang Diikuti -->
        <h2 class="text-2xl font-bold mb-6 flex items-center gap-2 dark:text-white">
            <x-heroicon-o-rocket-launch class="w-7 h-7 text-indigo-500 dark:text-indigo-400" /> Kursus yang Sedang Kamu Ikuti
        </h2>
        <div class="p-4 rounded-lg mb-6 border bg-indigo-50 dark:bg-indigo-900/20 dark:border-indigo-800">
            <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-1">
                <li>Bila ingin menambah kursus, selesaikan salah satu kursus yang sedang diikuti.</li>
                <li>Bila salah memilih kursus, kamu dapat menghapus progres saat ini dan memilih kursus lain.</li>
            </ul>
        </div>
        <div class="p-8 rounded-xl shadow-xl border bg-white dark:bg-gray-800 dark:border-gray-700">
            @if ($courseRecords->isNotEmpty())
                <ul class="space-y-4">
                    @foreach ($courseRecords as $record)
                        @livewire('dashboard.course-list', ['record' => $record])
                    @endforeach
                </ul>
            @else
                <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                    <x-heroicon-o-information-circle class="w-8 h-8 mx-auto mb-2 text-indigo-400" />
                    Kamu belum mengikuti kursus apapun saat ini.
                </div>
            @endif
            <div class="mt-8 text-center">
                @if (auth()->user()->level === 'pemula')
                    <a href="{{ route('courses.pemula') }}"
                        class="inline-block bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600 text-white font-semibold py-2 px-6 rounded-full shadow transition">
                        Lihat Kursus Pemula
                    </a>
                @elseif(auth()->user()->level === 'menengah')
                    <a href="{{ route('courses.menengah') }}"
                        class="inline-block bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600 text-white font-semibold py-2 px-6 rounded-full shadow transition">
                        Lihat Kursus Menengah
                    </a>
                @elseif(auth()->user()->level === 'lanjut')
                    <a href="{{ route('courses.lanjut') }}"
                        class="inline-block bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600 text-white font-semibold py-2 px-6 rounded-full shadow transition">
                        Lihat Kursus Lanjutan
                    </a>
                @else
                    <a href="{{ route('courses') }}"
                        class="inline-block bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600 text-white font-semibold py-2 px-6 rounded-full shadow transition">
                        Lihat Semua Kursus
                    </a>
                @endif
            </div>
        </div>

        <!-- Motivasi -->
        <div
            class="mt-12 bg-gradient-to-r from-indigo-500 to-indigo-700 dark:from-indigo-600 dark:to-indigo-800 p-8 rounded-xl shadow-xl text-center relative overflow-hidden">
            <x-heroicon-o-light-bulb class="w-12 h-12 text-yellow-300 dark:text-yellow-200 mx-auto mb-2 animate-pulse" />
            <h2 class="text-2xl font-bold text-white">💡 Tetap Semangat!</h2>
            <p class="mt-2 text-indigo-100 dark:text-indigo-200 text-lg italic">"Belajar adalah investasi terbaik yang bisa kamu lakukan
                untuk masa depanmu."</p>
            <div class="absolute -bottom-8 -right-8 opacity-20">
                <x-heroicon-o-sparkles class="w-32 h-32 text-white" />
            </div>
        </div>
    </div>
</x-layouts.app>
