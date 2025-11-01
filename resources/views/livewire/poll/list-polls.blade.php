

<div>
    <style>
        /* CSS kustom untuk animasi masuk (dipertahankan) */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.5s ease-out forwards;
        }
        
        .aksi-button {
            @apply p-2 rounded-full transition duration-200 ease-in-out text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transform hover:scale-110 active:scale-90 shadow-sm;
        }
    </style>
    {{-- ========================================================================= --}}
    {{-- BAGIAN BARU: Jajak Pendapat Milik Sendiri (Own Polls) --}}
    {{-- ========================================================================= --}}
    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-6 animate-fadeInUp" style="animation-delay: 0.1s;">
        🗳️ Jajak Pendapat Saya
    </h2>

    <div class="space-y-6 mb-10">
        @forelse($own_polls as $poll)
            <div
                class="bg-indigo-50 dark:bg-gray-800 shadow-lg rounded-xl p-5 transition duration-500 ease-out hover:shadow-2xl hover:border-indigo-600 border border-indigo-200 dark:border-gray-700 transform animate-fadeInUp"
                style="animation-delay: 0.2s;">
                <div class="flex justify-between items-start">
                    <div class="flex-1 pr-4">
                        <h3 class="text-xl font-bold text-indigo-800 dark:text-indigo-300 mb-1 transition duration-300">
                            {{ $poll->title }} <span class="text-sm font-normal text-gray-500 dark:text-gray-400">(Milik Anda)</span>
                        </h3>
                        <p class="mt-1 text-indigo-700 dark:text-gray-300 text-sm italic">{{ $poll->description }}</p>
                        
                        {{-- Status Badge (dengan pulse animation untuk Active) --}}
                        <div class="mt-3">
                            <span @class([
                                'px-3 py-1 text-xs font-semibold rounded-full uppercase tracking-wider transition duration-300 shadow-md',
                                'bg-green-200 text-green-800 border border-green-400 dark:bg-green-900 dark:text-green-200 dark:border-green-700 relative',
                                'animate-pulse-slow' => $poll->isOpen(),
                                'bg-red-200 text-red-800 border border-red-400 dark:bg-red-900 dark:text-red-200 dark:border-red-700' => !$poll->isOpen(),
                            ])>
                                {{ $poll->isOpen() ? 'Aktif' : 'Ditutup' }}
                            </span>
                        </div>
                    </div>

                    {{-- Action Buttons (Dipertahankan) --}}
                    <div class="flex space-x-1 items-center flex-shrink-0">
                        {{-- View Results Button --}}
                        <a href="{{ route('polls.show', $poll) }}" title="Lihat Hasil" class="aksi-button group hover:text-blue-600 dark:hover:text-blue-400">
                            <svg class="w-5 h-5 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition duration-200" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>

                        {{-- Toggle Status Button --}}
                        <button wire:click="toggleStatus('{{ $poll->id }}')"
                            title="{{ $poll->isOpen() ? 'Tutup Jajak Pendapat' : 'Buka Jajak Pendapat' }}"
                            class="aksi-button group">
                            <svg class="w-5 h-5 transition duration-200 
                                {{ $poll->isOpen() ? 'text-red-500 group-hover:text-red-700 dark:text-red-400 dark:group-hover:text-red-300' : 'text-green-500 group-hover:text-green-700 dark:text-green-400 dark:group-hover:text-green-300' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if ($poll->isOpen())
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                @endif
                            </svg>
                        </button>

                        {{-- Edit Button (Hanya tampil jika poll terbuka) --}}
                        @if ($poll->isOpen())
                            <button wire:click="editPoll('{{ $poll->id }}')" title="Edit"
                                class="aksi-button group hover:text-yellow-600 dark:hover:text-yellow-400">
                                <svg class="w-5 h-5 group-hover:text-yellow-600 dark:group-hover:text-yellow-400 transition duration-200" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                        @endif

                        {{-- Tombol Delete yang dikomentari dipertahankan --}}
                        {{-- @if ($poll->isOpen())
                        <button wire:click="deletePoll('{{ $poll->id }}')" title="Hapus" class="aksi-button group hover:text-red-600 dark:hover:text-red-400"
                        onclick="confirm('Apakah Anda yakin ingin menghapus jajak pendapat ini secara permanen?') || event.stopImmediatePropagation()">
                        <svg class="w-5 h-5 group-hover:text-red-600 dark:group-hover:text-red-400 transition duration-200" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        </button>
                        @endif --}}
                    </div>
                </div>
            </div>
        @empty
            {{-- Empty State untuk Own Polls --}}
            <div class="text-center py-8 bg-white dark:bg-gray-900 rounded-xl border border-dashed border-gray-300 dark:border-gray-700 animate-fadeInUp" style="animation-delay: 0.2s;">
                <p class="text-gray-500 dark:text-gray-400">Anda belum membuat jajak pendapat apapun.</p>
            </div>
        @endforelse
    </div>

    {{-- Garis Pemisah --}}
    @if($own_polls->isNotEmpty() && $polls->isNotEmpty())
        <hr class="my-10 border-t border-gray-200 dark:border-gray-700" />
    @endif
    
    {{-- ========================================================================= --}}
    {{-- BAGIAN LAMA: Jajak Pendapat Lain (Polls) --}}
    {{-- ========================================================================= --}}
    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-6 animate-fadeInUp" style="animation-delay: 0.3s;">
        🌍 Jajak Pendapat Lain
    </h2>
    
    <div class="space-y-6">
        @forelse($polls as $poll)
            <div
                class="bg-white dark:bg-gray-900 shadow-xl rounded-xl p-6 transition duration-500 ease-out hover:shadow-2xl hover:border-indigo-400 border border-gray-100 dark:border-gray-800 transform animate-fadeInUp"
                style="animation-delay: 0.4s;">
                <div class="flex justify-between items-start">
                    <div class="flex-1 pr-4">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1 transition duration-300 hover:text-indigo-600 dark:hover:text-indigo-400">{{ $poll->title }}</h3>
                        <p class="mt-1 text-gray-500 dark:text-gray-300 italic">{{ $poll->description }}</p>

                        {{-- Options List (Enhanced with a subtle border and hover) --}}
                        <div class="mt-5 pt-3 border-t border-gray-100 dark:border-gray-800">
                            <h4
                                class="text-xs font-semibold uppercase text-indigo-600 dark:text-indigo-400 mb-2 tracking-wider">
                                Pilihan:
                            </h4>
                            <ul class="list-none space-y-2 pl-0">
                                @foreach ($poll->options as $option)
                                    <li class="text-gray-700 dark:text-gray-200 flex items-center p-1 rounded transition duration-200 hover:bg-indigo-50 dark:hover:bg-gray-800">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500 flex-shrink-0" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $option->option_text }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- Status Badge (with pulse animation for Active) --}}
                        <div class="mt-5">
                            <span @class([
                                'px-3 py-1 text-sm font-semibold rounded-full uppercase tracking-wider transition duration-300 shadow-md',
                                'bg-green-100 text-green-700 border border-green-300 dark:bg-green-900 dark:text-green-200 dark:border-green-700 relative',
                                'animate-pulse-slow' => $poll->isOpen(), // Tambahkan animasi pulse kustom untuk Active
                                'bg-red-100 text-red-700 border border-red-300 dark:bg-red-900 dark:text-red-200 dark:border-red-700' => !$poll->isOpen(),
                            ])>
                                {{ $poll->isOpen() ? 'Aktif' : 'Ditutup' }}
                            </span>
                        </div>
                    </div>

                    {{-- Action Buttons (Dipertahankan) --}}
                    <div class="flex space-x-1 items-center flex-shrink-0">
                        {{-- View Results Button --}}
                        <a href="{{ route('polls.show', $poll) }}" title="Lihat Hasil" class="aksi-button group hover:text-blue-600 dark:hover:text-blue-400">
                            <svg class="w-5 h-5 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition duration-200" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                        </a>
                        
                        {{-- Toggle Status Button dan Edit tidak ditampilkan di daftar Polls Umum --}}
                        {{-- Jika ini adalah daftar poll umum (bukan milik sendiri), tombol Toggle dan Edit sebaiknya disembunyikan di sini --}}

                    </div>
                </div>
            </div>
        @empty
            {{-- Empty State (Dipertahankan) --}}
            <div
                class="text-center py-16 bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-100 dark:border-gray-800 animate-fadeInUp" style="animation-delay: 0.5s;">
                <svg class="w-14 h-14 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                    </path>
                </svg>
                <h3 class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">Belum ada jajak pendapat</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">Mulai dengan membuat jajak pendapat pertama
                    Anda.</p>
                <div class="mt-8">
                    <button wire:click="openCreateModal"
                        class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-full font-extrabold text-sm text-white uppercase tracking-wider shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 transition ease-in-out duration-300 transform hover:scale-[1.05] active:scale-95">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        Buat Jajak Pendapat Baru
                    </button>
                </div>
            </div>
        @endforelse
    </div>
</div>