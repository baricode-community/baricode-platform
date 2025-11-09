@extends('components.layouts.app')

@section('title', 'Undang Teman - ' . $habit->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <a href="{{ route('satu-tapak.habits.show', $habit) }}" 
               class="text-gray-500 hover:text-gray-700 mr-4">
                ← Kembali
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Undang Teman</h1>
                <p class="text-gray-600 mt-1">ke habit "{{ $habit->name }}"</p>
            </div>
        </div>
    </div>

    <form action="{{ route('satu-tapak.habits.send-invitation', $habit) }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- User Selection -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Pilih Teman</h2>
                    
                    @if($users->isEmpty())
                        <div class="text-center py-12">
                            <div class="text-gray-400 text-6xl mb-4">👥</div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada User Tersedia</h3>
                            <p class="text-gray-500">Semua user sudah menjadi peserta atau sudah diundang ke habit ini.</p>
                        </div>
                    @else
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @foreach($users as $user)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" 
                                           name="user_ids[]" 
                                           value="{{ $user->id }}" 
                                           class="text-blue-600 mr-4">
                                    <div class="flex items-center space-x-3 flex-1">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold">
                                                {{ $user->initials() }}
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        @error('user_ids')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <!-- Select All -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       id="select-all" 
                                       class="text-blue-600 mr-3">
                                <span class="text-sm font-medium text-gray-700">Pilih Semua</span>
                            </label>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Invitation Details -->
            <div class="space-y-6">
                <!-- Habit Info -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Info Habit</h2>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Nama</label>
                            <p class="text-gray-900">{{ $habit->name }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Durasi</label>
                            <p class="text-gray-900">{{ $habit->duration_days }} hari</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Periode</label>
                            <p class="text-gray-900">{{ $habit->start_date->format('d M Y') }} - {{ $habit->end_date->format('d M Y') }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Peserta Saat Ini</label>
                            <p class="text-gray-900">{{ $habit->approvedParticipants->count() }} orang</p>
                        </div>
                    </div>
                </div>

                <!-- Invitation Message -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Pesan Undangan</h2>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                            Pesan (Opsional)
                        </label>
                        <textarea name="message" 
                                  id="message" 
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Tulis pesan untuk mengajak teman bergabung dalam habit ini...">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                @if(!$users->isEmpty())
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
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
                selectAll.indeterminate = someChecked && !allChecked;
            });
        });
    }
});
</script>
@endsection