<?php

use App\Models\Meet;
use App\Models\User\User;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.meet')] class extends Component {
    public Meet $meet;

    public function mount(Meet $meet)
    {
        $this->meet = $meet->load('users');
    }

    public function joinMeet()
    {
        if (!auth()->check()) {
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $user = auth()->user();

        if ($this->meet->isParticipant($user)) {
            session()->flash('error', 'Anda sudah terdaftar dalam meet ini.');
            return;
        }

        $this->meet->users()->attach($user->id, ['joined_at' => now()]);
        $this->meet->load('users'); // Refresh the relationship
        session()->flash('success', 'Berhasil bergabung dengan meet!');
    }

    public function leaveMeet()
    {
        if (!auth()->check()) {
            return;
        }

        $user = auth()->user();

        if (!$this->meet->isParticipant($user)) {
            session()->flash('error', 'Anda belum terdaftar dalam meet ini.');
            return;
        }

        // Mengecek apakah meet belum selesai
        if ($this->meet->is_finished) {
            session()->flash('error', 'Meet sudah selesai. Anda tidak bisa keluar sekarang.');
            return;
        }

        $this->meet->users()->detach($user->id);
        $this->meet->load('users'); // Refresh the relationship
        session()->flash('success', 'Berhasil keluar dari meet!');
    }

    public function openMeetLink()
    {
        if (auth()->check() && $this->meet->isParticipant(auth()->user())) {
            // Prioritize meet_link over youtube_link
            $link = $this->meet->meet_link ?: $this->meet->youtube_link;

            if ($link) {
                return redirect()->away($link);
            } else {
                session()->flash('error', 'Link meet tidak tersedia.');
            }
        } else {
            session()->flash('error', 'Anda harus bergabung dengan meet terlebih dahulu untuk mengakses link meet.');
        }
    }
}; ?>
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-gray-900 dark:via-slate-900 dark:to-indigo-950">
    <div class="container mx-auto px-4 py-8">
        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="fixed top-4 right-4 z-50 max-w-md">
                <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 dark:bg-emerald-900/90 dark:border-emerald-700 dark:text-emerald-200 px-6 py-4 rounded-2xl shadow-xl backdrop-blur-sm animate-slide-in-right">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="fixed top-4 right-4 z-50 max-w-md">
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 dark:bg-red-900/90 dark:border-red-700 dark:text-red-200 px-6 py-4 rounded-2xl shadow-xl backdrop-blur-sm animate-slide-in-right">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Main Content --}}
        <div class="max-w-5xl mx-auto">
            {{-- Meet Header Card --}}
            <div class="relative overflow-hidden bg-white/80 dark:bg-gray-900/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 dark:border-gray-700/70 mb-8">
                {{-- Animated Background Pattern --}}
                <div class="absolute inset-0 opacity-30 pointer-events-none">
                    <div class="absolute top-0 -left-4 w-72 h-72 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full mix-blend-multiply filter blur-xl animate-pulse dark:from-blue-700 dark:to-purple-800"></div>
                    <div class="absolute top-0 -right-4 w-72 h-72 bg-gradient-to-r from-purple-400 to-pink-500 rounded-full mix-blend-multiply filter blur-xl animate-pulse delay-1000 dark:from-purple-700 dark:to-pink-700"></div>
                    <div class="absolute -bottom-8 left-20 w-72 h-72 bg-gradient-to-r from-pink-400 to-red-500 rounded-full mix-blend-multiply filter blur-xl animate-pulse delay-2000 dark:from-pink-700 dark:to-red-700"></div>
                </div>

                <div class="relative z-10 p-8 lg:p-12">
                    {{-- Header Section --}}
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8">
                        <div class="flex-1">
                            <h1 class="text-4xl lg:text-6xl font-black bg-gradient-to-r from-indigo-600 via-purple-600 to-blue-600 dark:from-indigo-400 dark:via-purple-400 dark:to-blue-400 bg-clip-text text-transparent mb-4 leading-tight">
                                {{ $meet->title }}
                            </h1>
                            <div class="flex flex-wrap gap-6 text-base lg:text-lg text-gray-600 dark:text-gray-300">
                                @if ($meet->scheduled_at)
                                    <div class="flex items-center gap-3 bg-white/50 dark:bg-gray-800/60 px-4 py-2 rounded-full backdrop-blur-sm">
                                        <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-full">
                                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <span class="font-semibold">{{ $meet->scheduled_at->format('l, d F Y - H:i') }} WIB</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        {{-- Status Badge --}}
                        <div class="flex-shrink-0">
                            @if ($meet->is_finished)
                                <span class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold text-lg shadow-lg">
                                    <div class="w-3 h-3 bg-gray-500 rounded-full"></div>
                                    Meet Selesai
                                </span>
                            @elseif ($meet->scheduled_at && $meet->scheduled_at->isFuture())
                                <span class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200 font-bold text-lg shadow-lg animate-pulse">
                                    <div class="w-3 h-3 bg-amber-500 rounded-full animate-ping"></div>
                                    Akan Datang
                                </span>
                            @elseif (!$meet->scheduled_at)
                                <span class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-100 via-teal-100 to-green-100 dark:from-emerald-900 dark:via-teal-900 dark:to-green-900 text-emerald-800 dark:text-emerald-200 font-bold text-lg shadow-lg animate-pulse">
                                    <div class="w-3 h-3 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full animate-ping"></div>
                                    <svg class="w-5 h-5 text-emerald-500 dark:text-emerald-300 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Belum Dijadwalkan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 font-bold text-lg shadow-lg animate-pulse">
                                    <div class="w-3 h-3 bg-emerald-500 rounded-full animate-ping"></div>
                                    Sedang Berlangsung
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        @auth
                            @if ($meet->isParticipant(auth()->user()))
                                @if ($meet->meet_link)
                                    <button wire:click="openMeetLink" class="group relative overflow-hidden bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 dark:from-emerald-600 dark:to-teal-700 dark:hover:from-emerald-700 dark:hover:to-teal-800 text-white font-bold py-4 px-8 rounded-2xl shadow-xl transform transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-emerald-300 dark:focus:ring-emerald-600">
                                        <span class="relative z-10 flex items-center justify-center text-lg">
                                            <svg class="w-6 h-6 mr-3 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            Menuju Meet
                                        </span>
                                        <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300 dark:bg-gray-900"></div>
                                    </button>
                                @endif
                                @if (!$meet->is_finished)
                                    <button wire:click="leaveMeet" class="group relative overflow-hidden bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 dark:from-red-600 dark:to-pink-700 dark:hover:from-red-700 dark:hover:to-pink-800 text-white font-bold py-4 px-8 rounded-2xl shadow-xl transform transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-red-300 dark:focus:ring-red-600">
                                        <span class="relative z-10 flex items-center justify-center text-lg">
                                            <svg class="w-6 h-6 mr-3 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Keluar dari Meet
                                        </span>
                                        <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300 dark:bg-gray-900"></div>
                                    </button>
                                @endif
                            @else
                                <button wire:click="joinMeet" class="group relative overflow-hidden bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 dark:from-indigo-600 dark:to-purple-700 dark:hover:from-indigo-700 dark:hover:to-purple-800 text-white font-bold py-4 px-8 rounded-2xl shadow-xl transform transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-indigo-300 dark:focus:ring-indigo-600"></button>
                                    <span class="relative z-10 flex items-center justify-center text-lg"></span>
                                        <svg class="w-6 h-6 mr-3 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                        </svg>
                                        Bergabung dengan Meet
                                    </span>
                                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300 dark:bg-gray-900"></div>
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="group relative overflow-hidden bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 dark:from-indigo-600 dark:to-purple-700 dark:hover:from-indigo-700 dark:hover:to-purple-800 text-white font-bold py-4 px-8 rounded-2xl shadow-xl transform transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-indigo-300 dark:focus:ring-indigo-600">
                                <span class="relative z-10 flex items-center justify-center text-lg">
                                    <svg class="w-6 h-6 mr-3 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                    Login untuk Bergabung
                                </span>
                                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300 dark:bg-gray-900"></div>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            {{-- Description Section --}}
            @if ($meet->description)
                <div class="bg-white/80 dark:bg-gray-900/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 dark:border-gray-700/70 p-8 lg:p-12 mb-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 dark:from-blue-700 dark:to-indigo-800 rounded-2xl shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 dark:from-gray-200 dark:to-gray-400 bg-clip-text text-transparent">Deskripsi</h2>
                    </div>
                    <div class="prose prose-lg dark:prose-invert max-w-none">
                        <div class="text-gray-700 dark:text-gray-200 leading-relaxed bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 border border-gray-200 dark:border-gray-600">
                            {!! nl2br(e($meet->description)) !!}
                        </div>
                    </div>
                </div>
            @endif

            {{-- YouTube Video Section --}}
            @if ($meet->youtube_link)
                <div class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 dark:border-gray-700/50 p-6 lg:p-8 mb-8">
                    <div class="relative">
                        <div class="aspect-w-16 aspect-h-9 rounded-2xl overflow-hidden shadow-2xl border-4 border-gradient-to-r from-blue-500 to-purple-600">
                            <iframe
                                src="https://www.youtube.com/embed/{{ \Illuminate\Support\Str::afterLast(parse_url($meet->youtube_link, PHP_URL_PATH), '/') }}"
                                title="YouTube video player" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen height="350px" width="100%">
                            </iframe>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Participants Section --}}
            <div class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 dark:border-gray-700/50 p-8 lg:p-12">
                <div class="flex items-center gap-3 mb-8">
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 dark:from-gray-200 dark:to-gray-400 bg-clip-text text-transparent">
                        Daftar Peserta ({{ $meet->users->count() }})
                    </h2>
                </div>
                @if ($meet->users->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($meet->users as $participant)
                            <div class="group relative overflow-hidden bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg hover:shadow-2xl border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:scale-105">
                                <div class="p-6">
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-indigo-100 dark:ring-indigo-900 group-hover:ring-indigo-200 dark:group-hover:ring-indigo-800 transition-all duration-300">
                                                <span class="text-2xl font-black text-white dark:text-gray-700">
                                                    {{ strtoupper(substr($participant->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full border-2 border-white dark:border-gray-800"></div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-cyan-500 truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300">
                                                {{ $participant->name }}
                                            </h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-700 flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Bergabung
                                                {{ $participant->pivot->joined_at ? \Carbon\Carbon::parse($participant->pivot->joined_at)->diffForHumans() : '' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/0 to-purple-500/0 group-hover:from-indigo-500/5 group-hover:to-purple-500/5 transition-all duration-300"></div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700 rounded-full mb-6 shadow-lg">
                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">Belum ada peserta</h3>
                        <p class="text-lg text-gray-500 dark:text-gray-400 max-w-md mx-auto">Jadilah yang pertama bergabung dengan meet ini dan mulai perjalanan pembelajaran bersama!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        @keyframes slide-in-right {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        .animate-slide-in-right {
            animation: slide-in-right 0.5s ease-out;
        }
    </style>
</div>
