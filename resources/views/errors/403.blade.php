@extends('layouts.base')

@section('title', 'Akses Ditolak')

@section('content')
    <div class="flex items-center justify-center min-h-screen py-20 bg-gray-900 text-white">
        <div class="text-center">
            <h1 class="text-9xl font-extrabold text-red-500 tracking-wider">403</h1>
            <p class="text-2xl md:text-3xl font-bold tracking-wider text-gray-400 mt-4 mb-8">
                🚫 Akses Ditolak 🚫
            </p>
            <p class="text-lg text-gray-500 mb-8">
                Maaf, kamu tidak memiliki izin untuk mengakses halaman ini. <br>
                Silakan hubungi administrator jika kamu merasa ini adalah kesalahan. 🔒
            </p>
            <a href="{{ route('home') }}"
                class="inline-block px-6 py-3 text-lg font-semibold text-white bg-red-600 rounded-full hover:bg-red-700 transition duration-300">
                🏠 Kembali ke Beranda
            </a>
        </div>
    </div>
@endsection