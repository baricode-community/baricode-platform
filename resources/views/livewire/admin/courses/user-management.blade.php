<?php

use App\Models\User\User;
use Livewire\WithPagination;
use Livewire\Volt\Component;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    // Properties for user management
    public $search = '';
    public $editingUser = null;
    public $showCreateForm = false;
    public $showEditForm = false;
    public $showDeleteConfirm = false;
    public $userToDelete = null;

    // Form properties
    public $name = '';
    public $email = '';
    public $whatsapp = '';
    public $about = '';
    public $level = 'pemula';
    public $password = '';
    public $password_confirmation = '';
    public $selectedRoles = [];

    // Available options
    public $levels = ['pemula', 'menengah', 'mahir'];
    public $roles = [];

    protected $queryString = ['search'];

    public function mount()
    {
        $this->roles = Role::all()->pluck('name', 'name')->toArray();
    }

    public function users()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('whatsapp', 'like', '%' . $this->search . '%');
            });
        }

        return $query->with('roles')->orderBy('name')->paginate(10);
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

    public function openEditForm($userId)
    {
        $user = User::findOrFail($userId);
        $this->editingUser = $userId;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->whatsapp = $user->whatsapp ?? '';
        $this->about = $user->about ?? '';
        $this->level = $user->level;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        $this->password = '';
        $this->password_confirmation = '';
        $this->showEditForm = true;
    }

    public function confirmDelete($userId)
    {
        $this->userToDelete = $userId;
        $this->showDeleteConfirm = true;
    }

    public function createUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'whatsapp' => 'nullable|string|max:20|regex:/^\+?[0-9]{7,20}$/|unique:users',
            'about' => 'nullable|string|max:1000',
            'level' => 'required|in:pemula,menengah,mahir',
            'password' => 'required|string|min:8|confirmed',
            'selectedRoles.*' => 'exists:roles,name'
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'whatsapp' => $this->whatsapp ?: null,
            'about' => $this->about ?: null,
            'level' => $this->level,
            'password' => Hash::make($this->password),
        ]);

        if ($this->selectedRoles) {
            $user->assignRole($this->selectedRoles);
        }

        $this->closeForm();

        session()->flash('message', 'User berhasil dibuat!');
    }

    public function updateUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->editingUser,
            'whatsapp' => 'nullable|string|max:20|regex:/^\+?[0-9]{7,20}$/|unique:users,whatsapp,' . $this->editingUser,
            'about' => 'nullable|string|max:1000',
            'level' => 'required|in:pemula,menengah,mahir',
            'password' => 'nullable|string|min:8|confirmed',
            'selectedRoles.*' => 'exists:roles,name'
        ]);

        $user = User::findOrFail($this->editingUser);
        
        $userData = [
            'name' => $this->name,
            'email' => $this->email,
            'whatsapp' => $this->whatsapp ?: null,
            'about' => $this->about ?: null,
            'level' => $this->level,
        ];

        if ($this->password) {
            $userData['password'] = Hash::make($this->password);
        }

        $user->update($userData);

        // Update roles
        $user->syncRoles($this->selectedRoles);

        $this->closeForm();

        session()->flash('message', 'User berhasil diperbarui!');
    }

    public function deleteUser()
    {
        if ($this->userToDelete) {
            $user = User::findOrFail($this->userToDelete);
            $user->delete();

            $this->showDeleteConfirm = false;
            $this->userToDelete = null;

            session()->flash('message', 'User berhasil dihapus!');
        }
    }

    public function closeForm()
    {
        $this->showCreateForm = false;
        $this->showEditForm = false;
        $this->resetForm();
    }

    public function closeDeleteConfirm()
    {
        $this->showDeleteConfirm = false;
        $this->userToDelete = null;
    }

    private function resetForm()
    {
        $this->editingUser = null;
        $this->name = '';
        $this->email = '';
        $this->whatsapp = '';
        $this->about = '';
        $this->level = 'pemula';
        $this->password = '';
        $this->password_confirmation = '';
        $this->selectedRoles = [];
    }

    public function with(): array
    {
        return [
            'users' => $this->users()
        ];
    }
}; ?>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">User Management</h2>
                    <button wire:click="openCreateForm" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Tambah User
                    </button>
                </div>

                <!-- Flash Message -->
                @if (session()->has('message'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('message') }}
                    </div>
                @endif

                <!-- Search Bar -->
                <div class="mb-4">
                    <input type="text" 
                           wire:model.live="search"
                           placeholder="Cari berdasarkan nama, email, atau whatsapp..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Users Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">WhatsApp</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-gray-500">
                                                    <span class="text-sm font-medium leading-none text-white">{{ $user->initials() }}</span>
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->whatsapp ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $user->level === 'pemula' ? 'bg-green-100 text-green-800' : 
                                               ($user->level === 'menengah' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($user->level) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($user->roles->count() > 0)
                                            @foreach($user->roles as $role)
                                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded mr-1">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button wire:click="openEditForm({{ $user->id }})" 
                                                class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            Edit
                                        </button>
                                        <button wire:click="confirmDelete({{ $user->id }})" 
                                                class="text-red-600 hover:text-red-900">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create User Modal -->
    @if($showCreateForm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah User Baru</h3>
                    
                    <form wire:submit="createUser">
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama</label>
                                <input type="text" wire:model="name" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" wire:model="email" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">WhatsApp</label>
                                <input type="text" wire:model="whatsapp" 
                                       placeholder="contoh: +628123456789"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('whatsapp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Level</label>
                                <select wire:model="level" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @foreach($levels as $levelOption)
                                        <option value="{{ $levelOption }}">{{ ucfirst($levelOption) }}</option>
                                    @endforeach
                                </select>
                                @error('level') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">About</label>
                                <textarea wire:model="about" rows="3"
                                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                @error('about') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Password</label>
                                <input type="password" wire:model="password" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                                <input type="password" wire:model="password_confirmation" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Roles</label>
                                @foreach($roles as $role)
                                    <div class="flex items-center">
                                        <input type="checkbox" wire:model="selectedRoles" value="{{ $role }}" 
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label class="ml-2 text-sm text-gray-700">{{ $role }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeForm"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit User Modal -->
    @if($showEditForm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Edit User</h3>
                    
                    <form wire:submit="updateUser">
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama</label>
                                <input type="text" wire:model="name" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" wire:model="email" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">WhatsApp</label>
                                <input type="text" wire:model="whatsapp" 
                                       placeholder="contoh: +628123456789"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('whatsapp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Level</label>
                                <select wire:model="level" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @foreach($levels as $levelOption)
                                        <option value="{{ $levelOption }}">{{ ucfirst($levelOption) }}</option>
                                    @endforeach
                                </select>
                                @error('level') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">About</label>
                                <textarea wire:model="about" rows="3"
                                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                @error('about') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                                <input type="password" wire:model="password" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                                <input type="password" wire:model="password_confirmation" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Roles</label>
                                @foreach($roles as $role)
                                    <div class="flex items-center">
                                        <input type="checkbox" wire:model="selectedRoles" value="{{ $role }}" 
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label class="ml-2 text-sm text-gray-700">{{ $role }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeForm"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteConfirm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Konfirmasi Hapus</h3>
                    <p class="text-sm text-gray-500 mb-4">Apakah Anda yakin ingin menghapus user ini? Aksi ini tidak dapat dibatalkan.</p>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="closeDeleteConfirm"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="button" wire:click="deleteUser"
                                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
