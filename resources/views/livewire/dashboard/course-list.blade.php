<?php

use Livewire\Volt\Component;

new class extends Component {
    public $record;

    public function mount($record)
    {
        $this->record = $record;
    }

    public function delete()
    {
        $this->record->delete();
        flash()->success('Kursus berhasil dihapus.');
    }
    
}; ?>

<li class="group relative border-b pb-6 bg-white hover:bg-blue-50 transition-colors duration-200 rounded-xl px-6 py-5 shadow-md flex items-center justify-between">
    <div class="flex items-center space-x-5">
        <div class="bg-gradient-to-tr from-blue-200 to-blue-400 text-blue-800 rounded-full p-3 shadow group-hover:scale-105 transition-transform duration-200">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6m0 0H6m6 0h6" />
            </svg>
        </div>
        <div>
            <h3 class="text-xl font-semibold text-gray-900 group-hover:text-blue-700 transition-colors duration-200">
                {{ $record->course->title }}
            </h3>
            @if (isset($record->course->description))
                <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ $record->course->description }}</p>
            @endif
        </div>
    </div>
    <div class="flex items-center space-x-3">
        <a href="{{ route('course.continue', $record->course->id) }}"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
            <span>Lanjutkan</span>
            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </a>
        <button wire:click="delete" wire:confirm.prompt="Apakah Anda yakin?\n\nKetik HAPUS untuk konfirmasi|HAPUS" class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400 transition"
            title="Hapus">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    <span class="absolute left-0 top-0 h-full w-1 bg-blue-500 opacity-0 group-hover:opacity-100 rounded-l transition"></span>
</li>
