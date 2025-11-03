@extends('layouts.base')

@section('title', 'Belajar Ngoding Gratis - 100% Gratis & Kolaboratif')

@section('content')
    <style>
        /* CSS untuk Animasi */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 100%, 0);
            }

            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 1s ease-out;
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out both;
        }

        .animate-fade-in-up-2 {
            animation: fadeInUp 0.8s ease-out 0.3s both;
        }

        .animate-fade-in-up-3 {
            animation: fadeInUp 0.8s ease-out 0.6s both;
        }
    </style>

    <section
        class="hero text-center py-20 md:py-32 px-4 min-h-screen flex items-center justify-center bg-gray-900 text-white relative overflow-hidden">
        <div
            class="absolute inset-0 bg-gradient-to-br from-indigo-900/80 via-purple-900/80 to-pink-900/80 pointer-events-none z-0">
        </div>
        <img src="https://images.unsplash.com/photo-1513258496099-48168024aec0?auto=format&fit=crop&w=1400&q=80"
            alt="Mahasiswa Bingung" class="absolute inset-0 w-full h-full object-cover opacity-10 pointer-events-none z-0">
        <div class="max-w-5xl mx-auto relative z-10">
            <h1 class="text-2xl sm:text-3xl md:text-5xl font-extrabold leading-snug mb-6 animate-fade-in-up drop-shadow-lg">
                <span class="text-indigo-300">Belajar Ngoding Gratis 100%.</span> <br class="hidden md:inline-block"> Bangun
                Proyek Bareng.
            </h1>
            <p
                class="text-base md:text-lg text-gray-300 mb-10 max-w-3xl mx-auto animate-fade-in-up-2 font-light drop-shadow-md">
                Akses kurikulum terstruktur, mentor berpengalaman, dan berkolaborasi membangun proyek nyata bersama
                komunitas aktif.
            </p>
            <a href="{{ route('cara_belajar') }}" wire:navigate
                class="inline-flex items-center gap-3 bg-gradient-to-r from-pink-500 via-fuchsia-500 to-indigo-500 text-white font-bold py-3 px-10 rounded-full text-lg transition duration-300 transform hover:scale-105 hover:from-pink-600 hover:to-indigo-600 animate-fade-in-up-3 shadow-xl shadow-pink-500/40 uppercase tracking-wider drop-shadow-lg">
                <i class="fas fa-play-circle text-2xl animate-pulse"></i>
                Mulai Belajar Gratis!
            </a>
        </div>
    </section>
    <section class="py-20 px-4 bg-gray-800">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-extrabold text-center mb-16 text-white tracking-tight">
                Mengapa Harus Bergabung dengan <span class="text-teal-400">Kami?</span>
            </h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div
                    class="bg-gray-700 p-8 rounded-xl shadow-2xl text-center transform hover:scale-105 transition duration-300 border-t-4 border-indigo-500 hover:shadow-indigo-500/30">
                    <i class="fas fa-rocket text-5xl mb-4 text-indigo-400"></i>
                    <h3 class="text-xl font-semibold mb-2 text-white">Kurikulum Terstruktur</h3>
                    <p class="text-gray-300">Belajar dari nol hingga mahir dengan kurikulum yang kami rancang bersama. Jalur
                        jelas.</p>
                </div>
                <div
                    class="bg-gray-700 p-8 rounded-xl shadow-2xl text-center transform hover:scale-105 transition duration-300 border-t-4 border-purple-500 hover:shadow-purple-500/30">
                    <i class="fas fa-users text-5xl mb-4 text-purple-400"></i>
                    <h3 class="text-xl font-semibold mb-2 text-white">Komunitas Aktif</h3>
                    <p class="text-gray-300">Berinteraksi, saling bantu, dan bangun koneksi kuat dengan sesama developer.
                    </p>
                </div>
                <div
                    class="bg-gray-700 p-8 rounded-xl shadow-2xl text-center transform hover:scale-105 transition duration-300 border-t-4 border-pink-500 hover:shadow-pink-500/30">
                    <i class="fas fa-code-branch text-5xl mb-4 text-pink-400"></i>
                    <h3 class="text-xl font-semibold mb-2 text-white">Proyek Kolaborasi</h3>
                    <p class="text-gray-300">Terapkan ilmu yang kamu dapat dengan membangun proyek nyata bersama. Portofolio
                        instan!</p>
                </div>
                <div
                    class="bg-gray-700 p-8 rounded-xl shadow-2xl text-center transform hover:scale-105 transition duration-300 border-t-4 border-teal-500 hover:shadow-teal-500/30">
                    <i class="fas fa-clock text-5xl mb-4 text-teal-400"></i>
                    <h3 class="text-xl font-semibold mb-2 text-white">Belajar Fleksibel</h3>
                    <p class="text-gray-300">Akses materi kapan saja dan di mana saja sesuai dengan ritme dan jadwalmu
                        sendiri.</p>
                </div>
            </div>
        </div>
    </section>
    <section class="py-20 px-4 bg-gray-900">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-extrabold text-center mb-16 text-white">
                <span class="text-indigo-400">Navigasi</span> Cepat & Teknis Seputar Komunitas
            </h2>
            <div class="grid md:grid-cols-2 gap-8">
                <a href="{{ route('home.rencana') }}" wire:navigate
                    class="block bg-gray-800 p-8 rounded-xl shadow-xl text-center border-2 border-transparent hover:border-indigo-500 transition duration-300 group">
                    <i class="fas fa-book-reader text-5xl mb-4 text-indigo-400 group-hover:text-indigo-300 transition"></i>
                    <h3 class="text-2xl font-semibold mb-2 text-white">Rencana Komunitas</h3>
                    <p class="text-gray-400">Lihat roadmap, target, dan langkah komunitas untuk membangun platform belajar bersama dan bangun proyek bareng.</p>
                </a>
                <a href="{{ route('home.progres') }}" wire:navigate
                    class="block bg-gray-800 p-8 rounded-xl shadow-xl text-center border-2 border-transparent hover:border-teal-500 transition duration-300 group">
                    <i class="fas fa-chart-line text-5xl mb-4 text-teal-400 group-hover:text-teal-300 transition"></i>
                    <h3 class="text-2xl font-semibold mb-2 text-white">Sedang Apa Kita?</h3>
                    <p class="text-gray-400">Pantau progres proyek yang sedang komunitas lakukan saat ini!
                    </p>
                </a>
            </div>
        </div>
    </section>
    <section class="py-20 px-4 bg-gray-800">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-extrabold text-center mb-16 text-white">
                <span class="text-pink-400">Keunggulan</span> yang Kamu Dapatkan
            </h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center p-6 bg-gray-700 rounded-lg">
                    <i class="fas fa-cubes text-6xl mb-4 text-indigo-400"></i>
                    <h3 class="text-xl font-semibold mb-2 text-white">Materi Komprehensif</h3>
                    <p class="text-gray-300">Akses ke materi eksklusif yang dirancang oleh praktisi industri.</p>
                </div>
                <div class="text-center p-6 bg-gray-700 rounded-lg">
                    <i class="fas fa-graduation-cap text-6xl mb-4 text-purple-400"></i>
                    <h3 class="text-xl font-semibold mb-2 text-white">Mentor Berpengalaman</h3>
                    <p class="text-gray-300">Dibimbing langsung oleh mentor yang sudah berpengalaman di bidangnya.</p>
                </div>
                <div class="text-center p-6 bg-gray-700 rounded-lg">
                    <i class="fas fa-certificate text-6xl mb-4 text-pink-400"></i>
                    <h3 class="text-xl font-semibold mb-2 text-white">Sertifikat Digital</h3>
                    <p class="text-gray-300">Dapatkan sertifikat sebagai bukti kompetensi untuk setiap kursus.</p>
                </div>
                <div class="text-center p-6 bg-gray-700 rounded-lg">
                    <i class="fas fa-globe-americas text-6xl mb-4 text-teal-400"></i>
                    <h3 class="text-xl font-semibold mb-2 text-white">Komunitas Global</h3>
                    <p class="text-gray-300">Bergabung dengan komunitas yang solid dan saling mendukung untuk tumbuh
                        bersama.</p>
                </div>
            </div>
        </div>
    </section>
    <section class="py-20 px-4 bg-gray-900">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-10">
                Teknologi <span class="text-teal-400">Inti</span> yang Akan Kamu Kuasai
            </h2>
            <div class="mb-10 max-w-3xl mx-auto">
                <p class="text-gray-400 text-lg">
                    Saat ini, kami berfokus pada pembangunan platform yang tangguh. Oleh karena itu, kurikulum utama kami
                    berpusat pada dua teknologi terdepan untuk pengembangan web dan mobile:
                </p>
            </div>
            <div class="flex flex-wrap justify-center gap-6">
                <span
                    class="bg-indigo-600 text-white font-bold py-3 px-6 rounded-full text-xl shadow-lg hover:bg-indigo-700 transition duration-200">
                    <i class="fab fa-laravel mr-2"></i> Laravel (Backend)
                </span>
                <span
                    class="bg-purple-600 text-white font-bold py-3 px-6 rounded-full text-xl shadow-lg hover:bg-purple-700 transition duration-200">
                    <i class="fab fa-flutter mr-2"></i> Flutter (Mobile/Frontend)
                </span>
            </div>
        </div>
    </section>
    <section class="py-20 px-4 bg-gray-800">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-8">
                Baca <span class="text-pink-400">Artikel & Blog</span> Terbaru
            </h2>
            <p class="text-gray-300 mb-8 max-w-3xl mx-auto text-lg">
                Dapatkan insight, tips, dan cerita inspiratif seputar dunia ngoding dan perkembangan komunitas kami.
            </p>

            <div
                class="inline-block bg-yellow-500 text-gray-900 font-bold px-6 py-3 rounded-xl text-md mb-6 uppercase tracking-wider shadow-lg">
                <i class="fas fa-bell mr-2"></i> Segera Hadir (Platform Blog sedang dikembangkan!)
            </div>
            <br>
            <a href="{{ route('blog.index') }}" wire:navigate
                class="bg-gray-600 text-white font-bold py-3 px-10 rounded-full text-lg opacity-70 cursor-not-allowed shadow-xl">
                <i class="fas fa-external-link-alt mr-2"></i> Kunjungi Blog
            </a>
        </div>
    </section>
    <section class="py-20 px-4 bg-gray-900">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-extrabold text-center mb-12 text-white">
                <span class="text-indigo-400">Tanya Jawab</span> yang Sering Ditanyakan (FAQ)
            </h2>
            <div class="space-y-6">
                <details class="bg-gray-800 p-6 rounded-xl shadow-lg cursor-pointer group">
                    <summary
                        class="flex justify-between items-center text-white font-semibold text-lg transition duration-200">
                        Apakah semua materi benar-benar gratis?
                        <i
                            class="fas fa-chevron-right w-4 h-4 text-indigo-400 transform transition-transform duration-300 group-open:rotate-90"></i>
                    </summary>
                    <p class="text-gray-300 mt-4 border-l-4 border-indigo-500 pl-4">Ya, **100% gratis**. Semua materi
                        pembelajaran dan akses ke komunitas sepenuhnya gratis, tanpa biaya tersembunyi. Kami percaya pada
                        pendidikan yang dapat diakses oleh semua orang.</p>
                </details>
                <details class="bg-gray-800 p-6 rounded-xl shadow-lg cursor-pointer group">
                    <summary
                        class="flex justify-between items-center text-white font-semibold text-lg transition duration-200">
                        Apakah platform ini ramah untuk pemula?
                        <i
                            class="fas fa-chevron-right w-4 h-4 text-indigo-400 transform transition-transform duration-300 group-open:rotate-90"></i>
                    </summary>
                    <p class="text-gray-300 mt-4 border-l-4 border-indigo-500 pl-4">Sangat ramah! Kurikulum dibuat
                        berjenjang. Semua materi dan fitur dirancang agar mudah dipahami oleh pemula, dengan dukungan
                        komunitas yang siap membantu kapan saja.</p>
                </details>
                <details class="bg-gray-800 p-6 rounded-xl shadow-lg cursor-pointer group">
                    <summary
                        class="flex justify-between items-center text-white font-semibold text-lg transition duration-200">
                        Bagaimana cara mendaftar?
                        <i
                            class="fas fa-chevron-right w-4 h-4 text-indigo-400 transform transition-transform duration-300 group-open:rotate-90"></i>
                    </summary>
                    <p class="text-gray-300 mt-4 border-l-4 border-indigo-500 pl-4">Anda cukup menekan tombol "**Daftar
                        Sekarang**" di bawah atau di bagian atas, dan ikuti langkah-langkah registrasi yang mudah. Anda akan
                        langsung bisa memulai.</p>
                </details>
                <details class="bg-gray-800 p-6 rounded-xl shadow-lg cursor-pointer group">
                    <summary
                        class="flex justify-between items-center text-white font-semibold text-lg transition duration-200">
                        Apakah ada persyaratan dasar untuk bergabung?
                        <i
                            class="fas fa-chevron-right w-4 h-4 text-indigo-400 transform transition-transform duration-300 group-open:rotate-90"></i>
                    </summary>
                    <p class="text-gray-300 mt-4 border-l-4 border-indigo-500 pl-4">Hanya **niat untuk belajar**! Kami
                        menyambut siapa saja, dari pemula yang belum pernah ngoding sampai mereka yang sudah berpengalaman.
                        Tidak ada tes masuk.</p>
                </details>
            </div>
        </div>
    </section>
    <section class="py-20 px-4 bg-gradient-to-r from-indigo-600 to-purple-600">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-6 leading-tight drop-shadow-md">
                Siap <span class="text-pink-200">Menciptakan</span> Proyek Pertamamu?
            </h2>
            <p class="text-white text-xl mb-10 max-w-3xl mx-auto font-light">
                Jangan lewatkan kesempatan untuk belajar, berkolaborasi, dan berkembang bersama komunitas kami, **sekarang
                juga**.
            </p>
            <a href="{{ route('register') }}" wire:navigate
                class="bg-white text-indigo-700 font-bold py-4 px-12 rounded-full text-xl transition duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-white/40 uppercase tracking-wider">
                <i class="fas fa-user-plus mr-2"></i> Daftar Sekarang GRATIS
            </a>
        </div>
    </section>
@endsection
