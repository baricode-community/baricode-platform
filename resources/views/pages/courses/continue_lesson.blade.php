<x-layouts.app :title="__('Course Details')">
    <div class="py-12 px-4 md:px-6 lg:px-8 bg-white text-gray-900 dark:bg-gray-900 dark:text-white min-h-screen">
        <div class="max-w-4xl mx-auto">
            <!-- Tombol Kembali -->
            <div class="mb-6 flex items-center gap-2">
                <a href="{{ route('course.continue', $lesson->module->course) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-300 dark:hover:bg-gray-700 transition">
                    <span class="mr-2">🔙</span> {{ __('Kembali') }}
                </a>
            </div>

            @if(isset($lesson))
                <!-- Informasi Kursus & Modul -->
                <div class="mb-4 p-4 rounded-lg bg-blue-50 dark:bg-blue-900 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                    <div>
                        <div class="font-semibold text-lg mb-1">📚 {{ $lesson->module->course->title ?? '-' }}</div>
                        <div class="text-sm text-blue-700 dark:text-blue-200">🗂️ {{ $lesson->module->title ?? '-' }}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500 dark:text-gray-300">Pelajaran ke-{{ $lesson->order ?? '-' }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-300">|</span>
                        <span class="text-xs text-gray-500 dark:text-gray-300">👤 {{ Auth::user()->name ?? 'Guest' }}</span>
                    </div>
                </div>

                <!-- Judul & Konten Lesson -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold mb-4 flex items-center gap-2">📖 {{ $lesson->title }}</h1>
                    <div class="prose dark:prose-invert max-w-none">
                        {!! $lesson->content !!}
                    </div>
                </div>
            @else
                <div class="text-red-500 flex items-center gap-2">
                    ❌ {{ __('Lesson not found.') }}
                </div>
            @endif

            <!-- Footer motivasi -->
            <div class="mt-12 text-center text-sm text-gray-400 dark:text-gray-500">
                🌟 {{ __('Tetap semangat belajar! Setiap langkah membawa kamu lebih dekat ke tujuan.') }}
            </div>
        </div>
    </div>
</x-layouts.app>