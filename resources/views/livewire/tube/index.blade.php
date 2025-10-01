<?php

use Livewire\Volt\Component;
use Livewire\Volt\Attributes\Layout;
use App\Models\PersonalTube;

new #[Layout('layouts.app')] class extends Component {
    public bool $showAddTubeModal = false;
    public bool $showVideoModal = false;
    public ?PersonalTube $selectedVideo = null;
    public ?int $editId = null; // untuk simpan id saat edit
    public string $search = '';
    public string $newTitle = '';
    public string $newDescription = '';
    public string $newUrl = '';

    public function openAddTubeModal(): void
    {
        $this->showAddTubeModal = true;
    }

    public function openVideoModal($id): void
    {
        $this->selectedVideo = PersonalTube::where('user_id', auth()->id())->findOrFail($id);
        $this->showVideoModal = true;
    }

    public function closeVideoModal(): void
    {
        $this->showVideoModal = false;
        $this->selectedVideo = null;
    }

    public function openEditTubeModal($id): void
    {
        $tube = PersonalTube::where('user_id', auth()->id())->findOrFail($id);

        $this->editId = $tube->id;
        $this->newTitle = $tube->title;
        $this->newDescription = $tube->description ?? '';
        $this->newUrl = $tube->url;
        $this->showAddTubeModal = true;
    }

    public function closeAddTubeModal(): void
    {
        $this->showAddTubeModal = false;
        $this->reset(['newTitle', 'newDescription', 'newUrl', 'editId']);
    }

    public function saveTube(): void
    {
        $this->validate([
            'newTitle' => 'required|string|max:255',
            'newDescription' => 'nullable|string',
            'newUrl' => 'required|url',
        ]);

        if ($this->editId) {
            // update
            $tube = PersonalTube::where('user_id', auth()->id())->findOrFail($this->editId);
            $tube->update([
                'title' => $this->newTitle,
                'description' => $this->newDescription,
                'url' => $this->newUrl,
            ]);
            session()->flash('message', 'Video berhasil diperbarui!');
        } else {
            // tambah baru
            PersonalTube::create([
                'user_id' => auth()->id(),
                'title' => $this->newTitle,
                'description' => $this->newDescription,
                'url' => $this->newUrl,
            ]);
            session()->flash('message', 'Video berhasil ditambahkan!');
        }

        $this->closeAddTubeModal();
    }

    public function deleteTube($id): void
    {
        $tube = PersonalTube::where('user_id', auth()->id())->findOrFail($id);
        $tube->delete();

        session()->flash('message', 'Video berhasil dihapus!');
    }

    public function with(): array
    {
        $query = PersonalTube::where('user_id', auth()->id())->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        return [
            'tubes' => $query->get(),
        ];
    }
}; ?>

<div
    class="min-h-screen bg-gradient-to-br from-gray-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Flash Message -->
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                class="mb-6 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 rounded-xl shadow-lg flex items-center gap-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('message') }}
            </div>
        @endif

        <!-- Header Section -->
        <div class="mb-10 text-center">
            <div
                class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-red-500 to-pink-600 rounded-2xl shadow-lg mb-4 transform hover:scale-110 transition-transform duration-300">
                <x-heroicon-o-play-circle class="w-10 h-10 text-white" />
            </div>
            <h1
                class="text-4xl sm:text-5xl font-extrabold bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent mb-3">
                Playlist YouTube Pribadi
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Koleksi video favorit Anda dalam satu tempat</p>
        </div>

        <!-- Action Bar -->
        <div
            class="mb-8 flex flex-col sm:flex-row gap-4 items-center justify-between bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border border-gray-100 dark:border-gray-700">
            <div class="relative w-full sm:flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" wire:model.live="search"
                    class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200 placeholder-gray-400"
                    placeholder="Cari judul atau deskripsi video...">
            </div>
            <button type="button" wire:click="openAddTubeModal"
                class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200 flex items-center justify-center gap-2 group">
                <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Video
            </button>
        </div>

        <!-- Video Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($tubes as $index => $tube)
                <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 hover:-translate-y-2"
                    style="animation: fadeInUp 0.5s ease-out {{ $index * 0.1 }}s backwards;">
                    <div class="relative aspect-video overflow-hidden bg-gray-900 cursor-pointer"
                        wire:click="openVideoModal({{ $tube->id }})">
                        <iframe width="100%" height="100%"
                            src="https://www.youtube.com/embed/{{ \Illuminate\Support\Str::after($tube->url, 'v=') }}"
                            frameborder="0" allowfullscreen class="w-full h-full pointer-events-none"></iframe>
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <div
                                class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="flex items-start justify-between mb-3">
                            <h2
                                class="font-bold text-lg dark:text-white line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors flex-1">
                                {{ $tube->title }}
                            </h2>
                            <div class="flex gap-1">
                                <button wire:click="openEditTubeModal({{ $tube->id }})"
                                    class="p-1.5 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5h2m-1 0v2m-6 8l6-6 6 6" />
                                    </svg>
                                </button>
                                <button wire:click="deleteTube({{ $tube->id }})"
                                    wire:confirm="Apakah Anda yakin ingin menghapus video ini?"
                                    class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        @if ($tube->description)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
                                {{ $tube->description }}
                            </p>
                        @endif

                        <div class="flex items-center justify-between">
                            <a href="{{ $tube->url }}" target="_blank"
                                class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors group/link">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                                </svg>
                                Tonton
                                <svg class="w-4 h-4 group-hover/link:translate-x-1 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                            <span class="text-xs text-gray-400 dark:text-gray-500">
                                {{ $tube->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-20">
                    <div
                        class="w-24 h-24 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-600 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-xl font-semibold text-gray-500 dark:text-gray-400 mb-2">Tidak ada video ditemukan
                    </p>
                    <p class="text-gray-400 dark:text-gray-500">Mulai tambahkan video favorit Anda!</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal Tambah/Edit Video -->
    <div x-data="{ show: @entangle('showAddTubeModal') }" x-show="show" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-md"
        style="display: none;">
        <div x-show="show" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95" @click.away="$wire.closeAddTubeModal()"
            class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-8 max-w-md w-full m-4 relative border-2 border-indigo-100 dark:border-indigo-900">

            <!-- Close Button -->
            <button type="button" wire:click="closeAddTubeModal"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-all hover:rotate-90 duration-300"
                aria-label="Tutup">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>

            <!-- Modal Header -->
            <div class="mb-6">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="{{ $editId ? 'M5 13l4 4L19 7' : 'M12 4v16m8-8H4' }}" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $editId ? 'Edit Video' : 'Tambah Video Baru' }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $editId ? 'Perbarui detail video Anda' : 'Masukkan detail video YouTube Anda' }}
                </p>
            </div>

            <!-- Form -->
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-200">
                        Judul Video <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="newTitle"
                        class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                        placeholder="Masukkan judul video">
                    @error('newTitle')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-200">
                        Deskripsi (Opsional)
                    </label>
                    <textarea wire:model="newDescription" rows="3"
                        class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                        placeholder="Tambahkan deskripsi singkat"></textarea>
                    @error('newDescription')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-200">
                        URL YouTube <span class="text-red-500">*</span>
                    </label>
                    <input type="url" wire:model="newUrl"
                        class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                        placeholder="https://www.youtube.com/watch?v=XXXXXX">
                    @error('newUrl')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex justify-end gap-4">
                <button type="button" wire:click="closeAddTubeModal"
                    class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Batal
                </button>
                <button type="button" wire:click="saveTube"
                    class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl shadow-md hover:shadow-lg hover:scale-105 transition-all duration-200">
                    {{ $editId ? 'Perbarui' : 'Simpan' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Video Player -->
    <div x-data="{ show: @entangle('showVideoModal') }" x-show="show" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md"
        style="display: none;">
        <div x-show="show" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95" @click.away="$wire.closeVideoModal()"
            class="bg-black rounded-2xl shadow-2xl max-w-5xl w-full m-4 relative overflow-hidden">

            <!-- Close Button -->
            <button type="button" wire:click="closeVideoModal"
                class="absolute top-4 right-4 z-10 w-10 h-10 bg-black/50 hover:bg-black/70 text-white rounded-full flex items-center justify-center transition-all hover:scale-110 duration-300"
                aria-label="Tutup">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>

            @if ($selectedVideo)
                <!-- Video Player -->
                <div class="aspect-video">
                    <iframe width="100%" height="100%"
                        src="https://www.youtube.com/embed/{{ \Illuminate\Support\Str::after($selectedVideo->url, 'v=') }}?autoplay=1"
                        frameborder="0" allowfullscreen class="w-full h-full rounded-2xl"></iframe>
                </div>

                <!-- Video Info -->
                <div class="p-6 bg-gray-900 text-white">
                    <h3 class="text-xl font-bold mb-2">{{ $selectedVideo->title }}</h3>
                    @if ($selectedVideo->description)
                        <p class="text-gray-300 text-sm">{{ $selectedVideo->description }}</p>
                    @endif
                    <div class="flex items-center justify-between mt-4">
                        <span class="text-xs text-gray-400">
                            Ditambahkan {{ $selectedVideo->created_at->diffForHumans() }}
                        </span>
                        <a href="{{ $selectedVideo->url }}" target="_blank"
                            class="inline-flex items-center gap-2 text-sm font-medium text-red-400 hover:text-red-300 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                            </svg>
                            Buka di YouTube
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</div>
