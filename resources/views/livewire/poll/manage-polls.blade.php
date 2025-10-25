<div>
    {{-- Create Poll Button --}}
    <div class="mb-8 text-right">
        <button wire:click="openCreateModal"
            class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-full font-extrabold text-sm text-white uppercase tracking-wider shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 transition ease-in-out duration-300 transform hover:scale-[1.02]">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Buat Jajak Pendapat Baru
        </button>
    </div>

    {{-- Polls List --}}
    <div class="space-y-6">
        @forelse($polls as $poll)
            <div
                class="bg-white dark:bg-gray-900 shadow-xl rounded-xl p-6 transition duration-300 hover:shadow-2xl border border-gray-100 dark:border-gray-800">
                <div class="flex justify-between items-start">
                    <div class="flex-1 pr-4">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $poll->title }}</h3>
                        <p class="mt-1 text-gray-500 dark:text-gray-300 italic">{{ $poll->description }}</p>

                        {{-- Options List (Enhanced with a subtle border) --}}
                        <div class="mt-5 pt-3 border-t border-gray-100 dark:border-gray-800">
                            <h4 class="text-xs font-semibold uppercase text-indigo-600 dark:text-indigo-400 mb-2 tracking-wider">Pilihan:
                            </h4>
                            <ul class="list-none space-y-2 pl-0">
                                @foreach ($poll->options as $option)
                                    <li class="text-gray-700 dark:text-gray-200 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-indigo-400" fill="currentColor"
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

                        {{-- Status Badge --}}
                        <div class="mt-5">
                            <span @class([
                                'px-3 py-1 text-sm font-semibold rounded-full uppercase tracking-wider',
                                'bg-green-100 text-green-700 border border-green-300 dark:bg-green-900 dark:text-green-200 dark:border-green-700' => $poll->isOpen(),
                                'bg-red-100 text-red-700 border border-red-300 dark:bg-red-900 dark:text-red-200 dark:border-red-700' => !$poll->isOpen(),
                            ])>
                                {{ $poll->isOpen() ? 'Aktif' : 'Ditutup' }}
                            </span>
                        </div>
                    </div>

                    {{-- Action Buttons (Changed to sleek icon buttons) --}}
                    <div class="flex space-x-2 items-center">
                        {{-- View Results Button --}}
                        <a href="{{ route('polls.show', $poll) }}" title="Lihat Hasil" class="aksi-button group">
                            <svg class="w-5 h-5 group-hover:text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                        </a>

                        {{-- Edit Button --}}
                        <button wire:click="editPoll('{{ $poll->id }}')" title="Edit" class="aksi-button group">
                            <svg class="w-5 h-5 group-hover:text-yellow-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                        </button>

                        {{-- Toggle Status Button --}}
                        <button wire:click="toggleStatus('{{ $poll->id }}')"
                            title="{{ $poll->isOpen() ? 'Tutup Jajak Pendapat' : 'Buka Jajak Pendapat' }}"
                            class="aksi-button group">
                            <svg class="w-5 h-5 {{ $poll->isOpen() ? 'group-hover:text-red-600' : 'group-hover:text-green-600' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if ($poll->isOpen())
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                @endif
                            </svg>
                        </button>

                        {{-- Delete Button --}}
                        <button wire:click="deletePoll('{{ $poll->id }}')" title="Hapus" class="aksi-button group"
                            onclick="confirm('Apakah Anda yakin ingin menghapus jajak pendapat ini secara permanen?') || event.stopImmediatePropagation()">
                            <svg class="w-5 h-5 group-hover:text-red-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16 bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-100 dark:border-gray-800">
                <svg class="w-14 h-14 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                    </path>
                </svg>
                <h3 class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">Belum ada jajak pendapat</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">Mulai dengan membuat jajak pendapat pertama Anda.</p>
                <div class="mt-8">
                    <button wire:click="openCreateModal"
                        class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-full font-extrabold text-sm text-white uppercase tracking-wider shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 transition ease-in-out duration-300 transform hover:scale-[1.02]">
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

    {{-- Create/Edit Modal (Improved structure and visual focus) --}}
    <div x-show="$wire.showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="relative z-50"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">

        {{-- Background backdrop --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-40 dark:bg-opacity-70 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="$wire.showCreateModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-xl bg-white dark:bg-gray-900 px-6 pb-6 pt-7 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-8">

                    {{-- Modal header --}}
                    <div class="flex items-start justify-between border-b pb-4 border-gray-100 dark:border-gray-800">
                        <h3 class="text-xl font-bold leading-6 text-gray-900 dark:text-white" id="modal-title">
                            {{ isset($selectedPoll) ? 'Edit Jajak Pendapat' : 'Buat Jajak Pendapat Baru' }}
                        </h3>
                        <button wire:click="$set('showCreateModal', false)"
                            class="rounded-full p-1 bg-white text-gray-400 hover:text-gray-600 hover:bg-gray-50 focus:outline-none transition">
                            <span class="sr-only">Tutup</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Modal content --}}
                    <div class="mt-6">
                        {{-- Title Field --}}
                        <div class="mb-5">
                            <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Judul Jajak
                                Pendapat</label>
                            <input wire:model="title" type="text" id="title"
                                placeholder="Contoh: Voting Tema Proyek"
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description Field --}}
                        <div class="mb-5">
                            <label for="description"
                                class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Deskripsi</label>
                            <textarea wire:model="description" id="description" rows="3"
                                placeholder="Jelaskan tujuan jajak pendapat ini..."
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3"></textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Options Fields --}}
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Pilihan</label>
                            <div class="space-y-3">
                                @foreach ($options as $index => $option)
                                    <div class="flex items-center space-x-3">
                                        <input wire:model="options.{{ $index }}" type="text"
                                            placeholder="Pilihan {{ $index + 1 }}"
                                            class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3">
                                        @if (count($options) > 2)
                                            <button wire:click="removeOption({{ $index }})" type="button"
                                                title="Hapus Pilihan"
                                                class="p-2 text-red-500 hover:text-red-700 bg-red-50 rounded-full transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                                @error('options.*')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <button wire:click="addOption" type="button"
                                class="mt-3 inline-flex items-center text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 bg-indigo-50 dark:bg-indigo-900 px-3 py-1.5 rounded-full transition">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Tambah Pilihan
                            </button>
                        </div>
                    </div>

                    {{-- Modal footer --}}
                    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800 sm:flex sm:flex-row-reverse">
                        <button wire:click="{{ isset($selectedPoll) ? 'update' : 'create' }}" type="button"
                            class="inline-flex w-full justify-center rounded-lg bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800 px-4 py-2 text-base font-semibold text-white shadow-md transition sm:ml-3 sm:w-auto">
                            {{ isset($selectedPoll) ? 'Simpan Perubahan' : 'Buat Sekarang' }}
                        </button>
                        <button wire:click="$set('showCreateModal', false)" type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-gray-800 px-4 py-2 text-base font-semibold text-gray-700 dark:text-gray-200 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition sm:mt-0 sm:w-auto">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <style>
        /* CSS Tambahan untuk Tombol Aksi Ikon */
        .aksi-button {
            @apply p-2 rounded-full border border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out;
        }
    </style>
</div>
