<?php

use App\Models\Course\CourseCategory;
use Livewire\Volt\Component;

new class extends Component {
    public $courseCategory;
    public $courses;

    public function mount(CourseCategory $courseCategory)
    {
        $this->courseCategory = $courseCategory;
        $this->courses = $courseCategory->courses;
    }
};

?>

<div>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white">Kursus dalam Kategori: {{ $courseCategory->name }}</h1>

        @if($courses->isEmpty())
            <p class="text-gray-600 dark:text-gray-300">Tidak ada kursus dalam kategori ini.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($courses as $course)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                        <div class="p-4">
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $course->title }}</h2>
                            <p class="text-gray-600 dark:text-gray-300 mt-2">{{ Str::limit($course->description, 100) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>