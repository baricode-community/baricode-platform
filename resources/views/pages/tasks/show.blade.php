<x-layouts.app :title="$task->title">
    <div class="">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('tasks.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Daftar Tugas
                </a>
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

            <!-- Task Header -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden mb-6">
                <div class="p-6 md:p-8">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                                {{ $task->title }}
                            </h1>
                            <p class="text-gray-600 dark:text-gray-300 text-lg">
                                {{ $task->description }}
                            </p>
                        </div>
                        <span class="ml-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($assignment->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                            @elseif($assignment->status === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                            @elseif($assignment->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Assigned Date</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $assignment->assigned_at->format('d M Y') }}
                            </p>
                        </div>
                        @if($assignment->due_date)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Deadline</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $assignment->due_date->format('d M Y') }}
                            </p>
                        </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Submissions</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $submissionsCount }} / {{ $task->max_submissions_per_user }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Can Submit</p>
                            <p class="text-lg font-semibold">
                                @if($canSubmit)
                                    <span class="text-green-600 dark:text-green-400">✓ Yes</span>
                                @else
                                    <span class="text-red-600 dark:text-red-400">✗ No</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($assignment->notes)
                        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <p class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-1">Catatan dari Admin:</p>
                            <p class="text-blue-800 dark:text-blue-300">{{ $assignment->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Task Content -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden mb-6">
                <div class="p-6 md:p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Detail Tugas</h2>
                    
                    @if($task->content)
                        <div class="prose dark:prose-invert max-w-none mb-6">
                            {!! $task->content !!}
                        </div>
                    @endif

                    @if($task->instructions)
                        <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-200 mb-2">📝 Instruksi Pengerjaan</h3>
                            <div class="prose dark:prose-invert max-w-none text-yellow-800 dark:text-yellow-300">
                                {!! $task->instructions !!}
                            </div>
                        </div>
                    @endif

                    @if($task->attachments && count($task->attachments) > 0)
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">📎 Lampiran</h3>
                            <div class="grid gap-2">
                                @foreach($task->attachments as $attachment)
                                    <a href="{{ Storage::url($attachment) }}" target="_blank" 
                                       class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <svg class="w-5 h-5 mr-2 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ basename($attachment) }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Submit Form -->
            @if($canSubmit)
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden mb-6">
                    <div class="p-6 md:p-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">✍️ Submit Pengerjaan</h2>
                        
                        <form action="{{ route('tasks.submit', $task->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-6">
                                <label for="submission_content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Isi Pengerjaan <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    name="submission_content" 
                                    id="submission_content" 
                                    rows="10"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                    placeholder="Tulis hasil pengerjaan Anda di sini...">{{ old('submission_content') }}</textarea>
                                @error('submission_content')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="files" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    File Lampiran (Opsional)
                                </label>
                                <input 
                                    type="file" 
                                    name="files[]" 
                                    id="files" 
                                    multiple
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Maksimal 10 file, masing-masing 20MB. Format: PDF, DOC, DOCX, JPG, PNG, ZIP
                                </p>
                                @error('files.*')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex gap-3">
                                <button 
                                    type="submit"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors">
                                    🚀 Submit Pengerjaan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 mb-6">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-200">Tidak Dapat Submit</h3>
                            <p class="text-yellow-800 dark:text-yellow-300">
                                Anda telah mencapai batas maksimal submission untuk tugas ini ({{ $task->max_submissions_per_user }} submission).
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Previous Submissions -->
            @if($assignment->submissions->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">
                    <div class="p-6 md:p-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">📜 Riwayat Submission Anda</h2>
                        
                        <div class="space-y-4">
                            @foreach($assignment->submissions->sortByDesc('submitted_at') as $submission)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Submitted {{ $submission->submitted_at->format('d M Y H:i') }}
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($submission->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @elseif($submission->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($submission->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @else bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @endif">
                                            {{ $submission->status_label }}
                                        </span>
                                    </div>
                                    
                                    <div class="prose dark:prose-invert prose-sm max-w-none mb-3">
                                        {!! Str::limit($submission->submission_content, 200) !!}
                                    </div>

                                    @if($submission->review_notes)
                                        <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Feedback dari Reviewer:</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $submission->review_notes }}</p>
                                            @if($submission->score)
                                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">Skor: <span class="font-bold">{{ $submission->score }}/100</span></p>
                                            @endif
                                        </div>
                                    @endif

                                    <a href="{{ route('tasks.submission.view', $submission->id) }}" 
                                       class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 mt-2">
                                        Lihat Detail
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
