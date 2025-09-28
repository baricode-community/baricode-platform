<?php

use App\Models\Meet;
use App\Models\User\User;
use Livewire\WithPagination;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    // Properties for meet management
    public $search = '';
    public $editingMeet = null;
    public $showCreateForm = false;
    public $showEditForm = false;
    public $showDeleteConfirm = false;
    public $showParticipantsModal = false;
    public $meetToDelete = null;
    public $selectedMeet = null;

    // Form properties
    public $title = '';
    public $youtube_link = '';
    public $description = '';
    public $scheduled_at = '';
    public $is_finished = false;
    
    // Participants management
    public $availableUsers = [];
    public $selectedUsers = [];
    public $userSearch = '';

    protected $queryString = ['search'];

    public function mount()
    {
        $this->loadAvailableUsers();
    }

    public function loadAvailableUsers()
    {
        $this->availableUsers = User::orderBy('name')->get();
    }

    public function meets()
    {
        $query = Meet::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        return $query->withCount('users as participants_count')
                    ->orderBy('scheduled_at', 'desc')
                    ->paginate(10);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openCreateForm()
    {
        $this->resetForm();
        $this->showCreateForm = true;
    }

    public function openEditForm($meetId)
    {
        $meet = Meet::findOrFail($meetId);
        $this->editingMeet = $meetId;
        $this->title = $meet->title;
        $this->youtube_link = $meet->youtube_link ?? '';
        $this->description = $meet->description ?? '';
        $this->scheduled_at = $meet->scheduled_at ? $meet->scheduled_at->format('Y-m-d\TH:i') : '';
        $this->is_finished = $meet->is_finished;
        $this->showEditForm = true;
    }

    public function openParticipantsModal($meetId)
    {
        $this->selectedMeet = Meet::with('users')->findOrFail($meetId);
        $this->selectedUsers = $this->selectedMeet->users->pluck('id')->toArray();
        $this->showParticipantsModal = true;
    }

    public function confirmDelete($meetId)
    {
        $this->meetToDelete = $meetId;
        $this->showDeleteConfirm = true;
    }

    public function createMeet()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'youtube_link' => 'nullable|url',
            'description' => 'nullable|string',
            'scheduled_at' => 'nullable|date|after:now'
        ]);

        Meet::create([
            'title' => $this->title,
            'youtube_link' => $this->youtube_link ?: null,
            'description' => $this->description ?: null,
            'scheduled_at' => $this->scheduled_at ? \Carbon\Carbon::parse($this->scheduled_at) : null,
            'is_finished' => $this->is_finished,
        ]);

        $this->closeForm();
        session()->flash('message', 'Meet berhasil dibuat!');
    }

    public function updateMeet()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'youtube_link' => 'nullable|url',
            'description' => 'nullable|string',
            'scheduled_at' => 'nullable|date'
        ]);

        $meet = Meet::findOrFail($this->editingMeet);
        
        $meet->update([
            'title' => $this->title,
            'youtube_link' => $this->youtube_link ?: null,
            'description' => $this->description ?: null,
            'scheduled_at' => $this->scheduled_at ? \Carbon\Carbon::parse($this->scheduled_at) : null,
            'is_finished' => $this->is_finished,
        ]);

        $this->closeForm();
        session()->flash('message', 'Meet berhasil diupdate!');
    }

    public function updateParticipants()
    {
        if ($this->selectedMeet) {
            $this->selectedMeet->users()->sync($this->selectedUsers);
            $this->closeModal();
            session()->flash('message', 'Peserta meet berhasil diupdate!');
        }
    }

    public function deleteMeet()
    {
        if ($this->meetToDelete) {
            Meet::findOrFail($this->meetToDelete)->delete();
            $this->meetToDelete = null;
            $this->showDeleteConfirm = false;
            session()->flash('message', 'Meet berhasil dihapus!');
        }
    }

    public function resetForm()
    {
        $this->title = '';
        $this->youtube_link = '';
        $this->description = '';
        $this->scheduled_at = '';
        $this->is_finished = false;
        $this->editingMeet = null;
        $this->resetValidation();
    }

    public function closeForm()
    {
        $this->resetForm();
        $this->showCreateForm = false;
        $this->showEditForm = false;
    }

    public function closeModal()
    {
        $this->showParticipantsModal = false;
        $this->showDeleteConfirm = false;
        $this->selectedMeet = null;
        $this->selectedUsers = [];
    }
};
?>

<div class="">
    <div class="bg-white dark:bg-gray-900 shadow-xl rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Meet Management</h2>
                <button wire:click="openCreateForm" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Meet
                </button>
            </div>
        </div>

        <div class="p-6">
            <!-- Search -->
            <div class="mb-6">
                <div class="relative">
                    <input wire:model.live="search" type="text" placeholder="Cari meet..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if(session('message'))
                <div class="mb-4 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-md">
                    <div class="flex">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="ml-3 text-sm text-green-800 dark:text-green-100">{{ session('message') }}</p>
                    </div>
                </div>
            @endif

            <!-- Meets Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Scheduled</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Participants</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($this->meets() as $meet)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $meet->title }}</div>
                                        @if($meet->description)
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($meet->description, 50) }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($meet->is_finished)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200">
                                            Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $meet->scheduled_at ? $meet->scheduled_at->format('d M Y, H:i') : 'Not scheduled' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200">
                                        {{ $meet->participants_count }} peserta
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    @if($meet->youtube_link)
                                        <a href="{{ $meet->youtube_link }}" target="_blank" 
                                           class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">
                                            <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 00-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 00.75-.75v-3.5a.75.75 0 011.5 0v3.5A2.25 2.25 0 0112.75 17h-8.5A2.25 2.25 0 012 14.75v-8.5A2.25 2.25 0 014.25 4h5a.75.75 0 010 1.5h-5z" clip-rule="evenodd"/>
                                                <path fill-rule="evenodd" d="M6.194 12.753a.75.75 0 001.06.053L16.5 4.44v2.81a.75.75 0 001.5 0v-4.5a.75.75 0 00-.75-.75h-4.5a.75.75 0 000 1.5h2.553l-9.056 8.194a.75.75 0 00-.053 1.06z" clip-rule="evenodd"/>
                                            </svg>
                                        </a>
                                    @endif
                                    <button wire:click="openParticipantsModal({{ $meet->id }})" 
                                            class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="openEditForm({{ $meet->id }})" 
                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $meet->id }})" 
                                            class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                    Tidak ada meet yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $this->meets()->links() }}
            </div>
        </div>
    </div>

    <!-- Create/Edit Meet Modal -->
    @if($showCreateForm || $showEditForm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-80 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800 dark:border-gray-700">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        {{ $showCreateForm ? 'Tambah Meet Baru' : 'Edit Meet' }}
                    </h3>
                    
                    <form wire:submit="{{ $showCreateForm ? 'createMeet' : 'updateMeet' }}">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Title</label>
                                <input wire:model="title" type="text" 
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-gray-100">
                                @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">YouTube Link (Nanti Saat Selesai)</label>
                                <input wire:model="youtube_link" type="url" 
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-gray-100"
                                       placeholder="https://www.youtube.com/watch?v=...">
                                @error('youtube_link') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Description</label>
                                <textarea wire:model="description" rows="3"
                                          class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-gray-100"></textarea>
                                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Scheduled At</label>
                                <input wire:model="scheduled_at" type="datetime-local" 
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-gray-100">
                                @error('scheduled_at') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="flex items-center space-x-3">
                                    <input wire:model="is_finished" type="checkbox" 
                                           class="rounded border-gray-300 dark:border-gray-700 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 dark:bg-gray-900">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Meet Sudah Selesai</span>
                                </label>
                                @error('is_finished') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" wire:click="closeForm"
                                    class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600 transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                {{ $showCreateForm ? 'Buat' : 'Update' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Participants Modal -->
    @if($showParticipantsModal && $selectedMeet)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-80 overflow-y-auto h-full w-full z-50">
            <div class="relative top-10 mx-auto p-5 border w-2/3 max-w-4xl shadow-lg rounded-md bg-white dark:bg-gray-800 dark:border-gray-700">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Manage Participants: {{ $selectedMeet->title }}
                    </h3>
                    
                    <form wire:submit="updateParticipants">
                        <div class="space-y-4">
                            <div class="max-h-96 overflow-y-auto border rounded-md p-4 dark:border-gray-700">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($availableUsers as $user)
                                        <label class="flex items-center space-x-3">
                                            <input type="checkbox" wire:model="selectedUsers" value="{{ $user->id }}"
                                                   class="rounded border-gray-300 dark:border-gray-700 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 dark:bg-gray-900">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" wire:click="closeModal"
                                    class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600 transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                Update Participants
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteConfirm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-80 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800 dark:border-gray-700">
                <div class="mt-3 text-center">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Konfirmasi Hapus</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500 dark:text-gray-300">
                            Apakah Anda yakin ingin menghapus meet ini? Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                    <div class="flex justify-center space-x-3">
                        <button wire:click="closeModal"
                                class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600 transition-colors">
                            Batal
                        </button>
                        <button wire:click="deleteMeet"
                            wire:confirm.prompt="Apakah Anda yakin?\n\nKetik HAPUS untuk konfirmasi|HAPUS"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

