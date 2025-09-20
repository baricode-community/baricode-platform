<x-layouts.app :title="__('Course Details')">
    <div class="py-12 px-4 md:px-6 lg:px-8 bg-white text-gray-900 dark:bg-gray-900 dark:text-white min-h-screen">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">
                📚 {{ $course->title }}
            </h1>
            <p class="text-lg md:text-xl text-gray-400 dark:text-gray-400 mb-8">
                ℹ️ {{ $course->description }}
            </p>
            
            <div class="bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white p-8 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Course Information -->
                    <div>
                        <h2 class="text-2xl font-semibold mb-4">📋 Informasi Kursus</h2>
                        <p><strong>🎯 Level:</strong> {{ $course->category->level }}</p>
                        <p><strong>🏷️ Kategori:</strong> {{ $course->category->name }}</p>
                        @if(!empty($course->category->description))
                            <p class="mt-2 text-gray-500 dark:text-gray-300">
                                <strong>📝 Deskripsi Kategori:</strong> {{ $course->category->description }}
                            </p>
                        @endif
                    </div>

                    <!-- Progress Information -->
                    <div>
                        <h2 class="text-2xl font-semibold mb-4">📊 Progress Belajar</h2>
                        @php $progress = [
                            'percentage' => 45,
                            'completed_modules' => 9,
                            'total_modules' => 20,
                            'last_accessed' => '2024-06-15 14:30'
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
                        <p><strong>✅ Modul Selesai:</strong> {{ $progress['completed_modules'] }}/{{ $course->modules()->count() }}</p>
                    </div>
                </div>

                <!-- Course Modules -->
                @php
                    $modules = $course->modules()->get();
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
                                    <span>{{ $module->title }}</span>
                                </div>
                            </div>
                            @php
                                $lessons = $module->lessons()->get();
                            @endphp
                            @if(count($lessons))
                                <div class="ml-8 mt-2 space-y-1">
                                    @foreach($lessons as $lesson)
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs">{{ $lesson->is_completed ? '✅' : '📖' }}</span>
                                            <span class="text-sm">{{ $lesson->title }}</span>
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