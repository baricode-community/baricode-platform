@extends('layouts.base')

@section('title', 'Belajar Ngoding Gratis - 100% Gratis')

@section('content')
    <section
        class="hero text-center py-20 md:py-32 px-4 min-h-screen flex items-center justify-center bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white relative overflow-hidden">
        <!-- Background Image (Student Confused) -->
        <img src="https://images.unsplash.com/photo-1513258496099-48168024aec0?auto=format&fit=crop&w=1200&q=80"
            alt="Mahasiswa Bingung" class="absolute inset-0 w-full h-full object-cover opacity-30 pointer-events-none z-0">
        <div class="max-w-4xl mx-auto relative z-10">
            <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-4 animate-fade-in-up">
                Belajar Ngoding Gratis 100%. <br class="hidden md:inline-block"> Bangun Proyek Bareng.
            </h1>
            <p class="text-lg md:text-xl text-gray-200 mb-8 max-w-2xl mx-auto animate-fade-in-up-2">
                Bergabunglah dengan komunitas yang bersemangat untuk belajar, berbagi, dan berkolaborasi.
            </p>
            <a href="{{ route('cara_belajar') }}" wire:navigate
                class="bg-white text-indigo-600 font-bold py-3 px-8 rounded-full text-lg transition duration-300 transform hover:scale-105 animate-fade-in-up-3 shadow-lg">
                Cara Belajar
            </a>
        </div>
    </section>

    <section class="py-20 px-4 bg-gray-900">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 text-white">Kenapa Bergabung dengan Kami?</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div
                    class="bg-gray-800 p-8 rounded-lg shadow-lg text-center transform hover:scale-105 transition duration-300">
                    <div class="text-4xl mb-4 text-indigo-400">🚀</div>
                    <h3 class="text-xl font-semibold mb-2 text-white">Kurikulum Terstruktur</h3>
                    <p class="text-gray-400">Belajar dari nol hingga mahir dengan kurikulum yang kami rancang sendiri.</p>
                </div>
                <div
                    class="bg-gray-800 p-8 rounded-lg shadow-lg text-center transform hover:scale-105 transition duration-300">
                    <div class="text-4xl mb-4 text-indigo-400">🤝</div>
                    <h3 class="text-xl font-semibold mb-2 text-white">Komunitas Aktif</h3>
                    <p class="text-gray-400">Berinteraksi dengan sesama pengembang, saling bantu, dan bangun koneksi.</p>
                </div>
                <div
                    class="bg-gray-800 p-8 rounded-lg shadow-lg text-center transform hover:scale-105 transition duration-300">
                    <div class="text-4xl mb-4 text-indigo-400">💡</div>
                    <h3 class="text-xl font-semibold mb-2 text-white">Proyek Kolaborasi</h3>
                    <p class="text-gray-400">Terapkan ilmu yang kamu dapat dengan membangun proyek nyata bersama.</p>
                </div>
                <div
                    class="bg-gray-800 p-8 rounded-lg shadow-lg text-center transform hover:scale-105 transition duration-300">
                    <div class="text-4xl mb-4 text-indigo-400">🌟</div>
                    <h3 class="text-xl font-semibold mb-2 text-white">Belajar Fleksibel</h3>
                    <p class="text-gray-400">Akses materi kapan saja dan di mana saja sesuai dengan jadwalmu.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 px-4 bg-gray-900">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 text-white">Kata Mereka yang Sudah Bergabung</h2>
            <div class="mb-8 text-center">
                <span class="inline-block bg-yellow-500 text-gray-900 font-semibold px-4 py-2 rounded-full text-sm">
                    Testimoni di bawah ini hanya contoh (fake) karena kami baru launching versi beta. Data asli akan kami
                    update setelah ada pengguna yang bergabung.
                </span>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                    <p class="text-gray-400 italic mb-4">"Kurikulumnya terstruktur banget, dari nol jadi bisa bikin website
                        sendiri. Komunitasnya juga supportif!"</p>
                    <div class="flex items-center">
                        <img src="https://ui-avatars.com/api/?name=Budi+Santoso&background=4F46E5&color=fff"
                            alt="Foto Profil Pengguna" class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <p class="font-semibold text-white">Budi Santoso</p>
                            <p class="text-sm text-indigo-400">Junior Developer</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                    <p class="text-gray-400 italic mb-4">"Bisa kolaborasi di proyek bareng itu pengalaman yang luar biasa.
                        Sangat membantu untuk portofolio!"</p>
                    <div class="flex items-center">
                        <img src="https://ui-avatars.com/api/?name=Siti+Aisyah&background=4F46E5&color=fff"
                            alt="Foto Profil Pengguna" class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <p class="font-semibold text-white">Siti Aisyah</p>
                            <p class="text-sm text-indigo-400">Mahasiswa Informatika</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                    <p class="text-gray-400 italic mb-4">"Mentoringnya berkualitas. Pertanyaan apapun selalu dijawab dengan
                        sabar dan jelas."</p>
                    <div class="flex items-center">
                        <img src="https://ui-avatars.com/api/?name=Joko+Wicaksono&background=4F46E5&color=fff"
                            alt="Foto Profil Pengguna" class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <p class="font-semibold text-white">Joko Wicaksono</p>
                            <p class="text-sm text-indigo-400">Web Freelancer</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 px-4 bg-gradient-to-r from-gray-800 to-gray-900">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 text-white">Apa yang Akan Kamu Dapatkan?</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-6xl mb-4 text-indigo-400">📚</div>
                    <h3 class="text-xl font-semibold mb-2 text-white">Materi Berkualitas</h3>
                    <p class="text-gray-400">Akses ke materi eksklusif yang dirancang oleh para ahli.</p>
                </div>
                <div class="text-center">
                    <div class="text-6xl mb-4 text-indigo-400">🎓</div>
                    <h3 class="text-xl font-semibold mb-2 text-white">Mentor Berpengalaman</h3>
                    <p class="text-gray-400">Dibimbing oleh mentor yang sudah berpengalaman.</p>
                </div>
                <div class="text-center">
                    <div class="text-6xl mb-4 text-indigo-400">🏆</div>
                    <h3 class="text-xl font-semibold mb-2 text-white">Sertifikat</h3>
                    <p class="text-gray-400">Dapatkan sertifikat untuk setiap kursus yang kamu selesaikan.</p>
                </div>
                <div class="text-center">
                    <div class="text-6xl mb-4 text-indigo-400">🌍</div>
                    <h3 class="text-xl font-semibold mb-2 text-white">Komunitas yang Tangguh</h3>
                    <p class="text-gray-400">Bergabung dengan komunitas yang solid dan saling mendukung untuk tumbuh
                        bersama.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 px-4 bg-gray-900">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-12">Teknologi yang Akan Kamu Kuasai</h2>
            <div class="mb-8">
                <p class="text-gray-400 text-base md:text-lg">
                    Komunitas kami sudah berjalan beberapa bulan tanpa platform khusus. Kini, kami sedang membangun platform
                    yang tangguh agar manfaat belajar, kolaborasi, dan networking bisa dirasakan lebih besar oleh semua
                    anggota.
                </p>
            </div>
            <div class="flex flex-wrap justify-center gap-6">
                <span class="bg-gray-800 text-indigo-400 font-semibold py-2 px-4 rounded-full">Laravel</span>
                <span class="bg-gray-800 text-indigo-400 font-semibold py-2 px-4 rounded-full">Flutter</span>
            </div>
        </div>
    </section>

    <section class="py-20 px-4 bg-gray-900">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-8">Baca Artikel & Blog Terbaru</h2>
            <p class="text-gray-400 mb-8">Dapatkan insight, tips, dan cerita inspiratif seputar dunia ngoding dan komunitas kami.</p>
            <div class="inline-block bg-yellow-500 text-gray-900 font-semibold px-4 py-2 rounded-full text-sm mb-6">
                Segera Hadir
            </div>
            <br>
            <a href="{{ route('blog.index') }}" wire:navigate
                class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-3 px-8 rounded-full text-lg transition duration-300 transform hover:scale-105 shadow-lg disabled:opacity-50"
                disabled>
                Kunjungi Blog
            </a>
        </div>
    </section>

    <section class="py-20 px-4 bg-gray-800">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 text-white">Tanya Jawab (FAQ)</h2>
            <div class="space-y-4">
                <details class="bg-gray-700 p-6 rounded-lg shadow-lg cursor-pointer">
                    <summary class="flex justify-between items-center text-white font-semibold">
                        Apakah semua materi benar-benar gratis?
                        <svg class="w-6 h-6 text-indigo-400 transform transition-transform duration-200" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </summary>
                    <p class="text-gray-300 mt-4">Ya, semua materi pembelajaran dan akses ke komunitas sepenuhnya gratis,
                        tanpa biaya tersembunyi. Kami percaya pada pendidikan yang dapat diakses oleh semua orang.</p>
                </details>
                <details class="bg-gray-700 p-6 rounded-lg shadow-lg cursor-pointer">
                    <summary class="flex justify-between items-center text-white font-semibold">
                        Apakah platform ini ramah untuk pemula?
                        <svg class="w-6 h-6 text-indigo-400 transform transition-transform duration-200" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </summary>
                    <p class="text-gray-300 mt-4">Sangat ramah! Semua materi dan fitur dirancang agar mudah dipahami oleh
                        pemula, dengan dukungan komunitas yang siap membantu kapan saja.</p>
                </details>
                <details class="bg-gray-700 p-6 rounded-lg shadow-lg cursor-pointer">
                    <summary class="flex justify-between items-center text-white font-semibold">
                        Bagaimana cara mendaftar?
                        <svg class="w-6 h-6 text-indigo-400 transform transition-transform duration-200" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </summary>
                    <p class="text-gray-300 mt-4">Anda cukup menekan tombol "Daftar Sekarang" dan ikuti langkah-langkah
                        mudah yang ada. Anda akan langsung bisa memulai petualangan ngoding Anda.</p>
                </details>
                <details class="bg-gray-700 p-6 rounded-lg shadow-lg cursor-pointer">
                    <summary class="flex justify-between items-center text-white font-semibold">
                        Apakah ada persyaratan dasar untuk bergabung?
                        <svg class="w-6 h-6 text-indigo-400 transform transition-transform duration-200" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </summary>
                    <p class="text-gray-300 mt-4">Tidak ada! Kami menyambut siapa saja, dari pemula yang belum pernah
                        ngoding sampai mereka yang sudah berpengalaman. Yang penting niat untuk belajar.</p>
                </details>
                <details class="bg-gray-700 p-6 rounded-lg shadow-lg cursor-pointer">
                    <summary class="flex justify-between items-center text-white font-semibold">
                        Apakah saya bisa berkontribusi sebagai mentor atau kontributor?
                        <svg class="w-6 h-6 text-indigo-400 transform transition-transform duration-200" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </summary>
                    <p class="text-gray-300 mt-4">Tentu saja! Kami sangat terbuka untuk siapa saja yang ingin berbagi ilmu
                        atau membantu komunitas. Silakan hubungi kami untuk informasi lebih lanjut.</p>
                </details>
            </div>
        </div>
    </section>

    <section class="py-20 px-4 bg-gray-900">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-8">Siap untuk Memulai?</h2>
            <p class="text-gray-400 mb-8">Jangan lewatkan kesempatan untuk belajar dan berkembang bersama kami.</p>
            <a href="{{ route('register') }}" wire:navigate
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-full text-lg transition duration-300 transform hover:scale-105 shadow-lg">
                Daftar Sekarang
            </a>
        </div>
    </section>
@endsection
