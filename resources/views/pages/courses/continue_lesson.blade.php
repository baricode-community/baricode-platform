<x-layouts.app :title="__('Course Details')">
    <div class="">
        <div class="">
            <!-- Tombol Kembali & Mark as Learned -->
            <div class="mb-8 flex items-center gap-4">
                <a href="{{ route('course.continue', $courseRecord->id) }}" wire:navigate
                   class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-200 to-blue-400 dark:from-blue-800 dark:to-blue-600 text-blue-900 dark:text-blue-100 rounded-lg shadow hover:scale-105 hover:from-blue-300 hover:to-blue-500 dark:hover:from-blue-700 dark:hover:to-blue-500 transition-all font-semibold">
                    <span class="mr-2 text-lg">←</span> {{ __('Kembali') }}
                </a>
                @php
                    $lessonRecord = $courseRecord->moduleRecords->first()->lessonRecords->first();
                    // dd($lesson);
                @endphp
                @livewire('course.mark-as-learned', ['lessonRecord' => $lessonRecord, 'courseRecordId' => $courseRecord->id])
            </div>

            @if(isset($lesson))
                <!-- Informasi Kursus & Modul -->
                <div class="mb-6 p-6 rounded-xl bg-gradient-to-r from-blue-100 to-blue-200 dark:from-blue-900 dark:to-blue-800 shadow flex flex-col md:flex-row md:items-center md:justify-between gap-4 border border-blue-200 dark:border-blue-800">
                    <div>
                        <div class="font-bold text-2xl mb-1 flex items-center gap-2">
                            <span class="text-blue-500 dark:text-blue-300">📚</span>
                            {{ $lesson->module->course->title ?? '-' }}
                        </div>
                        <div class="text-base text-blue-700 dark:text-blue-200 flex items-center gap-2">
                            <span>🗂️</span> {{ $lesson->module->title ?? '-' }}
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-xs px-3 py-1 bg-blue-200 dark:bg-blue-700 rounded-full text-blue-900 dark:text-blue-100 font-medium shadow">
                            Pelajaran ke-{{ $lesson->order ?? '-' }}
                        </span>
                        <span class="text-xs text-gray-500 dark:text-gray-300">|</span>
                        <span class="text-xs px-3 py-1 bg-gray-100 dark:bg-gray-800 rounded-full text-gray-700 dark:text-gray-200 font-medium shadow flex items-center gap-1">
                            👤 {{ Auth::user()->name ?? 'Guest' }}
                        </span>
                    </div>
                </div>

                <!-- Judul & Konten Lesson -->
                <div class="mb-10">
                    <h1 class="text-4xl font-extrabold mb-6 flex items-center gap-3 text-blue-800 dark:text-blue-200 drop-shadow">
                        <span>📖</span> {{ $lesson->title }}
                    </h1>
                    <div class="prose dark:prose-invert max-w-none text-lg leading-relaxed">
                        {!! $lesson->content !!}
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
                        @livewire('lesson-notes', ['lesson' => $lessonRecord->lesson])
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