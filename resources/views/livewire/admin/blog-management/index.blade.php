<?php

use Livewire\Volt\Component;
use Livewire\Volt\Attributes\Layout;
use App\Models\Blog;
use Livewire\WithPagination;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $confirmingDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function getBlogs()
    {
        return Blog::when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('content', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function confirmDelete($id): void
    {
        $this->confirmingDelete = $id;
    }

    public function delete($id): void
    {
        Blog::find($id)?->delete();
        $this->confirmingDelete = null;
        $this->dispatch('blog-deleted');
    }

    public function toggleStatus($id)
    {
        $blog = Blog::find($id);
        if ($blog) {
            $blog->status = $blog->status === 'published' ? 'draft' : 'published';
            if ($blog->status === 'published' && !$blog->published_at) {
                $blog->published_at = now();
            }
            $blog->save();
        }
    }
};

?>

<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold flex items-center gap-3 dark:text-white">
            <x-heroicon-o-document-text class="w-8 h-8 text-indigo-500 dark:text-indigo-400" />
            Manajemen Blog
        </h2>
        
        <a href="{{ route('admin.blog.create') }}"
            class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg shadow hover:bg-indigo-700 transition">
            + Tulis Blog Baru
        </a>
    </div>

    {{-- Filter dan Search --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari blog..."
                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:bg-gray-700 dark:text-white dark:border-gray-600">
        </div>
        <div>
            <select wire:model.live="statusFilter"
                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:bg-gray-700 dark:text-white dark:border-gray-600">
                <option value="">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
            </select>
        </div>
    </div>

    {{-- Daftar Blog --}}
    <div class="space-y-4">
        @forelse ($this->getBlogs() as $blog)
            <div class="p-5 rounded-xl shadow-md bg-white dark:bg-gray-800 hover:shadow-lg transition">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="font-semibold text-xl dark:text-white">{{ $blog->title }}</h3>
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $blog->status === 'published' ? 'bg-green-100 text-green-800' : 
                                   ($blog->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($blog->status) }}
                            </span>
                        </div>
                        
                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-3 line-clamp-2">
                            {{ $blog->excerpt }}
                        </p>
                        
                        <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                            <span>{{ $blog->created_at->format('d M Y') }}</span>
                            @if($blog->published_at)
                                <span>Published: {{ $blog->published_at->format('d M Y') }}</span>
                            @endif
                            <span>{{ $blog->reading_time }} min read</span>
                        </div>
                    </div>
                    
                    <div class="flex gap-2 ml-4">
                        <button wire:click="toggleStatus({{ $blog->id }})"
                            class="px-3 py-1.5 {{ $blog->status === 'published' ? 'bg-orange-500 hover:bg-orange-600' : 'bg-green-500 hover:bg-green-600' }} text-white rounded-lg transition">
                            {{ $blog->status === 'published' ? 'Unpublish' : 'Publish' }}
                        </button>
                        
                        <a href="{{ route('admin.blog.edit', $blog) }}"
                            class="px-3 py-1.5 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                            Edit
                        </a>
                        
                        <button wire:click="confirmDelete({{ $blog->id }})"
                            class="px-3 py-1.5 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-10">
                <x-heroicon-o-document-text class="w-16 h-16 text-gray-400 mx-auto mb-4" />
                <p class="text-gray-500 dark:text-gray-400 text-lg">
                    @if($search || $statusFilter)
                        Tidak ada blog yang sesuai dengan filter.
                    @else
                        Belum ada blog yang dibuat.
                    @endif
                </p>
                @if(!$search && !$statusFilter)
                    <a href="{{ route('admin.blog.create') }}"
                        class="inline-block mt-4 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        Tulis Blog Pertama
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($this->getBlogs()->hasPages())
        <div class="mt-6">
            {{ $this->getBlogs()->links() }}
        </div>
    @endif

    {{-- Modal Konfirmasi Hapus --}}
    @if ($confirmingDelete !== null)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg w-full max-w-sm animate-fadeIn">
                <h4 class="text-lg font-bold mb-2 dark:text-white">Konfirmasi Hapus</h4>
                <p class="mb-5 text-gray-700 dark:text-gray-300">Apakah Anda yakin ingin menghapus blog ini? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="flex gap-2 justify-end">
                    <button wire:click="delete({{ $confirmingDelete }})"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Ya, Hapus
                    </button>
                    <button wire:click="$set('confirmingDelete', null)"
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-600 dark:text-white rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    @endif

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</div>
