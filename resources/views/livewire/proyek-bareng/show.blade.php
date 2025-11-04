<?php

use Livewire\Volt\Component;
use Livewire\Volt\Attributes\Layout;
use App\Models\ProyekBareng;
use Livewire\Attributes\Validate;

new #[Layout('layouts.app')] class extends Component {
    public ProyekBareng $proyekBareng;

    public bool $showingJoinForm = false;

    #[Validate('required|min:10|max:500')]
    public string $joinReason = '';

    public function mount(ProyekBareng $proyekBareng): void
    {
        $this->proyekBareng = $proyekBareng;
        $this->proyekBareng->load(['users', 'meets', 'kanboards', 'kanboardLinks', 'polls']);
    }

    public function canJoinProject(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        // Cek apakah user sudah bergabung
        if ($this->proyekBareng->users()->where('user_id', $user->id)->exists()) {
            return false;
        }

        // Cek apakah proyek sudah selesai
        if ($this->proyekBareng->is_finished) {
            return false;
        }

        // Cek apakah user memiliki whatsapp yang valid
        if (empty($user->whatsapp)) {
            return false;
        }

        return true;
    }

    public function showJoinForm(): void
    {
        logger('showJoinForm dipanggil');

        if (!$this->canJoinProject()) {
            logger('canJoinProject return false');
            return;
        }

        logger('Setting showingJoinForm to true');
        $this->showingJoinForm = true;
        $this->joinReason = '';
    }

    public function hideJoinForm(): void
    {
        $this->showingJoinForm = false;
        $this->joinReason = '';
        $this->resetValidation();
    }

    public function joinProject(): void
    {
        if (!$this->canJoinProject()) {
            session()->flash('error', 'Anda tidak dapat bergabung dengan proyek ini.');
            return;
        }

        $this->validate();

        $user = auth()->user();

        // Bergabung dengan proyek
        $this->proyekBareng->users()->attach($user->id, [
            'description' => $this->joinReason,
            'is_approved' => false, // Default menunggu persetujuan
        ]);

        // Refresh data
        $this->proyekBareng->load(['users', 'meets', 'kanboards', 'kanboardLinks', 'polls']);

        // Reset form
        $this->hideJoinForm();

        session()->flash('success', 'Berhasil bergabung dengan proyek!');
    }
};

?>

<div class="">
    <div class="">
        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Join Project Form -->
        @if ($showingJoinForm && $this->canJoinProject())
            <div
                class="bg-gradient-to-r from-green-50 to-blue-50 dark:from-green-900/20 dark:to-blue-900/20 border border-green-200 dark:border-green-700 rounded-lg p-6 mb-8 shadow-sm">
                <div class="flex items-start">
                    <div
                        class="flex items-center justify-center flex-shrink-0 w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
                                Bergabung dengan Proyek
                            </h3>
                            <button wire:click="hideJoinForm"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="mt-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Ceritakan alasan Anda ingin bergabung dengan proyek
                                "<strong>{{ $proyekBareng->title }}</strong>" dan kontribusi apa yang bisa Anda berikan.
                            </p>
                        </div>

                        <div class="mt-4">
                            <label for="joinReason"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Alasan & Kontribusi *
                            </label>
                            <textarea wire:model="joinReason" id="joinReason" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Contoh: Saya tertarik bergabung karena memiliki pengalaman di bidang web development dan ingin berkontribusi dalam pembuatan fitur backend. Saya bisa membantu dalam pengembangan API dan database design."></textarea>
                            @error('joinReason')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Minimal 10 karakter, maksimal 500 karakter
                            </p>
                        </div>

                        <div class="mt-5 flex items-center space-x-3">
                            <button type="button" wire:click="joinProject" wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg shadow transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove>
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Bergabung
                                </span>
                                <span wire:loading class="inline-flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Memproses...
                                </span>
                            </button>
                            <button type="button" wire:click="hideJoinForm"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('proyek-bareng.index') }}" wire:navigate
                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $proyekBareng->title }}</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">ID Proyek: {{ $proyekBareng->id }}</p>
                    </div>
                </div>

                <!-- Join Project Button -->
                @auth
                    @if ($this->canJoinProject())
                        <div class="flex items-center space-x-3">
                            <button wire:click="showJoinForm"
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg shadow transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2"
                                onclick="console.log('Tombol Bergabung diklik')">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Bergabung
                            </button>
                        </div>
                    @else
                        @if (auth()->user() &&
                                $proyekBareng->users()->where('user_id', auth()->id())->exists())
                            <div class="flex items-center space-x-2 text-green-600 dark:text-green-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm font-medium">Anda sudah bergabung</span>
                            </div>
                        @elseif($proyekBareng->is_finished)
                            <div class="flex items-center space-x-2 text-gray-500 dark:text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 0h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                                <span class="text-sm">Proyek sudah selesai</span>
                            </div>
                        @elseif(auth()->user() && empty(auth()->user()->whatsapp))
                            <div class="flex items-center space-x-2 text-amber-600 dark:text-amber-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                                <a href="{{ route('profile.edit') }}"
                                    class="text-sm underline hover:text-amber-700 dark:hover:text-amber-300">
                                    Lengkapi nomor WhatsApp valid untuk bergabung
                                </a>
                            </div>
                        @endif
                    @endif
                @else
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2">
                            Login untuk Bergabung
                        </a>
                    </div>
                @endauth
            </div>
        </div>

        <!-- Project Description -->
        @if ($proyekBareng->description)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Deskripsi Proyek</h2>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">{{ $proyekBareng->description }}</p>
            </div>
        @endif

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div
                        class="p-2 {{ $proyekBareng->is_finished ? 'bg-purple-100 dark:bg-purple-900' : 'bg-green-100 dark:bg-green-900' }} rounded-lg">
                        <svg class="w-6 h-6 {{ $proyekBareng->is_finished ? 'text-purple-600 dark:text-purple-400' : 'text-green-600 dark:text-green-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if ($proyekBareng->is_finished)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            @endif
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</h3>
                        <p
                            class="text-lg font-bold {{ $proyekBareng->is_finished ? 'text-purple-600 dark:text-purple-400' : 'text-green-600 dark:text-green-400' }}">
                            {{ $proyekBareng->is_finished ? 'Selesai' : 'Aktif' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Anggota</h3>
                        <div class="flex items-center space-x-2">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $proyekBareng->users->count() }}</p>
                            @if ($proyekBareng->users->count() > 0)
                                @php
                                    $approvedCount = $proyekBareng->users
                                        ->filter(
                                            fn($user) => isset($user->pivot->is_approved) && $user->pivot->is_approved,
                                        )
                                        ->count();
                                @endphp
                                @if ($approvedCount > 0)
                                    <span
                                        class="text-xs px-2 py-1 bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 rounded-full">
                                        {{ $approvedCount }} disetujui
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Meetings</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $proyekBareng->meets->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Kanboards</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $proyekBareng->kanboards->count() + $proyekBareng->kanboardLinks->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Dibuat</h3>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">
                            {{ $proyekBareng->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-pink-100 dark:bg-pink-900 rounded-lg">
                        <svg class="w-6 h-6 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Polls</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $proyekBareng->polls->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Team Members -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Anggota Tim</h3>
                </div>
                <div class="p-6">
                    @if ($proyekBareng->users->count() > 0)
                        <div class="space-y-4">
                            @foreach ($proyekBareng->users as $user)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                            {{ $user->initials() }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('users.show', $user) }}"
                                                    class="text-sm font-medium text-gray-900 dark:text-white hover:underline">
                                                    {{ $user->name }}
                                                </a>
                                                @if (isset($user->pivot->is_approved))
                                                    @if ($user->pivot->is_approved)
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                            <svg class="w-3 h-3 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                                </path>
                                                            </svg>
                                                            Disetujui
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                                            <svg class="w-3 h-3 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                                                </path>
                                                            </svg>
                                                            Menunggu Persetujuan
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                            @if ($user->pivot->description)
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    {{ $user->pivot->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end space-y-1">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $user->pivot->created_at->diffForHumans() }}
                                        </span>
                                        @if (isset($user->pivot->is_approved) && $user->pivot->is_approved)
                                            <div class="flex items-center text-xs text-green-600 dark:text-green-400">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Aktif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                </path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Belum ada anggota tim</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Meetings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Meetings</h3>
                </div>
                <div class="p-6">
                    @if ($proyekBareng->meets->count() > 0)
                        <div class="space-y-4">
                            @foreach ($proyekBareng->meets as $meet)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                            <a href="{{ route('meets.show', $meet) }}" class="hover:underline">
                                                {{ $meet->title }}
                                            </a>
                                        </h4>
                                        <div class="flex items-center space-x-2">
                                            <span
                                                class="text-xs px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                                                @if ($meet->scheduled_at)
                                                    {{ $meet->scheduled_at->format('d M') }}
                                                @endif
                                            </span>
                                            @if (isset($meet->is_finished) && $meet->is_finished)
                                                <span
                                                    class="text-xs px-2 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 rounded-full flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Selesai
                                                </span>
                                            @else
                                                <span
                                                    class="text-xs px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                    </svg>
                                                    Aktif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($meet->pivot->description)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                            {{ $meet->pivot->description }}</p>
                                    @endif
                                    @if ($meet->description)
                                        <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2">
                                            {{ $meet->description }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Belum ada meeting</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Polls -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Polls</h3>
                </div>
                <div class="p-6">
                    @if ($proyekBareng->polls->count() > 0)
                        <div class="space-y-4">
                            @foreach ($proyekBareng->polls as $poll)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                            <a href="{{ route('polls.show', $poll) }}" class="hover:underline">
                                                {{ $poll->title }}
                                            </a>
                                        </h4>
                                        <div class="flex items-center space-x-2">
                                            <span
                                                class="text-xs px-2 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 rounded-full">
                                                {{ $poll->created_at->format('d M') }}
                                            </span>
                                            @if (isset($poll->is_closed) && $poll->is_closed)
                                                <span
                                                    class="text-xs px-2 py-1 bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200 rounded-full flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Closed
                                                </span>
                                            @else
                                                <span
                                                    class="text-xs px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                    </svg>
                                                    Open
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($poll->pivot->description)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                            {{ $poll->pivot->description }}</p>
                                    @endif
                                    @if ($poll->description)
                                        <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2">
                                            {{ $poll->description }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Belum ada poll</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kanboards -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow lg:col-span-2">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Kanboards & Tools</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Platform internal dan eksternal untuk
                        manajemen proyek</p>
                </div>
                <div class="p-6">
                    @if ($proyekBareng->kanboards->count() > 0 || $proyekBareng->kanboardLinks->count() > 0)

                        <!-- Platform Kanboards -->
                        @if ($proyekBareng->kanboards->count() > 0)
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3 flex items-center">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                    Platform Kanboards ({{ $proyekBareng->kanboards->count() }})
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach ($proyekBareng->kanboards as $kanboard)
                                        <div
                                            class="border border-blue-200 dark:border-blue-700 rounded-lg p-4 bg-blue-50/50 dark:bg-blue-900/20">
                                            <div class="flex justify-between items-start mb-3">
                                                <h5 class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $kanboard->title }}</h5>
                                                <div class="flex items-center space-x-2">
                                                    <span
                                                        class="text-xs px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                                                        Platform
                                                    </span>
                                                    <span
                                                        class="text-xs px-2 py-1 bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 rounded-full">
                                                        {{ $kanboard->visibility }}
                                                    </span>
                                                </div>
                                            </div>
                                            @if ($kanboard->pivot->description)
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                                    {{ $kanboard->pivot->description }}</p>
                                            @endif
                                            @if ($kanboard->description)
                                                <p class="text-xs text-gray-600 dark:text-gray-300 mb-3 line-clamp-2">
                                                    {{ $kanboard->description }}</p>
                                            @endif
                                            <div class="flex justify-between items-center">
                                                <div
                                                    class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                                    <span>{{ $kanboard->cards()->count() }} cards</span>
                                                    <span>{{ $kanboard->users()->count() }} anggota</span>
                                                </div>
                                                <a href="{{ route('kanboard.show', $kanboard) }}" wire:navigate
                                                    class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded transition-colors">
                                                    Buka Platform
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- External Kanboard Links -->
                        @if ($proyekBareng->kanboardLinks->count() > 0)
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3 flex items-center">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    External Tools ({{ $proyekBareng->kanboardLinks->count() }})
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach ($proyekBareng->kanboardLinks as $kanboardLink)
                                        <div
                                            class="border border-green-200 dark:border-green-700 rounded-lg p-4 bg-green-50/50 dark:bg-green-900/20">
                                            <div class="flex justify-between items-start mb-3">
                                                <h5 class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $kanboardLink->title }}</h5>
                                                <span
                                                    class="text-xs px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                        </path>
                                                    </svg>
                                                    External
                                                </span>
                                            </div>
                                            @if ($kanboardLink->description)
                                                <p class="text-xs text-gray-600 dark:text-gray-300 mb-3 line-clamp-2">
                                                    {{ $kanboardLink->description }}</p>
                                            @endif
                                            <div class="flex justify-between items-center">
                                                <div
                                                    class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                                        </path>
                                                    </svg>
                                                    <span
                                                        class="truncate">{{ parse_url($kanboardLink->link, PHP_URL_HOST) }}</span>
                                                </div>
                                                <a href="{{ $kanboardLink->link }}" target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="text-xs bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded transition-colors flex items-center">
                                                    <span>Buka Link</span>
                                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                        </path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Belum ada tools</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada kanboard atau tools
                                eksternal yang terhubung.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
