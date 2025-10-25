<div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm overflow-hidden">
    {{-- Poll Header --}}
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $poll->title }}</h1>
                <p class="mt-2 text-gray-600">{{ $poll->description }}</p>
                <div class="mt-2 text-sm text-gray-500">
                    Created by {{ $poll->user->name }}
                </div>
            </div>

            {{-- Status Badge & Control --}}
            <div class="flex flex-col items-end space-y-2">
                <span @class([
                    'px-3 py-1 text-sm font-medium rounded-full',
                    'bg-green-100 text-green-800' => $poll->isOpen(),
                    'bg-red-100 text-red-800' => !$poll->isOpen(),
                ])>
                    {{ $poll->status }}
                </span>

                @if($poll->user_id === auth()->id())
                    <button wire:click="toggleStatus" 
                            class="text-sm px-3 py-1 rounded-md border
                                   {{ $poll->isOpen() 
                                      ? 'border-red-300 text-red-700 hover:bg-red-50' 
                                      : 'border-green-300 text-green-700 hover:bg-green-50' }}">
                        {{ $poll->isOpen() ? 'Close Poll' : 'Open Poll' }}
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Poll Content --}}
    <div class="p-6">
        @if(session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                {{ session('message') }}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        @if($showResults)
            {{-- Show Results --}}
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Results</h3>
                @foreach($results as $result)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-700">{{ $result['text'] }}</span>
                            <span class="text-gray-500">
                                {{ $result['votes'] }} votes ({{ $result['percentage'] }}%)
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" 
                                 style="width: {{ $result['percentage'] }}%">
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="mt-2 text-sm text-gray-500">
                    Total votes: {{ $results->sum('votes') }}
                </div>
            </div>
        @else
            {{-- Voting Form --}}
            <form wire:submit.prevent="vote">
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900">Cast your vote</h3>
                    
                    @foreach($poll->options as $option)
                        <label class="flex items-center p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                            <input type="radio" 
                                   wire:model="selectedOption"
                                   value="{{ $option->id }}"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <span class="ml-3 text-gray-700">{{ $option->option_text }}</span>
                        </label>
                    @endforeach

                    @error('selectedOption')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="mt-6">
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent 
                                       rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700 
                                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                                       disabled:opacity-50 disabled:cursor-not-allowed"
                                {{ $poll->isClosed() ? 'disabled' : '' }}>
                            Submit Vote
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>
