<x-layouts.app :title="'Submission Detail'">
    <div class="">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('tasks.submissions') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Riwayat Submission
                </a>
            </div>

            <!-- Submission Header -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden mb-6">
                <div class="p-6 md:p-8">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                                {{ $submission->task->title }}
                            </h1>
                            <p class="text-gray-600 dark:text-gray-300">
                                Submission Detail
                            </p>
                        </div>
                        <span class="ml-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($submission->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                            @elseif($submission->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @elseif($submission->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                            @else bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                            @endif">
                            {{ $submission->status_label }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Submitted At</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $submission->submitted_at->format('d M Y') }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $submission->submitted_at->format('H:i') }}
                            </p>
                        </div>
                        
                        @if($submission->reviewed_at)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Reviewed At</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $submission->reviewed_at->format('d M Y') }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $submission->reviewed_at->format('H:i') }}
                            </p>
                        </div>
                        @endif

                        @if($submission->reviewer)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Reviewed By</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $submission->reviewer->name }}
                            </p>
                        </div>
                        @endif

                        @if($submission->score)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Score</p>
                            <p class="text-2xl font-bold
                                @if($submission->score >= 80) text-green-600 dark:text-green-400
                                @elseif($submission->score >= 60) text-yellow-600 dark:text-yellow-400
                                @else text-red-600 dark:text-red-400
                                @endif">
                                {{ $submission->score }}/100
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Submission Content -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden mb-6">
                <div class="p-6 md:p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">📝 Isi Submission</h2>
                    
                    <div class="prose dark:prose-invert max-w-none bg-gray-50 dark:bg-gray-900 p-6 rounded-lg">
                        {!! nl2br(e($submission->submission_content)) !!}
                    </div>

                    @if($submission->files && count($submission->files) > 0)
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">📎 File Lampiran</h3>
                            <div class="grid gap-2">
                                @foreach($submission->files as $file)
                                    <a href="{{ Storage::url($file) }}" target="_blank" 
                                       class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <svg class="w-5 h-5 mr-2 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ basename($file) }}</span>
                                        <svg class="w-4 h-4 ml-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Review / Feedback -->
            @if($submission->review_notes || $submission->status !== 'pending')
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden mb-6">
                    <div class="p-6 md:p-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            @if($submission->status === 'approved')
                                ✅ Feedback dari Reviewer
                            @elseif($submission->status === 'rejected')
                                ❌ Feedback dari Reviewer
                            @elseif($submission->status === 'revision_requested')
                                🔄 Revisi Diminta
                            @else
                                💬 Feedback
                            @endif
                        </h2>
                        
                        @if($submission->review_notes)
                            <div class="p-6 rounded-lg
                                @if($submission->status === 'approved') bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800
                                @elseif($submission->status === 'rejected') bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800
                                @elseif($submission->status === 'revision_requested') bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800
                                @else bg-gray-50 dark:bg-gray-700
                                @endif">
                                <div class="prose dark:prose-invert max-w-none">
                                    {!! nl2br(e($submission->review_notes)) !!}
                                </div>
                            </div>
                        @else
                            <div class="p-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                <p class="text-yellow-800 dark:text-yellow-300">
                                    @if($submission->status === 'pending')
                                        ⏳ Submission Anda sedang menunggu review dari admin.
                                    @else
                                        Tidak ada catatan review untuk submission ini.
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Task Info -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">
                <div class="p-6 md:p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">📋 Informasi Tugas</h2>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Judul Tugas</p>
                            <p class="text-lg text-gray-900 dark:text-white">{{ $submission->task->title }}</p>
                        </div>
                        
                        @if($submission->task->description)
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi</p>
                            <p class="text-gray-900 dark:text-white">{{ $submission->task->description }}</p>
                        </div>
                        @endif

                        <div>
                            <a href="{{ route('tasks.show', $submission->task->id) }}" 
                               class="inline-flex items-center text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                Lihat Detail Tugas
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
