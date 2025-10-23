@extends('layouts.base')

@section('title', 'Akses Ditolak')

@section('content')
    <div class="flex items-center justify-center min-h-screen py-20 bg-gray-900 text-white">
        <div class="text-center max-w-2xl mx-auto px-4">
            <h1 class="text-9xl font-extrabold text-red-500 tracking-wider">403</h1>
            <p class="text-2xl md:text-3xl font-bold tracking-wider text-gray-400 mt-4 mb-8">
                🚫 Akses Ditolak 🚫
            </p>
            <p class="text-lg text-gray-500 mb-8">
                Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. <br>
                {{ $exception->getMessage() ?: 'Anda hanya dapat mengakses data yang Anda buat sendiri.' }} 🔒
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}"
                    class="inline-block px-6 py-3 text-lg font-semibold text-white bg-gray-600 rounded-lg hover:bg-gray-700 transition duration-300">
                    ← Kembali
                </a>
                <a href="{{ route('home') }}"
                    class="inline-block px-6 py-3 text-lg font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition duration-300">
                    🏠 Ke Beranda
                </a>
            </div>
        </div>
    </div>
@endsection