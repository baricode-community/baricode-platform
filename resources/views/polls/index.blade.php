@extends('components.layouts.app')

@section('content')
    {{-- Header Halaman yang Lebih Menonjol --}}
    <header class="">
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-gray-100 tracking-tight leading-tight">
            Kelola Jajak Pendapat Anda 📊
        </h1>
        <p class="mt-2 text-lg text-gray-500 dark:text-gray-400">
            Lihat, edit, atau buat jajak pendapat baru dengan mudah.
        </p>
    </header>

    {{-- Konten Utama (Komponen Livewire) --}}
    <div class="">
            @livewire('poll.manage-polls')
        </div>
    </div>
@endsection
