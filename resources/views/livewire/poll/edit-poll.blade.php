<div class="">
    @if(session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="updatePoll" class="space-y-6">
        <div>
            <label class="block mb-2 text-lg font-bold text-gray-700" for="title">Judul</label>
            <input
                type="text"
                id="title"
                wire:model="title"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                placeholder="Masukkan judul polling"
            />
            @error('title')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block mb-2 text-lg font-bold text-gray-700" for="description">Deskripsi</label>
            <textarea
                id="description"
                wire:model="description"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                rows="3"
                placeholder="Deskripsi singkat polling"
            ></textarea>
            @error('description')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block mb-2 text-lg font-bold text-gray-700">Pilihan</label>
            <div class="space-y-2">
                @foreach($options as $optionId => $optionText)
                    <div class="flex items-center gap-2" wire:key="option-{{ $optionId }}">
                        <input
                            type="text"
                            wire:model="options.{{ $optionId }}"
                            class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                            placeholder="Opsi {{ $loop->iteration }}"
                            maxlength="255"
                        />
                        @if(count($options) > 2)
                            <button
                                type="button"
                                wire:click="removeOption('{{ $optionId }}')"
                                wire:confirm.prompt="Apakah Anda yakin?\n\nKetik HAPUS untuk konfirmasi|HAPUS"
                                class="text-red-500 hover:text-red-700 transition px-2 py-1 rounded"
                                title="Hapus opsi"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @else
                            <div class="w-9 h-9"></div> {{-- Spacer untuk menjaga layout --}}
                        @endif
                    </div>
                @endforeach
            </div>
            <button
                type="button"
                wire:click="addOption"
                class="mt-3 inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Opsi
            </button>
            @error('options')
                <span class="text-red-500 text-sm block mt-1">{{ $message }}</span>
            @enderror
            @if(session()->has('error'))
                <span class="text-red-500 text-sm block mt-1">{{ session('error') }}</span>
            @endif
        </div>

        <div class="flex flex-col sm:flex-row sm:space-x-8 space-y-3 sm:space-y-0">
            <label class="inline-flex items-center bg-gray-50 px-4 py-2 rounded shadow-sm hover:bg-gray-100 transition cursor-pointer">
                <input
                    type="checkbox"
                    wire:model="is_active"
                    class="form-checkbox text-blue-600 focus:ring-blue-500"
                />
                <span class="ml-3 font-medium text-gray-700">Aktif</span>
            </label>
            <label class="inline-flex items-center bg-gray-50 px-4 py-2 rounded shadow-sm hover:bg-gray-100 transition cursor-pointer">
                <input
                    type="checkbox"
                    wire:model="is_public"
                    class="form-checkbox text-blue-600 focus:ring-blue-500"
                />
                <span class="ml-3 font-medium text-gray-700">Publik</span>
            </label>
        </div>

        <div class="pt-4">
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-lg shadow transition"
            >
                Update Poll
            </button>
        </div>
    </form>
</div>
