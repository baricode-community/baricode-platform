<?php

use Livewire\Volt\Component;
use App\Models\Habit;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component
{
    public Habit $habit;
    public string $name = '';
    public string $description = '';

    public function mount($habitId)
    {
        $this->habit = Habit::findOrFail($habitId);
        
        // Check authorization - only creator can edit
        if ($this->habit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if habit is locked
        if ($this->habit->is_locked) {
            session()->flash('error', 'Habit yang sudah dikunci tidak dapat diedit.');
            return redirect()->route('satu-tapak.show', $this->habit->id);
        }
        
        $this->name = $this->habit->name;
        $this->description = $this->habit->description ?? '';
    }

    public function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function updateHabit()
    {
        $this->validate();

        try {
            $this->habit->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);

            session()->flash('success', 'Habit berhasil diperbarui!');
            return redirect()->route('satu-tapak.show', $this->habit->id);
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat memperbarui habit.');
        }
    }

    public function title()
    {
        return 'Edit Habit - ' . $this->habit->name;
    }
}; ?>

<div>
    <div class="">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <a href="{{ route('satu-tapak.show', $habit->id) }}" 
                   class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                    ← Kembali
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        {{-- Edit Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <form wire:submit="updateHabit" class="space-y-6">
                {{-- Habit Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nama Habit <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           wire:model="name" 
                           id="name"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100"
                           placeholder="Contoh: Olahraga Pagi, Membaca Buku, Meditasi">
                    @error('name') 
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Deskripsi (Opsional)
                    </label>
                    <textarea wire:model="description" 
                              id="description" 
                              rows="4"
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100"
                              placeholder="Jelaskan detail habit yang ingin Anda bangun..."></textarea>
                    @error('description') 
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                    @enderror
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Maksimal 1000 karakter</p>
                </div>

                {{-- Current Info (Read-only) --}}
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Informasi Habit (Tidak dapat diubah)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <label class="text-gray-500 dark:text-gray-400">Durasi</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $habit->duration_days }} hari</p>
                        </div>
                        <div>
                            <label class="text-gray-500 dark:text-gray-400">Tanggal Mulai</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $habit->start_date->format('d M Y') }}</p>
                        </div>
                        <div>
                            <label class="text-gray-500 dark:text-gray-400">Tanggal Berakhir</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $habit->end_date->format('d M Y') }}</p>
                        </div>
                        <div>
                            <label class="text-gray-500 dark:text-gray-400">Status</label>
                            <p class="text-gray-900 dark:text-gray-100">
                                @if($habit->is_active)
                                    <span class="text-green-600">Aktif</span>
                                @else
                                    <span class="text-red-600">Tidak Aktif</span>
                                @endif
                                @if($habit->is_locked)
                                    <span class="text-orange-600 ml-2">• Dikunci</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('satu-tapak.show', $habit->id) }}" 
                       class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200">
                        Batal
                    </a>
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200">
                        <span wire:loading.remove>💾 Simpan Perubahan</span>
                        <span wire:loading>Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>