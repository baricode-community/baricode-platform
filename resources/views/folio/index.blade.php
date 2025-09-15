<?php
use function Laravel\Folio\name;

name('home');

?>

@extends('layouts.base')

@section('title', 'Belajar Ngoding Gratis - 100% Gratis')

@section('content')
    <div id="welcome-modal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 transition-opacity duration-300 opacity-0 pointer-events-none">
        <div
            class="bg-gray-800 rounded-lg shadow-lg p-8 m-4 max-w-lg w-full transform transition-transform duration-300 scale-95">
            <h2 class="text-3xl font-bold text-white mb-4 text-center">Selamat Datang!</h2>
            <p class="text-gray-400 mb-6 text-center">
                Apakah Anda ingin tahu cara memulai belajar di sini?
            </p>
            <div class="flex justify-center space-x-4">
                <a href="/cara-belajar"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-full transition duration-300">
                    Cara Belajar
                </a>
                <button id="close-modal"
                    class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-full transition duration-300">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <section
        class="hero text-center py-20 md:py-32 px-4 min-h-screen flex items-center justify-center bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-4 animate-fade-in-up">
                Belajar Ngoding Gratis 100%. <br class="hidden md:inline-block"> Bangun Proyek Bareng.
            </h1>
            <p class="text-lg md:text-xl text-gray-200 mb-8 max-w-2xl mx-auto animate-fade-in-up-2">
                Bergabunglah dengan komunitas yang bersemangat untuk belajar, berbagi, dan berkolaborasi.
            </p>
            <a href="#"
                class="bg-white text-indigo-600 font-bold py-3 px-8 rounded-full text-lg transition duration-300 transform hover:scale-105 animate-fade-in-up-3 shadow-lg">
                Mulai Belajar
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
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                    <p class="text-gray-400 italic mb-4">"Kurikulumnya terstruktur banget, dari nol jadi bisa bikin website
                        sendiri. Komunitasnya juga supportif!"</p>
                    <div class="flex items-center">
                        <img src="https://via.placeholder.com/60" alt="Foto Profil Pengguna"
                            class="w-12 h-12 rounded-full mr-4">
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
                        <img src="https://via.placeholder.com/60" alt="Foto Profil Pengguna"
                            class="w-12 h-12 rounded-full mr-4">
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
                        <img src="https://via.placeholder.com/60" alt="Foto Profil Pengguna"
                            class="w-12 h-12 rounded-full mr-4">
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
            <div class="flex flex-wrap justify-center gap-6">
                <span class="bg-gray-800 text-indigo-400 font-semibold py-2 px-4 rounded-full">Laravel</span>
                <span class="bg-gray-800 text-indigo-400 font-semibold py-2 px-4 rounded-full">PHP</span>
                <span class="bg-gray-800 text-indigo-400 font-semibold py-2 px-4 rounded-full">Tailwind CSS</span>
                <span class="bg-gray-800 text-indigo-400 font-semibold py-2 px-4 rounded-full">JavaScript</span>
                <span class="bg-gray-800 text-indigo-400 font-semibold py-2 px-4 rounded-full">React</span>
                <span class="bg-gray-800 text-indigo-400 font-semibold py-2 px-4 rounded-full">Vue.js</span>
                <span class="bg-gray-800 text-indigo-400 font-semibold py-2 px-4 rounded-full">Node.js</span>
                <span class="bg-gray-800 text-indigo-400 font-semibold py-2 px-4 rounded-full">MySQL</span>
            </div>
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
            </div>
        </div>
    </section>

    <section class="py-20 px-4 bg-gray-900">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-8">Siap untuk Memulai?</h2>
            <p class="text-gray-400 mb-8">Jangan lewatkan kesempatan untuk belajar dan berkembang bersama kami.</p>
            <a href="#"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-full text-lg transition duration-300 transform hover:scale-105 shadow-lg">
                Daftar Sekarang
            </a>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('welcome-modal');
            const closeButton = document.getElementById('close-modal');
            const floatingButton = document.createElement('button'); // Membuat elemen tombol baru

            // --- Logika Cookie (Popup Muncul Sekali per Jam) ---
            const popupLastShown = localStorage.getItem('popupLastShown');
            const now = new Date().getTime();
            const oneHour = 60 * 60 * 1000; // 1 jam dalam milidetik

            if (!popupLastShown || (now - popupLastShown > oneHour)) {
                // Tampilkan modal jika belum pernah atau sudah lebih dari 1 jam
                setTimeout(() => {
                    modal.classList.remove('opacity-0', 'pointer-events-none');
                    modal.querySelector('div').classList.remove('scale-95');
                }, 500);

                // Simpan timestamp saat modal ditampilkan
                localStorage.setItem('popupLastShown', now);
            }

            // Sembunyikan modal saat tombol tutup diklik
            closeButton.addEventListener('click', function() {
                modal.classList.add('opacity-0', 'pointer-events-none');
                modal.querySelector('div').classList.add('scale-95');
            });

            // --- Logika Tombol Floating (Munculkan Modal) ---
            // Konfigurasi tombol floating
            floatingButton.id = 'show-modal-button';
            floatingButton.className =
                'fixed bottom-4 right-4 z-40 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-3 rounded-full shadow-lg transition duration-300 transform hover:scale-110 focus:outline-none';
            floatingButton.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                     </svg>`; // Ikon plus SVG
            document.body.appendChild(floatingButton);

            // Munculkan modal saat tombol floating diklik
            floatingButton.addEventListener('click', function() {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                modal.querySelector('div').classList.remove('scale-95');
            });
        });
    </script>
@endpush
