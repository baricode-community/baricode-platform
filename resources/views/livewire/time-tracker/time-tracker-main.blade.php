<div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Projects List -->
        <div class="lg:col-span-1">
            @livewire('time-tracker.project-manager')
        </div>

        <!-- Tasks and Time Tracker -->
        <div class="lg:col-span-2">
            @if($selectedProjectId)
                @livewire('time-tracker.task-manager', ['projectId' => $selectedProjectId], 'task-manager-'.$selectedProjectId)
            @else
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No project selected</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Select a project from the list to view tasks and start tracking time.</p>
                </div>
            @endif
        </div>
    </div>
</div>
