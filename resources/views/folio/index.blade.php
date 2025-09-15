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

            // Tampilkan modal setelah halaman selesai dimuat
            setTimeout(() => {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                modal.querySelector('div').classList.remove('scale-95');
            }, 500); // Penundaan 500ms agar animasi terlihat lebih baik

            // Sembunyikan modal saat tombol tutup diklik
            closeButton.addEventListener('click', function() {
                modal.classList.add('opacity-0', 'pointer-events-none');
                modal.querySelector('div').classList.add('scale-95');
            });
        });
    </script>
@endpush
