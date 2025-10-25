@extends('components.layouts.app')

@section('content')
    {{-- Tombol Navigasi yang Dipercantik --}}
    <div class="mb-8">
        <a href="{{ route('polls.index') }}"
            class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-full text-indigo-700 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-900 dark:text-indigo-100 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Jajak Pendapat
        </a>
    </div>

    {{-- Wadah Utama Konten --}}
    <div
        class="bg-white dark:bg-gray-900 p-8 md:p-10 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 transition duration-300 transform hover:scale-[1.005]">
        <header class="mb-8 pb-4 border-b border-gray-100 dark:border-gray-700">
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 tracking-tight"></h1>
            Ikut Serta dalam Jajak Pendapat
            </h1>
        </header>

        {{-- Komponen Livewire Voting --}}
        <livewire:poll.vote-poll :poll="$poll" />
    </div>
@endsection
