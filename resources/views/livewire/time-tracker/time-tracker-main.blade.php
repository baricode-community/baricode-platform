<div class="mb-8">
    <!-- Projects Section -->
    <div class="mb-8">
        @livewire('time-tracker.project-manager')
    </div>

    <!-- Tasks Section - Only show when project is selected -->
    @if($selectedProjectId)
        <div class="transition-all duration-300 ease-in-out" 
             x-data 
             x-init="$el.scrollIntoView({ behavior: 'smooth', block: 'start' })">
            
            <!-- Divider with selected project info -->
            <div class="mb-6 flex items-center">
                <div class="flex-grow border-t border-gray-300 dark:border-gray-600"></div>
                <div class="mx-4 flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    <span>Selected Project Tasks</span>
                </div>
                <div class="flex-grow border-t border-gray-300 dark:border-gray-600"></div>
            </div>

            <!-- Tasks Manager -->
            <div class="animate-fadeIn">
                @livewire('time-tracker.task-manager', ['projectId' => $selectedProjectId], 'task-manager-'.$selectedProjectId)
            </div>
        </div>
    @endif
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</div>
