@extends('layouts.base')

@section('title', 'Sesi Kadaluarsa')

@section('content')
    <div class="flex items-center justify-center min-h-screen py-20 bg-gray-900 text-white">
        <div class="text-center">
            <h1 class="text-9xl font-extrabold text-yellow-500 tracking-wider">419</h1>
            <p class="text-2xl md:text-3xl font-bold tracking-wider text-gray-400 mt-4 mb-8">
                ⏳ Sesi Kadaluarsa ⏳
            </p>
            <p class="text-lg text-gray-500 mb-8">
                Maaf, sesi kamu telah berakhir atau token keamanan tidak valid. <br>
            </p>
            <a href="{{ route('home') }}"
                class="inline-block px-6 py-3 text-lg font-semibold text-white bg-yellow-600 rounded-full hover:bg-yellow-700 transition duration-300">
                🏠 Kembali ke Beranda
            </a>
        </div>
    </div>
@endsection