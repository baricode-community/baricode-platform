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
            'whatsapp' => 'nullable|unique:users',
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
<div class="">
    <div class="">
        <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">User Management</h2>
                    <button wire:click="openCreateForm" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Tambah User
                    </button>
                </div>

                <!-- Flash Message -->
                @if (session()->has('message'))
                    <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded mb-4">
                        {{ session('message') }}
                    </div>
                @endif

                <!-- Search Bar -->
                <div class="mb-4">
                    <input type="text" 
                           wire:model.live="search"
                           placeholder="Cari berdasarkan nama, email, atau whatsapp..."
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-gray-100">
                </div>

                <!-- Users Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">WhatsApp</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Level</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Roles</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                            @foreach($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-gray-500 dark:bg-gray-700">
                                                    <span class="text-sm font-medium leading-none text-white">{{ $user->initials() }}</span>
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $user->whatsapp ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $user->level === 'pemula' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                               ($user->level === 'menengah' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                            {{ ucfirst($user->level) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        @if($user->roles->count() > 0)
                                            @foreach($user->roles as $role)
                                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded mr-1">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button wire:click="openEditForm({{ $user->id }})" 
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                            Edit
                                        </button>
                                        <button wire:click="confirmDelete({{ $user->id }})" 
                                                wire:confirm.prompt="Apakah Anda yakin?\n\nKetik HAPUS untuk konfirmasi|HAPUS"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
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
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-80 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Tambah User Baru</h3>
                    
                    <form wire:submit="createUser">
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nama</label>
                                <input type="text" wire:model="name" 
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                                @error('name') <span class="text-red-500 dark:text-red-300 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
                                <input type="email" wire:model="email" 
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                                @error('email') <span class="text-red-500 dark:text-red-300 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">WhatsApp</label>
                                <input type="text" wire:model="whatsapp" 
                                       placeholder="contoh: +628123456789"
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                                @error('whatsapp') <span class="text-red-500 dark:text-red-300 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Level</label>
                                <select wire:model="level" 
                                        class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                                    @foreach($levels as $levelOption)
                                        <option value="{{ $levelOption }}">{{ ucfirst($levelOption) }}</option>
                                    @endforeach
                                </select>
                                @error('level') <span class="text-red-500 dark:text-red-300 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">About</label>
                                <textarea wire:model="about" rows="3"
                                          class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100"></textarea>
                                @error('about') <span class="text-red-500 dark:text-red-300 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Password</label>
                                <input type="password" wire:model="password" 
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                                @error('password') <span class="text-red-500 dark:text-red-300 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Konfirmasi Password</label>
                                <input type="password" wire:model="password_confirmation" 
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Roles</label>
                                @foreach($roles as $role)
                                    <div class="flex items-center">
                                        <input type="checkbox" wire:model="selectedRoles" value="{{ $role }}" 
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-700 rounded dark:bg-gray-700 dark:checked:bg-indigo-600">
                                        <label class="ml-2 text-sm text-gray-700 dark:text-gray-200">{{ $role }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeForm"
                                    class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-600">
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
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-80 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Edit User</h3>
                    
                    <form wire:submit="updateUser">
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nama</label>
                                <input type="text" wire:model="name" 
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                                @error('name') <span class="text-red-500 dark:text-red-300 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
                                <input type="email" wire:model="email" 
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                                @error('email') <span class="text-red-500 dark:text-red-300 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">WhatsApp</label>
                                <input type="text" wire:model="whatsapp" 
                                       placeholder="contoh: +628123456789"
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                                @error('whatsapp') <span class="text-red-500 dark:text-red-300 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Level</label>
                                <select wire:model="level" 
                                        class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                                    @foreach($levels as $levelOption)
                                        <option value="{{ $levelOption }}">{{ ucfirst($levelOption) }}</option>
                                    @endforeach
                                </select>
                                @error('level') <span class="text-red-500 dark:text-red-300 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">About</label>
                                <textarea wire:model="about" rows="3"
                                          class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100"></textarea>
                                @error('about') <span class="text-red-500 dark:text-red-300 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                                <input type="password" wire:model="password" 
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                                @error('password') <span class="text-red-500 dark:text-red-300 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Konfirmasi Password Baru</label>
                                <input type="password" wire:model="password_confirmation" 
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Roles</label>
                                @foreach($roles as $role)
                                    <div class="flex items-center">
                                        <input type="checkbox" wire:model="selectedRoles" value="{{ $role }}" 
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-700 rounded dark:bg-gray-700 dark:checked:bg-indigo-600">
                                        <label class="ml-2 text-sm text-gray-700 dark:text-gray-200">{{ $role }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeForm"
                                    class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-600">
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
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-80 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Konfirmasi Hapus</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-300 mb-4">Apakah Anda yakin ingin menghapus user ini? Aksi ini tidak dapat dibatalkan.</p>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="closeDeleteConfirm"
                                class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-600">
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
