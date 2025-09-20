<x-layouts.app :title="__('Course Details')">
    <div class="py-12 px-4 md:px-6 lg:px-8 bg-white text-gray-900 dark:bg-gray-900 dark:text-white min-h-screen">
        <div class="max-w-7xl mx-auto">
            @if(isset($lesson))
                <div class="mb-8">
                    <h1 class="text-3xl font-bold mb-4">{{ $lesson->title }}</h1>
                    <div class="prose dark:prose-invert max-w-none">
                        {!! $lesson->content !!}
                    </div>
                </div>
            @else
                <div class="text-red-500">
                    {{ __('Lesson not found.') }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>