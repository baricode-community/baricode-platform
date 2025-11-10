<?php

use Livewire\Volt\Component;
use App\Models\Habits\Habit;
use App\Models\Habits\HabitSchedule;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

new #[Layout('components.layouts.app')] class extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';
    
    #[Validate('nullable|string')]
    public string $description = '';
    
    #[Validate('required|integer|min:1|max:365')]
    public int $duration_days = 30;
    
    #[Validate('required|date|after_or_equal:today')]
    public string $start_date = '';
    
    #[Validate('required|array|min:1')]
    public array $schedules = [];

    public function mount()
    {
        $this->start_date = today()->format('Y-m-d');
        $this->schedules = [
            ['day' => '', 'time' => '07:00']
        ];
    }

    public function addSchedule()
    {
        $this->schedules[] = ['day' => '', 'time' => '07:00'];
    }

    public function removeSchedule($index)
    {
        if (count($this->schedules) > 1) {
            unset($this->schedules[$index]);
            $this->schedules = array_values($this->schedules);
        }
    }

    public function store()
    {
        // Validate schedules manually
        foreach ($this->schedules as $index => $schedule) {
            if (empty($schedule['day'])) {
                $this->addError("schedules.{$index}.day", 'Hari harus dipilih.');
            } elseif (!in_array($schedule['day'], ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])) {
                $this->addError("schedules.{$index}.day", 'Hari tidak valid.');
            }

            if (empty($schedule['time'])) {
                $this->addError("schedules.{$index}.time", 'Waktu harus diisi.');
            } elseif (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $schedule['time'])) {
                $this->addError("schedules.{$index}.time", 'Format waktu tidak valid.');
            }
        }

        $this->validate();

        try {
            $habit = Habit::create([
                'name' => $this->name,
                'description' => $this->description,
                'user_id' => Auth::id(),
                'duration_days' => $this->duration_days,
                'start_date' => $this->start_date,
                'is_active' => true,
            ]);

            // Create schedules
            foreach ($this->schedules as $schedule) {
                HabitSchedule::create([
                    'habit_id' => $habit->id,
                    'day_of_week' => $schedule['day'],
                    'scheduled_time' => $schedule['time'],
                    'is_active' => true,
                ]);
            }

            session()->flash('success', 'Habit berhasil dibuat!');
            return $this->redirect('/satu-tapak', navigate: true);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat membuat habit.');
        }
    }

    public function title()
    {
        return 'Buat Habit Baru - Daily Habit Tracker';
    }

    public function getDayName($day)
    {
        $days = [
            'monday' => 'Senin',
            'tuesday' => 'Selasa', 
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu'
        ];
        return $days[$day] ?? $day;
    }
}; ?>

<div>
    <div class="">
        @if (session()->has('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-8">
            <div class="flex items-center mb-4">
                <a href="{{ route('satu-tapak.index') }}" 
                   class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                    ← Kembali
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Buat Habit Baru</h1>
            </div>
            <p class="text-gray-600 dark:text-gray-400">Mulai perjalanan kebiasaan baik Anda dengan membuat habit yang konsisten</p>
        </div>

        <form wire:submit="store" class="space-y-6">
            <!-- Basic Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informasi Dasar</h2>
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nama Habit <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               wire:model="name"
                               id="name" 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100"
                               placeholder="Contoh: Olahraga Pagi, Membaca Buku, Meditasi"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Deskripsi
                        </label>
                        <textarea wire:model="description"
                                  id="description" 
                                  rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100"
                                  placeholder="Jelaskan tujuan dan detail habit ini..."></textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Duration and Start Date -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="duration_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Durasi (Hari) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   wire:model="duration_days"
                                   id="duration_days" 
                                   min="1" 
                                   max="365"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100"
                                   required>
                            @error('duration_days')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tanggal Mulai <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   wire:model="start_date"
                                   id="start_date" 
                                   min="{{ today()->format('Y-m-d') }}"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100"
                                   required>
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule Configuration -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Jadwal Habit</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Pilih hari dan waktu untuk menjalankan habit ini. Anda dapat memilih beberapa hari dengan waktu yang berbeda.</p>
                
                <div class="space-y-4">
                    @foreach($schedules as $index => $schedule)
                        <div class="schedule-item bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hari</label>
                                    <select wire:model="schedules.{{ $index }}.day" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-600 dark:text-gray-100" required>
                                        <option value="">Pilih Hari</option>
                                        <option value="monday">Senin</option>
                                        <option value="tuesday">Selasa</option>
                                        <option value="wednesday">Rabu</option>
                                        <option value="thursday">Kamis</option>
                                        <option value="friday">Jumat</option>
                                        <option value="saturday">Sabtu</option>
                                        <option value="sunday">Minggu</option>
                                    </select>
                                    @error('schedules.' . $index . '.day')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Waktu</label>
                                    <div class="flex space-x-2">
                                        <input type="time" 
                                               wire:model="schedules.{{ $index }}.time"
                                               class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-600 dark:text-gray-100"
                                               required>
                                        @if(count($schedules) > 1)
                                            <button type="button" 
                                                    wire:click="removeSchedule({{ $index }})"
                                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition duration-200">
                                                ✕
                                            </button>
                                        @endif
                                    </div>
                                    @error('schedules.' . $index . '.time')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @error('schedules')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror

                <div class="mt-4">
                    <button type="button" 
                            wire:click="addSchedule"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                        + Tambah Jadwal
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('satu-tapak.index') }}" 
                   class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200">
                    Batal
                </a>
                <button type="submit" 
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
                    <span wire:loading.remove>Buat Habit</span>
                    <span wire:loading>Membuat...</span>
                </button>
            </div>
        </form>
    </div>
</div>