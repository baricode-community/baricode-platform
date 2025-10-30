<?php

use Livewire\Volt\Component;
use Livewire\Volt\Attributes\Layout;
use App\Models\Kanboard;
use Livewire\WithPagination;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;
    
    public $showCreateModal = false;
    public $title = '';
    public $description = '';
    public $visibility = 'private';
    
    public function mount(): void
    {
        $this->authorize('viewAny', Kanboard::class);
    }
    
    public function with(): array
    {
        return [
            'kanboards' => Kanboard::accessibleBy(auth()->user())
                ->active()
                ->latest()
                ->paginate(12),
            'myKanboards' => Kanboard::where('owner_id', auth()->id())
                ->active()
                ->latest()
                ->take(6)
                ->get(),
            'sharedKanboards' => Kanboard::whereHas('users', function($query) {
                $query->where('user_id', auth()->id())->where('status', 'active');
            })
                ->active()
                ->latest()
                ->take(6)
                ->get(),
        ];
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
        
        $this->reset(['title', 'description', 'visibility', 'showCreateModal']);
        
        session()->flash('message', 'Kanboard berhasil dibuat!');
        
        $this->redirect(route('kanboard.show', $kanboard), navigate: true);
    }
    
    public function openCreateModal(): void
    {
        $this->showCreateModal = true;
        $this->visibility = 'private';
    }
    
    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->reset(['title', 'description', 'visibility']);
    }
};

?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Kanboard</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola proyek dan tugas dengan sistem Kanban</p>
            </div>
            <button 
                wire:click="openCreateModal"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Kanboard
            </button>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Kanboard Saya</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $myKanboards->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Berbagi Dengan Saya</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $sharedKanboards->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Kanboard</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $kanboards->total() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Kanboards -->
        @if($myKanboards->count() > 0)
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Kanboard Saya</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($myKanboards as $kanboard)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                {{ $kanboard->title }}
                            </h3>
                            <span class="text-xs px-2 py-1 rounded-full {{ $kanboard->visibility === 'public' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                {{ $kanboard->visibility === 'public' ? 'Publik' : 'Privat' }}
                            </span>
                        </div>
                        
                        @if($kanboard->description)
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                            {{ $kanboard->description }}
                        </p>
                        @endif
                        
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                <span>{{ $kanboard->cards()->count() }} cards</span>
                                <span>{{ $kanboard->users()->count() }} anggota</span>
                            </div>
                            
                            <a 
                                href="{{ route('kanboard.show', $kanboard) }}" 
                                wire:navigate
                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors"
                            >
                                Buka
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- All Kanboards -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Semua Kanboard</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($kanboards as $kanboard)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                {{ $kanboard->title }}
                            </h3>
                            <div class="flex items-center space-x-2">
                                @if($kanboard->owner_id === auth()->id())
                                <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    Owner
                                </span>
                                @endif
                                <span class="text-xs px-2 py-1 rounded-full {{ $kanboard->visibility === 'public' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                    {{ $kanboard->visibility === 'public' ? 'Publik' : 'Privat' }}
                                </span>
                            </div>
                        </div>
                        
                        @if($kanboard->description)
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                            {{ $kanboard->description }}
                        </p>
                        @endif
                        
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                <span>{{ $kanboard->cards()->count() }} cards</span>
                                <span>{{ $kanboard->users()->count() }} anggota</span>
                            </div>
                            
                            <a 
                                href="{{ route('kanboard.show', $kanboard) }}" 
                                wire:navigate
                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors"
                            >
                                Buka
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada kanboard</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mulai dengan membuat kanboard pertama Anda.</p>
                    <div class="mt-6">
                        <button 
                            wire:click="openCreateModal"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
                        >
                            Buat Kanboard
                        </button>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        @if($kanboards->hasPages())
        <div class="mt-8">
            {{ $kanboards->links() }}
        </div>
        @endif
    </div>

    <!-- Create Modal -->
    @if($showCreateModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Buat Kanboard Baru</h3>
                    <button 
                        wire:click="closeCreateModal"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form wire:submit="createKanboard">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Judul <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="title"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Masukkan judul kanboard"
                        >
                        @error('title') 
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Deskripsi
                        </label>
                        <textarea 
                            wire:model="description"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Deskripsi kanboard (opsional)"
                        ></textarea>
                        @error('description') 
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Visibilitas
                        </label>
                        <select 
                            wire:model="visibility"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="private">Privat - Hanya anggota yang diundang</option>
                            <option value="public">Publik - Semua orang dapat melihat</option>
                        </select>
                        @error('visibility') 
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button 
                            type="button"
                            wire:click="closeCreateModal"
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                        >
                            Buat Kanboard
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
