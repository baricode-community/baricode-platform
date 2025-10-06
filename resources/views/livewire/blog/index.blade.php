<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Blog;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

new #[Layout('layouts.base')] class extends Component {
    use WithPagination;

    public $search = '';
    public $sortBy = 'latest';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'latest'],
        'page' => ['except' => 1],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function getBlogs()
    {
        $query = Blog::published()
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('content', 'like', '%' . $this->search . '%')
                          ->orWhere('excerpt', 'like', '%' . $this->search . '%');
                });
            });

        switch ($this->sortBy) {
            case 'oldest':
                $query->orderBy('published_at', 'asc');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            default: // latest
                $query->orderBy('published_at', 'desc');
                break;
        }

        return $query->paginate(9);
    }

    public function incrementViews($blogId)
    {
        // Since we removed views_count from database, we'll skip this
        // You can implement analytics tracking here if needed
    }
};

?>

<div>
    {{-- Hero Section --}}
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-16">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-4">Blog Baricode</h1>
                <p class="text-xl md:text-2xl opacity-90 mb-8">
                    Artikel, Tutorial, dan Tips Seputar Programming
                </p>
                
                {{-- Search Bar --}}
                <div class="max-w-2xl mx-auto">
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="search" 
                            placeholder="Cari artikel..."
                            class="w-full px-6 py-4 pr-12 text-gray-900 bg-white rounded-full focus:ring-4 focus:ring-white/25 outline-none text-lg">
                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                            <x-heroicon-o-magnifying-glass class="w-6 h-6 text-gray-400" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 py-12">
        {{-- Filter & Sort Section --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div class="flex items-center gap-2">
                <span class="text-gray-700 dark:text-gray-300 font-medium">
                    @if($search)
                        Hasil pencarian untuk "{{ $search }}"
                    @else
                        Semua Artikel
                    @endif
                </span>
                @if($search)
                    <button wire:click="$set('search', '')" 
                        class="text-indigo-600 hover:text-indigo-800 text-sm">
                        Hapus filter
                    </button>
                @endif
            </div>
            
            <div class="flex items-center gap-2">
                <label class="text-gray-700 dark:text-gray-300 text-sm">Urutkan:</label>
                <select wire:model.live="sortBy" 
                    class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    <option value="latest">Terbaru</option>
                    <option value="oldest">Terlama</option>
                    <option value="title">Judul A-Z</option>
                </select>
            </div>
        </div>

        {{-- Blog Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($this->getBlogs() as $blog)
                <article class="bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden group">
                    {{-- Featured Image --}}
                    @if($blog->featured_image)
                        <div class="aspect-video overflow-hidden">
                            <img src="{{ Storage::url($blog->featured_image) }}" 
                                alt="{{ $blog->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                    @else
                        <div class="aspect-video bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                            <x-heroicon-o-document-text class="w-16 h-16 text-white opacity-50" />
                        </div>
                    @endif

                    {{-- Content --}}
                    <div class="p-6">
                        <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400 mb-3">
                            <span class="flex items-center gap-1">
                                <x-heroicon-o-calendar class="w-4 h-4" />
                                {{ $blog->published_at->format('d M Y') }}
                            </span>
                        </div>

                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            {{ $blog->title }}
                        </h2>

                        <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-3 mb-4">
                            {{ $blog->excerpt }}
                        </p>

                        <a href="{{ route('blog.show', $blog->slug) }}" 
                            wire:click="incrementViews({{ $blog->id }})"
                            class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium group-hover:gap-3 transition-all">
                            Baca Selengkapnya
                            <x-heroicon-o-arrow-right class="w-4 h-4" />
                        </a>
                    </div>
                </article>
            @empty
                {{-- Empty State --}}
                <div class="col-span-full text-center py-16">
                    <x-heroicon-o-document-text class="w-20 h-20 text-gray-400 mx-auto mb-4" />
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        @if($search)
                            Tidak ada artikel yang sesuai
                        @else
                            Belum ada artikel
                        @endif
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        @if($search)
                            Coba gunakan kata kunci yang berbeda
                        @else
                            Artikel akan segera hadir
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($this->getBlogs()->hasPages())
            <div class="mt-12">
                {{ $this->getBlogs()->links() }}
            </div>
        @endif
    </div>

    {{-- Stats Section --}}
    <div class="bg-gray-50 dark:bg-gray-900 py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div>
                    <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mb-2">
                        {{ Blog::published()->count() }}
                    </div>
                    <div class="text-gray-700 dark:text-gray-300">Artikel Published</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mb-2">
                        {{ Blog::whereYear('created_at', date('Y'))->count() }}
                    </div>
                    <div class="text-gray-700 dark:text-gray-300">Artikel Tahun Ini</div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</div>
