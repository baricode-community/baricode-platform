<?php

use Livewire\Volt\Component;
use Livewire\Volt\Attributes\Layout;
use App\Models\WhatsAppGroup;

new #[Layout('layouts.app')] class extends Component {
    public $groups = [];
    public $confirmingDelete = null;

    // Untuk form create/edit
    public $name;
    public $group_id;
    public $description;
    public $editingId = null;
    public $showForm = false;

    public function mount(): void
    {
        $this->loadGroups();
    }

    public function loadGroups(): void
    {
        $this->groups = WhatsAppGroup::all()->toArray();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function store(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'group_id' => 'required',
            'description' => 'nullable|string',
        ]);

        if ($this->editingId) {
            WhatsAppGroup::find($this->editingId)?->update([
                'name' => $this->name,
                'group_id' => $this->group_id,
                'description' => $this->description,
            ]);
        } else {
            WhatsAppGroup::create([
                'name' => $this->name,
                'group_id' => $this->group_id,
                'description' => $this->description,
            ]);
        }

        $this->resetForm();
        $this->loadGroups();
        $this->showForm = false;
    }

    public function edit($id): void
    {
        $group = WhatsAppGroup::find($id);
        if ($group) {
            $this->editingId = $group->id;
            $this->name = $group->name;
            $this->group_id = $group->group_id;
            $this->description = $group->description;
            $this->showForm = true;
        }
    }

    public function confirmDelete($id): void
    {
        $this->confirmingDelete = $id;
    }

    public function delete($id): void
    {
        WhatsAppGroup::find($id)?->delete();
        $this->loadGroups();
        $this->confirmingDelete = null;
    }

    public function resetForm(): void
    {
        $this->name = '';
        $this->group_id = '';
        $this->description = '';
        $this->editingId = null;
    }
};

?>

<div>
    <h2 class="text-3xl font-bold mb-6 flex items-center gap-3 dark:text-white">
        <x-heroicon-o-chat-bubble-left-right class="w-8 h-8 text-indigo-500 dark:text-indigo-400" />
        Grup WhatsApp
    </h2>

    <div class="mb-6 flex justify-end">
        <button wire:click="create"
            class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg shadow hover:bg-indigo-700 transition">
            + Tambah Grup
        </button>
    </div>

    {{-- Daftar Grup --}}
    <div class="space-y-4">
        @forelse ($groups as $group)
            <div
                class="p-5 rounded-xl shadow-md bg-white dark:bg-gray-800 flex justify-between items-start hover:shadow-lg transition">
                <div class="space-y-1">
                    <h3 class="font-semibold text-xl dark:text-white">{{ $group['name'] }}</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">{{ $group['description'] ?? '-' }}</p>
                    <a href=" https://chat.whatsapp.com/{{ $group['group_id'] }}" target="_blank"
                        class="inline-block mt-2 text-indigo-600 hover:text-indigo-800 font-medium transition">
                        Bergabung →
                    </a>
                </div>
                <div class="flex gap-2">
                    <button wire:click="edit({{ $group['id'] }})"
                        class="px-3 py-1.5 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                        Edit
                    </button>
                    <button wire:click="confirmDelete({{ $group['id'] }})"
                        class="px-3 py-1.5 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                        Hapus
                    </button>
                </div>
            </div>
        @empty
            <p class="text-gray-500 dark:text-gray-400 text-center py-10">Belum ada grup WhatsApp.</p>
        @endforelse
    </div>

    {{-- Modal Create/Edit --}}
    @if ($showForm)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 transition">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg w-full max-w-md animate-fadeIn">
                <h4 class="text-xl font-bold mb-4 dark:text-white">{{ $editingId ? 'Edit Grup' : 'Tambah Grup' }}</h4>
                <form wire:submit.prevent="store" class="space-y-3">
                    <div>
                        <input type="text" wire:model="name" placeholder="Nama grup"
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:bg-gray-700 dark:text-white">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <input type="text" wire:model="group_id" placeholder="Link grup WhatsApp"
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:bg-gray-700 dark:text-white">
                        @error('group_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <textarea wire:model="description" placeholder="Deskripsi"
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:bg-gray-700 dark:text-white"></textarea>
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
                <p class="mb-5 text-gray-700 dark:text-gray-300">Apakah Anda yakin ingin menghapus grup ini?</p>
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
