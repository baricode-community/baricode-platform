@extends('layouts.base')

@section('title', 'Menengah - Baricode Community')

@section('content')
    <section class="py-20 md:py-32 px-4 bg-gray-900 text-white min-h-screen">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-bold text-center mb-8">Kursus untuk Menengah</h1>
            <p class="text-lg md:text-xl text-gray-400 text-center mb-16">
                Tingkatkan keahlianmu dengan kursus-kursus level menengah yang lebih menantang.
            </p>
        </div>
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($categories as $category)
                <div>
                    <h2 class="text-2xl font-semibold mb-4">{{ $category->name }}</h2>
                    <ul>
                        @forelse ($category->courses as $course)
                            @if ($course->is_published)
                            <li class="mb-4 bg-gray-800 rounded-lg p-6 shadow hover:shadow-lg transition">
                                <a href="{{ route('course.show', $course->slug) }}" class="block text-xl font-bold text-blue-400 hover:underline">
                                    {{ $course->title }}
                                </a>
                                <p class="text-gray-300 mt-2">{{ Str::limit($course->description, 100) }}</p>
                            </li>
                            @endif
                        @empty
                            <li class="text-gray-500">Belum ada kursus di kategori ini.</li>
                        @endforelse
                    </ul>
                </div>
            @empty
                <div class="col-span-full text-center text-gray-500">
                    Belum ada kategori untuk menengah.
                </div>
            @endforelse
        </div>
    </section>
@endsection
