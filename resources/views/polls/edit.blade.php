@extends('components.layouts.app')

@section('content')
    <header class="mb-8">
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-gray-100 tracking-tight leading-tight">
            Edit Jajak Pendapat 📋
        </h1>
        <p class="mt-2 text-lg text-gray-500 dark:text-gray-400">
            Ubah detail jajak pendapat Anda di sini.
        </p>
    </header>

    @livewire('poll.edit-poll', ['poll' => $poll])
@endsection
