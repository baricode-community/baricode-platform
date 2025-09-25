<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Course\CourseCategory;
use App\Models\Course\Course;
use App\Models\Course\CourseModule;
use App\Models\Course\CourseModuleLesson;

new class extends Component {
    public $activeTab = 'categories';
    public $categoriesCount = 0;
    public $coursesCount = 0;
    public $modulesCount = 0;
    public $lessonsCount = 0;
    
    public function mount()
    {
        $this->loadStats();
    }
    
    public function loadStats()
    {
        $this->categoriesCount = CourseCategory::count();
        $this->coursesCount = Course::count();
        $this->modulesCount = CourseModule::count();
        $this->lessonsCount = CourseModuleLesson::count();
    }
    
    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }
}; ?>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Manajemen Kursus</h1>
                    <p class="text-gray-600">Kelola semua kategori, kursus, modul, dan pelajaran dari satu tempat</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Kategori</h3>
                                <p class="text-2xl font-bold text-blue-600">{{ $categoriesCount }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 p-6 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Kursus</h3>
                                <p class="text-2xl font-bold text-green-600">{{ $coursesCount }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 p-6 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Modul</h3>
                                <p class="text-2xl font-bold text-yellow-600">{{ $modulesCount }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-purple-50 p-6 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Pelajaran</h3>
                                <p class="text-2xl font-bold text-purple-600">{{ $lessonsCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <div class="border-b border-gray-200 mb-8">
                    <nav class="-mb-px flex space-x-8">
                        <button 
                            wire:click="setActiveTab('categories')" 
                            class="{{ $activeTab === 'categories' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                            Kategori
                        </button>
                        <button 
                            wire:click="setActiveTab('courses')" 
                            class="{{ $activeTab === 'courses' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                            Kursus
                        </button>
                        <button 
                            wire:click="setActiveTab('modules')" 
                            class="{{ $activeTab === 'modules' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                            Modul
                        </button>
                        <button 
                            wire:click="setActiveTab('lessons')" 
                            class="{{ $activeTab === 'lessons' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                            Pelajaran
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div>
                    @if($activeTab === 'categories')
                        <livewire:admin.category-management />
                    @elseif($activeTab === 'courses')
                        <livewire:admin.course-list />
                    @elseif($activeTab === 'modules')
                        <livewire:admin.module-management />
                    @elseif($activeTab === 'lessons')
                        <livewire:admin.lesson-management />
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
