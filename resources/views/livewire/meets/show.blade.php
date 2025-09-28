<?php

use App\Models\Meet;
use App\Models\User\User;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
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

    public function openYoutube()
    {
        if (auth()->check() && $this->meet->isParticipant(auth()->user())) {
            return redirect()->away($this->meet->youtube_link);
        } else {
            session()->flash('error', 'Anda harus bergabung dengan meet terlebih dahulu untuk mengakses link YouTube.');
        }
    }
}; ?>
<div class="">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('meets.index') }}"
            class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Meet
        </a>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div
            class="bg-green-50 border border-green-200 text-green-600 dark:bg-green-900 dark:border-green-700 dark:text-green-200 px-4 py-3 rounded-md mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div
            class="bg-red-50 border border-red-200 text-red-600 dark:bg-red-900 dark:border-red-700 dark:text-red-200 px-4 py-3 rounded-md mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Meet Details -->
    <div
        class="bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-700 overflow-hidden">
        <div class="p-8">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{ $meet->title }}</h1>

                <div class="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-300">
                    @if ($meet->scheduled_at)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-400 dark:text-gray-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $meet->scheduled_at->format('l, d F Y - H:i') }} WIB
                        </div>
                    @endif

                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                        {{ $meet->users->count() }} Peserta
                    </div>
                </div>
            </div>

            <!-- Description -->
            @if ($meet->description)
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Deskripsi</h2>
                    <div class="prose text-gray-700 dark:text-gray-300 dark:prose-invert">
                        {!! nl2br(e($meet->description)) !!}
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 mb-8">
                @auth
                    @if ($meet->isParticipant(auth()->user()))
                        @if ($meet->youtube_link)
                            <button wire:click="openYoutube"
                                class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M2 6a2 2 0 012-2h6l2 2h6a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
                                </svg>
                                Buka YouTube
                            </button>
                        @endif

                        @if ($meet->is_finished)
                            <div
                                class="bg-gray-50 border border-gray-200 text-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 px-4 py-3 rounded-md mb-4 sm:mb-0">
                                Meet telah selesai.
                            </div>
                        @else
                            <button wire:click="leaveMeet"
                                class="inline-flex items-center justify-center px-6 py-3 border border-red-300 text-base font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 dark:border-red-700 dark:text-red-200 dark:bg-red-900 dark:hover:bg-red-800 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Keluar dari Meet
                            </button>
                        @endif
                    @else
                        <button wire:click="joinMeet"
                            class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            Bergabung dengan Meet
                        </button>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Login untuk Bergabung
                    </a>
                @endauth
            </div>

            <!-- Participants List -->
            @if ($meet->users->count() > 0)
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Daftar Peserta</h2>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach ($meet->users as $participant)
                                <div class="flex items-center space-x-3 p-3 bg-white dark:bg-gray-900 rounded-md">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">
                                                {{ substr($participant->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                            {{ $participant->name }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Bergabung
                                            {{ $participant->pivot->joined_at ? $participant->pivot->joined_at : '' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-gray-100">Belum ada peserta</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Jadilah yang pertama bergabung dengan meet
                        ini!</p>
                </div>
            @endif
        </div>
    </div>
</div>
