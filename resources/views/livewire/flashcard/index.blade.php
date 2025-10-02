<?php

use Livewire\Volt\Component;
use Livewire\Volt\Attributes\Layout;
use App\Models\PersonalFlashCard;

new #[Layout('layouts.app')] class extends Component {
    public bool $showAddCardModal = false;
    public bool $showPlayMode = false;
    public ?int $editId = null;
    public string $search = '';
    public string $newFront = '';
    public string $newBack = '';

    // Play mode properties
    public array $shuffledCards = [];
    public int $currentCardIndex = 0;
    public bool $showBack = false;

    public function openAddCardModal(): void
    {
        $this->showAddCardModal = true;
    }

    public function openEditCardModal($id): void
    {
        $card = PersonalFlashCard::where('user_id', auth()->id())->findOrFail($id);

        $this->editId = $card->id;
        $this->newFront = $card->front;
        $this->newBack = $card->back;
        $this->showAddCardModal = true;
    }

    public function closeAddCardModal(): void
    {
        $this->showAddCardModal = false;
        $this->reset(['newFront', 'newBack', 'editId']);
    }

    public function saveCard(): void
    {
        $this->validate([
            'newFront' => 'required|string',
            'newBack' => 'required|string',
        ]);

        if ($this->editId) {
            $card = PersonalFlashCard::where('user_id', auth()->id())->findOrFail($this->editId);
            $card->update([
                'front' => $this->newFront,
                'back' => $this->newBack,
            ]);
            session()->flash('message', 'Flashcard berhasil diperbarui!');
        } else {
            PersonalFlashCard::create([
                'user_id' => auth()->id(),
                'front' => $this->newFront,
                'back' => $this->newBack,
            ]);
            session()->flash('message', 'Flashcard berhasil ditambahkan!');
        }

        $this->closeAddCardModal();
    }

    public function deleteCard($id): void
    {
        $card = PersonalFlashCard::where('user_id', auth()->id())->findOrFail($id);
        $card->delete();

        session()->flash('message', 'Flashcard berhasil dihapus!');
    }

    public function startPlayMode(): void
    {
        $cards = PersonalFlashCard::where('user_id', auth()->id())
            ->get()
            ->toArray();

        if (count($cards) === 0) {
            session()->flash('error', 'Tidak ada flashcard untuk dimainkan!');
            return;
        }

        shuffle($cards);
        $this->shuffledCards = $cards;
        $this->currentCardIndex = 0;
        $this->showBack = false;
        $this->showPlayMode = true;
    }

    public function closePlayMode(): void
    {
        $this->showPlayMode = false;
        $this->shuffledCards = [];
        $this->currentCardIndex = 0;
        $this->showBack = false;
    }

    public function toggleCard(): void
    {
        $this->showBack = !$this->showBack;
    }

    public function nextCard(): void
    {
        if ($this->currentCardIndex < count($this->shuffledCards) - 1) {
            $this->currentCardIndex++;
            $this->showBack = false;
        }
    }

    public function previousCard(): void
    {
        if ($this->currentCardIndex > 0) {
            $this->currentCardIndex--;
            $this->showBack = false;
        }
    }

    public function with(): array
    {
        $query = PersonalFlashCard::where('user_id', auth()->id())->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('front', 'like', '%' . $this->search . '%')->orWhere('back', 'like', '%' . $this->search . '%');
            });
        }

        return [
            'cards' => $query->get(),
        ];
    }
}; ?>

<div
    class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-8 px-4 sm:px-6 lg:px-8">
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

        @if (session()->has('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                class="mb-6 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 rounded-xl shadow-lg flex items-center gap-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Header Section -->
        <div class="mb-10 text-center">
            <div
                class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl shadow-lg mb-4 transform hover:scale-110 transition-transform duration-300">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                </svg>
            </div>
            <h1
                class="text-4xl sm:text-5xl font-extrabold bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent mb-3">
                Flashcard Pribadi
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Belajar lebih efektif dengan flashcard digital</p>
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
                    class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200 placeholder-gray-400"
                    placeholder="Cari flashcard...">
            </div>
            <div class="flex gap-3 w-full sm:w-auto">
                <button type="button" wire:click="startPlayMode"
                    class="flex-1 sm:flex-none px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200 flex items-center justify-center gap-2 group">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z" />
                    </svg>
                    Main
                </button>
                <button type="button" wire:click="openAddCardModal"
                    class="flex-1 sm:flex-none px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200 flex items-center justify-center gap-2 group">
                    <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah
                </button>
            </div>
        </div>

        <!-- Flashcard Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($cards as $index => $card)
                <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 hover:-translate-y-2"
                    style="animation: fadeInUp 0.5s ease-out {{ $index * 0.1 }}s backwards;">

                    <!-- Card Preview with Flip Effect -->
                    <div
                        class="relative h-48 bg-gradient-to-br from-blue-500 to-purple-600 p-6 flex items-center justify-center perspective-1000">
                        <div class="text-center">
                            <p class="text-white font-bold text-lg line-clamp-4">{{ Str::limit($card->front, 120) }}</p>
                        </div>
                    </div>

                    <!-- Card Content -->
                    <div class="p-5">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <span
                                    class="text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded-lg">
                                    Flashcard
                                </span>
                            </div>
                            <div class="flex gap-1">
                                <button wire:click="openEditCardModal({{ $card->id }})"
                                    class="p-1.5 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="deleteCard({{ $card->id }})"
                                    wire:confirm="Apakah Anda yakin ingin menghapus flashcard ini?"
                                    class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-2 text-sm">
                            <div>
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Depan:</span>
                                <p class="text-gray-600 dark:text-gray-400 line-clamp-2">
                                    {{ Str::limit($card->front, 60) }}</p>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Belakang:</span>
                                <p class="text-gray-600 dark:text-gray-400 line-clamp-2">
                                    {{ Str::limit($card->back, 60) }}</p>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <span class="text-xs text-gray-400 dark:text-gray-500">
                                {{ $card->created_at->diffForHumans() }}
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
                                d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                        </svg>
                    </div>
                    <p class="text-xl font-semibold text-gray-500 dark:text-gray-400 mb-2">Tidak ada flashcard
                        ditemukan</p>
                    <p class="text-gray-400 dark:text-gray-500">Mulai buat flashcard pertama Anda!</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal Tambah/Edit Flashcard -->
    <div x-data="{ show: @entangle('showAddCardModal') }" x-show="show" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-md p-4"
        style="display: none;">
        <div x-show="show" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95" @click.away="$wire.closeAddCardModal()"
            class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-8 max-w-lg w-full relative border-2 border-blue-100 dark:border-blue-900 
               max-h-[90vh] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 my-auto">

            <button type="button" wire:click="closeAddCardModal"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-all hover:rotate-90 duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="mb-6">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="{{ $editId ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : 'M12 4v16m8-8H4' }}" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $editId ? 'Edit Flashcard' : 'Tambah Flashcard Baru' }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $editId ? 'Perbarui informasi flashcard' : 'Buat flashcard untuk belajar' }}
                </p>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-200">
                        Sisi Depan <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model="newFront" rows="4"
                        class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl 
                           focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white 
                           transition-all duration-200"
                        placeholder="Pertanyaan atau istilah"></textarea>
                    @error('newFront')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-200">
                        Sisi Belakang <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model="newBack" rows="4"
                        class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl 
                           focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white 
                           transition-all duration-200"
                        placeholder="Jawaban atau definisi"></textarea>
                    @error('newBack')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-4">
                <button type="button" wire:click="closeAddCardModal"
                    class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 
                       rounded-xl font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Batal
                </button>
                <button type="button" wire:click="saveCard"
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold 
                       rounded-xl shadow-md hover:shadow-lg hover:scale-105 transition-all duration-200">
                    {{ $editId ? 'Perbarui' : 'Simpan' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Play Mode -->
    <div x-data="{ show: @entangle('showPlayMode') }" x-show="show" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4"
        style="display: none;">
        <div x-show="show" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-2xl w-full relative border-2 border-blue-100 
               dark:border-blue-900 my-auto max-h-[90vh] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100">

            <button type="button" wire:click="closePlayMode"
                class="absolute top-4 right-4 z-10 w-10 h-10 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 
                   text-gray-600 dark:text-gray-300 rounded-full flex items-center justify-center transition-all hover:scale-110 duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            @if (count($shuffledCards) > 0)
                <div class="p-8">
                    <!-- Progress Bar -->
                    <div class="mb-6">
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                            <span class="font-semibold">Kartu {{ $currentCardIndex + 1 }} dari
                                {{ count($shuffledCards) }}</span>
                            <span>{{ round((($currentCardIndex + 1) / count($shuffledCards)) * 100) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full transition-all duration-300"
                                style="width: {{ (($currentCardIndex + 1) / count($shuffledCards)) * 100 }}%"></div>
                        </div>
                    </div>

                    <!-- Flashcard -->
                    <div class="relative mb-8">
                        <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl shadow-2xl p-8 min-h-[300px] 
                                flex items-center justify-center cursor-pointer transition-all duration-300 hover:shadow-3xl"
                            wire:click="toggleCard">
                            <div class="text-center text-white">
                                <div class="text-lg leading-relaxed">
                                    @if ($showBack)
                                        <div class="space-y-2">
                                            <span
                                                class="inline-block px-3 py-1 bg-white/20 rounded-lg text-sm font-semibold mb-2">Belakang</span>
                                            <p class="text-xl">{{ $shuffledCards[$currentCardIndex]['back'] }}</p>
                                        </div>
                                    @else
                                        <div class="space-y-2">
                                            <span
                                                class="inline-block px-3 py-1 bg-white/20 rounded-lg text-sm font-semibold mb-2">Depan</span>
                                            <p class="text-xl">{{ $shuffledCards[$currentCardIndex]['front'] }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Flip Indicator -->
                        <div class="text-center mt-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Klik kartu untuk {{ $showBack ? 'melihat pertanyaan' : 'melihat jawaban' }}
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                        <button type="button" wire:click="previousCard"
                            @if ($currentCardIndex === 0) disabled @endif
                            class="w-full sm:w-auto px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 
                               rounded-xl font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 
                               transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Sebelumnya
                        </button>

                        <button type="button" wire:click="toggleCard"
                            class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white 
                               font-semibold rounded-xl shadow-md hover:shadow-lg hover:scale-105 
                               transition-all duration-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            {{ $showBack ? 'Lihat Depan' : 'Lihat Belakang' }}
                        </button>

                        <button type="button" wire:click="nextCard" @if ($currentCardIndex === count($shuffledCards) - 1) disabled @endif
                            class="w-full sm:w-auto px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 
                               rounded-xl font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 
                               transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            Selanjutnya
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Completion Message -->
                    @if ($currentCardIndex === count($shuffledCards) - 1)
                        <div
                            class="mt-6 p-4 bg-green-50 dark:bg-green-900/20 border-2 border-green-200 dark:border-green-800 
                                rounded-xl text-center">
                            <p class="text-green-700 dark:text-green-300 font-semibold">
                                🎉 Ini adalah kartu terakhir! Klik "Selesai" untuk keluar atau "Acak Ulang" untuk
                                bermain lagi.
                            </p>
                            <button type="button" wire:click="startPlayMode"
                                class="mt-3 px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                                🔄 Acak Ulang
                            </button>
                        </div>
                    @endif
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
