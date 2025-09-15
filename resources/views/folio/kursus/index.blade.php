<?php
use App\Models\Course;
use function Laravel\Folio\name;
 
name('courses');

$courses = Course::where('is_published', true)->get();
?>

@extends('layouts.base')

@section('title', 'Daftar Kursus - Baricode Community')

@section('content')
    <section class="py-20 md:py-32 px-4 bg-gray-900 text-white min-h-screen">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-bold text-center mb-8">Jelajahi Semua Kursus</h1>
            <p class="text-lg md:text-xl text-gray-400 text-center mb-16">
                Pilih kursus yang sesuai dengan minatmu dan mulai perjalanan belajarmu sekarang.
            </p>

            @if ($courses->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($courses as $course)
                        <div
                            class="bg-gray-800 rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition duration-300 ease-in-out">
                            <a href="#">
                                <img src="{{ $course->thumbnail ?? 'https://via.placeholder.com/600x400.png?text=Thumbnail' }}"
                                    alt="{{ $course->title }}" class="w-full h-48 object-cover">
                            </a>
                            <div class="p-6">
                                <h2 class="text-xl font-semibold mb-2">{{ $course->title }}</h2>
                                <p class="text-gray-400 text-sm mb-4 line-clamp-2">{{ $course->description }}</p>
                                <a href="#"
                                    class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">Lihat
                                    Kursus</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-gray-400 py-16">
                    <p>Belum ada kursus yang tersedia saat ini. Kami akan segera menambahkan konten baru!</p>
                </div>
            @endif
        </div>
    </section>
@endsection
