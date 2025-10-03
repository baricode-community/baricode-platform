<?php

use App\Models\Course\CourseCategory;
use App\Models\Course\Course;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

new class extends Component {
    use WithFileUploads;

    public $courseCategory;
    public $courses;
    public $showEditModal = false;
    public $showCreateModal = false;
    public $editingCourse = null;
    
    // Form fields
    public $title = '';
    public $description = '';
    public $thumbnail = null;
    public $is_published = false;

    public function mount(CourseCategory $courseCategory)
    {
        $this->courseCategory = $courseCategory;
        $this->courses = $courseCategory->courses;
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function createCourse()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'is_published' => 'boolean'
        ]);

        $slug = Str::slug($this->title);
        $originalSlug = $slug;
        $counter = 1;

        // Ensure unique slug
        while (Course::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $thumbnailPath = null;
        if ($this->thumbnail) {
            $thumbnailPath = $this->thumbnail->store('courses/thumbnails', 'public');
        }

        Course::create([
            'title' => $this->title,
            'slug' => $slug,
            'description' => $this->description,
            'thumbnail' => $thumbnailPath,
            'is_published' => $this->is_published,
            'course_category_id' => $this->courseCategory->id
        ]);

        $this->showCreateModal = false;
        
        flash('Kursus berhasil dibuat!', 'success');
        
        // Refresh the courses data
        $this->courses = $this->courseCategory->courses()->get();
        $this->resetForm();
    }

    public function openEditModal($courseId)
    {
        $this->editingCourse = $this->courses->find($courseId);
        
        if ($this->editingCourse) {
            $this->title = $this->editingCourse->title;
            $this->description = $this->editingCourse->description;
            $this->is_published = $this->editingCourse->is_published;
            $this->thumbnail = null; // Reset thumbnail untuk upload baru
            $this->showEditModal = true;
        }
    }

    public function updateCourse()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'is_published' => 'boolean'
        ]);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'is_published' => $this->is_published
        ];

        // Generate new slug if title changed
        if ($this->title !== $this->editingCourse->title) {
            $slug = Str::slug($this->title);
            $originalSlug = $slug;
            $counter = 1;

            while (Course::where('slug', $slug)->where('id', '!=', $this->editingCourse->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $data['slug'] = $slug;
        }

        // Handle thumbnail upload
        if ($this->thumbnail) {
            // Delete old thumbnail if exists
            if ($this->editingCourse->thumbnail) {
                Storage::disk('public')->delete($this->editingCourse->thumbnail);
            }
            $data['thumbnail'] = $this->thumbnail->store('courses/thumbnails', 'public');
        }

        $this->editingCourse->update($data);

        $this->showEditModal = false;
        
        flash('Kursus berhasil diperbarui!', 'success');
        
        // Refresh the courses data
        $this->courses = $this->courseCategory->courses()->get();
        $this->resetForm();
    }

    public function deleteCourse($courseId)
    {
        $course = $this->courses->find($courseId);
        
        if ($course) {
            $courseTitle = $course->title;
            
            // Delete thumbnail if exists
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            
            $course->delete();
            
            flash("Kursus '{$courseTitle}' berhasil dihapus!", 'success');
            
            // Refresh the courses data
            $this->courses = $this->courseCategory->courses()->get();
        }
    }

    public function cancelCreate()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function cancelEdit()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->thumbnail = null;
        $this->is_published = false;
        $this->editingCourse = null;
        $this->resetValidation();
    }
};

?>

<div>
    <div class="container mx-auto p-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold mb-2 text-gray-800 dark:text-white">
                    Kursus dalam Kategori: {{ $courseCategory->name }}
                </h1>
                <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-300">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        {{ $courseCategory->level === 'pemula' ? 'bg-green-100 text-green-800' : 
                           ($courseCategory->level === 'menengah' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($courseCategory->level) }}
                    </span>
                    <span>{{ $courses->count() }} kursus</span>
                </div>
                @if($courseCategory->description)
                    <p class="mt-2 text-gray-600 dark:text-gray-300">{{ $courseCategory->description }}</p>
                @endif
            </div>

            <button 
                wire:click="openCreateModal"
                class="mt-4 sm:mt-0 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Kursus
            </button>
        </div>

        @if($courses->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada kursus</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mulai dengan membuat kursus pertama Anda.</p>
                <div class="mt-6">
                    <button 
                        wire:click="openCreateModal"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Kursus
                    </button>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($courses as $course)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        @if($course->thumbnail)
                            <img src="{{ Storage::url($course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">{{ $course->title }}</h2>
                            <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed mb-4">
                                {{ Str::limit($course->description, 100) }}
                            </p>
                            
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    Dibuat {{ $course->created_at->diffForHumans() }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $course->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $course->is_published ? 'Dipublikasi' : 'Draft' }}
                                </span>
                            </div>

                            <div class="flex space-x-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button 
                                    wire:click="openEditModal({{ $course->id }})"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg flex items-center justify-center text-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </button>
                                
                                <button 
                                    wire:click="deleteCourse({{ $course->id }})"
                                    wire:confirm="Apakah Anda yakin ingin menghapus kursus '{{ $course->title }}'? Tindakan ini tidak dapat dibatalkan."
                                    class="flex-1 bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg flex items-center justify-center text-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cancelCreate"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block z-50 relative align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="createCourse">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                                        Tambah Kursus Baru
                                    </h3>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label for="create_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Judul Kursus <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                wire:model="title"
                                                type="text" 
                                                id="create_title"
                                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                                                placeholder="Masukkan judul kursus">
                                            @error('title') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div>
                                            <label for="create_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Deskripsi
                                            </label>
                                            <textarea 
                                                wire:model="description"
                                                id="create_description"
                                                rows="3"
                                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                                                placeholder="Masukkan deskripsi kursus (opsional)"></textarea>
                                            @error('description') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label for="create_thumbnail" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Thumbnail
                                            </label>
                                            <input 
                                                wire:model="thumbnail"
                                                type="file"
                                                accept="image/*"
                                                id="create_thumbnail"
                                                class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                                                    file:mr-4 file:py-2 file:px-4
                                                    file:rounded-md file:border-0
                                                    file:text-sm file:font-semibold
                                                    file:bg-blue-50 file:text-blue-700
                                                    hover:file:bg-blue-100
                                                    dark:file:bg-gray-700 dark:file:text-gray-300">
                                            @error('thumbnail') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                            @if ($thumbnail)
                                                <div class="mt-2">
                                                    <img src="{{ $thumbnail->temporaryUrl() }}" alt="Preview" class="h-32 rounded-md">
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div>
                                            <label class="flex items-center">
                                                <input 
                                                    wire:model="is_published"
                                                    type="checkbox"
                                                    class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700">
                                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Publikasikan kursus</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button 
                                type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Simpan
                            </button>
                            <button 
                                type="button"
                                wire:click="cancelCreate"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cancelEdit"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block z-50 relative align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="updateCourse">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                                        Edit Kursus
                                    </h3>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label for="edit_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Judul Kursus <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                wire:model="title"
                                                type="text" 
                                                id="edit_title"
                                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                                                placeholder="Masukkan judul kursus">
                                            @error('title') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div>
                                            <label for="edit_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Deskripsi
                                            </label>
                                            <textarea 
                                                wire:model="description"
                                                id="edit_description"
                                                rows="3"
                                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                                                placeholder="Masukkan deskripsi kursus (opsional)"></textarea>
                                            @error('description') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label for="edit_thumbnail" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Thumbnail Baru (opsional)
                                            </label>
                                            @if($editingCourse && $editingCourse->thumbnail && !$thumbnail)
                                                <div class="mt-2 mb-2">
                                                    <img src="{{ Storage::url($editingCourse->thumbnail) }}" alt="Current thumbnail" class="h-32 rounded-md">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Thumbnail saat ini</p>
                                                </div>
                                            @endif
                                            <input 
                                                wire:model="thumbnail"
                                                type="file"
                                                accept="image/*"
                                                id="edit_thumbnail"
                                                class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                                                    file:mr-4 file:py-2 file:px-4
                                                    file:rounded-md file:border-0
                                                    file:text-sm file:font-semibold
                                                    file:bg-blue-50 file:text-blue-700
                                                    hover:file:bg-blue-100
                                                    dark:file:bg-gray-700 dark:file:text-gray-300">
                                            @error('thumbnail') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                            @if ($thumbnail)
                                                <div class="mt-2">
                                                    <img src="{{ $thumbnail->temporaryUrl() }}" alt="Preview" class="h-32 rounded-md">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Preview thumbnail baru</p>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div>
                                            <label class="flex items-center">
                                                <input 
                                                    wire:model="is_published"
                                                    type="checkbox"
                                                    class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700">
                                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Publikasikan kursus</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button 
                                type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Update
                            </button>
                            <button 
                                type="button"
                                wire:click="cancelEdit"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>