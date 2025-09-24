@extends('layouts.base')

@section('title', 'Layanan Tidak Tersedia')

@section('content')
    <div class="flex items-center justify-center min-h-screen py-20 bg-gray-900 text-white">
        <div class="text-center">
            <h1 class="text-9xl font-extrabold text-yellow-500 tracking-wider">503</h1>
            <p class="text-2xl md:text-3xl font-bold tracking-wider text-gray-400 mt-4 mb-8">
                ⚠️ Layanan Tidak Tersedia ⚠️
            </p>
            <p class="text-lg text-gray-500 mb-4">
                Maaf, layanan sedang tidak tersedia untuk sementara waktu.<br>
                Silakan coba beberapa saat lagi atau hubungi administrator jika masalah berlanjut. 🕒
            </p>
            <p class="text-lg text-yellow-400 mb-8">
                Platform sedang dalam fase perbaikan untuk peningkatan layanan. Terima kasih atas pengertiannya.
            </p>
            <a href="{{ route('home') }}"
                class="inline-block px-6 py-3 text-lg font-semibold text-white bg-yellow-600 rounded-full hover:bg-yellow-700 transition duration-300">
                🏠 Kembali ke Beranda
            </a>
        </div>
    </div>
@endsection