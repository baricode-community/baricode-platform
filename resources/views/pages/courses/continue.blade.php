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
                        <p><strong>👨‍🏫 Instruktur:</strong> {{ $course->instructor }}</p>
                        <p><strong>⏱️ Durasi:</strong> {{ $course->duration }} jam</p>
                        <p><strong>🎯 Level:</strong> {{ $course->level }}</p>
                        <p><strong>🏷️ Kategori:</strong> {{ $course->category }}</p>
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
                        <p><strong>✅ Modul Selesai:</strong> {{ $progress['completed_modules'] }}/{{ $progress['total_modules'] }}</p>
                        <p><strong>🕒 Terakhir Akses:</strong> {{ $progress['last_accessed'] }}</p>
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
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                @if($module->is_completed)
                                    <span class="text-green-500 mr-3">✅</span>
                                @else
                                    <span class="mr-3">📝</span>
                                @endif
                                <span>{{ $module->title }}</span>
                            </div>
                            <a href="" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                {{ $module->is_completed ? '🔄 Ulangi' : '▶️ Mulai' }}
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>