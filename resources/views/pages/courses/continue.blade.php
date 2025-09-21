<x-layouts.app :title="__('Course Details')">
    <div class="py-12 px-4 md:px-6 lg:px-8 bg-white text-gray-900 dark:bg-gray-900 dark:text-white min-h-screen">
        <div class="max-w-7xl mx-auto">
            @php
                $course = $courseRecord->course;
            @endphp
            <h1 class="text-3xl md:text-4xl font-bold mb-4">
                📚 {{ $course->title }}
            </h1>
            <p class="text-lg md:text-xl text-gray-400 dark:text-gray-400 mb-8">
                ℹ️ {{ $course->description }}
            </p>
            
            <div class="bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white p-8 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-2xl font-semibold mb-4">📋 Informasi Kursus</h2>
                        <p><strong>🎯 Level:</strong> {{ $course->category->level }}</p>
                        @php
                            $courseRecordSessions = $courseRecord->courseRecordSessions;
                        @endphp
                        @if(isset($courseRecordSessions) && count($courseRecordSessions))
                            <div class="mt-6">
                                <h3 class="font-semibold mb-3 flex items-center gap-2">
                                    <span class="text-lg">🗓️</span> 
                                    <span>Jadwal Sesi Pembelajaran</span>
                                </h3>
                                <ul class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900 rounded-lg shadow-sm">
                                    @foreach($courseRecordSessions as $session)
                                        <li class="py-3 px-4 flex flex-col md:flex-row md:items-center md:justify-between">
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium text-blue-600 dark:text-blue-400">
                                                    {{ \Illuminate\Support\Str::ucfirst($session->getHari()) }}
                                                </span>
                                                <span class="text-gray-500 dark:text-gray-400 text-xs">
                                                    (Sesi: 
                                                    {{ $session->reminder_1 ? \Carbon\Carbon::parse($session->reminder_1)->format('H:i') : '' }},
                                                    {{ $session->reminder_2 ? \Carbon\Carbon::parse($session->reminder_2)->format('H:i') : '' }},
                                                    {{ $session->reminder_3 ? \Carbon\Carbon::parse($session->reminder_3)->format('H:i') : '' }}
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

                    <!-- Progress Information -->
                    <div>
                        <h2 class="text-2xl font-semibold mb-4">📊 Progress Belajar</h2>
                        @php $progress = [
                            'percentage' => 45,
                            'completed_modules' => 9,
                            'total_modules' => $course->modules()->count()
                        ]; @endphp
                        <div class="mb-4">
                            <div class="flex justify-between mb-2">
                                <span>📈 Progress Keseluruhan</span>
                                <span>{{ $progress['percentage'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progress['percentage'] }}%"></div>
                            </div>
                        </div>
                        <p><strong>✅ Modul Selesai:</strong> {{ $progress['completed_modules'] }}/{{ $progress['total_modules'] }}</p>
                    </div>
                </div>
                
                
                <!-- Course Modules -->
                @php
                    $modules = $courseRecord->moduleRecords()->get();
                    // dd($modules);
                @endphp
                <div class="mt-8">
                    <h2 class="text-2xl font-semibold mb-4">📚 Daftar Modul</h2>
                    <div class="space-y-4">
                        @foreach($modules as $module)
                        <div class="flex flex-col gap-2 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    @if($module->is_completed)
                                        <span class="text-green-500 mr-3">✅</span>
                                    @else
                                        <span class="mr-3">📝</span>
                                    @endif
                                    <span>{{ $module->module->title }}</span>
                                </div>
                            </div>
                            @php
                                $lessons = $module->lessonRecords()->get();
                                // dd($lessons);
                            @endphp
                            @if(count($lessons))
                                <div class="ml-8 mt-2 space-y-1">
                                    @foreach($lessons as $lesson)
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs">{{ $lesson->is_completed ? '✅' : '📖' }}</span>
                                            <a href="{{ route('course.continue.lesson', [$courseRecord->id, $lesson->id]) }}" class="text-sm text-blue-600 hover:underline">
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