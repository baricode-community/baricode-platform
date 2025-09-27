<?php

use App\Models\Meet;
use App\Models\User\User;
use Livewire\WithPagination;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    public $search = '';
    public $sortBy = 'scheduled_at';
    public $sortDirection = 'desc';

    protected $queryString = ['search', 'sortBy', 'sortDirection'];

    public function meets()
    {
        $query = Meet::query();

        // Search functionality
        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Sort functionality
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->withCount('users as participants_count')
                    ->paginate(12);
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function joinMeet($meetId)
    {
        if (!auth()->check()) {
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $meet = Meet::findOrFail($meetId);
        $user = auth()->user();

        if ($meet->isParticipant($user)) {
            session()->flash('error', 'Anda sudah terdaftar dalam meet ini.');
            return;
        }

        $meet->users()->attach($user->id, ['joined_at' => now()]);
        session()->flash('success', 'Berhasil bergabung dengan meet!');
    }

    public function leaveMeet($meetId)
    {
        if (!auth()->check()) {
            return;
        }

        $meet = Meet::findOrFail($meetId);
        $user = auth()->user();

        if (!$meet->isParticipant($user)) {
            session()->flash('error', 'Anda belum terdaftar dalam meet ini.');
            return;
        }

        $meet->users()->detach($user->id);
        session()->flash('success', 'Berhasil keluar dari meet!');
    }
}; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Daftar Meet</h1>
        <p class="mt-2 text-gray-600">Bergabunglah dengan meet online untuk belajar bersama!</p>
    </div>

    <!-- Search and Sort Controls -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <label for="search" class="sr-only">Cari meet</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" id="search" 
                           class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" 
                           placeholder="Cari berdasarkan judul atau deskripsi...">
                </div>
            </div>

            <!-- Sort Options -->
            <div class="flex gap-2">
                <button wire:click="sortBy('scheduled_at')" 
                        class="px-4 py-2 text-sm font-medium rounded-md border {{ $sortBy === 'scheduled_at' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                    Tanggal 
                    @if($sortBy === 'scheduled_at')
                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </button>
                
                <button wire:click="sortBy('title')" 
                        class="px-4 py-2 text-sm font-medium rounded-md border {{ $sortBy === 'title' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                    Judul
                    @if($sortBy === 'title')
                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-md mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Meet Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @forelse ($this->meets() as $meet)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">{{ $meet->title }}</h3>
                        <div class="flex items-center text-sm text-gray-500 ml-4">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                            {{ $meet->participants_count }}
                        </div>
                    </div>

                    @if ($meet->description)
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $meet->description }}</p>
                    @endif

                    @if ($meet->scheduled_at)
                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $meet->scheduled_at->format('d/m/Y H:i') }}
                        </div>
                    @endif

                    <div class="flex items-center gap-2">
                        <a href="{{ route('meets.show', $meet) }}" 
                           class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-indigo-600 text-sm font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Detail
                        </a>

                        @auth
                            @if ($meet->isParticipant(auth()->user()))
                                <button wire:click="leaveMeet({{ $meet->id }})" 
                                        class="px-4 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-md transition-colors">
                                    Keluar
                                </button>
                            @else
                                <button wire:click="joinMeet({{ $meet->id }})" 
                                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors">
                                    Gabung
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" 
                               class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors">
                                Login
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-4 text-sm font-medium text-gray-900">Tidak ada meet</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        @if ($search)
                            Tidak ada meet yang cocok dengan pencarian "{{ $search }}".
                        @else
                            Belum ada meet yang tersedia saat ini.
                        @endif
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $this->meets()->links() }}
    </div>
</div>
