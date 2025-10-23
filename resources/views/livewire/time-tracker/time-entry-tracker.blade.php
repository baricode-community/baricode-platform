<div class="mt-3 border-t border-gray-200 dark:border-gray-700 pt-3">
    @if(session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded-lg relative mb-3 text-sm" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <!-- Timer Display -->
            <div class="flex items-center text-2xl font-mono font-bold text-gray-900 dark:text-gray-100" 
                 x-data="{ duration: {{ $currentDuration }} }" 
                 x-init="
                     @if($isRunning)
                     setInterval(() => { duration++; $wire.call('refreshTimer'); }, 1000)
                     @endif
                 ">
                <svg class="w-6 h-6 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span x-text="
                    Math.floor(duration / 3600).toString().padStart(2, '0') + ':' +
                    Math.floor((duration % 3600) / 60).toString().padStart(2, '0') + ':' +
                    (duration % 60).toString().padStart(2, '0')
                ">00:00:00</span>
            </div>
        </div>

        <!-- Control Buttons -->
        <div class="flex items-center space-x-2">
            @if($isRunning)
                <!-- Stop Button -->
                <button wire:click="stop" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                    </svg>
                    Stop
                </button>

                <!-- Discard Button -->
                <button wire:click="discard" 
                        onclick="return confirm('Are you sure you want to discard this time entry?')"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Discard
                </button>
            @else
                <!-- Play Button -->
                <button wire:click="start" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Start
                </button>
            @endif
        </div>
    </div>

    <!-- Time Entries History -->
    @if($task->entries()->where('stopped_at', '!=', null)->count() > 0)
        <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-3">
            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Time Entries:</h4>
            <div class="space-y-2">
                @foreach($task->entries()->where('stopped_at', '!=', null)->latest()->get() as $entry)
                    <div class="flex justify-between items-center text-xs text-gray-600 dark:text-gray-400">
                        <span>{{ $entry->started_at->format('M d, Y H:i') }} - {{ $entry->stopped_at->format('H:i') }}</span>
                        <span class="font-mono font-semibold">{{ $entry->formatted_duration }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@script
<script>
    // Auto-refresh timer every second when running
    @if($isRunning)
        setInterval(() => {
            $wire.$refresh();
        }, 1000);
    @endif
</script>
@endscript
