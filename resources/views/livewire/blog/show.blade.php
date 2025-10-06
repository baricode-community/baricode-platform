<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Blog;
use Illuminate\Support\Facades\Storage;

new #[Layout('layouts.base')] class extends Component {
    public Blog $blog;
    public $relatedBlogs = [];

    public function mount(Blog $blog)
    {
        // Check if blog is published or handle 404
        if ($blog->status !== 'published' || !$blog->published_at || $blog->published_at->isFuture()) {
            abort(404);
        }

        $this->blog = $blog;
        $this->loadRelatedBlogs();
    }

    public function loadRelatedBlogs()
    {
        $this->relatedBlogs = Blog::published()
            ->where('id', '!=', $this->blog->id)
            ->inRandomOrder()
            ->limit(3)
            ->get();
    }

    public function getNextBlog()
    {
        return Blog::published()
            ->where('published_at', '>', $this->blog->published_at)
            ->orderBy('published_at', 'asc')
            ->first();
    }

    public function getPrevBlog()
    {
        return Blog::published()
            ->where('published_at', '<', $this->blog->published_at)
            ->orderBy('published_at', 'desc')
            ->first();
    }
};

?>

<div>
    {{-- Hero Section --}}
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-16">
        <div class="container mx-auto px-4">
            {{-- Breadcrumb --}}
            <nav class="mb-8">
                <ol class="flex items-center gap-2 text-sm opacity-75">
                    <li><a href="{{ route('home') }}" class="hover:opacity-100">Home</a></li>
                    <li>/</li>
                    <li><a href="{{ route('blog.index') }}" class="hover:opacity-100">Blog</a></li>
                    <li>/</li>
                    <li class="opacity-100">{{ $blog->title }}</li>
                </ol>
            </nav>

            <div class="max-w-4xl">
                <h1 class="text-3xl md:text-5xl font-bold mb-6 leading-tight">
                    {{ $blog->title }}
                </h1>
                
                <div class="flex flex-wrap items-center gap-6 text-lg opacity-90">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-calendar class="w-5 h-5" />
                        <span>{{ $blog->published_at->format('d F Y') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-clock class="w-5 h-5" />
                        <span>{{ $blog->reading_time }} menit baca</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-eye class="w-5 h-5" />
                        <span>{{ number_format(rand(50, 1000)) }} views</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            {{-- Featured Image --}}
            @if($blog->featured_image)
                <div class="mb-8">
                    <img src="{{ Storage::url($blog->featured_image) }}" 
                        alt="{{ $blog->title }}"
                        class="w-full h-64 md:h-96 object-cover rounded-xl shadow-lg">
                </div>
            @endif

            {{-- Content --}}
            <article class="prose prose-lg max-w-none dark:prose-invert prose-headings:text-gray-900 dark:prose-headings:text-white prose-p:text-gray-700 dark:prose-p:text-gray-300 prose-a:text-indigo-600 dark:prose-a:text-indigo-400 prose-strong:text-gray-900 dark:prose-strong:text-white prose-code:text-indigo-600 dark:prose-code:text-indigo-400 prose-pre:bg-gray-100 dark:prose-pre:bg-gray-800">
                {!! nl2br(e($blog->content)) !!}
            </article>

            {{-- Share Buttons --}}
            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Bagikan Artikel</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($blog->title) }}&url={{ urlencode(request()->url()) }}" 
                        target="_blank"
                        class="flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                        Twitter
                    </a>
                    
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                        target="_blank"
                        class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Facebook
                    </a>
                    
                    <a href="https://wa.me/?text={{ urlencode($blog->title . ' - ' . request()->url()) }}" 
                        target="_blank"
                        class="flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.570-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                        </svg>
                        WhatsApp
                    </a>

                    <button onclick="navigator.clipboard.writeText(window.location.href)" 
                        class="flex items-center gap-2 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        <x-heroicon-o-link class="w-4 h-4" />
                        Copy Link
                    </button>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($this->getPrevBlog())
                        <a href="{{ route('blog.show', $this->getPrevBlog()->slug) }}" 
                            class="group p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-indigo-500 dark:hover:border-indigo-400 transition">
                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">← Artikel Sebelumnya</div>
                            <div class="font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                                {{ $this->getPrevBlog()->title }}
                            </div>
                        </a>
                    @endif

                    @if($this->getNextBlog())
                        <a href="{{ route('blog.show', $this->getNextBlog()->slug) }}" 
                            class="group p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-indigo-500 dark:hover:border-indigo-400 transition text-right md:ml-auto">
                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">Artikel Selanjutnya →</div>
                            <div class="font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                                {{ $this->getNextBlog()->title }}
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Related Articles --}}
    @if(count($relatedBlogs) > 0)
        <div class="bg-gray-50 dark:bg-gray-900 py-16">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-12">
                    Artikel Terkait
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    @foreach($relatedBlogs as $relatedBlog)
                        <article class="bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden group">
                            @if($relatedBlog->featured_image)
                                <div class="aspect-video overflow-hidden">
                                    <img src="{{ Storage::url($relatedBlog->featured_image) }}" 
                                        alt="{{ $relatedBlog->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>
                            @else
                                <div class="aspect-video bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                    <x-heroicon-o-document-text class="w-12 h-12 text-white opacity-50" />
                                </div>
                            @endif

                            <div class="p-6">
                                <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400 mb-3">
                                    <span>{{ $relatedBlog->published_at->format('d M Y') }}</span>
                                    <span>{{ $relatedBlog->reading_time }} min</span>
                                </div>

                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    {{ $relatedBlog->title }}
                                </h3>

                                <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-2 mb-4">
                                    {{ $relatedBlog->excerpt }}
                                </p>

                                <a href="{{ route('blog.show', $relatedBlog->slug) }}" 
                                    class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                                    Baca Artikel
                                    <x-heroicon-o-arrow-right class="w-4 h-4" />
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Back to Blog --}}
    <div class="container mx-auto px-4 py-8">
        <div class="text-center">
            <a href="{{ route('blog.index') }}" 
                class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                <x-heroicon-o-arrow-left class="w-5 h-5" />
                Kembali ke Blog
            </a>
        </div>
    </div>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .prose {
            max-width: none;
        }

        .prose pre {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1rem;
            overflow-x: auto;
        }

        .dark .prose pre {
            background-color: #1f2937;
        }
    </style>

    {{-- Copy Link Notification --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.copyLink = function() {
                navigator.clipboard.writeText(window.location.href).then(function() {
                    // You can add a toast notification here
                    alert('Link copied to clipboard!');
                });
            }
        });
    </script>
</div>