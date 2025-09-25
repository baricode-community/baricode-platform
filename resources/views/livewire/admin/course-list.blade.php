<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Course\Course;
use App\Models\Course\CourseCategory;

new class extends Component {
    use WithPagination;
    
    public $search = '';
    public $categoryFilter = '';
    public $showModal = false;
    public $editingId = null;
    public $title = '';
    public $description = '';
    public $category_id = '';
    public $slug = '';
    public $is_published = false;
    public $price = 0;
    public $duration_hours = 0;
    
    public function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->category_id = '';
        $this->slug = '';
        $this->is_published = false;
        $this->price = 0;
        $this->duration_hours = 0;
        $this->editingId = null;
    }
    
    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }
    
    public function openEditModal($courseId)
    {
        $course = Course::findOrFail($courseId);
        $this->editingId = $course->id;
        $this->title = $course->title;
        $this->description = $course->description;
        $this->category_id = $course->category_id;
        $this->slug = $course->slug;
        $this->is_published = $course->is_published;
        $this->price = $course->price ?? 0;
        $this->duration_hours = $course->duration_hours ?? 0;
        $this->showModal = true;
    }
    
    public function updatedTitle()
    {
        $this->slug = Str::slug($this->title);
    }
    
    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:course_categories,id',
            'slug' => 'required|string|max:255|unique:courses,slug,' . $this->editingId,
            'is_published' => 'boolean',
            'price' => 'nullable|numeric|min:0',
            'duration_hours' => 'nullable|integer|min:0'
        ]);
        
        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'slug' => $this->slug,
            'is_published' => $this->is_published,
            'price' => $this->price ?: null,
            'duration_hours' => $this->duration_hours ?: null,
        ];
        
        if ($this->editingId) {
            $course = Course::findOrFail($this->editingId);
            $course->update($data);
            flash('Kursus berhasil diperbarui!', 'success');
        } else {
            Course::create($data);
            flash('Kursus berhasil dibuat!', 'success');
        }
        
        $this->resetForm();
        $this->showModal = false;
    }
    
    public function delete($courseId)
    {
        $course = Course::findOrFail($courseId);
        $modulesCount = $course->courseModules()->count();
        $enrollmentsCount = $course->enrollments()->count();
        
        if ($modulesCount > 0 || $enrollmentsCount > 0) {
            flash("Tidak dapat menghapus kursus yang memiliki {$modulesCount} modul atau {$enrollmentsCount} peserta.", 'error');
            return;
        }
        
        $course->delete();
        flash('Kursus berhasil dihapus!', 'success');
    }
    
    public function togglePublished($courseId)
    {
        $course = Course::findOrFail($courseId);
        $course->update(['is_published' => !$course->is_published]);
        
        $status = $course->is_published ? 'dipublikasikan' : 'dibuat draft';
        flash("Kursus berhasil {$status}!", 'success');
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }
    
    public function with()
    {
        return [
            'courses' => Course::query()
                ->with(['courseCategory', 'courseModules'])
                ->withCount(['courseModules', 'enrollments'])
                ->when($this->search, fn($query) => $query->where('title', 'like', '%' . $this->search . '%'))
                ->when($this->categoryFilter, fn($query) => $query->where('category_id', $this->categoryFilter))
                ->orderBy('created_at', 'desc')
                ->paginate(10),
            'categories' => CourseCategory::orderBy('name')->get()
        ];
    }
}; ?>

<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Manajemen Kursus</h2>
        <button 
            wire:click="openCreateModal"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Kursus
        </button>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input 
                wire:model.live.debounce.300ms="search"
                type="text" 
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                placeholder="Cari kursus...">
        </div>
        
        <div>
            <select 
                wire:model.live="categoryFilter"
                class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kursus</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peserta</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($courses as $course)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $course->title }}</div>
                                    @if($course->description)
                                        <div class="text-sm text-gray-500">{{ Str::limit($course->description, 50) }}</div>
                                    @endif
                                    <div class="text-xs text-gray-400">{{ $course->slug }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($course->courseCategory)
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                        {{ $course->courseCategory->level === 'pemula' ? 'bg-green-100 text-green-800' : 
                                           ($course->courseCategory->level === 'menengah' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $course->courseCategory->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button 
                                    wire:click="togglePublished({{ $course->id }})"
                                    class="inline-flex px-2 py-1 text-xs font-medium rounded-full transition-colors
                                        {{ $course->is_published ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                                    {{ $course->is_published ? 'Published' : 'Draft' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $course->course_modules_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $course->enrollments_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a 
                                        href="{{ route('admin.course.modules', $course->id) }}"
                                        class="text-green-600 hover:text-green-800 transition-colors" 
                                        title="Kelola Modul">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                        </svg>
                                    </a>
                                    <button 
                                        wire:click="openEditModal({{ $course->id }})"
                                        class="text-blue-600 hover:text-blue-800 transition-colors" 
                                        title="Edit">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button 
                                        wire:click="delete({{ $course->id }})"
                                        onclick="return confirm('Yakin ingin menghapus kursus ini?')"
                                        class="text-red-600 hover:text-red-800 transition-colors" 
                                        title="Hapus">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    Belum ada kursus
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $courses->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showModal', false)"></div>

                <!-- Modal panel -->
                <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                    <form wire:submit.prevent="save">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                        {{ $editingId ? 'Edit Kursus' : 'Tambah Kursus' }}
                                    </h3>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="md:col-span-2">
                                            <label for="title" class="block text-sm font-medium text-gray-700">Judul Kursus</label>
                                            <input 
                                                wire:model.blur="title"
                                                type="text" 
                                                id="title"
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div>
                                            <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                                            <select 
                                                wire:model="category_id"
                                                id="category_id"
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Pilih Kategori</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('category_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div>
                                            <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                                            <input 
                                                wire:model="slug"
                                                type="text" 
                                                id="slug"
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            @error('slug') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div>
                                            <label for="price" class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
                                            <input 
                                                wire:model="price"
                                                type="number" 
                                                id="price"
                                                min="0"
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            @error('price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div>
                                            <label for="duration_hours" class="block text-sm font-medium text-gray-700">Durasi (Jam)</label>
                                            <input 
                                                wire:model="duration_hours"
                                                type="number" 
                                                id="duration_hours"
                                                min="0"
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            @error('duration_hours') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div class="md:col-span-2">
                                            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                            <textarea 
                                                wire:model="description"
                                                id="description"
                                                rows="3"
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                                placeholder="Deskripsi kursus"></textarea>
                                            @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div class="md:col-span-2">
                                            <div class="flex items-center">
                                                <input 
                                                    wire:model="is_published"
                                                    type="checkbox" 
                                                    id="is_published"
                                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                <label for="is_published" class="ml-2 block text-sm text-gray-900">
                                                    Publikasikan kursus
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button 
                                type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ $editingId ? 'Perbarui' : 'Simpan' }}
                            </button>
                            <button 
                                type="button"
                                wire:click="$set('showModal', false)"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
