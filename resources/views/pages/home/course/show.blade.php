@extends('layouts.base')

@section('title', $course->title)

@section('content')
<section class="py-20 md:py-32 px-4 bg-gray-900 text-white min-h-screen">
    <div class="max-w-4xl mx-auto text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $course->title }}</h1>
        <p class="text-lg md:text-xl text-gray-400">{{ $course->description }}</p>
    </div>

    <div class="max-w-5xl mx-auto bg-gray-800 rounded-lg shadow-xl p-8">
        @if($course->courseModules->count() > 0)
            <h2 class="text-2xl font-bold mb-6 text-center">Daftar Modul</h2>
            <div class="space-y-4">
                @foreach ($course->courseModules as $module)
                    <div class="bg-gray-700 rounded-lg p-4">
                        <h3 class="text-xl font-semibold mb-2">
                            <span class="text-indigo-400 font-bold mr-2">{{ $loop->iteration }}.</span>
                            {{ $module->name }}
                        </h3>
                        <div class="ml-8 mt-2 space-y-2">
                            @if($module->lessonDetails->count() > 0)
                                @foreach ($module->lessonDetails as $lesson)
                                    <div class="flex items-center">
                                        <span class="text-indigo-400 font-bold mr-2">{{ $loop->iteration }}.</span>
                                        <span>{{ $lesson->title }}</span>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-gray-400 italic">Daftar pelajaran untuk modul ini akan segera ditambahkan.</p>
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="text-center mt-8">
                    <a href="{{ route('course.prepare', ['course' => $course]) }}" class="inline-block bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg">
                        Mulai Kursus
                    </a>
                </div>
            </div>
        @else
            <div class="text-center text-gray-400 py-8">
                <p>Belum ada modul yang tersedia untuk kursus ini.</p>
            </div>
        @endif
    </div>
</section>
@endsection