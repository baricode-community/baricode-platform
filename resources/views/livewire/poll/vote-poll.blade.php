<div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl overflow-hidden transition duration-500 transform hover:shadow-3xl border-4 border-indigo-100 dark:border-gray-800/50">
    {{-- HEADER POLL ELEGANT & STATUS/KONTROL --}}
    <div class="p-8 border-b-2 border-indigo-200 dark:border-gray-800 bg-gradient-to-br from-indigo-50 dark:from-gray-800/50 to-white dark:to-gray-900/50">
        <div class="flex justify-between items-start flex-col sm:flex-row sm:items-center">
            {{-- Status & Kontrol --}}
            <div class="pr-4 mb-4 sm:mb-0">
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white leading-tight">
                    {{ $poll->question }} {{-- Asumsi ada variabel $poll->question --}}
                </h1>
                <p class="mt-2 text-lg text-indigo-700 dark:text-indigo-400 font-medium italic">{{ $poll->description }}</p>
                <div class="mt-3 text-sm font-semibold text-gray-500 dark:text-gray-400">
                    Dibuat oleh <span class="text-indigo-600 dark:text-indigo-400 font-bold hover:text-indigo-500 transition duration-150">{{ $poll->user->name }}</span>
                </div>
            </div>

            {{-- Status Badge & Kontrol --}}
            <div class="flex flex-col items-end space-y-3 flex-shrink-0">
                <span @class([
                    'px-4 py-2 text-xs font-bold uppercase tracking-widest rounded-full shadow-lg transition duration-300 transform',
                    'bg-green-600 text-white animate-pulse' => $poll->isOpen(),
                    'bg-red-600 text-white' => !$poll->isOpen(),
                ])>
                    {{ $poll->isOpen() ? 'POLLING AKTIF' : 'POLLING DITUTUP' }}
                </span>

                @if($poll->user_id === auth()->id())
                    <button wire:click="toggleStatus" 
                            class="inline-flex items-center px-4 py-2 text-sm font-bold rounded-xl border-2 transition duration-300 transform hover:scale-105 shadow-md
                                   {{ $poll->isOpen() 
                                      ? 'border-red-400 text-red-600 bg-white hover:bg-red-50 dark:border-red-500 dark:text-red-400 dark:bg-gray-800 dark:hover:bg-red-900/50' 
                                      : 'border-green-400 text-green-600 bg-white hover:bg-green-50 dark:border-green-500 dark:text-green-400 dark:bg-gray-800 dark:hover:bg-green-900/50' }}">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($poll->isOpen())
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
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

    {{-- KONTEN UTAMA POLL --}}
    <div class="p-8">
        
        {{-- Notifikasi --}}
        @if(session()->has('message') || session()->has('error'))
            <div @class([
                'mb-8 p-5 rounded-2xl font-semibold shadow-xl flex items-center border-l-4 transition duration-300',
                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 border-green-600' => session()->has('message'),
                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 border-red-600' => session()->has('error'),
            ])>
                <svg class="w-6 h-6 mr-3 flex-shrink-0 {{ session()->has('message') ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if(session()->has('message'))
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    @endif
                </svg>
                <p>{{ session('message') ?? session('error') }}</p>
            </div>
        @endif

        @if($showResults)
            {{-- TAMPILKAN HASIL POLLING --}}
            <div class="space-y-6">
                <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white border-b-4 pb-3 border-indigo-500/50 dark:border-indigo-800 flex items-center">
                    <span class="text-indigo-600 dark:text-indigo-400 mr-3">📊</span> Hasil Polling Saat Ini
                </h3>
                
                @foreach($results as $result)
                    <div class="bg-gray-50 rounded-2xl p-6 dark:bg-gray-800 shadow-xl hover:shadow-2xl transition duration-300 transform hover:-translate-y-0.5 border border-indigo-100 dark:border-gray-700 overflow-x-auto">
                        
                        {{-- Opsi dan Persentase --}}
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $result['text'] }}</span>
                            <span class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400 min-w-[80px] text-right">
                                {{ $result['percentage'] }}%
                            </span>
                        </div>
                        
                        {{-- Progress Bar --}}
                        <div class="w-full bg-gray-200 rounded-full h-5 dark:bg-gray-700 overflow-hidden">
                            <div class="bg-indigo-500 h-5 rounded-full transition-all duration-1000 ease-out shadow-lg shadow-indigo-300/50 dark:shadow-indigo-900/50" 
                                 style="width: {{ $result['percentage'] }}%">
                            </div>
                        </div>
                        
                        {{-- Total Suara --}}
                        <div class="mt-3 text-sm font-semibold text-gray-500 dark:text-gray-400 text-right">
                            Total <span class="text-gray-700 dark:text-gray-300 font-bold">{{ $result['votes'] }}</span> suara
                        </div>

                        @if(!$poll->isOpen() && count($result['participants']) > 0)
                            {{-- Dropdown Partisipan (Hanya jika Polling Ditutup) --}}
                            <details class="mt-5 border-t border-gray-200 dark:border-gray-700 pt-4">
                                <summary class="text-sm font-bold text-indigo-600 dark:text-indigo-400 cursor-pointer hover:text-indigo-700 transition duration-150 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10-2v-2a3 3 0 00-5.356-1.857M7 20h10v-2m4-2a5 5 0 00-10 0V8a5 5 0 0010 0zM9 8a5 5 0 0110 0M10 18H5a2 2 0 01-2-2v-5a2 2 0 012-2h14a2 2 0 012 2v5a2 2 0 01-2 2h-5"></path></svg>
                                    Lihat {{ count($result['participants']) }} Partisipan
                                </summary>
                                <div class="mt-4 space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar"> {{-- Tambahkan class custom-scrollbar jika ada CSS nya --}}
                                    @foreach($result['participants'] as $participant)
                                        <div class="flex justify-between items-center text-sm p-3 bg-gray-100 dark:bg-gray-700 rounded-lg border-l-4 border-indigo-400">
                                            <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $participant['name'] }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $participant['voted_at'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </details>
                        @endif
                    </div>
                @endforeach

                <div class="pt-6 border-t border-gray-100 dark:border-gray-800 text-xl font-extrabold text-gray-700 dark:text-gray-300 text-center bg-indigo-50 dark:bg-gray-800/50 p-5 rounded-2xl shadow-inner border border-indigo-200/50">
                    <span class="text-base font-medium">Total suara masuk:</span>
                    <span class="text-indigo-600 dark:text-indigo-400 text-3xl ml-2">{{ $results->sum('votes') }}</span>
                </div>
            </div>
        @else
            {{-- FORM VOTING --}}
            <form wire:submit.prevent="vote">
                <div class="space-y-6">
                    
                    {{-- Judul Form Dinamis --}}
                    <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white flex items-center border-b pb-3 border-gray-100 dark:border-gray-800">
                        @if($hasVoted)
                            <svg class="w-6 h-6 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            Perbarui Pilihan Anda
                        @else
                            <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Silakan Pilih Opsi Terbaik Anda
                        @endif
                    </h3>

                    {{-- Tombol Batalkan Vote (Jika Sudah Vote) --}}
                    @if($hasVoted)
                        <div class="flex justify-end">
                            <button type="button"
                                    wire:click="cancelVote"
                                    class="inline-flex items-center px-5 py-2 border border-red-400 text-red-600 font-bold rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-900/30 dark:hover:bg-red-900/60 dark:text-red-400 dark:border-red-500 transition duration-200 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-300/70">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Batalkan Vote
                            </button>
                        </div>
                    @endif
                    
                    {{-- Daftar Opsi --}}
                    @foreach($poll->options as $option)
                        <label class="flex items-center p-6 rounded-2xl cursor-pointer transition duration-300 ease-in-out border-2 
                                       hover:bg-indigo-50/70 hover:shadow-xl dark:hover:bg-gray-700/70 shadow-md
                                       {{ $selectedOption == $option->id ? 'border-indigo-600 bg-indigo-100/70 dark:bg-indigo-900/50 dark:border-indigo-400 shadow-indigo-300/50 dark:shadow-indigo-900/50 shadow-2xl scale-[1.01]' : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800' }}">
                            
                            <input type="radio" 
                                   wire:model="selectedOption"
                                   value="{{ $option->id }}"
                                   class="h-6 w-6 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 transition duration-150">
                            <span class="ml-4 text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $option->option_text }}</span>
                        </label>
                    @endforeach

                    @error('selectedOption')
                        <p class="mt-4 text-sm font-medium text-red-600 dark:text-red-400 flex items-center p-3 bg-red-50 dark:bg-red-900/30 rounded-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror

                    {{-- Peringatan --}}
                    <div class="mt-6 text-sm flex items-start text-indigo-800 bg-indigo-100/80 rounded-xl p-4 dark:bg-indigo-900/50 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                        <svg class="w-5 h-5 mt-0.5 mr-3 flex-shrink-0 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Pilihan Anda masih dapat diubah selama polling belum ditutup. Setelah ditutup, hasilnya akan ditampilkan.
                    </div>

                    <div class="pt-6 border-t border-gray-100 dark:border-gray-800">
                        {{-- Tombol Kirim Suara Dinamis --}}
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-8 py-4 border border-transparent 
                                       rounded-2xl shadow-xl text-xl font-extrabold text-white bg-indigo-600 hover:bg-indigo-700 
                                       focus:outline-none focus:ring-4 focus:ring-indigo-300 transition duration-300 transform hover:scale-[1.01] hover:shadow-2xl
                                       disabled:opacity-50 disabled:shadow-none disabled:cursor-not-allowed dark:bg-indigo-700 dark:hover:bg-indigo-600 dark:focus:ring-indigo-800/50">
                            
                            {{-- Ikon & Teks Tombol Berubah --}}
                            @if($hasVoted)
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                PERBARUI PILIHAN
                            @else
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                KIRIMKAN SUARA
                            @endif
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>