<div class="todo-messaging" {!! $showMessaging ? 'wire:poll.3s="checkForNewMessages"' : '' !!}>
    @if($canViewMessage)
        <!-- Toggle Messaging Button -->
        <button wire:click="toggleMessaging" 
                class="flex items-center space-x-2 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <span>{{ $showMessaging ? 'Sembunyikan Chat' : 'Chat' }}</span>
            @if(count($messages) > 0)
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ count($messages) }}</span>
            @endif
        </button>

        @if($showMessaging)
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 p-4 mt-2">
                <!-- Messages List -->
                <div class="messages-container max-h-64 overflow-y-auto mb-4 space-y-3">
                    @if(count($messages) === 0)
                        <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                            <svg class="w-8 h-8 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <p class="text-sm">Belum ada pesan</p>
                        </div>
                    @else
                        @foreach($messages as $message)
                            <div class="message-item flex space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                            {{ strtoupper(substr($message['user']['name'], 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $message['user']['name'] }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($message['created_at'])->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 mt-1 border border-gray-200 dark:border-gray-600">
                                        <p class="text-sm text-gray-900 dark:text-white">{{ $message['message'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Send Message Form (only if user can send messages) -->
                @if($canSendMessage)
                    <form wire:submit.prevent="sendMessage" class="border-t border-gray-200 dark:border-gray-600 pt-4">
                        <div class="flex space-x-2">
                            <div class="flex-1">
                                <textarea 
                                    wire:model="newMessage" 
                                    placeholder="Tulis pesan..."
                                    rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white text-sm resize-none"
                                    maxlength="1000"></textarea>
                                @error('newMessage')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <button 
                                type="submit" 
                                :disabled="!$wire.newMessage.trim()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                            Anda hanya dapat melihat pesan. Hanya yang ditugaskan, manager, dan admin yang dapat mengirim pesan.
                        </p>
                    </div>
                @endif
            </div>
        @endif

        @if(session()->has('message-success'))
            <div class="mt-2 p-2 bg-green-100 border border-green-400 text-green-700 text-xs rounded">
                {{ session('message-success') }}
            </div>
        @endif
    @endif
</div>

<!-- Simple JavaScript for auto-scroll to latest message -->
<script>
    document.addEventListener('livewire:init', function () {
        // Auto scroll to bottom when messages container is updated
        const observer = new MutationObserver(function() {
            const messagesContainer = document.querySelector('.messages-container');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        });
        
        const messagesContainer = document.querySelector('.messages-container');
        if (messagesContainer) {
            observer.observe(messagesContainer, { childList: true, subtree: true });
        }
    });
</script>
