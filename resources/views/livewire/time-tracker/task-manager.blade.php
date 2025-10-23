<div>
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-500 to-emerald-600 dark:from-green-600 dark:to-emerald-700 rounded-xl shadow-lg p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white mb-1">Project Tasks</h2>
                <p class="text-green-100 text-sm">Track time for individual tasks</p>
            </div>
            @if(!$isProjectCompleted)
                <button wire:click="openCreateModal" 
                        class="bg-white hover:bg-green-50 text-green-600 px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>New Task</span>
                </button>
            @else
                <div class="flex items-center bg-white/20 backdrop-blur-sm px-4 py-2 rounded-lg">
                    <svg class="w-5 h-5 text-white mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-white font-medium text-sm">Project Completed</span>
                </div>
            @endif
        </div>
    </div>

    @if(session()->has('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg relative mb-4 shadow-sm" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Tasks List -->
    <div class="space-y-4">
        @forelse($tasks as $task)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden {{ $task->is_completed ? 'ring-2 ring-green-400' : '' }}">
                
                <!-- Task Header -->
                <div class="p-5 {{ $task->is_completed ? 'bg-green-50 dark:bg-green-900/20' : 'bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800' }}">
                    <div class="flex justify-between items-start">
                        <div class="flex items-start flex-1">
                            <!-- Completion Checkbox -->
                            <div class="mr-3 mt-0.5">
                                <input type="checkbox" 
                                       wire:click="toggleCompletion({{ $task->id }})"
                                       {{ $task->is_completed ? 'checked' : '' }}
                                       class="w-6 h-6 text-green-600 bg-gray-100 border-gray-300 rounded-md focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 cursor-pointer transition-all">
                            </div>
                            
                            <div class="flex-1">
                                <h3 class="font-bold text-lg text-gray-900 dark:text-gray-100 mb-1 {{ $task->is_completed ? 'line-through text-gray-500' : '' }}">
                                    {{ $task->title }}
                                </h3>
                                @if($task->is_completed)
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-200 mb-2">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Task Completed
                                    </span>
                                @endif
                                @if($task->description)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 leading-relaxed">{{ $task->description }}</p>
                                @endif
                            
                                <!-- Stats -->
                                <div class="flex flex-wrap items-center gap-4 text-sm">
                                    @if($task->estimated_duration)
                                        <div class="flex items-center text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-3 py-1.5 rounded-lg">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="font-medium">Est: {{ $task->formatted_estimated_duration }}</span>
                                        </div>
                                    @endif
                                    <div class="flex items-center text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/30 px-3 py-1.5 rounded-lg">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="font-medium">Actual: {{ $task->formatted_total_duration }}</span>
                                    </div>
                                </div>

                                @if($task->isOverEstimate())
                                    <div class="mt-3 flex items-center text-sm text-orange-700 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/30 px-3 py-2 rounded-lg">
                                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <span class="font-medium">Task execution exceeded the estimate</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex space-x-2 ml-4">
                            @if(!$task->is_completed)
                                <button wire:click="openEditModal({{ $task->id }})" 
                                        class="p-2 text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/30 rounded-lg transition-colors"
                                        title="Edit Task">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                @if($task->entries()->count() == 0)
                                    <button wire:click="delete({{ $task->id }})" 
                                            onclick="return confirm('Are you sure you want to delete this task?')"
                                            class="p-2 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/30 rounded-lg transition-colors"
                                            title="Delete Task">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @endif
                            @else
                                <div class="text-xs text-gray-500 dark:text-gray-400 italic bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-lg">
                                    Completed tasks cannot be edited
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Time Tracker Component -->
                <div class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 p-4">
                    @if(!$task->is_completed)
                        @livewire('time-tracker.time-entry-tracker', ['taskId' => $task->id], 'tracker-'.$task->id)
                    @else
                        <div class="text-center py-3">
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic mb-3 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                </svg>
                                Time tracking is disabled for completed tasks
                            </p>
                            
                            <!-- Show time entries history for completed tasks -->
                            @if($task->entries()->count() > 0)
                                <div class="mt-4 bg-white dark:bg-gray-800 rounded-lg p-4">
                                    <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Time Entries History
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($task->entries()->latest()->get() as $entry)
                                            <div class="bg-gray-50 dark:bg-gray-900/50 px-3 py-2 rounded">
                                                <div class="flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        {{ $entry->started_at->format('M d, Y H:i') }} - {{ $entry->stopped_at ? $entry->stopped_at->format('H:i') : 'Running' }}
                                                    </span>
                                                    <span class="font-mono font-bold text-purple-600 dark:text-purple-400">{{ $entry->formatted_duration }}</span>
                                                </div>
                                                @if($entry->note)
                                                    <div class="mt-2 text-xs text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 rounded px-2 py-1">
                                                        <svg class="w-3 h-3 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                                        </svg>
                                                        {{ $entry->note }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-md p-12 text-center">
                <div class="max-w-sm mx-auto">
                    <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-green-500 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">No Tasks Yet</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Create your first task to start tracking time for this project.</p>
                    @if(!$isProjectCompleted)
                        <button wire:click="openCreateModal" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create Your First Task
                        </button>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4" 
             wire:click="closeModal"
             x-data 
             x-init="document.body.style.overflow = 'hidden'"
             x-destroy="document.body.style.overflow = 'auto'">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all" 
                 wire:click.stop
                 x-data
                 x-init="$el.animate([
                    { opacity: 0, transform: 'scale(0.9)' },
                    { opacity: 1, transform: 'scale(1)' }
                 ], { duration: 200, easing: 'ease-out' })">
                
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 dark:from-green-600 dark:to-emerald-700 px-6 py-4 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            {{ $editMode ? 'Edit Task' : 'Create New Task' }}
                        </h3>
                        <button wire:click="closeModal" 
                                class="text-white hover:text-green-100 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <form wire:submit.prevent="save" class="p-6">
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Task Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   wire:model="title" 
                                   placeholder="Enter task name..."
                                   class="w-full border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                                   autofocus>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Description
                            </label>
                            <textarea wire:model="description" 
                                      rows="4"
                                      placeholder="Add task description..."
                                      class="w-full border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all resize-none"></textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Estimated Duration (Optional)
                            </label>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <input type="number" 
                                           wire:model="estimatedHours" 
                                           min="0" 
                                           placeholder="00"
                                           class="w-full border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2.5 text-center font-mono text-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                                    <span class="block text-xs text-gray-500 dark:text-gray-400 text-center mt-1 font-medium">Hours</span>
                                </div>
                                <div>
                                    <input type="number" 
                                           wire:model="estimatedMinutes" 
                                           min="0" 
                                           max="59" 
                                           placeholder="00"
                                           class="w-full border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2.5 text-center font-mono text-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                                    <span class="block text-xs text-gray-500 dark:text-gray-400 text-center mt-1 font-medium">Minutes</span>
                                </div>
                                <div>
                                    <input type="number" 
                                           wire:model="estimatedSeconds" 
                                           min="0" 
                                           max="59" 
                                           placeholder="00"
                                           class="w-full border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2.5 text-center font-mono text-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                                    <span class="block text-xs text-gray-500 dark:text-gray-400 text-center mt-1 font-medium">Seconds</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" 
                                wire:click="closeModal"
                                class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 font-medium transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-200 flex items-center">
                            @if($editMode)
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Update Task
                            @else
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create Task
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
