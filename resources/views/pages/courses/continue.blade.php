<x-layouts.app :title="__('Course Details')">
    <div class="bg-gradient-to-br from-blue-50 via-white to-blue-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 text-gray-900 dark:text-white">
        <div class="">
            @php  $course = $courseEnrollment->course; @endphp

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
                    <div>
                        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
                            <span>📋</span> Informasi Kursus
                        </h2>
                        @php $courseEnrollmentSessions = $courseEnrollment->courseRecordSessions; @endphp
                        @if(isset($courseEnrollmentSessions) && count($courseEnrollmentSessions))
                            <div class="mt-6">
                                <h3 class="font-semibold mb-3 flex items-center gap-2 text-blue-600 dark:text-blue-400">
                                    <span>🗓️ Jadwal Sesi Pembelajaran</span>
                                </h3>
                                <ul class="divide-y divide-gray-200 dark:divide-gray-700 bg-blue-50 dark:bg-gray-900 rounded-lg shadow-sm">
                                    @foreach($courseEnrollmentSessions as $session)
                                        <li class="py-3 px-4 flex flex-col md:flex-row md:items-center md:justify-between hover:bg-blue-100 dark:hover:bg-gray-800 transition">
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium text-blue-700 dark:text-blue-300">
                                                    {{ \Illuminate\Support\Str::ucfirst($session->getHari()) }}
                                                </span>
                                                <span class="text-gray-500 dark:text-gray-400 text-xs">
                                                    (Sesi:
                                                    {{ $session->reminder_1 ? \Carbon\Carbon::parse($session->reminder_1)->format('H:i') : '-' }},
                                                    {{ $session->reminder_2 ? \Carbon\Carbon::parse($session->reminder_2)->format('H:i') : '-' }},
                                                    {{ $session->reminder_3 ? \Carbon\Carbon::parse($session->reminder_3)->format('H:i') : '-' }}
                                                    )
                                                </span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <p class="mt-6 text-sm text-gray-500 italic">Belum ada jadwal sesi pembelajaran.</p>
                        @endif
                    </div>

                    <!-- Progress Belajar -->
                    <div>
                        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
                            <span>📊</span> Progress Belajar
                        </h2>
                        @php
                            $progress = [
                                'percentage' => 45,
                                'completed_modules' => 9,
                                'total_modules' => $course->courseModules()->count()
                            ];
                        @endphp
                        <div class="mb-4">
                            <div class="flex justify-between mb-2">
                                <span class="font-medium">📈 Progress Keseluruhan</span>
                                <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $progress['percentage'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700">
                                <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ $progress['percentage'] }}%"></div>
                            </div>
                        </div>
                        <p class="mt-2 text-sm">
                            <strong>✅ Modul Selesai:</strong>
                            <span class="text-green-600 dark:text-green-400">{{ $progress['completed_modules'] }}</span>
                            /
                            <span class="text-gray-600 dark:text-gray-300">{{ $progress['total_modules'] }}</span>
                        </p>
                    </div>
                </div>

                <!-- Daftar Modul -->
                @php $modules = $courseEnrollment->moduleProgresses()->get(); @endphp
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
                                        <span class="font-semibold text-lg">{{ $module->module->name ?? 'Module Name Not Available' }}</span>
                                    </div>
                                </div>
                                @php $lessonProgresses = $module->lessonProgresses()->get(); @endphp
                                @if(count($lessonProgresses))
                                    <div class="ml-8 mt-2 space-y-1">
                                        @foreach($lessonProgresses as $lesson)
                                            <div class="flex items-center gap-2 group">
                                                <span class="text-xs">{{ $lesson->is_completed ? '✅' : '📖' }}</span>
                                                <a href="{{ route('course.continue.lesson', [$courseEnrollment->id, $lesson->id]) }}"
                                                   class="text-sm text-blue-700 dark:text-blue-300 group-hover:underline transition">
                                                    {{ $lesson->lessonDetail->title }}
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