<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Course\CourseModuleLesson;
use App\Models\Course\CourseModule;

new class extends Component {
    use WithPagination;
    
    public $moduleId;
    public $module;
    public $search = '';
    public $showModal = false;
    public $editingId = null;
    public $title = '';
    public $content = '';
    public $order = 1;
    
    public function mount($moduleId)
    {
        $this->moduleId = $moduleId;
        $this->module = CourseModule::with(['course'])->findOrFail($moduleId);
    }
    
    public function resetForm()
    {
        $this->title = '';
        $this->content = '';
        $this->order = 1;
        $this->editingId = null;
    }
    
    public function openCreateModal()
    {
        $this->resetForm();
        // Get next order number
        $maxOrder = CourseModuleLesson::where('module_id', $this->moduleId)->max('order');
        $this->order = $maxOrder ? $maxOrder + 1 : 1;
        $this->showModal = true;
    }
    
    public function openEditModal($lessonId)
    {
        $lesson = CourseModuleLesson::findOrFail($lessonId);
        $this->editingId = $lesson->id;
        $this->title = $lesson->title;
        $this->content = $lesson->content;
        $this->order = $lesson->order;
        $this->showModal = true;
    }
    
    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'order' => 'required|integer|min:1'
        ]);
        
        // Check for unique order per module
        $existingLesson = CourseModuleLesson::where('module_id', $this->moduleId)
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
                'module_id' => $this->moduleId,
                'order' => $this->order
            ]);
            flash('Pelajaran berhasil diperbarui!', 'success');
        } else {
            CourseModuleLesson::create([
                'title' => $this->title,
                'content' => $this->content,
                'module_id' => $this->moduleId,
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
    
    public function with()
    {
        return [
            'lessons' => CourseModuleLesson::query()
                ->where('module_id', $this->moduleId)
                ->when($this->search, fn($query) => $query->where('title', 'like', '%' . $this->search . '%'))
                ->orderBy('order')
                ->paginate(10)
        ];
    }
}; ?>

<div>
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-semibold text-gray-900">Daftar Pelajaran</h3>
        <button 
            wire:click="openCreateModal"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Pelajaran
        </button>
    </div>

    <!-- Search -->
    <div class="mb-6">
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
    </div>

    <!-- Lessons List -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        @if($lessons->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($lessons as $lesson)
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-start gap-4">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center justify-center w-8 h-8 bg-purple-100 text-purple-800 text-sm font-medium rounded-full">
                                            {{ $lesson->order }}
                                        </span>
                                        <div class="flex flex-col gap-1">
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
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <h4 class="text-lg font-medium text-gray-900">{{ $lesson->title }}</h4>
                                                @if($lesson->content)
                                                    <p class="text-gray-600 mt-1">{{ Str::limit(strip_tags($lesson->content), 100) }}</p>
                                                @else
                                                    <p class="text-gray-400 mt-1 italic">Belum ada konten</p>
                                                @endif
                                                <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                                        {{ $lesson->content ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                        {{ $lesson->content ? 'Lengkap' : 'Kosong' }}
                                                    </span>
                                                    <span>Dibuat: {{ $lesson->created_at->format('d M Y') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 ml-4">
                                <button 
                                    wire:click="openEditModal({{ $lesson->id }})"
                                    class="text-blue-600 hover:text-blue-800 transition-colors p-2 rounded-md hover:bg-blue-50" 
                                    title="Edit">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button 
                                    wire:click="delete({{ $lesson->id }})"
                                    onclick="return confirm('Yakin ingin menghapus pelajaran ini?')"
                                    class="text-red-600 hover:text-red-800 transition-colors p-2 rounded-md hover:bg-red-50" 
                                    title="Hapus">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $lessons->links() }}
            </div>
        @else
            <div class="p-8 text-center text-gray-500">
                <div class="flex flex-col items-center">
                    <svg class="h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Belum ada pelajaran untuk modul ini
                </div>
            </div>
        @endif
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
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
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
