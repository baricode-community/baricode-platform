@extends('layouts.base')

@section('title', 'Daftar Kursus - Baricode Community')

@section('content')
    <section class="py-20 md:py-32 px-4 bg-gray-900 text-white min-h-screen">
        <div class="max-w-3xl mx-auto mb-12 text-center">
            <div class="bg-indigo-700/20 border border-indigo-700 rounded-lg p-6">
                <p class="text-lg md:text-xl font-semibold mb-2">
                    Halaman ini menampilkan semua kursus untuk pemula, menengah, dan lanjut.
                </p>
                <p class="text-gray-300 mb-4">
                    Untuk mendapatkan rekomendasi kursus yang sesuai dengan kebutuhan Anda, silakan lakukan registrasi
                    terlebih dahulu.
                </p>
                <a href="{{ route('login') }}"
                    class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-md transition duration-300">
                    Gabung Sekarang
                </a>
            </div>
        </div>
        <div class="max-w-7xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-bold text-center mb-8">Jelajahi Semua Kursus</h1>
            <p class="text-lg md:text-xl text-gray-400 text-center mb-16">
                Pilih kursus yang sesuai dengan minatmu dan mulai perjalanan belajarmu sekarang.
            </p>

            @if ($categories->count() > 0)
                @php $hasCourses = false; @endphp
                @foreach ($categories as $category)
                    @if ($category->courses->count() > 0)
                        @php $hasCourses = true; @endphp
                        <h2 class="text-2xl font-bold mb-4 mt-12">{{ $category->name }}</h2>
                        <p class="text-gray-400 mb-8">{{ $category->description }}</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
                            @foreach ($category->courses as $course)
                                @if ($course->is_published)
                                    <div
                                        class="bg-gray-800 rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition duration-300 ease-in-out">
                                        <a href="#">
                                            <img src="{{ $course->thumbnail ?? 'https://ui-avatars.com/api/?name=' . urlencode($course->title) . '&background=4F46E5&color=fff' }}"
                                                alt="{{ $course->title }}" class="w-full h-48 object-cover">
                                        </a>
                                        <div class="p-6">
                                            <h3 class="text-xl font-semibold mb-2">{{ $course->title }}</h3>
                                            <p class="text-gray-400 text-sm mb-4 line-clamp-2">{{ $course->description }}
                                            </p>
                                            <a href="{{ route('course.show', $course->slug) }}"
                                                class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">Lihat
                                                Kursus</a>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endforeach
                @if (!$hasCourses)
                    <div class="text-center text-gray-400 py-16">
                        <p>Belum ada kursus yang tersedia saat ini. Kami akan segera menambahkan konten baru!</p>
                    </div>
                @endif
            @else
                <div class="text-center text-gray-400 py-16">
                    <p>Belum ada kursus yang tersedia saat ini. Kami akan segera menambahkan konten baru!</p>
                </div>
            @endif
        </div>
    </section>
@endsection
