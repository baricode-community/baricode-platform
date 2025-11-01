@extends('components.layouts.app')

@section('content')
    {{-- Tombol Navigasi --}}
    <div class="mb-6">
        <a href="{{ route('polls.index') }}"
            class="inline-flex items-center px-4 py-2 text-base font-semibold rounded-full text-indigo-800 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-900 dark:text-indigo-100 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200 shadow-md gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="hidden xs:inline">Kembali ke Daftar Jajak Pendapat</span>
        </a>
    </div>

    {{-- Konten Utama --}}
    <div class="bg-gradient-to-br from-indigo-50 via-white to-indigo-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4 sm:p-6 md:p-10 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-800 transition duration-300 hover:scale-[1.01]">
        <header class="mb-6 pb-3 border-b border-gray-200 dark:border-gray-700 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-indigo-900 dark:text-indigo-100 tracking-tight">
                {{ $poll->title ?? 'Jajak Pendapat' }}
            </h1>
            <span class="text-base sm:text-lg text-gray-600 dark:text-gray-300 font-medium">Ikut Serta dalam Jajak Pendapat</span>
        </header>

        {{-- Komponen Livewire Voting --}}
        <div class="my-4 sm:my-6">
            <livewire:poll.vote-poll :poll="$poll" />
        </div>
    </div>

    @can('viewAnyVotes', $poll)
        <div class="mt-8 sm:mt-12 bg-white dark:bg-gray-900 p-4 sm:p-6 md:p-8 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800">
            <h2 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 text-indigo-800 dark:text-indigo-100 flex items-center gap-2"></h2>
                <svg class="w-6 h-6 text-indigo-500 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-4a4 4 0 10-8 0 4 4 0 008 0zm6 4v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2h14a2 2 0 012 2z" />
                </svg>
                Daftar Peserta Jajak Pendapat
            </h2>
            @php
                $votes = $poll->getAllVotes();
            @endphp
            @if($votes->isEmpty())
                <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2m-4-4a4 4 0 100-8 4 4 0 000 8z" />
                    </svg>
                    <span>Belum ada yang mengisi jajak pendapat ini.</span>
                </div>
            @else
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($votes as $vote)
                        <li class="py-2 sm:py-3 flex items-center gap-3">
                            <div>
                                @if($vote->user)
                                    <a href="{{ route('users.show', $vote->user->id) }}" class="font-semibold text-indigo-700 dark:text-indigo-300 hover:underline text-sm sm:text-base">
                                        {{ $vote->user->name }}
                                    </a>
                                @else
                                    <span class="font-semibold text-gray-900 dark:text-gray-100 text-sm sm:text-base">Anonim</span>
                                @endif
                                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $vote->created_at->diffForHumans() }})</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endcan
@endsection
