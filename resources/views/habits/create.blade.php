@extends('components.layouts.app')

@section('title', 'Buat Habit Baru - Daily Habit Tracker')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <a href="{{ route('satu-tapak.habits.index') }}" 
               class="text-gray-500 hover:text-gray-700 mr-4">
                ← Kembali
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Buat Habit Baru</h1>
        </div>
        <p class="text-gray-600">Mulai perjalanan kebiasaan baik Anda dengan membuat habit yang konsisten</p>
    </div>

    <form action="{{ route('satu-tapak.habits.store') }}" method="POST" class="space-y-6">
        @csrf
        
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
                           value="{{ old('name') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Contoh: Olahraga Pagi, Membaca Buku, Meditasi"
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
                              placeholder="Jelaskan tujuan dan detail habit ini...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Duration and Start Date -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="duration_days" class="block text-sm font-medium text-gray-700 mb-2">
                            Durasi (Hari) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="duration_days" 
                               id="duration_days" 
                               value="{{ old('duration_days', 30) }}"
                               min="1" 
                               max="365"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                        @error('duration_days')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="start_date" 
                               id="start_date" 
                               value="{{ old('start_date', today()->format('Y-m-d')) }}"
                               min="{{ today()->format('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule Configuration -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Jadwal Habit</h2>
            <p class="text-sm text-gray-600 mb-4">Pilih hari dan waktu untuk menjalankan habit ini. Anda dapat memilih beberapa hari dengan waktu yang berbeda.</p>
            
            <div id="schedules-container" class="space-y-4">
                <div class="schedule-item bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hari</label>
                            <select name="schedules[0][day]" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <option value="">Pilih Hari</option>
                                <option value="monday" {{ old('schedules.0.day') == 'monday' ? 'selected' : '' }}>Senin</option>
                                <option value="tuesday" {{ old('schedules.0.day') == 'tuesday' ? 'selected' : '' }}>Selasa</option>
                                <option value="wednesday" {{ old('schedules.0.day') == 'wednesday' ? 'selected' : '' }}>Rabu</option>
                                <option value="thursday" {{ old('schedules.0.day') == 'thursday' ? 'selected' : '' }}>Kamis</option>
                                <option value="friday" {{ old('schedules.0.day') == 'friday' ? 'selected' : '' }}>Jumat</option>
                                <option value="saturday" {{ old('schedules.0.day') == 'saturday' ? 'selected' : '' }}>Sabtu</option>
                                <option value="sunday" {{ old('schedules.0.day') == 'sunday' ? 'selected' : '' }}>Minggu</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Waktu</label>
                            <input type="time" 
                                   name="schedules[0][time]" 
                                   value="{{ old('schedules.0.time', '07:00') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required>
                        </div>
                    </div>
                </div>
            </div>

            @error('schedules')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="mt-4 flex space-x-3">
                <button type="button" 
                        id="add-schedule" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                    + Tambah Jadwal
                </button>
                <button type="button" 
                        id="remove-schedule" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200"
                        style="display: none;">
                    - Hapus Jadwal
                </button>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('satu-tapak.habits.index') }}" 
               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition duration-200">
                Batal
            </a>
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
                Buat Habit
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let scheduleIndex = 1;
    const container = document.getElementById('schedules-container');
    const addBtn = document.getElementById('add-schedule');
    const removeBtn = document.getElementById('remove-schedule');

    addBtn.addEventListener('click', function() {
        const scheduleHtml = `
            <div class="schedule-item bg-gray-50 p-4 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hari</label>
                        <select name="schedules[${scheduleIndex}][day]" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Pilih Hari</option>
                            <option value="monday">Senin</option>
                            <option value="tuesday">Selasa</option>
                            <option value="wednesday">Rabu</option>
                            <option value="thursday">Kamis</option>
                            <option value="friday">Jumat</option>
                            <option value="saturday">Sabtu</option>
                            <option value="sunday">Minggu</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Waktu</label>
                        <input type="time" 
                               name="schedules[${scheduleIndex}][time]" 
                               value="07:00"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', scheduleHtml);
        scheduleIndex++;
        updateRemoveButtonVisibility();
    });

    removeBtn.addEventListener('click', function() {
        const scheduleItems = container.querySelectorAll('.schedule-item');
        if (scheduleItems.length > 1) {
            scheduleItems[scheduleItems.length - 1].remove();
            updateRemoveButtonVisibility();
        }
    });

    function updateRemoveButtonVisibility() {
        const scheduleItems = container.querySelectorAll('.schedule-item');
        removeBtn.style.display = scheduleItems.length > 1 ? 'inline-block' : 'none';
    }

    // Initial visibility check
    updateRemoveButtonVisibility();
});
</script>
@endsection