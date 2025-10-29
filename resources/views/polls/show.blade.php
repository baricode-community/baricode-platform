@extends('components.layouts.app')

@section('content')
    {{-- Tombol Navigasi --}}
    <div class="mb-8">
        <a href="{{ route('polls.index') }}"
            class="inline-flex items-center px-5 py-2.5 text-base font-semibold rounded-full text-indigo-800 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-900 dark:text-indigo-100 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200 shadow-md gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Daftar Jajak Pendapat</span>
        </a>
    </div>

    {{-- Konten Utama --}}
    <div class="bg-gradient-to-br from-indigo-50 via-white to-indigo-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-10 md:p-14 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-800 transition duration-300 hover:scale-[1.01]">
        <header class="mb-8 pb-4 border-b border-gray-200 dark:border-gray-700 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <h1 class="text-4xl font-extrabold text-indigo-900 dark:text-indigo-100 tracking-tight">
                {{ $poll->title ?? 'Jajak Pendapat' }}
            </h1>
            <span class="text-lg text-gray-600 dark:text-gray-300 font-medium">Ikut Serta dalam Jajak Pendapat</span>
        </header>

        {{-- Komponen Livewire Voting --}}
        <div class="my-6">
            <livewire:poll.vote-poll :poll="$poll" />
        </div>
    </div>

    @can('viewAnyVotes', $poll)
        <div class="mt-12 bg-white dark:bg-gray-900 p-8 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800">
            <h2 class="text-2xl font-bold mb-6 text-indigo-800 dark:text-indigo-100 flex items-center gap-2">
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
                        <li class="py-3 flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-800 flex items-center justify-center text-indigo-700 dark:text-indigo-100 font-bold text-lg">
                                    {{ strtoupper(substr($vote->user->name ?? 'A', 0, 1)) }}
                                </div>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $vote->user->name ?? 'Anonim' }}</span>
                                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $vote->created_at->diffForHumans() }})</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endcan
@endsection
