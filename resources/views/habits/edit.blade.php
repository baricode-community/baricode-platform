@extends('layouts.base')

@section('title', 'Edit Habit - ' . $habit->name)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <a href="{{ route('satu-tapak.habits.show', $habit) }}" 
               class="text-gray-500 hover:text-gray-700 mr-4">
                ← Kembali
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Edit Habit</h1>
        </div>
        <p class="text-gray-600">Edit informasi dasar habit (jadwal tidak dapat diubah setelah habit dimulai)</p>
    </div>

    <form action="{{ route('satu-tapak.habits.update', $habit) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Basic Information -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar</h2>
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Habit <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name', $habit->name) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Jelaskan tujuan dan detail habit ini...">{{ old('description', $habit->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Current Schedule (Read-only) -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Jadwal Saat Ini</h2>
            <p class="text-sm text-gray-600 mb-4">Jadwal habit tidak dapat diubah setelah habit dimulai.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($habit->schedules as $schedule)
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="text-lg font-semibold text-blue-900">{{ $schedule->day_name }}</div>
                        <div class="text-blue-700">{{ $schedule->formatted_time }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Current Settings (Read-only) -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Habit</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Durasi</label>
                    <div class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900">
                        {{ $habit->duration_days }} hari
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                    <div class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900">
                        {{ $habit->start_date->format('d M Y') }} - {{ $habit->end_date->format('d M Y') }}
                    </div>
                </div>
            </div>

            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-800">
                    <strong>Catatan:</strong> Durasi, tanggal mulai, dan jadwal tidak dapat diubah setelah habit dimulai untuk menjaga konsistensi tracking.
                </p>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('satu-tapak.habits.show', $habit) }}" 
               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition duration-200">
                Batal
            </a>
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
                Simpan Perubahan
            </button>
        </div>
    </form>

    <!-- Danger Zone -->
    @if($habit->user_id === Auth::id())
        <div class="mt-8 bg-red-50 border border-red-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-red-900 mb-4">Zona Berbahaya</h3>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-red-700 mb-3">
                        Menghapus habit akan menghapus semua data log aktivitas dan tidak dapat dikembalikan.
                    </p>
                    <form action="{{ route('satu-tapak.habits.destroy', $habit) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Apakah Anda yakin ingin menghapus habit ini? Semua data log aktivitas akan hilang dan tidak dapat dikembalikan!')"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                            Hapus Habit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection