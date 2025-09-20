<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <form wire:submit.prevent="createNote">
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Judul</label>
            <input type="text" wire:model.defer="noteTitle"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring" placeholder="Judul catatan">
            @error('noteTitle')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Catatan</label>
            <textarea wire:model.defer="noteContent" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring"
                rows="3" placeholder="Tulis catatan Anda di sini..."></textarea>
            @error('noteContent')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tambah Catatan</button>
    </form>

    <div class="bg-white rounded-xl shadow-md p-6">
        <ul class="space-y-3">
            @forelse($notes as $note)
                <li class="border border-gray-200 rounded-lg p-4 flex justify-between items-start">
                    <div>
                        <h3 class="font-semibold text-lg mb-2">{{ $note->title }}</h3>
                        <p class="text-gray-700 dark:text-gray-300">{{ $note->note }}</p>
                        <span class="text-xs text-gray-400">{{ $note->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <button wire:click="deleteNote({{ $note->id }})" class="ml-4 text-red-500 hover:text-red-700 text-sm">
                        Hapus
                    </button>
                </li>
            @empty
                <li class="text-gray-500">Belum ada catatan untuk pelajaran ini.</li>
            @endforelse
        </ul>
    </div>

</div>
