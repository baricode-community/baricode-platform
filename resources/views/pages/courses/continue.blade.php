<x-layouts.app :title="__('Course Details')">
    <div class="bg-gradient-to-br from-blue-50 via-white to-blue-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 text-gray-900 dark:text-white">
        <div class="">
            @php  $course = $enrollment->course; @endphp

            <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-extrabold mb-2 flex items-center gap-2">
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full p-2">📚</span>
                        {{ $course->title }}
                    </h1>
                    <p class="text-lg text-gray-500 dark:text-gray-400 max-w-2xl">
                        {{ $course->description }}
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 text-sm font-semibold">
                        🎯 Level: {{ $course->courseCategory->level }}
                    </span>
                </div>
            </div>

            <div class="bg-white/80 dark:bg-gray-800/80 shadow-xl rounded-2xl p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Informasi Kursus -->
                    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2 text-blue-700 dark:text-blue-300">
                            <span class="text-3xl">📋</span> Informasi Kursus
                        </h2>
                        @php $enrollmentSessions = $enrollment->courseRecordSessions; @endphp
                        @if(isset($enrollmentSessions) && count($enrollmentSessions))
                            <div class="mt-6">
                                <h3 class="font-semibold mb-3 flex items-center gap-2 text-blue-600 dark:text-blue-400">
                                    <span class="text-xl">🗓️</span> Jadwal Sesi Pembelajaran
                                </h3>
                                <ul class="divide-y divide-gray-200 dark:divide-gray-700 bg-blue-50 dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                                    @foreach($enrollmentSessions as $session)
                                        <li class="py-4 px-5 flex flex-col md:flex-row md:items-center md:justify-between hover:bg-blue-100 dark:hover:bg-gray-700 transition-all duration-200">
                                            <div class="flex items-center gap-3">
                                                <span class="font-semibold text-blue-700 dark:text-blue-300 text-lg">
                                                    {{ \Illuminate\Support\Str::ucfirst($session->getNamaHari()) }}
                                                </span>
                                                <span class="text-gray-500 dark:text-gray-400 text-xs">
                                                    (Sesi:
                                                    <span class="font-mono">{{ $session->reminder_1 ? \Carbon\Carbon::parse($session->reminder_1)->format('H:i') : '-' }}</span>,
                                                    <span class="font-mono">{{ $session->reminder_2 ? \Carbon\Carbon::parse($session->reminder_2)->format('H:i') : '-' }}</span>,
                                                    <span class="font-mono">{{ $session->reminder_3 ? \Carbon\Carbon::parse($session->reminder_3)->format('H:i') : '-' }}</span>
                                                    )
                                                </span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <p class="mt-6 text-sm text-gray-500 italic flex items-center gap-2">
                                <span class="text-xl">😔</span> Belum ada jadwal sesi pembelajaran.
                            </p>
                        @endif
                    </div>

                    <!-- Status Waktu Belajar -->
                    <div class="flex flex-col justify-center items-center h-full bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6">
                        @if($enrollment->isWaktunyaBelajar())
                            <span class="inline-flex items-center px-4 py-2 rounded-full bg-blue-200 dark:bg-blue-800 text-blue-800 dark:text-blue-200 text-base font-semibold shadow">
                                <span class="text-2xl animate-pulse">⏰</span>
                                <span class="ml-2">Saat ini waktunya belajar!</span>
                            </span>
                            <p class="mt-4 text-sm text-blue-600 dark:text-blue-300 text-center">
                                Manfaatkan waktu ini untuk menyelesaikan modul dan pelajaran yang tersedia.
                            </p>
                        @else
                            <span class="inline-flex items-center px-4 py-2 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-base font-semibold shadow">
                                <span class="text-2xl">⏰</span>
                                <span class="ml-2">Bukan waktu belajar saat ini.</span>
                            </span>
                            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                                Silakan cek jadwal sesi pembelajaran untuk mengetahui waktu belajar berikutnya.
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Daftar Modul -->
                @php $modules = $enrollment->enrollmentModules()->get(); @endphp
                <div class="mt-10">
                    <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
                        <span>📚</span> Daftar Modul
                    </h2>
                    <div class="space-y-4">
                        @foreach($modules as $module)
                            <div class="flex flex-col gap-2 p-5 bg-blue-50 dark:bg-gray-700 rounded-xl shadow hover:shadow-lg transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        @if($module->is_completed)
                                            <span class="text-green-500 text-xl">✅</span>
                                        @else
                                            <span class="text-yellow-500 text-xl">📝</span>
                                        @endif
                                        <span class="font-semibold text-lg">{{ $module->courseModule->name ?? 'Module Name Not Available' }}</span>
                                    </div>
                                </div>
                                @php $lessonProgresses = $module->enrollmentLessons()->get(); @endphp
                                @if(count($lessonProgresses))
                                    <div class="ml-8 mt-2 space-y-1">
                                        @foreach($lessonProgresses as $lesson)
                                            <div class="flex items-center gap-2 group">
                                                <span class="text-xs">{{ $lesson->is_completed ? '✅' : '📖' }}</span>
                                                <a href="{{ route('course.continue.lesson', [$enrollment->id, $lesson->id]) }}"
                                                   class="text-sm text-blue-700 dark:text-blue-300 group-hover:underline transition">
                                                    {{ $lesson->lesson->title }}
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>