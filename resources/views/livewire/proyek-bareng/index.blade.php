<?php

use Livewire\Volt\Component;
use Livewire\Volt\Attributes\Layout;
use App\Models\Projects\ProyekBareng;
use Livewire\WithPagination;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public $statusFilter = 'all'; // all, active, finished

    public function with(): array
    {
        $query = ProyekBareng::query();

        if ($this->statusFilter === 'active') {
            $query->where('is_finished', false);
        } elseif ($this->statusFilter === 'finished') {
            $query->where('is_finished', true);
        }

        return [
            'proyekBarengs' => $query
                ->with(['kanboards', 'kanboardLinks', 'polls'])
                ->latest()
                ->paginate(12),
            'totalProjects' => ProyekBareng::count(),
            'activeProjects' => ProyekBareng::where('is_finished', false)->count(),
            'finishedProjects' => ProyekBareng::where('is_finished', true)->count(),
            'myProjects' => ProyekBareng::whereHas('users', function ($query) {
                $query->where('user_id', auth()->id());
            })->count(),
            'recentProjects' => ProyekBareng::whereHas('users', function ($query) {
                $query->where('user_id', auth()->id());
            })
                ->where('is_finished', false)
                ->with(['kanboards', 'kanboardLinks', 'polls'])
                ->latest()
                ->take(3)
                ->get(),
        ];
    }

    public function setStatusFilter($status)
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }
};

?>

<div class="">
    <div class="">
        <!-- Penjelasan Alur Proyek Bareng -->
        <div class="mb-8">
            <div
                class="bg-blue-50 dark:bg-blue-900 rounded-lg p-6 shadow flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-2">Alur Proyek Bareng</h2>
                    <p class="mb-4 text-gray-700 dark:text-gray-300 text-sm">
                        Setiap beberapa periode, komunitas akan mengadakan <span
                            class="font-semibold text-blue-700 dark:text-blue-300">Proyek Bareng</span> yang dapat
                        diikuti oleh semua anggota. Proyek ini menjadi kesempatan untuk belajar, berkolaborasi, dan
                        membangun sesuatu bersama-sama.
                    </p>
                    <ol class="list-decimal list-inside text-gray-700 dark:text-gray-300 text-sm space-y-1">
                        <li>
                            <span class="font-semibold">Sebelum Dimulai:</span>
                            <ul class="list-disc list-inside ml-5 space-y-1">
                                <li>Pengumuman ide atau polling untuk menentukan proyek yang akan dijalankan.</li>
                                <li>Pembahasan bersama mengenai fitur-fitur apa saja yang akan dibuat beserta siapa saja
                                    yang ingin ikut terlibat.</li>
                                <li>Pendaftaran anggota yang ingin terlibat dalam proyek setelah mendaftar dan
                                    memasukkan nomor whatsapp di akunnya.</li>
                            </ul>
                        </li>
                        <li>
                            <span class="font-semibold">Saat Proyek Dimulai:</span>
                            <ul class="list-disc list-inside ml-5 space-y-1">
                                <li>Tim terbentuk dan pembagian tugas dilakukan melalui kanban board.</li>
                                <li>Diskusi, koordinasi, dan update progres dilakukan secara rutin.</li>
                                <li>Setiap anggota dapat memantau perkembangan dan berkontribusi sesuai peran.</li>
                            </ul>
                        </li>
                        <li>Proyek berjalan hingga tujuan tercapai atau dinyatakan selesai oleh pengelola.</li>
                        <li>Semua aktivitas tercatat dan dapat dipantau oleh anggota proyek.</li>
                        <li>
                            <span class="font-semibold">Setelah Proyek Selesai:</span>
                            <ul class="list-disc list-inside ml-5 space-y-1">
                                <li>
                                    Proyek dapat dipublikasikan atas kesepakatan bersama, wajib mencantumkan para kontributor, dan isi tidak boleh diubah tanpa persetujuan tim.
                                </li>
                            </ul>
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Proyek</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalProjects }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Proyek Aktif</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $activeProjects }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Proyek Selesai</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $finishedProjects }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 dark:bg-orange-900 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Proyek Saya</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $myProjects }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="mb-8">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8">
                    <button wire:click="setStatusFilter('all')"
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $statusFilter === 'all' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        Semua Proyek
                    </button>
                    <button wire:click="setStatusFilter('active')"
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $statusFilter === 'active' ? 'border-green-500 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        Proyek Aktif ({{ $activeProjects }})
                    </button>
                    <button wire:click="setStatusFilter('finished')"
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $statusFilter === 'finished' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        Proyek Selesai ({{ $finishedProjects }})
                    </button>
                </nav>
            </div>
        </div>

        <!-- Recent Projects -->
        @if ($recentProjects->count() > 0)
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Proyek Terbaru Saya</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($recentProjects as $proyek)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $proyek->title }}
                                    </h3>
                                    <span
                                        class="text-xs px-2 py-1 rounded-full {{ $proyek->is_finished ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                        {{ $proyek->is_finished ? 'Selesai' : 'Aktif' }}
                                    </span>
                                </div>

                                @if ($proyek->description)
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                                        {{ $proyek->description }}
                                    </p>
                                @endif

                                <div class="flex justify-between items-center">
                                    <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                        <span>{{ $proyek->users()->count() }} anggota</span>
                                        <span>{{ $proyek->meets()->count() }} meets</span>
                                    </div>

                                    <a href="{{ route('proyek-bareng.show', $proyek) }}" wire:navigate
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                        Lihat
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- All Projects -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                @if ($statusFilter === 'active')
                    Proyek Aktif
                @elseif($statusFilter === 'finished')
                    Proyek Selesai
                @else
                    Semua Proyek
                @endif
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($proyekBarengs as $proyek)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $proyek->title }}
                                </h3>
                                <div class="flex items-center space-x-2">
                                    <span
                                        class="text-xs px-2 py-1 rounded-full {{ $proyek->is_finished ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                        {{ $proyek->is_finished ? 'Selesai' : 'Aktif' }}
                                    </span>
                                    <span
                                        class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        ID: {{ $proyek->id }}
                                    </span>
                                </div>
                            </div>

                            @if ($proyek->description)
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-3">
                                    {{ $proyek->description }}
                                </p>
                            @endif

                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                            </path>
                                        </svg>
                                        {{ $proyek->users()->count() }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        {{ $proyek->meets()->count() }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                            </path>
                                        </svg>
                                        {{ $proyek->kanboards->count() + $proyek->kanboardLinks->count() }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                            </path>
                                        </svg>
                                        {{ $proyek->polls->count() }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $proyek->created_at->diffForHumans() }}
                                </span>

                                <a href="{{ route('proyek-bareng.show', $proyek) }}" wire:navigate
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Belum ada proyek</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada proyek kolaboratif yang
                            tersedia.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        @if ($proyekBarengs->hasPages())
            <div class="mt-8">
                {{ $proyekBarengs->links() }}
            </div>
        @endif
    </div>
</div>
