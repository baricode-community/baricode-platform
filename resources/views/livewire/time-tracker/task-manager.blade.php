<div>
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 mb-4">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Tasks</h2>
            <button wire:click="openCreateModal" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Task
            </button>
        </div>
    </div>

    @if(session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Tasks List -->
    <div class="space-y-4">
        @forelse($tasks as $task)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 {{ $task->is_completed ? 'border-2 border-green-500' : '' }}">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-start flex-1">
                        <!-- Completion Checkbox -->
                        <div class="mr-3 mt-1">
                            <input type="checkbox" 
                                   wire:click="toggleCompletion({{ $task->id }})"
                                   {{ $task->is_completed ? 'checked' : '' }}
                                   class="w-5 h-5 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 cursor-pointer">
                        </div>
                        
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-1 {{ $task->is_completed ? 'line-through text-gray-500' : '' }}">
                                {{ $task->title }}
                                @if($task->is_completed)
                                    <span class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-200">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Completed
                                    </span>
                                @endif
                            </h3>
                            @if($task->description)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $task->description }}</p>
                            @endif
                        
                        <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                            @if($task->estimated_duration)
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Est: {{ $task->formatted_estimated_duration }}
                                </span>
                            @endif
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Actual: {{ $task->formatted_total_duration }}
                            </span>
                        </div>

                        @if($task->isOverEstimate())
                            <div class="mt-2 text-xs text-orange-600 dark:text-orange-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Task execution exceeded the estimate
                            </div>
                        @endif
                    </div>
                    </div>
                    
                    <div class="flex space-x-2 ml-4">
                        <button wire:click="openEditModal({{ $task->id }})" 
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        @if($task->entries()->count() == 0)
                            <button wire:click="delete({{ $task->id }})" 
                                    onclick="return confirm('Are you sure you want to delete this task?')"
                                    class="text-red-600 hover:text-red-800 dark:text-red-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Time Tracker Component -->
                @livewire('time-tracker.time-entry-tracker', ['taskId' => $task->id], 'tracker-'.$task->id)
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No tasks</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create a task to start tracking time.</p>
            </div>
        @endforelse
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800" wire:click.stop>
                <div class="mt-3">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4">
                        {{ $editMode ? 'Edit Task' : 'New Task' }}
                    </h3>
                    <form wire:submit.prevent="save">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Title *
                            </label>
                            <input type="text" wire:model="title" 
                                   class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            @error('title')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description
                            </label>
                            <textarea wire:model="description" rows="3"
                                      class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                            @error('description')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Estimated Duration (Optional)
                            </label>
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <input type="number" wire:model="estimatedHours" min="0" placeholder="HH"
                                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <span class="text-xs text-gray-500">Hours</span>
                                </div>
                                <div>
                                    <input type="number" wire:model="estimatedMinutes" min="0" max="59" placeholder="MM"
                                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <span class="text-xs text-gray-500">Minutes</span>
                                </div>
                                <div>
                                    <input type="number" wire:model="estimatedSeconds" min="0" max="59" placeholder="SS"
                                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <span class="text-xs text-gray-500">Seconds</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button type="button" wire:click="closeModal"
                                    class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-700">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                {{ $editMode ? 'Update' : 'Create' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
