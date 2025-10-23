@extends('components.layouts.app')

@section('title', 'Pelacak Waktu - Daftar Proyek')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">Pelacak Waktu</h1>
        <p class="text-gray-600 dark:text-gray-400">Kelola proyek dan lacak waktu kerja Anda</p>
    </div>

    <!-- Active Timer Alert -->
    @php
        $runningEntry = \App\Models\TimeTrackerEntry::where('user_id', auth()->id())
            ->where('is_running', true)
            ->with(['task.project'])
            ->first();
    @endphp
    
    @if($runningEntry)
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-l-4 border-green-500 rounded-lg p-4 mb-6 shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <!-- Pulsing Indicator -->
                    <div class="relative">
                        <div class="w-3 h-3 bg-green-500 rounded-full animate-ping absolute"></div>
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                    
                    <div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-semibold text-green-800 dark:text-green-200">Timer Berjalan</span>
                        </div>
                        <div class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                            <span class="font-medium">{{ $runningEntry->task->project->title }}</span>
                            <span class="mx-2">›</span>
                            <span>{{ $runningEntry->task->title }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Timer Display -->
                <div class="flex items-center space-x-4">
                    <div class="text-2xl font-mono font-bold text-green-700 dark:text-green-300"
                         x-data="{ 
                             duration: 0,
                             startedAt: {{ $runningEntry->started_at->timestamp }}
                         }"
                         x-init="
                             const updateDuration = () => {
                                 const now = Math.floor(Date.now() / 1000);
                                 duration = now - startedAt;
                             };
                             updateDuration();
                             setInterval(updateDuration, 1000);
                         ">
                        <span x-text="
                            Math.floor(duration / 3600).toString().padStart(2, '0') + ':' +
                            Math.floor((duration % 3600) / 60).toString().padStart(2, '0') + ':' +
                            (duration % 60).toString().padStart(2, '0')
                        ">00:00:00</span>
                    </div>
                    
                    <a href="{{ route('time-tracker.show', $runningEntry->task->project_id) }}"
                       class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span>Lihat Task</span>
                    </a>
                </div>
            </div>
            
            @if($runningEntry->note)
                <div class="mt-3 pl-7 text-sm text-gray-600 dark:text-gray-400 italic">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    "{{ $runningEntry->note }}"
                </div>
            @endif
        </div>
    @endif

    <!-- Projects List -->
    @livewire('time-tracker.project-manager')
</div>
@endsection
