<?php

use Livewire\Volt\Component;
use Livewire\Volt\Attributes\Layout;
use App\Models\Blog;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

new #[Layout('layout                {{-- Action Buttons --}}
                <div class="space-y-3">app')] class extends Component {
    use WithFileUploads;

    public $title = '';
    public $slug = '';
    public $content = '';
    public $excerpt = '';
    public $featured_image = null;
    public $status = 'draft';
    public $published_at = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255|unique:blogs,slug',
        'content' => 'required|string',
        'excerpt' => 'nullable|string|max:500',
        'featured_image' => 'nullable|image|max:2048',
        'status' => 'required|in:draft,published,archived',
        'published_at' => 'nullable|date',
    ];

    public function updatedTitle()
    {
        if (empty($this->slug)) {
            $this->slug = \Illuminate\Support\Str::slug($this->title);
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'slug' => $this->slug ?: \Illuminate\Support\Str::slug($this->title),
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'status' => $this->status,
        ];

        // Handle featured image upload
        if ($this->featured_image) {
            $data['featured_image'] = $this->featured_image->store('blog-images', 'public');
        }

        // Set published_at if status is published
        if ($this->status === 'published') {
            $data['published_at'] = $this->published_at ?: now();
        }

        Blog::create($data);

        session()->flash('success', 'Blog berhasil dibuat!');
        return redirect()->route('admin.blog.index');
    }

    public function cancel()
    {
        return redirect()->route('admin.blog.index');
    }
};

?>

<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold flex items-center gap-3 dark:text-white">
            <x-heroicon-o-document-plus class="w-8 h-8 text-indigo-500 dark:text-indigo-400" />
            Tulis Blog Baru
        </h2>
        
        <button wire:click="cancel" class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
            ← Kembali ke Daftar Blog
        </button>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Title --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Judul Blog *
                    </label>
                    <input type="text" wire:model.live="title" 
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:bg-gray-700 dark:text-white dark:border-gray-600"
                        placeholder="Masukkan judul blog...">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Slug --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        URL Slug
                    </label>
                    <input type="text" wire:model="slug" 
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:bg-gray-700 dark:text-white dark:border-gray-600"
                        placeholder="url-slug-otomatis">
                    <p class="text-xs text-gray-500 mt-1">Akan dibuat otomatis jika dikosongkan</p>
                    @error('slug')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Content --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Konten Blog *
                    </label>
                    <textarea wire:model="content" rows="15"
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:bg-gray-700 dark:text-white dark:border-gray-600"
                        placeholder="Tulis konten blog Anda di sini..."></textarea>
                    @error('content')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Excerpt --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Ringkasan
                    </label>
                    <textarea wire:model="excerpt" rows="3"
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:bg-gray-700 dark:text-white dark:border-gray-600"
                        placeholder="Ringkasan singkat blog (akan dibuat otomatis jika dikosongkan)"></textarea>
                    @error('excerpt')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Publish Settings --}}
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="font-semibold text-lg mb-4 dark:text-white">Pengaturan Publikasi</h3>
                    
                    <div class="space-y-4">
                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status
                            </label>
                            <select wire:model="status" 
                                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:bg-gray-700 dark:text-white dark:border-gray-600">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>

                        {{-- Published Date --}}
                        @if($status === 'published')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Tanggal Publikasi
                                </label>
                                <input type="datetime-local" wire:model="published_at"
                                    class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:bg-gray-700 dark:text-white dark:border-gray-600">
                                <p class="text-xs text-gray-500 mt-1">Kosongkan untuk menggunakan waktu saat ini</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Featured Image --}}
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="font-semibold text-lg mb-4 dark:text-white">Gambar Unggulan</h3>
                    
                    <div>
                        <input type="file" wire:model="featured_image" accept="image/*"
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:bg-gray-700 dark:text-white dark:border-gray-600">
                        
                        @if ($featured_image)
                            <div class="mt-3">
                                <img src="{{ $featured_image->temporaryUrl() }}" alt="Preview" 
                                    class="w-full h-32 object-cover rounded-lg">
                            </div>
                        @endif
                        
                        @error('featured_image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Categories --}}
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="font-semibold text-lg mb-4 dark:text-white">Kategori</h3>
                    
                    <div>
                        <input type="text" wire:model="categories" 
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:bg-gray-700 dark:text-white dark:border-gray-600"
                            placeholder="Web Development, Tutorial">
                        <p class="text-xs text-gray-500 mt-1">Pisahkan dengan koma</p>
                    </div>
                </div>

                {{-- Tags --}}
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="font-semibold text-lg mb-4 dark:text-white">Tags</h3>
                    
                    <div>
                        <input type="text" wire:model="tags" 
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:bg-gray-700 dark:text-white dark:border-gray-600"
                            placeholder="laravel, php, coding">
                        <p class="text-xs text-gray-500 mt-1">Pisahkan dengan koma</p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="space-y-3">"
                    <button type="submit"
                        class="w-full px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                        <x-heroicon-o-check class="w-5 h-5 inline mr-2" />
                        {{ $status === 'published' ? 'Publish Blog' : 'Simpan Draft' }}
                    </button>
                    
                    <button type="button" wire:click="cancel"
                        class="w-full px-4 py-3 bg-gray-300 dark:bg-gray-600 dark:text-white rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition">
                        <x-heroicon-o-x-mark class="w-5 h-5 inline mr-2" />
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </form>

    <style>
        /* Loading state for file uploads */
        .wire-loading.wire-target.featured_image {
            opacity: 0.6;
        }
    </style>
</div>