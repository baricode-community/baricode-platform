<?php

use Livewire\Volt\Component;
use Livewire\Volt\Attributes\Layout;
use App\Models\DailyQuote;

new #[Layout('layouts.app')] class extends Component {
    public $quotes = [];
    public $confirmingDelete = null;

    // Untuk form create/edit
    public $quote_text;
    public $category;
    public $editingId = null;
    public $showForm = false;

    public function mount(): void
    {
        $this->loadQuotes();
    }

    public function loadQuotes(): void
    {
        $this->quotes = DailyQuote::all()->toArray();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function store(): void
    {
        $this->validate([
            'quote_text' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
        ]);

        if ($this->editingId) {
            DailyQuote::find($this->editingId)?->update([
                'quote_text' => $this->quote_text,
                'category' => $this->category,
            ]);
        } else {
            DailyQuote::create([
                'quote_text' => $this->quote_text,
                'category' => $this->category,
            ]);
        }

        $this->resetForm();
        $this->loadQuotes();
        $this->showForm = false;
    }

    public function edit($id): void
    {
        $quote = DailyQuote::find($id);
        if ($quote) {
            $this->editingId = $quote->id;
            $this->quote_text = $quote->quote_text;  // ✅ Diperbaiki
            $this->category = $quote->category;      // ✅ Diperbaiki
            $this->showForm = true;
        }
    }

    public function confirmDelete($id): void
    {
        $this->confirmingDelete = $id;
    }

    public function delete($id): void
    {
        DailyQuote::find($id)?->delete();
        $this->loadQuotes();
        $this->confirmingDelete = null;
    }

    public function resetForm(): void
    {
        $this->quote_text = '';
        $this->category = '';
        $this->editingId = null;
    }
};

?>

<div>
    <h2 class="text-3xl font-bold mb-6 flex items-center gap-3 dark:text-white">
        <x-heroicon-o-sparkles class="w-8 h-8 text-indigo-500 dark:text-indigo-400" />
        Daily Quotes
    </h2>

    <div class="mb-6 flex justify-end">
        <button wire:click="create"
            class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg shadow hover:bg-indigo-700 transition">
            + Tambah Quote
        </button>
    </div>

    {{-- Daftar Quotes --}}
    <div class="space-y-4">
        @forelse ($quotes as $quote)
            <div
                class="p-5 rounded-xl shadow-md bg-white dark:bg-gray-800 flex justify-between items-start hover:shadow-lg transition">
                <div class="space-y-1">
                    <h3 class="font-semibold text-xl dark:text-white">"{{ $quote['quote_text'] }}"</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">{{ $quote['category'] ?? '-' }}</p>
                </div>
                <div class="flex gap-2">
                    <button wire:click="edit({{ $quote['id'] }})"
                        class="px-3 py-1.5 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                        Edit
                    </button>
                    <button wire:click="confirmDelete({{ $quote['id'] }})"
                        class="px-3 py-1.5 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                        Hapus
                    </button>
                </div>
            </div>
        @empty
            <p class="text-gray-500 dark:text-gray-400 text-center py-10">Belum ada quote harian.</p>
        @endforelse
    </div>

    {{-- Modal Create/Edit --}}
    @if ($showForm)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 transition">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg w-full max-w-md animate-fadeIn">
                <h4 class="text-xl font-bold mb-4 dark:text-white">{{ $editingId ? 'Edit Quote' : 'Tambah Quote' }}</h4>
                <form wire:submit.prevent="store" class="space-y-3">
                    <div>
                        <textarea wire:model="quote_text" placeholder="Isi quote" name="quote_text"
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:bg-gray-700 dark:text-white"></textarea>
                        @error('quote_text')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <input type="text" wire:model="category" placeholder="Kategori (opsional)"
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:bg-gray-700 dark:text-white">
                        @error('category')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex gap-2 justify-end pt-2">
                        <button type="submit"
                            class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            {{ $editingId ? 'Update' : 'Simpan' }}
                        </button>
                        <button type="button" wire:click="$set('showForm', false)"
                            class="px-5 py-2 bg-gray-300 dark:bg-gray-600 dark:text-white rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Konfirmasi Hapus --}}
    @if ($confirmingDelete !== null)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg w-full max-w-sm animate-fadeIn">
                <h4 class="text-lg font-bold mb-2 dark:text-white">Konfirmasi Hapus</h4>
                <p class="mb-5 text-gray-700 dark:text-gray-300">Apakah Anda yakin ingin menghapus quote ini?</p>
                <div class="flex gap-2 justify-end">
                    <button wire:click="delete({{ $confirmingDelete }})"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Ya, Hapus
                    </button>
                    <button wire:click="$set('confirmingDelete', null)"
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-600 dark:text-white rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    @endif

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
</div>