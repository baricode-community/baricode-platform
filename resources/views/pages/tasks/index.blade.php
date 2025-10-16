<x-layouts.app :title="__('Tugas Saya')">
    <div class="">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-extrabold mb-2 dark:text-slate-200">
                        📋 Tugas yang Didelegasikan
                    </h1>
                    <p class="text-gray-600 dark:text-slate-400">
                        Daftar tugas yang telah didelegasikan kepada Anda. Klik pada tugas untuk melihat detail dan mengerjakan.
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('tasks.submissions') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white text-sm font-semibold rounded-lg shadow transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Riwayat Submission
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative dark:bg-green-900 dark:border-green-700 dark:text-green-100" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative dark:bg-red-900 dark:border-red-700 dark:text-red-100" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Task Cards -->
            @if($assignments->isEmpty())
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden p-12 text-center">
                    <svg class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Belum Ada Tugas</h3>
                    <p class="text-gray-600 dark:text-gray-400">Saat ini belum ada tugas yang didelegasikan kepada Anda.</p>
                </div>
            @else
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($assignments as $assignment)
                        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <div class="p-6">
                                <!-- Status Badge -->
                                <div class="flex items-center justify-between mb-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                        @if($assignment->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($assignment->status === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($assignment->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                                    </span>
                                    @if($assignment->due_date)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            Due: {{ $assignment->due_date->format('d M Y') }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Task Title -->
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                    {{ $assignment->task->title }}
                                </h3>

                                <!-- Task Description -->
                                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">
                                    {{ $assignment->task->description ?? 'Tidak ada deskripsi' }}
                                </p>

                                <!-- Stats -->
                                <div class="flex items-center gap-4 mb-4 text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        <span class="font-semibold">{{ $assignment->submissions()->count() }}</span> submission
                                    </span>
                                    <span class="text-gray-600 dark:text-gray-400">
                                        <span class="font-semibold">{{ $assignment->task->max_submissions_per_user }}</span> max
                                    </span>
                                </div>

                                <!-- Assigned Info -->
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                                    Assigned {{ $assignment->assigned_at->diffForHumans() }}
                                    @if($assignment->notes)
                                        <p class="mt-1 italic">"{{ Str::limit($assignment->notes, 60) }}"</p>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-2">
                                    <a href="{{ route('tasks.show', $assignment->task->id) }}" 
                                       class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $assignments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
