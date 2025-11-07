<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Blog;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed; // Digunakan untuk Computed Property

// Class Definition (Livewire Volt Component)
new #[Layout('layouts.base')] class extends Component {
    use WithPagination;

    // --- State Properties ---
    public $search = '';
    public $sortBy = 'latest';

    // Query string configuration is fine, but we'll use a protected property
    // for configuration clarity.

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'latest'],
        'page' => ['except' => 1],
    ];

    // --- Pagination Reset Hooks (Method naming is correct) ---

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    // --- Computed Property for Blogs ---
    // Menggunakan #[Computed] lebih Livewire-friendly daripada method getBlogs().
    // Data hanya akan di-query ulang jika dependensinya ($search, $sortBy, $page) berubah.

    #[Computed]
    public function blogs()
    {
        $query = Blog::published()
            ->when($this->search, function ($q) {
                // Menggabungkan semua pencarian dengan where() dan orWhere() di dalam satu closure
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
            default: // latest (default)
                $query->orderBy('published_at', 'desc');
                break;
        }

        return $query->paginate(9);
    }

    // --- Utility Computed Property (Optional, for Stats) ---

    #[Computed]
    public function totalPublishedBlogs()
    {
        // Langsung hitung tanpa filter pencarian
        return Blog::published()->count();
    }

    // --- Action Method (Optional, keeping it clean) ---
    // IncrementViews dihilangkan karena Anda menyebutkan views_count dihapus.
    // Jika nanti diperlukan, Anda bisa menambahkannya kembali.
};

?>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 pt-16 sm:pt-20">
    {{-- Hero Section --}}
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-16 dark:from-indigo-900 dark:to-purple-900">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-extrabold mb-4 tracking-tight">
                    Blog Baricode
                </h1>
                <p class="text-xl md:text-2xl opacity-90 mb-8 max-w-3xl mx-auto">
                    Artikel, Tutorial, dan Tips Seputar Programming
                </p>

                {{-- Search Bar --}}
                <div class="max-w-2xl mx-auto">
                    <div class="relative shadow-xl rounded-full">
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Cari artikel berdasarkan judul atau isi..."
                            class="w-full px-6 py-4 pl-14 text-gray-900 bg-white border-0 rounded-full focus:ring-4 focus:ring-white/50 outline-none text-lg dark:bg-gray-800 dark:text-white dark:focus:ring-indigo-700/50 transition-colors">
                        <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                            {{-- Mengganti x-heroicon dengan SVG standar karena x-heroicon tidak terdefinisi di sini --}}
                            <svg class="w-6 h-6 text-gray-400 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
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
            <div class="flex items-center gap-4 flex-wrap">
                <span class="text-gray-700 dark:text-gray-300 font-medium text-lg">
                    @if($search)
                        Menampilkan hasil pencarian untuk: <span class="font-bold text-indigo-600 dark:text-indigo-400">"{{ $search }}"</span>
                    @else
                        Semua Artikel
                    @endif
                </span>
                @if($search)
                    {{-- FIX: Perbaikan tombol agar teks "Hapus filter" berada di dalam button --}}
                    <button wire:click="$set('search', '')"
                        class="text-red-600 hover:text-red-800 text-sm font-semibold dark:text-red-400 dark:hover:text-red-300 flex items-center gap-1 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Hapus Filter
                    </button>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <label class="text-gray-700 dark:text-gray-300 text-sm font-medium">Urutkan:</label>
                <select wire:model.live="sortBy"
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none dark:bg-gray-700 dark:text-white dark:border-gray-600 transition-shadow">
                    <option value="latest">Terbaru</option>
                    <option value="oldest">Terlama</option>
                    <option value="title">Judul A-Z</option>
                </select>
            </div>
        </div>

        {{-- Blog Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {{-- Menggunakan $this->blogs daripada getBlogs() --}}
            @forelse ($this->blogs as $blog)
                <article class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden group border border-gray-200 dark:border-gray-700">
                    {{-- Featured Image --}}
                    <a href="{{ route('blog.show', $blog->slug) }}">
                        @if($blog->featured_image)
                            <div class="aspect-video overflow-hidden">
                                <img src="{{ Storage::url($blog->featured_image) }}"
                                    alt="{{ $blog->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                        @else
                            {{-- FIX: Penempatan SVG di dalam div agar tidak ada penutup div ganda --}}
                            <div class="aspect-video bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center dark:from-indigo-900 dark:to-purple-900">
                                <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        @endif
                    </a>

                    {{-- Content --}}
                    <div class="p-6">
                        <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400 mb-3">
                            <span class="flex items-center gap-1">
                                {{-- Mengganti x-heroicon dengan SVG standar --}}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h.01M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $blog->published_at->format('d F Y') }}
                            </span>
                        </div>

                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            <a href="{{ route('blog.show', $blog->slug) }}" class="hover:underline">
                                {{ $blog->title }}
                            </a>
                        </h2>

                        <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-3 mb-4">
                            {{ $blog->excerpt }}
                        </p>

                        <a href="{{ route('blog.show', $blog->slug) }}"
                            class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold group-hover:gap-3 transition-all">
                            Baca Selengkapnya
                            {{-- Mengganti x-heroicon dengan SVG standar --}}
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>
                </article>
            @empty
                {{-- Empty State --}}
                <div class="col-span-full text-center py-16 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                    {{-- Mengganti x-heroicon dengan SVG standar --}}
                    <svg class="w-20 h-20 text-gray-400 dark:text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        @if($search)
                            Tidak ada artikel yang sesuai
                        @else
                            Belum ada artikel yang dipublikasikan
                        @endif
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        @if($search)
                            Coba gunakan kata kunci yang berbeda, atau hapus filter pencarian.
                        @else
                            Artikel akan segera hadir. Tunggu kabar terbaru dari Baricode!
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        {{-- Menggunakan $this->blogs alih-alih memanggil method lagi --}}
        @if($this->blogs->hasPages())
            <div class="mt-12">
                {{ $this->blogs->links() }}
            </div>
        @endif
    </div>

    {{-- Stats Section --}}
    <div class="bg-white dark:bg-gray-800 py-12 border-t border-gray-100 dark:border-gray-700">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                    <div class="text-4xl font-extrabold text-indigo-600 dark:text-indigo-400 mb-2">
                        {{-- Menggunakan computed property --}}
                        {{ $this->totalPublishedBlogs }}
                    </div>
                    <div class="text-gray-700 dark:text-gray-300 font-medium">Artikel Published</div>
                </div>
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                    <div class="text-4xl font-extrabold text-indigo-600 dark:text-indigo-400 mb-2">
                        {{ Blog::whereYear('created_at', date('Y'))->count() }}
                    </div>
                    <div class="text-gray-700 dark:text-gray-300 font-medium">Artikel Tahun Ini</div>
                </div>
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                    <div class="text-4xl font-extrabold text-indigo-600 dark:text-indigo-400 mb-2">
                        {{ $this->totalPublishedBlogs > 0 ? number_format(Blog::published()->pluck('content')->map(fn($c) => str_word_count(strip_tags($c)))->avg(), 0) : 0 }}
                    </div>
                    <div class="text-gray-700 dark:text-gray-300 font-medium">Rata-rata Kata (Estimasi)</div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Styles untuk line-clamp, perlu ditambahkan agar berfungsi */
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

        /* Styling for Livewire Pagination links (Tambahkan agar tampilan pagination konsisten) */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }
        .pagination nav {
            display: flex;
            gap: 0.5rem;
        }
        .pagination nav > div:first-child {
            /* Hide previous/next text on small screens if Tailwind doesn't handle it */
        }
        .pagination span, .pagination a {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s;
        }
        .pagination span.bg-indigo-500 {
            background-color: #6366f1;
            color: white;
        }
        .pagination a {
            color: #4f46e5;
            background-color: #eef2ff;
        }
        .pagination a:hover {
            background-color: #e0e7ff;
        }
        .dark .pagination a {
            color: #818cf8;
            background-color: #1f2937;
            border: 1px solid #374151;
        }
        .dark .pagination span.bg-indigo-500 {
            background-color: #4f46e5;
            color: white;
        }
        .dark .pagination a:hover {
            background-color: #374151;
        }

        /* Memastikan warna teks di dark mode bekerja dengan baik */
        @media (prefers-color-scheme: dark) {
            .dark\:bg-gray-500 { background-color: #6b7280; }
            .dark\:bg-gray-900 { background-color: #111827; }
            .dark\:bg-gray-800 { background-color: #1f2937; }
            .dark\:text-white { color: #ffffff; }
            .dark\:text-gray-300 { color: #d1d5db; }
            .dark\:text-gray-400 { color: #9ca3af; }
        }
    </style>
</div>