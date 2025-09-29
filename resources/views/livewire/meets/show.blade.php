<?php

use App\Models\Meet;
use App\Models\User\User;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.blank')] class extends Component {
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

<div>
    <div class="mt-8 max-w-4xl mx-auto">
        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div
                class="flex items-center gap-2 bg-green-100 border border-green-300 text-green-800 dark:bg-green-900 dark:border-green-700 dark:text-green-200 px-6 py-4 rounded-lg shadow mb-6 animate-fade-in">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div
                class="flex items-center gap-2 bg-red-100 border border-red-300 text-red-800 dark:bg-red-900 dark:border-red-700 dark:text-red-200 px-6 py-4 rounded-lg shadow mb-6 animate-fade-in">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Meet Details Card -->
        <div
            class="relative bg-gradient-to-br from-indigo-100 via-white to-indigo-200 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 rounded-3xl shadow-2xl border border-indigo-200 dark:border-indigo-700 overflow-hidden transition-all duration-500 group">
            <!-- Decorative SVG Top Right -->
            <svg class="absolute top-0 right-0 w-40 h-40 opacity-20 text-indigo-300 dark:text-indigo-900 pointer-events-none"
                fill="none" viewBox="0 0 160 160">
                <circle cx="80" cy="80" r="80" fill="currentColor" />
            </svg>
            <div class="relative z-10 p-10">
                <!-- Header -->
                <div class="mb-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                    <div>
                        <h1
                            class="text-5xl font-black text-indigo-900 dark:text-indigo-100 mb-3 tracking-tight drop-shadow-lg">
                            {{ $meet->title }}</h1>
                        <div class="flex flex-wrap gap-8 text-lg text-gray-700 dark:text-gray-300 font-medium">
                            @if ($meet->scheduled_at)
                                <div class="flex items-center gap-2">
                                    <svg class="w-6 h-6 text-indigo-500 dark:text-indigo-300" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>{{ $meet->scheduled_at->format('l, d F Y - H:i') }} WIB</span>
                                </div>
                            @endif
                            <div class="flex items-center gap-2">
                                <svg class="w-6 h-6 text-indigo-500 dark:text-indigo-300" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                                <span>{{ $meet->users->count() }} Peserta</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        @if ($meet->is_finished)
                            <span
                                class="inline-block px-6 py-2 rounded-full bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-base shadow-lg">Meet
                                Selesai</span>
                        @elseif ($meet->scheduled_at && $meet->scheduled_at->isFuture())
                            <span
                                class="inline-block px-6 py-2 rounded-full bg-yellow-200 dark:bg-yellow-900 text-yellow-900 dark:text-yellow-200 font-bold text-base shadow-lg animate-pulse">Akan
                                Datang</span>
                        @else
                            <span
                                class="inline-block px-6 py-2 rounded-full bg-green-200 dark:bg-green-900 text-green-900 dark:text-green-200 font-bold text-base shadow-lg animate-pulse">Sedang
                                Berlangsung</span>
                        @endif
                    </div>
                </div>

                <!-- Description -->
                @if ($meet->description)
                    <div class="mb-12">
                        <h2 class="text-2xl font-bold text-indigo-800 dark:text-indigo-200 mb-3">Deskripsi</h2>
                        <div
                            class="prose prose-indigo dark:prose-invert text-gray-800 dark:text-gray-200 max-w-none leading-relaxed text-lg bg-white/70 dark:bg-gray-900/70 rounded-xl p-6 shadow-inner">
                            {!! nl2br(e($meet->description)) !!}
                        </div>
                    </div>
                @endif

                @if ($meet->youtube_link)
                    <div class="w-full max-w-2xl mx-auto my-8">
                        <div
                            class="aspect-w-16 aspect-h-[7] rounded-2xl overflow-hidden shadow-2xl border-4 border-indigo-200 dark:border-indigo-700 min-h-[240px] sm:min-h-[320px] md:min-h-[480px]">
                            <iframe
                                src="https://www.youtube.com/embed/{{ \Illuminate\Support\Str::afterLast(parse_url($meet->youtube_link, PHP_URL_PATH), '/') }}"
                                title="YouTube video player" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                                class="w-full h-full min-h-[240px] sm:min-h-[320px] md:min-h-[480px]"></iframe>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 mb-12 justify-center">
                    @auth
                        @if ($meet->isParticipant(auth()->user()))
                            @if ($meet->meet_link)
                                <button wire:click="openMeetLink"
                                    class="inline-flex items-center justify-center px-8 py-4 border-0 text-xl font-bold rounded-xl text-white bg-gradient-to-r from-green-500 to-green-700 hover:from-green-600 hover:to-green-800 dark:from-green-700 dark:to-green-900 shadow-xl transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-green-300">
                                    <svg class="w-7 h-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2M7 9l3 3 3-3m4 4V9a4 4 0 00-8 0v3" />
                                    </svg>
                                    Menuju Meet
                                </button>
                            @endif

                            @if (!$meet->is_finished)
                                <button wire:click="leaveMeet"
                                    class="inline-flex items-center justify-center px-8 py-4 border-0 text-xl font-bold rounded-xl text-red-700 bg-gradient-to-r from-red-100 to-red-200 hover:from-red-200 hover:to-red-300 dark:from-red-900 dark:to-red-800 dark:text-red-200 shadow-xl transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-red-300">
                                    <svg class="w-7 h-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Keluar dari Meet
                                </button>
                            @endif
                        @else
                            <button wire:click="joinMeet"
                                class="inline-flex items-center justify-center px-8 py-4 border-0 text-xl font-bold rounded-xl text-white bg-gradient-to-r from-indigo-500 to-indigo-700 hover:from-indigo-600 hover:to-indigo-800 dark:from-indigo-700 dark:to-indigo-900 shadow-xl transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-300">
                                <svg class="w-7 h-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Bergabung dengan Meet
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center px-8 py-4 border-0 text-xl font-bold rounded-xl text-white bg-gradient-to-r from-indigo-500 to-indigo-700 hover:from-indigo-600 hover:to-indigo-800 dark:from-indigo-700 dark:to-indigo-900 shadow-xl transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-300">
                            <svg class="w-7 h-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Login untuk Bergabung
                        </a>
                    @endauth
                </div>

                <!-- Participants List -->
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-indigo-800 dark:text-indigo-200 mb-6">Daftar Peserta</h2>
                    @if ($meet->users->count() > 0)
                        <div
                            class="bg-white/80 dark:bg-gray-900/80 rounded-2xl p-8 shadow-xl border border-indigo-100 dark:border-indigo-800 transition-all duration-300">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7">
                                @foreach ($meet->users as $participant)
                                    <div
                                        class="flex items-center gap-5 p-5 bg-gradient-to-r from-indigo-100 to-white dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-md hover:shadow-2xl transition-all duration-200 border border-indigo-50 dark:border-gray-800">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-14 h-14 bg-indigo-600 dark:bg-indigo-700 rounded-full flex items-center justify-center shadow-lg ring-4 ring-indigo-200 dark:ring-indigo-900">
                                                <span class="text-2xl font-extrabold text-white">
                                                    {{ strtoupper(substr($participant->name, 0, 1)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-lg font-bold text-gray-900 dark:text-gray-100 truncate">
                                                {{ $participant->name }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Bergabung
                                                {{ $participant->pivot->joined_at ? \Carbon\Carbon::parse($participant->pivot->joined_at)->diffForHumans() : '' }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-16">
                            <svg class="mx-auto h-16 w-16 text-indigo-300 dark:text-indigo-700" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                            <h3 class="mt-6 text-xl font-bold text-gray-900 dark:text-gray-100">Belum ada peserta</h3>
                            <p class="mt-3 text-lg text-gray-500 dark:text-gray-400">Jadilah yang pertama bergabung
                                dengan meet ini!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
