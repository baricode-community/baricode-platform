<?php

use Livewire\Volt\Component;
use Livewire\Volt\Attributes\Layout;
use App\Models\ProyekBareng;

new #[Layout('layouts.app')] class extends Component {
    public ProyekBareng $proyekBareng;
    
    public function mount(ProyekBareng $proyekBareng): void
    {
        $this->proyekBareng = $proyekBareng;
        $this->proyekBareng->load(['users', 'meets', 'kanboards', 'kanboardLinks', 'polls']);
    }
};

?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a 
                        href="{{ route('proyek-bareng.index') }}" 
                        wire:navigate
                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $proyekBareng->title }}</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">ID Proyek: {{ $proyekBareng->id }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 {{ $proyekBareng->is_finished ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }} rounded-full text-sm font-medium">
                        {{ $proyekBareng->is_finished ? 'Proyek Selesai' : 'Proyek Aktif' }}
                    </span>
                    @if($proyekBareng->is_finished)
                    <div class="flex items-center text-purple-600 dark:text-purple-400">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium">Completed</span>
                    </div>
                    @else
                    <div class="flex items-center text-green-600 dark:text-green-400">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span class="text-sm font-medium">In Progress</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Project Description -->
        @if($proyekBareng->description)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Deskripsi Proyek</h2>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">{{ $proyekBareng->description }}</p>
        </div>
        @endif

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 {{ $proyekBareng->is_finished ? 'bg-purple-100 dark:bg-purple-900' : 'bg-green-100 dark:bg-green-900' }} rounded-lg">
                        <svg class="w-6 h-6 {{ $proyekBareng->is_finished ? 'text-purple-600 dark:text-purple-400' : 'text-green-600 dark:text-green-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($proyekBareng->is_finished)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            @endif
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</h3>
                        <p class="text-lg font-bold {{ $proyekBareng->is_finished ? 'text-purple-600 dark:text-purple-400' : 'text-green-600 dark:text-green-400' }}">
                            {{ $proyekBareng->is_finished ? 'Selesai' : 'Aktif' }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Anggota</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $proyekBareng->users->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Meetings</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $proyekBareng->meets->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Kanboards</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $proyekBareng->kanboards->count() + $proyekBareng->kanboardLinks->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Dibuat</h3>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $proyekBareng->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-pink-100 dark:bg-pink-900 rounded-lg">
                        <svg class="w-6 h-6 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Polls</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $proyekBareng->polls->count() }}</p>
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
                    @if($proyekBareng->users->count() > 0)
                    <div class="space-y-4">
                        @foreach($proyekBareng->users as $user)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ $user->initials() }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                                    @if($user->pivot->description)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->pivot->description }}</p>
                                    @endif
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $user->pivot->created_at->diffForHumans() }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
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
                    @if($proyekBareng->meets->count() > 0)
                    <div class="space-y-4">
                        @foreach($proyekBareng->meets as $meet)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $meet->title }}</h4>
                                <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                                    @if ($meet->scheduled_at)
                                        {{ $meet->scheduled_at->format('d M') }}
                                    @endif
                                </span>
                            </div>
                            @if($meet->pivot->description)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $meet->pivot->description }}</p>
                            @endif
                            @if($meet->description)
                            <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2">{{ $meet->description }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
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
                    @if($proyekBareng->polls->count() > 0)
                    <div class="space-y-4">
                        @foreach($proyekBareng->polls as $poll)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $poll->title }}</h4>
                                <span class="text-xs px-2 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 rounded-full">
                                    {{ $poll->created_at->format('d M') }}
                                </span>
                            </div>
                            @if($poll->pivot->description)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $poll->pivot->description }}</p>
                            @endif
                            @if($poll->description)
                            <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2">{{ $poll->description }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
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
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Platform internal dan eksternal untuk manajemen proyek</p>
                </div>
                <div class="p-6">
                    @if($proyekBareng->kanboards->count() > 0 || $proyekBareng->kanboardLinks->count() > 0)
                    
                    <!-- Platform Kanboards -->
                    @if($proyekBareng->kanboards->count() > 0)
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3 flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            Platform Kanboards ({{ $proyekBareng->kanboards->count() }})
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($proyekBareng->kanboards as $kanboard)
                            <div class="border border-blue-200 dark:border-blue-700 rounded-lg p-4 bg-blue-50/50 dark:bg-blue-900/20">
                                <div class="flex justify-between items-start mb-3">
                                    <h5 class="text-sm font-medium text-gray-900 dark:text-white">{{ $kanboard->title }}</h5>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                                            Platform
                                        </span>
                                        <span class="text-xs px-2 py-1 bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 rounded-full">
                                            {{ $kanboard->visibility }}
                                        </span>
                                    </div>
                                </div>
                                @if($kanboard->pivot->description)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $kanboard->pivot->description }}</p>
                                @endif
                                @if($kanboard->description)
                                <p class="text-xs text-gray-600 dark:text-gray-300 mb-3 line-clamp-2">{{ $kanboard->description }}</p>
                                @endif
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                        <span>{{ $kanboard->cards()->count() }} cards</span>
                                        <span>{{ $kanboard->users()->count() }} anggota</span>
                                    </div>
                                    <a 
                                        href="{{ route('kanboard.show', $kanboard) }}" 
                                        wire:navigate
                                        class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded transition-colors"
                                    >
                                        Buka Platform
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- External Kanboard Links -->
                    @if($proyekBareng->kanboardLinks->count() > 0)
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3 flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            External Tools ({{ $proyekBareng->kanboardLinks->count() }})
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($proyekBareng->kanboardLinks as $kanboardLink)
                            <div class="border border-green-200 dark:border-green-700 rounded-lg p-4 bg-green-50/50 dark:bg-green-900/20">
                                <div class="flex justify-between items-start mb-3">
                                    <h5 class="text-sm font-medium text-gray-900 dark:text-white">{{ $kanboardLink->title }}</h5>
                                    <span class="text-xs px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                        External
                                    </span>
                                </div>
                                @if($kanboardLink->description)
                                <p class="text-xs text-gray-600 dark:text-gray-300 mb-3 line-clamp-2">{{ $kanboardLink->description }}</p>
                                @endif
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                        </svg>
                                        <span class="truncate">{{ parse_url($kanboardLink->link, PHP_URL_HOST) }}</span>
                                    </div>
                                    <a 
                                        href="{{ $kanboardLink->link }}" 
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-xs bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded transition-colors flex items-center"
                                    >
                                        <span>Buka Link</span>
                                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
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
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Belum ada tools</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada kanboard atau tools eksternal yang terhubung.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>