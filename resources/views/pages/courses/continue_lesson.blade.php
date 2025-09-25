<x-layouts.app :title="__('Course Details')">
    <div class="">
        <div class="">
            <!-- Tombol Kembali & Mark as Learned -->
            <div class="mb-8 flex items-center gap-4">
                <a href="{{ route('course.continue', $enrollment->id) }}" wire:navigate
                   class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-200 to-blue-400 dark:from-blue-800 dark:to-blue-600 text-blue-900 dark:text-blue-100 rounded-lg shadow hover:scale-105 hover:from-blue-300 hover:to-blue-500 dark:hover:from-blue-700 dark:hover:to-blue-500 transition-all font-semibold">
                    <span class="mr-2 text-lg">←</span> {{ __('Kembali') }}
                </a>
                @livewire('course.mark-as-learned', ['enrollmentLesson' => $enrollmentLesson, 'enrollmentId' => $enrollment->id])
            </div>

            @if(isset($enrollmentLesson) && $enrollmentLesson->lesson)
                <!-- Judul & Konten Lesson -->
                <div class="mb-10">
                    <h1 class="text-4xl font-extrabold mb-6 flex items-center gap-3 text-blue-800 dark:text-blue-200 drop-shadow">
                        <span>📖</span> {{ $enrollmentLesson->lesson->title }}
                    </h1>
                    <div class="prose dark:prose-invert max-w-none text-lg leading-relaxed">
                        {!! Str::markdown($enrollmentLesson->lesson->content) !!}
                    </div>
                </div>
            @else
                <div class="text-red-500 flex items-center gap-2 text-lg font-semibold">
                    ❌ {{ __('Lesson not found.') }}
                </div>
            @endif

            <!-- Catatan Pribadi -->
            <div class="mb-10">
                <details class="mb-2 group">
                    <summary class="cursor-pointer text-xl font-bold flex items-center gap-2 select-none transition-colors group-open:text-blue-700 dark:group-open:text-blue-300">
                        📝 <span>Simpan Catatan Pribadi di Sini</span>
                    </summary>
                    <div class="mt-4 p-4 rounded-lg bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 shadow-inner">
                        @livewire('course.lesson-notes', ['courseModuleLesson' => $enrollmentLesson->lesson])
                    </div>
                </details>
            </div>

            <!-- Footer motivasi -->
            <div class="mt-16 text-center text-base text-gray-400 dark:text-gray-500 italic tracking-wide">
                🌟 <span class="font-semibold">{{ __('Tetap semangat belajar! Setiap langkah membawa kamu lebih dekat ke tujuan.') }}</span>
            </div>
        </div>
    </div>
</x-layouts.app>