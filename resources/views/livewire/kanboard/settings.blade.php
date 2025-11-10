<?php

use Livewire\Volt\Component;
use Livewire\Volt\Attributes\Layout;
use App\Models\Projects\Kanboard;
use App\Models\Auth\User;
use Illuminate\Support\Str;

new #[Layout('layouts.app')] class extends Component {
    public Kanboard $kanboard;
    public $title;
    public $description;
    public $visibility;
    public $showDeleteModal = false;
    public $confirmDeleteText = '';

    public $showInviteModal = false;
    public $inviteName = ''; 
    public $inviteRole = 'member';
    public $searchResults = []; // hasil pencarian user

    public function mount(): void
    {
        $this->authorize('update', $this->kanboard);

        $this->title = $this->kanboard->title;
        $this->description = $this->kanboard->description;
        $this->visibility = $this->kanboard->visibility;
    }

    public function with(): array
    {
        return [
            'kanboardUsers' => $this->kanboard->kanboardUsers()
                ->with(['user', 'invitedBy'])
                ->orderBy('role')
                ->orderBy('created_at')
                ->get(),
            'isOwner' => $this->kanboard->isOwner(auth()->user()),
            'canManage' => $this->kanboard->canManage(auth()->user()),
        ];
    }

    public function updatedInviteName()
    {
        // Cari user yang namanya mirip dengan input
        $this->searchResults = [];

        if (strlen(trim($this->inviteName)) > 1) {
            $this->searchResults = \App\Models\User\User::where('name', 'like', '%' . trim($this->inviteName) . '%')
                ->orWhere('email', 'like', '%' . trim($this->inviteName) . '%')
                ->take(5)
                ->get(['id', 'name', 'email'])
                ->toArray();
        }
    }

    public function selectUser($name)
    {
        $this->inviteName = $name;
        $this->searchResults = [];
    }

    public function clearSearch()
    {
        $this->searchResults = [];
    }

    public function updateKanboard(): void
    {
        $this->authorize('update', $this->kanboard);

        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'visibility' => 'required|in:private,public',
        ]);

        $this->kanboard->update([
            'title' => $this->title,
            'description' => $this->description,
            'visibility' => $this->visibility,
        ]);

        session()->flash('message', 'Kanboard berhasil diperbarui!');
    }

    public function inviteUser(): void
    {
        $this->authorize('inviteUsers', $this->kanboard);

        $this->validate([
            'inviteName' => 'required|string|max:255',
            'inviteRole' => 'required|in:member,manager,admin',
        ]);

        $user = \App\Models\User\User::where('name', $this->inviteName)->first();

        if (!$user) {
            $this->addError('inviteName', 'User dengan nama tersebut tidak ditemukan.');
            return;
        }

        if ($this->kanboard->users()->where('user_id', $user->id)->exists()) {
            $this->addError('inviteName', 'User sudah menjadi anggota kanboard ini.');
            return;
        }

        $this->kanboard->kanboardUsers()->create([
            'user_id' => $user->id,
            'role' => $this->inviteRole,
            'invited_by' => auth()->id(),
            'invited_at' => now(),
            'status' => 'active',
            'joined_at' => now(),
        ]);

        $this->reset(['inviteName', 'inviteRole', 'showInviteModal', 'searchResults']);
        $this->inviteRole = 'member';

        session()->flash('message', 'User berhasil diundang ke kanboard!');
    }

    public function removeUser($userId): void
    {
        $this->authorize('removeUsers', $this->kanboard);

        if ($userId == $this->kanboard->owner_id) {
            session()->flash('error', 'Tidak dapat menghapus pemilik kanboard.');
            return;
        }

        $this->kanboard->kanboardUsers()->where('user_id', $userId)->delete();

        session()->flash('message', 'User berhasil dihapus dari kanboard.');
    }

    public function changeUserRole($userId, $newRole): void
    {
        $this->authorize('changeUserRoles', $this->kanboard);

        if ($userId == $this->kanboard->owner_id) {
            session()->flash('error', 'Tidak dapat mengubah role pemilik kanboard.');
            return;
        }

        $this->kanboard->kanboardUsers()
            ->where('user_id', $userId)
            ->update(['role' => $newRole]);

        session()->flash('message', 'Role user berhasil diubah.');
    }

    public function deleteKanboard(): void
    {
        $this->authorize('delete', $this->kanboard);

        if ($this->confirmDeleteText !== $this->kanboard->title) {
            $this->addError('confirmDeleteText', 'Nama kanboard tidak cocok.');
            return;
        }

        $this->kanboard->delete();

        session()->flash('message', 'Kanboard berhasil dihapus.');

        $this->redirect(route('kanboard.index'), navigate: true);
    }

    public function openInviteModal(): void
    {
        $this->showInviteModal = true;
        $this->inviteRole = 'member';
        $this->searchResults = [];
    }

    public function closeInviteModal(): void
    {
        $this->showInviteModal = false;
        $this->reset(['inviteName', 'inviteRole', 'searchResults']);
        $this->inviteRole = 'member';
    }

    public function openDeleteModal(): void
    {
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->confirmDeleteText = '';
    }
};

?>

<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <div class="flex items-center space-x-3 mb-2">
                <a href="{{ route('kanboard.show', $kanboard) }}" wire:navigate class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pengaturan Kanboard</h1>
            </div>
            <p class="text-gray-600 dark:text-gray-400">Kelola pengaturan dan anggota kanboard "{{ $kanboard->title }}"</p>
        </div>
        
        @if (session('message'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('message') }}
        </div>
        @endif
        
        @if (session('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Dasar</h3>
                        
                        <form wire:submit="updateKanboard">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Judul Kanboard <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="title"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                >
                                @error('title') 
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Deskripsi
                                </label>
                                <textarea 
                                    wire:model="description"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                ></textarea>
                                @error('description') 
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Visibilitas
                                </label>
                                <select 
                                    wire:model="visibility"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                >
                                    <option value="private">Privat - Hanya anggota yang diundang</option>
                                    <option value="public">Publik - Semua orang dapat melihat</option>
                                </select>
                                @error('visibility') 
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <button 
                                type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors"
                            >
                                Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>

                @if($canManage)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Anggota ({{ $kanboardUsers->count() + 1 }})</h3>
                            <button 
                                wire:click="openInviteModal"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm transition-colors"
                            >
                                Undang Anggota
                            </button>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                        {{ substr($kanboard->owner->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $kanboard->owner->name }}</p>
                                    </div>
                                </div>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-xs rounded-full">
                                    Owner
                                </span>
                            </div>
                            
                            @foreach($kanboardUsers as $kanboardUser)
                            <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-600 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                        {{ substr($kanboardUser->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $kanboardUser->user->name }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-3">
                                    @if($isOwner || auth()->user()->id !== $kanboardUser->user_id)
                                    <select 
                                        wire:change="changeUserRole({{ $kanboardUser->user_id }}, $event.target.value)"
                                        class="text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-700 dark:text-white"
                                        @if(!$isOwner) disabled @endif
                                    >
                                        <option value="member" {{ $kanboardUser->role === 'member' ? 'selected' : '' }}>Member</option>
                                        <option value="manager" {{ $kanboardUser->role === 'manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="admin" {{ $kanboardUser->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    
                                    <button 
                                        wire:click="removeUser({{ $kanboardUser->user_id }})"
                                        class="text-red-600 hover:text-red-800 text-sm"
                                        @if(!$isOwner) disabled @endif
                                    >
                                        Hapus
                                    </button>
                                    @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200 text-xs rounded-full">
                                        {{ ucfirst($kanboardUser->role) }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                @if($isOwner)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-red-200 dark:border-red-800">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-4">Zona Bahaya</h3>
                        
                        <div class="border border-red-200 dark:border-red-700 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Hapus Kanboard</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Tindakan ini akan menghapus kanboard, semua cards, todos, dan data terkait secara permanen. 
                                Tindakan ini tidak dapat dibatalkan.
                            </p>
                            <button 
                                wire:click="openDeleteModal"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm transition-colors"
                            >
                                Hapus Kanboard
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Kanboard</h3>
                        
                        <dl class="space-y-3 text-sm">
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Board ID</dt>
                                <dd class="text-gray-900 dark:text-white font-mono">{{ $kanboard->board_id }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Dibuat</dt>
                                <dd class="text-gray-900 dark:text-white">{{ $kanboard->created_at->format('d M Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Terakhir diperbarui</dt>
                                <dd class="text-gray-900 dark:text-white">{{ $kanboard->updated_at->format('d M Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Total Cards</dt>
                                <dd class="text-gray-900 dark:text-white">{{ $kanboard->cards()->count() }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Total Anggota</dt>
                                <dd class="text-gray-900 dark:text-white">{{ $kanboardUsers->count() + 1 }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

 @if($showInviteModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="p-6 relative">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Undang Anggota Baru</h3>
                    <button 
                        wire:click="closeInviteModal"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form wire:submit="inviteUser" class="relative">
                    <div class="mb-4 relative" data-search-container>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nama User <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="inviteName"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md 
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Ketik nama atau email user..."
                                autocomplete="off"
                                wire:focus="$set('searchResults', [])"
                            >
                            
                            <!-- Loading indicator -->
                            <div wire:loading wire:target="inviteName" class="absolute right-3 top-2.5">
                                <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>

                            <!-- Dropdown hasil pencarian -->
                            @if(strlen(trim($inviteName)) > 1)
                            <div class="absolute z-[60] bg-white dark:bg-gray-700 border border-gray-300 
                                        dark:border-gray-600 mt-1 w-full rounded-md shadow-lg max-h-60 overflow-y-auto">
                                @if(empty($searchResults))
                                <div class="px-3 py-2 text-gray-500 dark:text-gray-400 text-sm">
                                    Tidak ada user ditemukan
                                </div>
                                @else
                                @foreach($searchResults as $user)
                                <div 
                                    wire:click="selectUser('{{ $user['name'] }}')" 
                                    class="px-3 py-2 hover:bg-blue-100 dark:hover:bg-blue-800 cursor-pointer 
                                           text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-600 last:border-b-0"
                                >
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="font-medium">{{ $user['name'] }}</p>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                                @endforeach
                                @endif
                            </div>
                            @endif
                        </div>

                        @error('inviteName') 
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Role
                        </label>
                        <select 
                            wire:model="inviteRole"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md 
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="member">Member</option>
                            <option value="manager">Manager</option>
                            <option value="admin">Admin</option>
                        </select>
                        @error('inviteRole') 
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button 
                            type="button"
                            wire:click="closeInviteModal"
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 
                                   rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors"
                        >
                            Kirim Undangan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif


    @if($showDeleteModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-red-600 dark:text-red-400">Hapus Kanboard</h3>
                    <button 
                        wire:click="closeDeleteModal"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="mb-4">
                    <p class="text-gray-700 dark:text-gray-300 mb-4">
                        Tindakan ini akan menghapus kanboard <strong>"{{ $kanboard->title }}"</strong> dan semua data terkait secara permanen.
                    </p>
                    <p class="text-red-600 dark:text-red-400 text-sm mb-4">
                        Tindakan ini tidak dapat dibatalkan!
                    </p>
                </div>
                
                <form wire:submit="deleteKanboard">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Ketik <strong>"{{ $kanboard->title }}"</strong> untuk konfirmasi
                        </label>
                        <input 
                            type="text" 
                            wire:model="confirmDeleteText"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Nama kanboard"
                        >
                        @error('confirmDeleteText') 
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button 
                            type="button"
                            wire:click="closeDeleteModal"
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
                        >
                            Hapus Kanboard
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    // Close search dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const searchContainer = event.target.closest('[data-search-container]');
        if (!searchContainer) {
            @this.call('clearSearch');
        }
    });
</script>