<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Course\CourseModule;
use App\Models\Course\Course;

new class extends Component {
    use WithPagination;
    
    public $search = '';
    public $courseFilter = '';
    public $showModal = false;
    public $editingId = null;
    public $name = '';
    public $description = '';
    public $course_id = '';
    public $order = 1;
    
    public function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->course_id = '';
        $this->order = 1;
        $this->editingId = null;
    }
    
    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }
    
    public function openEditModal($moduleId)
    {
        $module = CourseModule::findOrFail($moduleId);
        $this->editingId = $module->id;
        $this->name = $module->name;
        $this->description = $module->description;
        $this->course_id = $module->course_id;
        $this->order = $module->order;
        $this->showModal = true;
    }
    
    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'order' => 'required|integer|min:1'
        ]);
        
        // Check for unique order per course
        $existingModule = CourseModule::where('course_id', $this->course_id)
            ->where('order', $this->order)
            ->when($this->editingId, fn($query) => $query->where('id', '!=', $this->editingId))
            ->first();
            
        if ($existingModule) {
            $this->addError('order', 'Urutan sudah digunakan untuk kursus ini.');
            return;
        }
        
        if ($this->editingId) {
            $module = CourseModule::findOrFail($this->editingId);
            $module->update([
                'name' => $this->name,
                'description' => $this->description,
                'course_id' => $this->course_id,
                'order' => $this->order
            ]);
            flash('Modul berhasil diperbarui!', 'success');
        } else {
            CourseModule::create([
                'name' => $this->name,
                'description' => $this->description,
                'course_id' => $this->course_id,
                'order' => $this->order
            ]);
            flash('Modul berhasil dibuat!', 'success');
        }
        
        $this->resetForm();
        $this->showModal = false;
    }
    
    public function delete($moduleId)
    {
        $module = CourseModule::findOrFail($moduleId);
        $lessonsCount = $module->courseModuleLessons()->count();
        
        if ($lessonsCount > 0) {
            flash("Tidak dapat menghapus modul yang memiliki {$lessonsCount} pelajaran.", 'error');
            return;
        }
        
        $module->delete();
        flash('Modul berhasil dihapus!', 'success');
    }
    
    public function moveUp($moduleId)
    {
        $module = CourseModule::findOrFail($moduleId);
        $previousModule = CourseModule::where('course_id', $module->course_id)
            ->where('order', '<', $module->order)
            ->orderBy('order', 'desc')
            ->first();
            
        if ($previousModule) {
            $tempOrder = $module->order;
            $module->update(['order' => $previousModule->order]);
            $previousModule->update(['order' => $tempOrder]);
            flash('Urutan modul berhasil diubah!', 'success');
        }
    }
    
    public function moveDown($moduleId)
    {
        $module = CourseModule::findOrFail($moduleId);
        $nextModule = CourseModule::where('course_id', $module->course_id)
            ->where('order', '>', $module->order)
            ->orderBy('order', 'asc')
            ->first();
            
        if ($nextModule) {
            $tempOrder = $module->order;
            $module->update(['order' => $nextModule->order]);
            $nextModule->update(['order' => $tempOrder]);
            flash('Urutan modul berhasil diubah!', 'success');
        }
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedCourseFilter()
    {
        $this->resetPage();
    }
    
    public function with()
    {
        return [
            'modules' => CourseModule::query()
                ->with(['course.courseCategory'])
                ->withCount('courseModuleLessons')
                ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
                ->when($this->courseFilter, fn($query) => $query->where('course_id', $this->courseFilter))
                ->orderBy('course_id')
                ->orderBy('order')
                ->paginate(10),
            'courses' => Course::with('courseCategory')->orderBy('title')->get()
        ];
    }
}; ?>
<div class="">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Manajemen Modul</h2>
        <button 
            wire:click="openCreateModal"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Modul
        </button>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input 
                wire:model.live.debounce.300ms="search"
                type="text" 
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-400" 
                placeholder="Cari modul...">
        </div>
        
        <div>
            <select 
                wire:model.live="courseFilter"
                class="block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-gray-100">
                <option value="">Semua Kursus</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Modules Table -->
    <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg overflow-auto">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Modul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kursus</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Urutan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pelajaran</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($modules as $module)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $module->name }}</div>
                                    @if($module->description)
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($module->description, 50) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $module->course->title }}</div>
                                    @if($module->course->courseCategory)
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                            {{ $module->course->courseCategory->level === 'pemula' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                               ($module->course->courseCategory->level === 'menengah' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                            {{ $module->course->courseCategory->name }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-1">
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $module->order }}</span>
                                    <div class="flex flex-col gap-1">
                                        <button 
                                            wire:click="moveUp({{ $module->id }})"
                                            class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors"
                                            title="Naik">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                            </svg>
                                        </button>
                                        <button 
                                            wire:click="moveDown({{ $module->id }})"
                                            class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors"
                                            title="Turun">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $module->course_module_lessons_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a 
                                        href="{{ route('admin.courses.lessons', $module->id) }}"
                                        class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 transition-colors" 
                                        title="Kelola Pelajaran">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </a>
                                    <button 
                                        wire:click="openEditModal({{ $module->id }})"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors" 
                                        title="Edit">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button 
                                        wire:click="delete({{ $module->id }})"
                                        onclick="return confirm('Yakin ingin menghapus modul ini?')"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors" 
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
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900">
                                <div class="flex flex-col items-center">
                                    <svg class="h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                    </svg>
                                    Belum ada modul
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
            {{ $modules->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showModal', false)"></div>

                <!-- Modal panel -->
                <div class="relative bg-white dark:bg-gray-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <form wire:submit.prevent="save">
                        <div class="bg-white dark:bg-gray-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                                        {{ $editingId ? 'Edit Modul' : 'Tambah Modul' }}
                                    </h3>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nama Modul</label>
                                            <input 
                                                wire:model="name"
                                                type="text" 
                                                id="name"
                                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-400">
                                            @error('name') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div>
                                            <label for="course_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Kursus</label>
                                            <select 
                                                wire:model="course_id"
                                                id="course_id"
                                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                                                <option value="">Pilih Kursus</option>
                                                @foreach($courses as $course)
                                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                                @endforeach
                                            </select>
                                            @error('course_id') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div>
                                            <label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Urutan</label>
                                            <input 
                                                wire:model="order"
                                                type="number" 
                                                id="order"
                                                min="1"
                                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                                            @error('order') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div>
                                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Deskripsi</label>
                                            <textarea 
                                                wire:model="description"
                                                id="description"
                                                rows="3"
                                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-400"
                                                placeholder="Deskripsi modul (opsional)"></textarea>
                                            @error('description') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button 
                                type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ $editingId ? 'Perbarui' : 'Simpan' }}
                            </button>
                            <button 
                                type="button"
                                wire:click="$set('showModal', false)"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-900 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
