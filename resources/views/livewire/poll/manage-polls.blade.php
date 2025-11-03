<div>
    {{-- Create Poll Button --}}
    @if (auth()->check())
        <div class="mb-8 text-right">
            <button wire:click="openCreateModal"
                class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-full font-extrabold text-sm text-white uppercase tracking-wider shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 transition ease-in-out duration-300 transform hover:scale-[1.02]">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Jajak Pendapat Baru
            </button>
        </div>
    @endif

    {{-- Create/Edit Modal (Improved structure and visual focus) --}}
    <div x-show="$wire.showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="relative z-50"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">

        {{-- Background backdrop --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-40 dark:bg-opacity-70 backdrop-blur-sm transition-opacity">
        </div>

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
                            <label for="title"
                                class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Judul Jajak
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
                            <textarea wire:model="description" id="description" rows="3" placeholder="Jelaskan tujuan jajak pendapat ini..."
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3"></textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Is Public Field --}}
                        <div class="mb-5">
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" wire:model="is_public"
                                    class="h-5 w-5 text-indigo-600 dark:text-indigo-400 border-gray-300 dark:border-gray-700 rounded focus:ring-indigo-500">
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Jadikan Jajak
                                    Pendapat
                                    Publik</span>
                            </label>
                            @error('is_public')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Options Fields --}}
                        <div class="mb-5">
                            <label
                                class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Pilihan</label>
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
