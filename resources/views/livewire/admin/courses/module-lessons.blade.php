<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Course\CourseModule;

new #[Layout('layouts.app')] class extends Component {
    public $moduleId;
    public $module;
    
    public function mount($moduleId)
    {
        $this->moduleId = $moduleId;
        $this->module = CourseModule::with(['course.courseCategory'])->findOrFail($moduleId);
    }
}; ?>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Header with module info -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">Pelajaran untuk "{{ $module->name }}"</h1>
                            <p class="text-gray-600">
                                Modul dari kursus: <strong>{{ $module->course->title }}</strong>
                                @if($module->course->courseCategory)
                                    <span class="inline-flex px-2 py-1 ml-2 text-xs font-medium rounded-full
                                        {{ $module->course->courseCategory->level === 'pemula' ? 'bg-green-100 text-green-800' : 
                                           ($module->course->courseCategory->level === 'menengah' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $module->course->courseCategory->name }}
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <a 
                                href="{{ route('admin.course.modules', $module->course_id) }}"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Kembali ke Modul
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Lesson management component with module filter -->
                <livewire:admin.module-lesson-management :moduleId="$moduleId" />
            </div>
        </div>
    </div>
</div>
