<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Course\CourseModuleLesson;
use App\Models\Course\CourseModule;
use App\Models\Course\Course;

new class extends Component {
    use WithPagination;
    
    public $search = '';
    public $moduleFilter = '';
    public $showModal = false;
    public $editingId = null;
    public $title = '';
    public $content = '';
    public $module_id = '';
    public $order = 1;
    
    public function resetForm()
    {
        $this->title = '';
        $this->content = '';
        $this->module_id = '';
        $this->order = 1;
        $this->editingId = null;
    }
    
    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }
    
    public function openEditModal($lessonId)
    {
        $lesson = CourseModuleLesson::findOrFail($lessonId);
        $this->editingId = $lesson->id;
        $this->title = $lesson->title;
        $this->content = $lesson->content;
        $this->module_id = $lesson->module_id;
        $this->order = $lesson->order;
        $this->showModal = true;
    }
    
    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'module_id' => 'required|exists:course_modules,id',
            'order' => 'required|integer|min:1'
        ]);
        
        // Check for unique order per module
        $existingLesson = CourseModuleLesson::where('module_id', $this->module_id)
            ->where('order', $this->order)
            ->when($this->editingId, fn($query) => $query->where('id', '!=', $this->editingId))
            ->first();
            
        if ($existingLesson) {
            $this->addError('order', 'Urutan sudah digunakan untuk modul ini.');
            return;
        }
        
        if ($this->editingId) {
            $lesson = CourseModuleLesson::findOrFail($this->editingId);
            $lesson->update([
                'title' => $this->title,
                'content' => $this->content,
                'module_id' => $this->module_id,
                'order' => $this->order
            ]);
            flash('Pelajaran berhasil diperbarui!', 'success');
        } else {
            CourseModuleLesson::create([
                'title' => $this->title,
                'content' => $this->content,
                'module_id' => $this->module_id,
                'order' => $this->order
            ]);
            flash('Pelajaran berhasil dibuat!', 'success');
        }
        
        $this->resetForm();
        $this->showModal = false;
    }
    
    public function delete($lessonId)
    {
        $lesson = CourseModuleLesson::findOrFail($lessonId);
        $lesson->delete();
        flash('Pelajaran berhasil dihapus!', 'success');
    }
    
    public function moveUp($lessonId)
    {
        $lesson = CourseModuleLesson::findOrFail($lessonId);
        $previousLesson = CourseModuleLesson::where('module_id', $lesson->module_id)
            ->where('order', '<', $lesson->order)
            ->orderBy('order', 'desc')
            ->first();
            
        if ($previousLesson) {
            $tempOrder = $lesson->order;
            $lesson->update(['order' => $previousLesson->order]);
            $previousLesson->update(['order' => $tempOrder]);
            flash('Urutan pelajaran berhasil diubah!', 'success');
        }
    }
    
    public function moveDown($lessonId)
    {
        $lesson = CourseModuleLesson::findOrFail($lessonId);
        $nextLesson = CourseModuleLesson::where('module_id', $lesson->module_id)
            ->where('order', '>', $lesson->order)
            ->orderBy('order', 'asc')
            ->first();
            
        if ($nextLesson) {
            $tempOrder = $lesson->order;
            $lesson->update(['order' => $nextLesson->order]);
            $nextLesson->update(['order' => $tempOrder]);
            flash('Urutan pelajaran berhasil diubah!', 'success');
        }
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedModuleFilter()
    {
        $this->resetPage();
    }
    
    public function with()
    {
        return [
            'lessons' => CourseModuleLesson::query()
                ->with(['courseModule.course.courseCategory'])
                ->when($this->search, fn($query) => $query->where('title', 'like', '%' . $this->search . '%'))
                ->when($this->moduleFilter, fn($query) => $query->where('module_id', $this->moduleFilter))
                ->orderBy('module_id')
                ->orderBy('order')
                ->paginate(10),
            'modules' => CourseModule::with('course')->orderBy('name')->get()
        ];
    }
}; ?>

<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Manajemen Pelajaran</h2>
        <button 
            wire:click="openCreateModal"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Pelajaran
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
                placeholder="Cari pelajaran...">
        </div>
        
        <div>
            <select 
                wire:model.live="moduleFilter"
                class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Modul</option>
                @foreach($modules as $module)
                    <option value="{{ $module->id }}">{{ $module->course->title }} - {{ $module->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Lessons Table -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelajaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modul & Kursus</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urutan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($lessons as $lesson)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $lesson->title }}</div>
                                    @if($lesson->content)
                                        <div class="text-sm text-gray-500">{{ Str::limit(strip_tags($lesson->content), 50) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $lesson->courseModule->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $lesson->courseModule->course->title }}</div>
                                    @if($lesson->courseModule->course->courseCategory)
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full mt-1
                                            {{ $lesson->courseModule->course->courseCategory->level === 'pemula' ? 'bg-green-100 text-green-800' : 
                                               ($lesson->courseModule->course->courseCategory->level === 'menengah' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $lesson->courseModule->course->courseCategory->name }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-1">
                                    <span class="text-sm font-medium text-gray-900">{{ $lesson->order }}</span>
                                    {{-- <div class="flex flex-col gap-1">
                                        <button 
                                            wire:click="moveUp({{ $lesson->id }})"
                                            class="text-gray-400 hover:text-gray-600 transition-colors"
                                            title="Naik">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                            </svg>
                                        </button>
                                        <button 
                                            wire:click="moveDown({{ $lesson->id }})"
                                            class="text-gray-400 hover:text-gray-600 transition-colors"
                                            title="Turun">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                    </div> --}}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                    {{ $lesson->content ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $lesson->content ? 'Lengkap' : 'Kosong' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <button 
                                        wire:click="openEditModal({{ $lesson->id }})"
                                        class="text-blue-600 hover:text-blue-800 transition-colors" 
                                        title="Edit">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button 
                                        wire:click="delete({{ $lesson->id }})"
                                        onclick="return confirm('Yakin ingin menghapus pelajaran ini?')"
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
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Belum ada pelajaran
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $lessons->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showModal', false)"></div>

                <!-- Modal panel -->
                <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:w-full sm:max-w-4xl">
                    <form wire:submit.prevent="save">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                        {{ $editingId ? 'Edit Pelajaran' : 'Tambah Pelajaran' }}
                                    </h3>
                                    
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div class="md:col-span-2">
                                                <label for="title" class="block text-sm font-medium text-gray-700">Judul Pelajaran</label>
                                                <input 
                                                    wire:model="title"
                                                    type="text" 
                                                    id="title"
                                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                                @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            
                                            <div>
                                                <label for="order" class="block text-sm font-medium text-gray-700">Urutan</label>
                                                <input 
                                                    wire:model="order"
                                                    type="number" 
                                                    id="order"
                                                    min="1"
                                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                                @error('order') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label for="module_id" class="block text-sm font-medium text-gray-700">Modul</label>
                                            <select 
                                                wire:model="module_id"
                                                id="module_id"
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Pilih Modul</option>
                                                @foreach($modules as $module)
                                                    <option value="{{ $module->id }}">{{ $module->course->title }} - {{ $module->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('module_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div>
                                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Konten Pelajaran (Markdown)</label>
                                            <div class="relative">
                                                <textarea 
                                                    wire:model="content"
                                                    id="markdown-editor"
                                                    rows="15"
                                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                                                    placeholder="Tulis konten pelajaran dengan markdown...&#10;&#10;# Contoh Heading&#10;&#10;Ini adalah paragraf dengan **teks tebal** dan *teks miring*.&#10;&#10;## Sub Heading&#10;&#10;- Item pertama&#10;- Item kedua&#10;- Item ketiga&#10;&#10;```php&#10;// Contoh kode PHP&#10;echo 'Hello World!';&#10;```"></textarea>
                                            </div>
                                            @error('content') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            
                                            <!-- Markdown Help -->
                                            <div class="mt-2 p-3 bg-gray-50 rounded-md">
                                                <h4 class="text-sm font-medium text-gray-800 mb-2">Panduan Markdown:</h4>
                                                <div class="text-xs text-gray-600 grid grid-cols-2 gap-2">
                                                    <div>
                                                        <strong># Heading 1</strong><br>
                                                        <strong>## Heading 2</strong><br>
                                                        <strong>**teks tebal**</strong><br>
                                                        <strong>*teks miring*</strong>
                                                    </div>
                                                    <div>
                                                        <strong>- Item list</strong><br>
                                                        <strong>1. Item bernomor</strong><br>
                                                        <strong>`kode inline`</strong><br>
                                                        <strong>```bahasa</strong> (untuk blok kode)
                                                    </div>
                                                </div>
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

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
@endpush

@push('scripts')
<script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let easyMDE;
    
    function initializeEditor() {
        const element = document.getElementById('markdown-editor');
        if (element && !easyMDE) {
            easyMDE = new EasyMDE({ 
                element: element,
                spellChecker: false,
                status: false,
                toolbar: [
                    "bold", "italic", "heading", "|",
                    "quote", "unordered-list", "ordered-list", "|",
                    "link", "code", "table", "|",
                    "preview", "side-by-side", "fullscreen", "|",
                    "guide"
                ],
                placeholder: "Tulis konten pelajaran dengan markdown...",
                renderingConfig: {
                    singleLineBreaks: false,
                    codeSyntaxHighlighting: true,
                }
            });
            
            easyMDE.codemirror.on("change", function() {
                @this.set('content', easyMDE.value());
            });
        }
    }
    
    // Initialize when modal opens
    document.addEventListener('livewire:updated', function() {
        setTimeout(initializeEditor, 100);
    });
    
    // Clean up when modal closes
    Livewire.on('modal-closed', function() {
        if (easyMDE) {
            easyMDE.toTextArea();
            easyMDE = null;
        }
    });
});
</script>
@endpush
