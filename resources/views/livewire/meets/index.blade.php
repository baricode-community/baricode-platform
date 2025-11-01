<?php

use App\Models\Meet;
use App\Models\User\User;
use Livewire\WithPagination;
use Livewire\Volt\Component;
use Livewire\Volt\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public $search = '';
    public $sortBy = 'scheduled_at';
    public $sortDirection = 'desc';
    public $showFinished = 'all'; // 'all', 'finished', 'unfinished'

    protected $queryString = ['search', 'sortBy', 'sortDirection', 'showFinished'];

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

        // Filter by finished status
        if ($this->showFinished === 'finished') {
            $query->where('is_finished', true);
        } elseif ($this->showFinished === 'unfinished') {
            $query->where('is_finished', false);
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

    public function updatedShowFinished()
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

    public function openMeetLink($meetId)
    {
        if (!auth()->check()) {
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $meet = Meet::findOrFail($meetId);
        $user = auth()->user();

        if (!$meet->isParticipant($user)) {
            session()->flash('error', 'Anda harus bergabung dengan meet terlebih dahulu untuk mengakses link meet.');
            return;
        }

        // Prioritize meet_link over youtube_link
        $link = $meet->meet_link ?: $meet->youtube_link;
        
        if ($link) {
            return redirect()->away($link);
        } else {
            session()->flash('error', 'Link meet tidak tersedia.');
        }
    }
}; ?>

<div class="">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Daftar Meet</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Bergabunglah dengan meet online untuk belajar bersama!</p>
    </div>

    <!-- Tata Tertib Meet -->
    @include('livewire.meets.partials.tata_tertib')

    <!-- Meet Mendatang Section -->
    @php
        $upcomingMeets = $this->meets()->where('scheduled_at', '>', now())->sortBy('scheduled_at')->take(3);
    @endphp
    <!-- Meet Mendatang -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-indigo-700 dark:text-indigo-400 mb-2">Meet Mendatang</h2>
        @if($upcomingMeets->count())
            <div class="flex flex-col gap-4">
                @foreach($upcomingMeets as $upcomingMeet)
                    <div class="bg-indigo-50 dark:bg-indigo-900 border border-indigo-200 dark:border-indigo-700 rounded-lg p-4 flex flex-col md:flex-row items-start md:items-center gap-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-indigo-800 dark:text-indigo-200">{{ $upcomingMeet->title }}</h3>
                            @if($upcomingMeet->scheduled_at)
                                <div class="text-sm text-indigo-700 dark:text-indigo-300 mt-1">
                                    Jadwal: {{ $upcomingMeet->scheduled_at->format('d/m/Y H:i') }}
                                </div>
                            @endif
                            @if($upcomingMeet->description)
                                <p class="text-indigo-700 dark:text-indigo-300 mt-2">{!! $upcomingMeet->description !!}</p>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('meets.show', $upcomingMeet) }}" 
                               class="px-4 py-2 text-sm font-medium rounded-md bg-indigo-600 text-white hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800 transition-colors">
                                Detail
                            </a>
                            @auth
                                @if ($upcomingMeet->isParticipant(auth()->user()))
                                    @if($upcomingMeet->meet_link || $upcomingMeet->youtube_link)
                                        <button wire:click="openMeetLink({{ $upcomingMeet->id }})" 
                                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 rounded-md transition-colors">
                                            Masuk
                                        </button>
                                    @endif
                                @else
                                    <button wire:click="joinMeet({{ $upcomingMeet->id }})" 
                                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800 rounded-md transition-colors">
                                        Gabung
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}" 
                                   class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800 rounded-md transition-colors">
                                    Login
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 text-yellow-800 dark:text-yellow-200">
                Meet Mendatang Belum Ditentukan
            </div>
        @endif
    </div>

    <!-- Search and Sort Controls -->
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <label for="search" class="sr-only">Cari meet</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" id="search" 
                           class="pl-10 pr-4 py-2 w-full border border-gray-300 dark:border-gray-700 rounded-md focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500" 
                           placeholder="Cari berdasarkan judul atau deskripsi...">
                </div>
            </div>

            <!-- Status Filter -->
            <div class="flex-shrink-0">
                <label for="showFinished" class="sr-only">Filter status meet</label>
                <select wire:model.live="showFinished" id="showFinished" 
                        class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                    <option value="all">Semua Meet</option>
                    <option value="unfinished">Belum Selesai</option>
                    <option value="finished">Sudah Selesai</option>
                </select>
            </div>

            <!-- Sort Options -->
            <div class="flex gap-2">
                <button wire:click="sortBy('scheduled_at')" 
                        class="px-4 py-2 text-sm font-medium rounded-md border 
                        {{ $sortBy === 'scheduled_at' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    Tanggal 
                    @if($sortBy === 'scheduled_at')
                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </button>
                
                <button wire:click="sortBy('title')" 
                        class="px-4 py-2 text-sm font-medium rounded-md border 
                        {{ $sortBy === 'title' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
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
        <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-600 dark:text-green-400 px-4 py-3 rounded-md mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 text-red-600 dark:text-red-400 px-4 py-3 rounded-md mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Meet Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @forelse ($this->meets() as $meet)
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden {{ $meet->is_finished ? 'opacity-75' : '' }}">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 line-clamp-2">{{ $meet->title }}</h3>
                                @if($meet->is_finished)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200">
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200">
                                        Aktif
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 ml-4">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                            {{ $meet->participants_count }}
                        </div>
                    </div>

                    @if ($meet->description)
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-3">{{ $meet->description }}</p>
                    @endif

                    @if ($meet->scheduled_at)
                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-4">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $meet->scheduled_at->format('d/m/Y H:i') }}
                        </div>
                    @endif

                    <div class="flex items-center gap-2">
                        <a href="{{ route('meets.show', $meet) }}" 
                           class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-indigo-600 text-sm font-medium rounded-md text-indigo-600 dark:text-indigo-400 bg-white dark:bg-gray-900 hover:bg-indigo-50 dark:hover:bg-gray-800 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Detail
                        </a>

                        @if($meet->is_finished)
                            <span class="px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 rounded-md cursor-not-allowed">
                                Selesai
                            </span>
                        @else
                            @auth
                                @if ($meet->isParticipant(auth()->user()))
                                    @if($meet->meet_link || $meet->youtube_link)
                                        <button wire:click="openMeetLink({{ $meet->id }})" 
                                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 rounded-md transition-colors">
                                            Masuk
                                        </button>
                                    @endif
                                @elseif ($meet->is_finished)
                                    <button wire:click="joinMeet({{ $meet->id }})" 
                                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800 rounded-md transition-colors">
                                        Gabung
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}" 
                                   class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800 rounded-md transition-colors">
                                    Login
                                </a>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada meet</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
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

