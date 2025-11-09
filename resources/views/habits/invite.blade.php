@extends('components.layouts.app')

@section('title', 'Undang Teman - ' . $habit->name)

@section('content')
<div class="">
    <div class="mb-8">
        <div class="flex items-center mb-4">
            {{-- Tombol Kembali --}}
            <a href="{{ route('satu-tapak.show', $habit) }}" 
               class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                ← Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('satu-tapak.send-invitation', $habit) }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Pilih Teman</h2>
                    
                    @if($users->isEmpty())
                        {{-- Empty State --}}
                        <div class="text-center py-12">
                            <div class="text-gray-400 text-6xl mb-4">👥</div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Tidak Ada User Tersedia</h3>
                            <p class="text-gray-500 dark:text-gray-400">Semua user sudah menjadi peserta atau sudah diundang ke habit ini.</p>
                        </div>
                    @else
                        <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                            @foreach($users as $user)
                                {{-- User Card/Label --}}
                                <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition duration-150">
                                    <input type="checkbox" 
                                           name="user_ids[]" 
                                           value="{{ $user->id }}" 
                                           class="text-blue-600 dark:bg-gray-600 dark:border-gray-500 mr-4 focus:ring-blue-500">
                                    <div class="flex items-center space-x-3 flex-1">
                                        {{-- Initial Badge --}}
                                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 dark:text-blue-300 font-semibold">
                                                {{ $user->initials() }}
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        @error('user_ids')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       id="select-all" 
                                       class="text-blue-600 dark:bg-gray-600 dark:border-gray-500 mr-3 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Semua</span>
                            </label>
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Info Habit</h2>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $habit->name }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Durasi</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $habit->duration_days }} hari</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Periode</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $habit->start_date->format('d M Y') }} - {{ $habit->end_date->format('d M Y') }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Peserta Saat Ini</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $habit->approvedParticipants->count() }} orang</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Pesan Undangan</h2>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Pesan (Opsional)
                        </label>
                        {{-- Textarea --}}
                        <textarea name="message" 
                                  id="message" 
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100"
                                  placeholder="Tulis pesan untuk mengajak teman bergabung dalam habit ini...">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                @if(!$users->isEmpty())
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition duration-200">
                            Kirim Undangan
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('input[name="user_ids[]"]');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        });

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                const someChecked = Array.from(checkboxes).some(cb => cb.checked);
                
                selectAll.checked = allChecked;
                // Menggunakan properti indeterminate untuk tampilan checkbox parsial
                // Properti ini hanya dapat diatur melalui JavaScript
                selectAll.indeterminate = someChecked && !allChecked;
            });
        });
    }
});
</script>
@endsection