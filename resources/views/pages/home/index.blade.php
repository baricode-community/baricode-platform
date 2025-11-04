@extends('layouts.base')

@section('title', 'Belajar Ngoding Gratis - 100% Gratis & Kolaboratif')

@section('content')
    <style>
        / CSS untuk Animasi /
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

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translate3d(-100%, 0, 0);
            }

            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translate3d(100%, 0, 0);
            }

            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }

            to {
                opacity: 1;
                transform: scale(1);
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

        / Animasi Baru & Variasi /
        .animate-fade-in-up-4 {
            animation: fadeInUp 0.8s ease-out 0.9s both;
        }

        .animate-slide-in-left {
            animation: slideInLeft 0.8s ease-out both;
        }

        .animate-slide-in-right {
            animation: slideInRight 0.8s ease-out both;
        }

        .animate-scale-in {
            animation: scaleIn 0.8s ease-out both;
        }

        .animate-scale-in-2 {
            animation: scaleIn 0.8s ease-out 0.3s both;
        }
        
        @keyframes spinSlow {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin-slow {
            animation: spinSlow 30s linear infinite;
        }
    </style>

    {{-- HERO --}}
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

    {{-- MENGAPA HARUS BERGABUNG --}}
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

    {{-- ARTIKEL & BLOG --}}
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

    {{-- FAQ --}}
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
                    <p class="text-gray-300 mt-4 border-l-4 border-indigo-500 pl-4">Ya, 100% gratis. Semua materi
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
                    <p class="text-gray-300 mt-4 border-l-4 border-indigo-500 pl-4">Anda cukup menekan tombol "Daftar
                        Sekarang" di bawah atau di bagian atas, dan ikuti langkah-langkah registrasi yang mudah. Anda akan
                        langsung bisa memulai.</p>
                </details>
                <details class="bg-gray-800 p-6 rounded-xl shadow-lg cursor-pointer group">
                    <summary
                        class="flex justify-between items-center text-white font-semibold text-lg transition duration-200">
                        Apakah ada persyaratan dasar untuk bergabung?
                        <i
                            class="fas fa-chevron-right w-4 h-4 text-indigo-400 transform transition-transform duration-300 group-open:rotate-90"></i>
                    </summary>
                    <p class="text-gray-300 mt-4 border-l-4 border-indigo-500 pl-4">Hanya niat untuk belajar! Kami
                        menyambut siapa saja, dari pemula yang belum pernah ngoding sampai mereka yang sudah berpengalaman.
                        Tidak ada tes masuk.</p>
                </details>
            </div>
        </div>
    </section>

    {{-- VISI & MISI --}}
    <section class="py-20 px-4 bg-gray-900 border-t border-b border-gray-700">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center">
            <div class="animate-slide-in-left">
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4 text-white">
                    Visi Kami: Masa Depan <span class="text-teal-400">Terbuka</span>
                </h2>
                <p class="text-gray-400 text-lg mb-6">
                    Menciptakan ekosistem belajar di mana setiap orang, tanpa memandang latar belakang, memiliki akses
                    penuh ke pendidikan coding berkualitas tinggi dan kesempatan untuk berkolaborasi dalam proyek nyata.
                </p>
                <a href="{{ route('home.rencana') }}" wire:navigate
                    class="inline-flex items-center text-teal-400 hover:text-teal-300 font-semibold transition duration-300">
                    Pelajari Visi Lebih Detail <i class="fas fa-arrow-right ml-2 text-sm"></i>
                </a>
            </div>
            <div class="space-y-6 animate-slide-in-right">
                <div class="bg-gray-800 p-6 rounded-xl shadow-xl border-l-4 border-indigo-500">
                    <h3 class="text-xl font-semibold mb-1 text-indigo-400">Misi 1: Aksesibilitas</h3>
                    <p class="text-gray-300">Menyediakan materi 100% gratis dan tanpa batas.</p>
                </div>
                <div class="bg-gray-800 p-6 rounded-xl shadow-xl border-l-4 border-purple-500">
                    <h3 class="text-xl font-semibold mb-1 text-purple-400">Misi 2: Kolaborasi</h3>
                    <p class="text-gray-300">Mendorong pembangunan proyek bersama sebagai portofolio.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- SKILL PATH --}}
    <section class="py-20 px-4 bg-gray-900">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-extrabold text-center mb-16 text-white animate-fade-in-up">
                Jalur Belajar <span class="text-indigo-400">Fleksibel</span> Kami
            </h2>
            <div class="relative flex flex-col md:flex-row justify-between items-start gap-12">

                {{-- Garis penghubung --}}
                <div class="hidden md:block absolute top-10 left-0 right-0 h-1 bg-gray-700 z-0 animate-scale-in-2"></div>

                {{-- Tahap 1 --}}
                <div
                    class="text-center p-6 bg-gray-800 rounded-xl shadow-xl border-t-4 border-indigo-500 z-10 md:w-1/4 animate-fade-in-up">
                    <div
                        class="w-12 h-12 bg-indigo-500 rounded-full mx-auto mb-4 flex items-center justify-center text-white font-bold text-xl">
                        1</div>
                    <h3 class="text-xl font-semibold mb-2 text-white">Dasar Kodifikasi</h3>
                    <p class="text-gray-300 text-sm">HTML, CSS, dan Logika dasar programming.</p>
                </div>

                {{-- Tahap 2 --}}
                <div
                    class="text-center p-6 bg-gray-800 rounded-xl shadow-xl border-t-4 border-purple-500 z-10 md:w-1/4 animate-fade-in-up-2">
                    <div
                        class="w-12 h-12 bg-purple-500 rounded-full mx-auto mb-4 flex items-center justify-center text-white font-bold text-xl">
                        2</div>
                    <h3 class="text-xl font-semibold mb-2 text-white">Inti Teknologi</h3>
                    <p class="text-gray-300 text-sm">Mendalami Laravel, Flutter, dan database.</p>
                </div>

                {{-- Tahap 3 --}}
                <div
                    class="text-center p-6 bg-gray-800 rounded-xl shadow-xl border-t-4 border-pink-500 z-10 md:w-1/4 animate-fade-in-up-3">
                    <div
                        class="w-12 h-12 bg-pink-500 rounded-full mx-auto mb-4 flex items-center justify-center text-white font-bold text-xl">
                        3</div>
                    <h3 class="text-xl font-semibold mb-2 text-white">Proyek Kolaboratif</h3>
                    <p class="text-gray-300 text-sm">Berkontribusi ke proyek open-source komunitas.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- STATISTIK KOMUNITAS --}}
    <section class="py-20 px-4 bg-gray-900">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-extrabold mb-12 text-white animate-fade-in-up">
                Komunitas Kami dalam <span class="text-purple-400">Angka</span>
            </h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="p-6 bg-gray-800 rounded-xl border-b-4 border-indigo-500 animate-scale-in">
                    <p class="text-5xl font-extrabold text-indigo-400 mb-2">{{ $usersCount }}</p>
                    <p class="text-gray-300 font-medium">Anggota Terdaftar</p>
                </div>
                <div class="p-6 bg-gray-800 rounded-xl border-b-4 border-pink-500 animate-scale-in-2">
                    <p class="text-5xl font-extrabold text-pink-400 mb-2">{{ $projectsCount }}</p>
                    <p class="text-gray-300 font-medium">Proyek Utama Berjalan</p>
                </div>
                <div class="p-6 bg-gray-800 rounded-xl border-b-4 border-teal-500 animate-scale-in">
                    <p class="text-5xl font-extrabold text-teal-400 mb-2">100%</p>
                    <p class="text-gray-300 font-medium">Akses Gratis Selamanya</p>
                </div>
            </div>
        </div>
    </section>

    {{-- KEBEBASAN OPEN-SOURCE --}}
    <section class="py-20 px-4 bg-gray-900">
        <div class="max-w-7xl mx-auto text-center">
            <i class="fas fa-hand-holding-heart text-6xl text-teal-400 mb-6 animate-scale-in"></i>
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4 animate-fade-in-up">
                Filosofi <span class="text-teal-400">Open-Source</span>
            </h2>
            <p class="text-gray-300 text-lg mb-8 max-w-4xl mx-auto animate-fade-in-up-2">
                Kami membangun platform ini bersama. Semua materi, kode sumber, dan kurikulum kami adalah open-source. Ini
                adalah kesempatan Anda untuk belajar dari kontribusi, dan berkontribusi untuk belajar.
            </p>
            {{-- <a href="URL_GITHUB_KOMUNITAS" target="_blank"
                class="inline-flex items-center gap-2 bg-teal-500 text-gray-900 font-bold py-3 px-8 rounded-full text-lg transition duration-300 transform hover:scale-105 hover:bg-teal-400 animate-fade-in-up-3 shadow-xl shadow-teal-500/40">
                <i class="fab fa-github"></i>
                Lihat Semua Repo Kami
            </a> --}}
        </div>
    </section>

    {{-- KENAPA GRATIS? --}}
    <section class="py-20 px-4 bg-gray-800 border-t-4 border-double border-pink-500">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-8 animate-fade-in-up">
                Kenapa Kami <span class="text-pink-400">100% Gratis</span>?
            </h2>
            <p class="text-gray-300 text-xl mb-8 font-light animate-fade-in-up-2">
                Kami percaya pada kekuatan gotong royong dan demokratisasi pendidikan. Misi kami adalah
                menghilangkan hambatan finansial agar setiap pemuda Indonesia bisa menjadi developer handal.
            </p>
            <div class="flex justify-center items-center space-x-6 animate-fade-in-up-3">
                <div class="text-center">
                    <i class="fas fa-lock-open text-5xl text-pink-400 mb-2"></i>
                    <p class="text-gray-300">Akses Tanpa Kunci</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-hand-holding-usd text-5xl text-pink-400 mb-2"></i>
                    <p class="text-gray-300">Tanpa Biaya Tersembunyi</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-hands-helping text-5xl text-pink-400 mb-2"></i>
                    <p class="text-gray-300">Didukung Komunitas</p>
                </div>
            </div>
        </div>
    </section>

    {{-- DAILY MOTIVATION (BARU) --}}
    <section class="py-20 px-4 bg-gray-900 border-t border-gray-700">
        <div class="max-w-4xl mx-auto text-center">
            <i class="fas fa-lightbulb text-6xl text-yellow-400 mb-6 animate-pulse"></i>
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-6 animate-fade-in-up">
                Hadapi <span class="text-yellow-400">Error</span> dengan Semangat!
            </h2>
            <blockquote class="text-2xl italic text-gray-300 border-l-4 border-yellow-500 pl-4 mx-auto animate-fade-in-up-2">
                "Setiap bug yang berhasil kamu pecahkan bukan hanya mengasah skill-mu, tapi juga menambah satu cerita heroik
                di portofolio karirmu."
            </blockquote>
            <p class="text-gray-500 mt-4 text-sm animate-fade-in-up-3">– Manusia di Balik Layar</p>
        </div>
    </section>
@endsection