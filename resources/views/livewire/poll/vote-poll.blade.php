<div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden transition duration-300 transform hover:shadow-3xl border border-gray-100 dark:border-gray-700">
    
    {{-- Header Poll yang Lebih Elegan --}}
    <div class="p-8 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
        <div class="flex justify-between items-start">
            <div class="pr-4">
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 tracking-tight">{{ $poll->title }}</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400 italic">{{ $poll->description }}</p>
                <div class="mt-4 text-sm font-medium text-gray-500 dark:text-gray-400">
                    Dibuat oleh <span class="text-indigo-600 dark:text-indigo-400">{{ $poll->user->name }}</span>
                </div>
            </div>

            {{-- Status Badge & Kontrol (Tombol Ikon Modern) --}}
            <div class="flex flex-col items-end space-y-3">
                <span @class([
                    'px-4 py-1.5 text-xs font-bold uppercase tracking-wider rounded-full shadow-md',
                    'bg-green-600 text-white' => $poll->isOpen(),
                    'bg-red-600 text-white' => !$poll->isOpen(),
                ])>
                    {{ $poll->status == 'Open' ? 'Polling Aktif' : 'Polling Ditutup' }}
                </span>

                @if($poll->user_id === auth()->id())
                    <button wire:click="toggleStatus" 
                            class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border transition duration-150
                                   {{ $poll->isOpen() 
                                      ? 'border-red-500 text-red-600 hover:bg-red-50 dark:border-red-400 dark:text-red-400 dark:hover:bg-red-900/50' 
                                      : 'border-green-500 text-green-600 hover:bg-green-50 dark:border-green-400 dark:text-green-400 dark:hover:bg-green-900/50' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($poll->isOpen())
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            @endif
                        </svg>
                        {{ $poll->isOpen() ? 'Tutup Polling' : 'Buka Polling' }}
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Konten Poll --}}
    <div class="p-8">
        @if(session()->has('message') || session()->has('error'))
            <div @class([
                'mb-6 p-4 rounded-xl font-medium shadow-md',
                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' => session()->has('message'),
                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' => session()->has('error'),
            ])>
                {{ session('message') ?? session('error') }}
            </div>
        @endif

        @if($showResults)
            {{-- Tampilkan Hasil (Bar yang Lebih Berani) --}}
            <div class="space-y-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 border-b pb-2 border-gray-100 dark:border-gray-700">Hasil Polling Saat Ini</h3>
                
                @foreach($results as $result)
                    <div class="bg-gray-50 rounded-xl p-4 dark:bg-gray-700/50 shadow-sm">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ $result['text'] }}</span>
                            <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                                {{ $result['percentage'] }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-600">
                            <div class="bg-indigo-600 h-3 rounded-full transition-all duration-700 ease-out shadow-inner" 
                                 style="width: {{ $result['percentage'] }}%">
                            </div>
                        </div>
                         <div class="mt-2 text-sm text-gray-500 dark:text-gray-400 text-right">
                            ({{ $result['votes'] }} suara)
                        </div>
                    </div>
                @endforeach

                <div class="pt-4 border-t border-gray-100 dark:border-gray-700 text-lg font-bold text-gray-700 dark:text-gray-300 text-center">
                    Total suara masuk: <span class="text-indigo-600 dark:text-indigo-400">{{ $results->sum('votes') }}</span>
                </div>
            </div>
        @else
            {{-- Form Voting (Pilihan Radio yang Lebih Interaktif) --}}
            <form wire:submit.prevent="vote">
                <div class="space-y-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Silakan Pilih Pilihan Anda</h3>
                    
                    @foreach($poll->options as $option)
                        <label class="flex items-center p-5 bg-gray-50 rounded-xl cursor-pointer transition duration-200
                                       hover:bg-indigo-50/50 hover:border-indigo-300 border border-transparent dark:bg-gray-700 dark:hover:bg-gray-700/70"
                               @if($selectedOption == $option->id) style="border-color: var(--color-indigo-400); background-color: var(--color-indigo-50);" @endif>
                            
                            <input type="radio" 
                                   wire:model="selectedOption"
                                   value="{{ $option->id }}"
                                   class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 dark:bg-gray-900/50 dark:checked:bg-indigo-600">
                            <span class="ml-4 text-lg font-medium text-gray-800 dark:text-gray-100">{{ $option->option_text }}</span>
                        </label>
                    @endforeach

                    @error('selectedOption')
                        <p class="mt-3 text-sm font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror

                    {{-- Peringatan yang Lebih Lembut --}}
                    <div class="mt-4 text-sm text-indigo-700 bg-indigo-100 rounded-lg p-3 dark:bg-indigo-900/50 dark:text-indigo-300">
                        <strong class="font-semibold">Perhatian:</strong> Pilihan Anda bersifat final dan **tidak dapat diubah** setelah dikirimkan.
                    </div>

                    <div class="pt-4">
                        {{-- Tombol Kirim Suara yang Menonjol --}}
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent 
                                       rounded-xl shadow-lg text-lg font-bold text-white bg-indigo-600 hover:bg-indigo-700 
                                       focus:outline-none focus:ring-4 focus:ring-indigo-300 transition duration-200 transform hover:scale-[1.005]
                                       disabled:opacity-60 disabled:shadow-none disabled:cursor-not-allowed dark:bg-indigo-700 dark:hover:bg-indigo-600">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Kirim Suara Anda
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>