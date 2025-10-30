<?php

use Livewire\Volt\Component;
use Livewire\Volt\Attributes\Layout;
use App\Models\Kanboard;

new #[Layout('layouts.app')] class extends Component {
    public $title = '';
    public $description = '';
    public $visibility = 'private';
    
    public function mount(): void
    {
        $this->authorize('create', Kanboard::class);
    }
    
    public function createKanboard(): void
    {
        $this->authorize('create', Kanboard::class);
        
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'visibility' => 'required|in:private,public',
        ]);
        
        $kanboard = Kanboard::create([
            'title' => $this->title,
            'description' => $this->description,
            'visibility' => $this->visibility,
            'owner_id' => auth()->id(),
        ]);
        
        session()->flash('message', 'Kanboard berhasil dibuat!');
        
        $this->redirect(route('kanboard.show', $kanboard), navigate: true);
    }
};

?>

<div class="py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6">
                <div class="mb-6">
                    <div class="flex items-center space-x-3 mb-2">
                        <a href="{{ route('kanboard.index') }}" wire:navigate class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Buat Kanboard Baru</h1>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">Buat kanboard baru untuk mengelola proyek dan tugas tim Anda.</p>
                </div>
                
                <form wire:submit="createKanboard">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Judul Kanboard <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="title"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Masukkan judul kanboard"
                        >
                        @error('title') 
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Deskripsi
                        </label>
                        <textarea 
                            wire:model="description"
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Deskripsi kanboard (opsional)"
                        ></textarea>
                        @error('description') 
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Visibilitas
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-start">
                                <input 
                                    type="radio" 
                                    wire:model="visibility" 
                                    value="private"
                                    class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                >
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Privat</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Hanya anggota yang diundang yang dapat melihat dan mengakses kanboard ini.
                                    </div>
                                </div>
                            </label>
                            
                            <label class="flex items-start">
                                <input 
                                    type="radio" 
                                    wire:model="visibility" 
                                    value="public"
                                    class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                >
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Publik</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Semua orang dapat melihat kanboard ini, tetapi hanya anggota yang dapat mengedit.
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('visibility') 
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <a 
                            href="{{ route('kanboard.index') }}" 
                            wire:navigate
                            class="px-6 py-3 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors"
                        >
                            Batal
                        </a>
                        <button 
                            type="submit"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Buat Kanboard
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>