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

    {{-- NAVIGASI CEPAT --}}
    <section class="py-20 px-4 bg-gray-900">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-extrabold text-center mb-10 text-white">
                <span class="text-indigo-400">Jelajahi</span> Info & Progres Komunitas
            </h2>
            <ul class="list-disc list-inside text-lg text-gray-200 max-w-2xl mx-auto space-y-4">
                <li>
                    <a href="{{ route('home.rencana') }}" wire:navigate class="text-indigo-400 hover:underline font-semibold">
                        Rencana Komunitas
                    </a>
                    <span class="block text-gray-400 text-base ml-6">Lihat roadmap, target, dan langkah komunitas untuk membangun platform belajar bersama dan bangun proyek bareng.</span>
                </li>
                <li>
                    <a href="{{ route('home.progres') }}" wire:navigate class="text-teal-400 hover:underline font-semibold">
                        Sedang Apa Kita?
                    </a>
                    <span class="block text-gray-400 text-base ml-6">Pantau progres proyek yang sedang komunitas lakukan saat ini!</span>
                </li>
            </ul>
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

    {{-- MENTORSHIP CALL TO ACTION --}}
    <section class="py-20 px-4 bg-gray-800">
        <div class="max-w-7xl mx-auto text-center">
            <i class="fas fa-handshake text-6xl text-teal-400 mb-6 animate-scale-in"></i>
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4 animate-fade-in-up">
                Butuh Bantuan Personal? <span class="text-teal-400">Hubungi Mentor!</span>
            </h2>
            <p class="text-gray-300 text-lg mb-8 max-w-3xl mx-auto animate-fade-in-up-2">
                Jangan pernah merasa sendirian. Tim mentor kami siap membantu memecahkan bug dan memberikan arahan karir.
            </p>
            <a href="" wire:navigate
                class="inline-flex items-center gap-2 bg-teal-500 text-gray-900 font-bold py-3 px-8 rounded-full text-lg transition duration-300 transform hover:scale-105 hover:bg-teal-400 animate-fade-in-up-3 shadow-xl shadow-teal-500/40">
                <i class="fas fa-comments"></i>
                Minta Bimbingan Mentor
            </a>
        </div>
    </section>

    {{-- STATISTIK KOMUNITAS --}}
    <section class="py-20 px-4 bg-gray-900">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-extrabold mb-12 text-white animate-fade-in-up">
                Komunitas Kami dalam <span class="text-purple-400">Angka</span>
            </h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="p-6 bg-gray-800 rounded-xl border-b-4 border-indigo-500 animate-scale-in">
                    <p class="text-5xl font-extrabold text-indigo-400 mb-2">{{ $usersCount }}+</p>
                    <p class="text-gray-300 font-medium">Anggota Aktif</p>
                </div>
                <div class="p-6 bg-gray-800 rounded-xl border-b-4 border-purple-500 animate-scale-in-2">
                    <p class="text-5xl font-extrabold text-purple-400 mb-2">350+</p>
                    <p class="text-gray-300 font-medium">Jam Materi Video</p>
                </div>
                <div class="p-6 bg-gray-800 rounded-xl border-b-4 border-pink-500 animate-scale-in-2">
                    <p class="text-5xl font-extrabold text-pink-400 mb-2">12+</p>
                    <p class="text-gray-300 font-medium">Proyek Utama Berjalan</p>
                </div>
                <div class="p-6 bg-gray-800 rounded-xl border-b-4 border-teal-500 animate-scale-in">
                    <p class="text-5xl font-extrabold text-teal-400 mb-2">100%</p>
                    <p class="text-gray-300 font-medium">Akses Gratis Selamanya</p>
                </div>
            </div>
        </div>
    </section>

    {{-- KOLABORASI GIT --}}
    <section class="py-20 px-4 bg-gray-800">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center">
            <div class="order-2 md:order-1 animate-slide-in-left">
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4 text-white">
                    Belajar <span class="text-pink-400">Kolaborasi</span> Nyata dengan Git
                </h2>
                <p class="text-gray-300 text-lg mb-6">
                    Kami mengajarkan alur kerja developer profesional. Semua proyek komunitas dikelola menggunakan
                    Git dan GitHub. Pelajari pull request, code review, dan branching dari project langsung.
                </p>
                <div class="flex items-center space-x-4 mb-6">
                    <i class="fab fa-github text-4xl text-white"></i>
                    <i class="fas fa-code-branch text-4xl text-white"></i>
                    <i class="fas fa-users-cog text-4xl text-white"></i>
                </div>
                <a href="{{ route('cara_belajar') }}" wire:navigate
                    class="inline-flex items-center text-pink-400 hover:text-pink-300 font-semibold transition duration-300">
                    Lihat Alur Kerja Kami <i class="fas fa-external-link-alt ml-2 text-sm"></i>
                </a>
            </div>
            <div class="order-1 md:order-2 bg-gray-900 p-6 rounded-xl shadow-xl animate-slide-in-right">
                <pre class="text-sm overflow-x-auto text-green-400 font-mono">
<code class="text-sm"># Mulai kontribusi hari ini
git clone https://github.com/project-community
cd project-community
git checkout -b feature/nama-fitur
git add .
git commit -m "feat: tambahkan fitur X"
git push origin feature/nama-fitur
</code></pre>
            </div>
        </div>
    </section>

    {{-- BELAJAR DARI PROYEK NYATA --}}
    <section class="py-20 px-4 bg-gray-900">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-extrabold mb-12 text-white animate-fade-in-up">
                Bangun <span class="text-indigo-400">Portofolio</span> dengan Proyek Nyata
            </h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div
                    class="p-6 bg-gray-800 rounded-xl shadow-xl transform hover:shadow-indigo-500/30 transition duration-300 animate-fade-in-up">
                    <i class="fas fa-shopping-cart text-5xl mb-4 text-indigo-400"></i>
                    <h3 class="text-xl font-semibold mb-2 text-white">E-Commerce Platform</h3>
                    <p class="text-gray-400">Belajar otentikasi, pembayaran, dan manajemen inventori.</p>
                </div>
                <div
                    class="p-6 bg-gray-800 rounded-xl shadow-xl transform hover:shadow-purple-500/30 transition duration-300 animate-fade-in-up-2">
                    <i class="fas fa-blog text-5xl mb-4 text-purple-400"></i>
                    <h3 class="text-xl font-semibold mb-2 text-white">CMS Modern</h3>
                    <p class="text-gray-400">Membangun sistem manajemen konten (CMS) yang cepat dan aman.</p>
                </div>
                <div
                    class="p-6 bg-gray-800 rounded-xl shadow-xl transform hover:shadow-pink-500/30 transition duration-300 animate-fade-in-up-3">
                    <i class="fas fa-mobile-alt text-5xl mb-4 text-pink-400"></i>
                    <h3 class="text-xl font-semibold mb-2 text-white">Aplikasi Mobile Tracking</h3>
                    <p class="text-gray-400">Pengembangan aplikasi cross-platform dengan Flutter dan API.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- KOMUNITAS DISCORD --}}
    <section class="py-20 px-4 bg-gray-800">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center">
            <div class="animate-slide-in-left">
                <i class="fab fa-discord text-6xl text-indigo-400 mb-4"></i>
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4 text-white">
                    Diskusi Langsung di <span class="text-indigo-400">Discord</span>
                </h2>
                <p class="text-gray-300 text-lg mb-6">
                    Bergabunglah dengan server Discord kami! Tempat terbaik untuk bertanya, berdiskusi, voice chat saat
                    ngoding, dan mendapatkan notifikasi proyek terbaru secara real-time.
                </p>
                <a href="URL_DISCORD_ANDA" target="_blank"
                    class="inline-flex items-center gap-2 bg-indigo-500 text-white font-bold py-3 px-8 rounded-full text-lg transition duration-300 transform hover:scale-105 hover:bg-indigo-600 shadow-xl shadow-indigo-500/40">
                    <i class="fas fa-sign-in-alt"></i>
                    Gabung Discord Sekarang
                </a>
            </div>
            <div class="hidden md:block bg-gray-900 p-8 rounded-xl shadow-2xl animate-slide-in-right">
                <p class="text-gray-300 font-mono text-center">
                    #general-chat (120 online) <br>
                    #laravel-help <br>
                    #flutter-bug-report
                </p>
                <div class="h-40 bg-gray-700 rounded-lg mt-4 flex items-center justify-center">
                    <p class="text-gray-400 italic">Preview Chat Aktif</p>
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
            <a href="URL_GITHUB_KOMUNITAS" target="_blank"
                class="inline-flex items-center gap-2 bg-teal-500 text-gray-900 font-bold py-3 px-8 rounded-full text-lg transition duration-300 transform hover:scale-105 hover:bg-teal-400 animate-fade-in-up-3 shadow-xl shadow-teal-500/40">
                <i class="fab fa-github"></i>
                Lihat Semua Repo Kami
            </a>
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

    {{-- CAREER PREPARATION (BARU) --}}
    <section class="py-20 px-4 bg-gray-900">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center">
            <div class="animate-slide-in-left">
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4 text-white">
                    Persiapan Karir <span class="text-purple-400">Siap Kerja</span>
                </h2>
                <p class="text-gray-400 text-lg mb-6">
                    Materi kami tidak hanya tentang syntax. Kami fokus pada soft skill, persiapan wawancara teknis,
                    dan cara membangun profil LinkedIn yang profesional, diintegrasikan langsung ke dalam kurikulum.
                </p>
                <ul class="space-y-3 text-gray-300">
                    <li class="flex items-center"><i class="fas fa-check-circle mr-3 text-purple-400"></i> Mock Interview Rutin</li>
                    <li class="flex items-center"><i class="fas fa-check-circle mr-3 text-purple-400"></i> Workshop Portofolio</li>
                    <li class="flex items-center"><i class="fas fa-check-circle mr-3 text-purple-400"></i> Sesi Strategi Melamar Kerja</li>
                </ul>
            </div>
            <div class="p-6 text-center animate-slide-in-right">
                <i class="fas fa-briefcase text-8xl text-purple-500 drop-shadow-lg"></i>
            </div>
        </div>
    </section>

    {{-- LEARNING RESOURCES - Ebook/Video (BARU) --}}
    <section class="py-20 px-4 bg-gray-800">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-12 animate-fade-in-up">
                Akses <span class="text-teal-400">Sumber Belajar</span> Eksklusif
            </h2>
            <div class="grid md:grid-cols-2 gap-8">
                <div
                    class="bg-gray-900 p-8 rounded-xl shadow-2xl border-b-4 border-teal-500 transform hover:scale-105 transition duration-300 animate-scale-in">
                    <i class="fas fa-file-pdf text-5xl mb-4 text-teal-400"></i>
                    <h3 class="text-2xl font-semibold mb-2 text-white">Panduan E-book Developer</h3>
                    <p class="text-gray-300 mb-4">Koleksi ringkas E-book untuk referensi cepat Laravel & Flutter.</p>
                    <a href="" wire:navigate
                        class="text-teal-400 font-medium hover:text-teal-300">Download Gratis <i
                            class="fas fa-download ml-2"></i></a>
                </div>
                <div
                    class="bg-gray-900 p-8 rounded-xl shadow-2xl border-b-4 border-indigo-500 transform hover:scale-105 transition duration-300 animate-scale-in-2">
                    <i class="fab fa-youtube text-5xl mb-4 text-indigo-400"></i>
                    <h3 class="text-2xl font-semibold mb-2 text-white">Video Tutorial Premium</h3>
                    <p class="text-gray-300 mb-4">Video pembelajaran mendalam dari studi kasus nyata.</p>
                    <a href="" wire:navigate
                        class="text-indigo-400 font-medium hover:text-indigo-300">Tonton Sekarang <i
                            class="fas fa-arrow-right ml-2"></i></a>
                </div>
            </div>
        </div>
    </section>

    {{-- PARTNERSHIPS (BARU) --}}
    <section class="py-20 px-4 bg-gray-900">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-10 animate-fade-in-up">
                Didukung oleh <span class="text-pink-400">Ekosistem Digital</span>
            </h2>
            <p class="text-gray-400 text-lg mb-12 max-w-3xl mx-auto animate-fade-in-up-2">
                Kami membangun koneksi dengan platform dan perusahaan teknologi yang peduli pada pendidikan terbuka.
            </p>
            <div class="flex flex-wrap justify-center items-center gap-10 opacity-70 animate-fade-in-up-3">
                {{-- Placeholder Logo --}}
                <div class="text-2xl font-bold text-gray-500 hover:text-white transition duration-200">
                    <i class="fas fa-server mr-2 text-pink-500"></i> Cloud Hosting Co.
                </div>
                <div class="text-2xl font-bold text-gray-500 hover:text-white transition duration-200">
                    <i class="fas fa-terminal mr-2 text-indigo-500"></i> Dev Tools Inc.
                </div>
                <div class="text-2xl font-bold text-gray-500 hover:text-white transition duration-200">
                    <i class="fas fa-city mr-2 text-purple-500"></i> Startup Studio
                </div>
            </div>
        </div>
    </section>

    {{-- FUTURE TECH ROADMAP (BARU) --}}
    <section class="py-20 px-4 bg-gray-800 border-t border-gray-700">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-extrabold mb-12 text-white text-center animate-fade-in-up">
                Masa Depan: Teknologi <span class="text-indigo-400">yang Akan Datang</span>
            </h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div
                    class="bg-gray-700 p-6 rounded-xl shadow-xl border-l-4 border-indigo-500 hover:bg-gray-600 transition duration-300 animate-slide-in-left">
                    <i class="fas fa-brain text-4xl mb-3 text-indigo-400"></i>
                    <h3 class="text-xl font-semibold mb-1 text-white">Artificial Intelligence (AI)</h3>
                    <p class="text-gray-300 text-sm">Integrasi Python dan machine learning untuk proyek data.</p>
                </div>
                <div
                    class="bg-gray-700 p-6 rounded-xl shadow-xl border-l-4 border-purple-500 hover:bg-gray-600 transition duration-300 animate-fade-in-up-2">
                    <i class="fab fa-bitcoin text-4xl mb-3 text-purple-400"></i>
                    <h3 class="text-xl font-semibold mb-1 text-white">Blockchain & Web3</h3>
                    <p class="text-gray-300 text-sm">Pengembangan DApps dasar dan konsep smart contract.</p>
                </div>
                <div
                    class="bg-gray-700 p-6 rounded-xl shadow-xl border-l-4 border-pink-500 hover:bg-gray-600 transition duration-300 animate-slide-in-right">
                    <i class="fas fa-vr-cardboard text-4xl mb-3 text-pink-400"></i>
                    <h3 class="text-xl font-semibold mb-1 text-white">Augmented Reality (AR)</h3>
                    <p class="text-gray-300 text-sm">Eksplorasi penggunaan Flutter untuk aplikasi AR mobile.</p>
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
            <p class="text-gray-500 mt-4 text-sm animate-fade-in-up-3">– Komunitas Developer Ngoding</p>
        </div>
    </section>

    {{-- CODE CHALLENGES (BARU) --}}
    <section class="py-20 px-4 bg-gray-800">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center">
            <div class="animate-slide-in-left">
                <i class="fas fa-trophy text-6xl text-pink-400 mb-4"></i>
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4 text-white">
                    Tantangan Kodifikasi <span class="text-pink-400">Mingguan</span>
                </h2>
                <p class="text-gray-300 text-lg mb-6">
                    Asah kemampuanmu dengan code challenge yang dirancang untuk menguji pemahaman algoritma dan
                    struktur data. Dapatkan lencana khusus bagi yang berhasil!
                </p>
                <a href="" wire:navigate
                    class="inline-flex items-center gap-2 text-pink-400 hover:text-pink-300 font-semibold transition duration-300">
                    Lihat Tantangan Terbaru <i class="fas fa-arrow-right ml-2 text-sm"></i>
                </a>
            </div>
            <div class="bg-gray-900 p-6 rounded-xl shadow-xl animate-scale-in-2">
                <p class="text-sm text-green-400 font-mono mb-4">// Tantangan Pekan Ini: FizzBuzz Lanjutan</p>
                <div class="h-40 bg-gray-700 rounded-lg flex items-center justify-center">
                    <p class="text-gray-400 italic">Antarmuka Challenge (UI/UX)</p>
                </div>
            </div>
        </div>
    </section>

    {{-- FINAL CTA --}}
    <section class="py-20 px-4 bg-gradient-to-r from-indigo-600 to-purple-600">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-6 leading-tight drop-shadow-md">
                Siap <span class="text-pink-200">Menciptakan</span> Proyek Pertamamu?
            </h2>
            <p class="text-white text-xl mb-10 max-w-3xl mx-auto font-light">
                Jangan lewatkan kesempatan untuk belajar, berkolaborasi, dan berkembang bersama komunitas kami, sekarang
                juga.
            </p>
            <a href="{{ route('register') }}" wire:navigate
                class="bg-white text-indigo-700 font-bold py-4 px-12 rounded-full text-xl transition duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-white/40 uppercase tracking-wider">
                <i class="fas fa-user-plus mr-2"></i> Daftar Sekarang GRATIS
            </a>
        </div>
    </section>
@endsection