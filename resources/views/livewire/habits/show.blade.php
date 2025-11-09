<?php

use Livewire\Volt\Component;
use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    public Habit $habit;
    public bool $userIsParticipant = false;
    public bool $userIsCreator = false;
    public bool $canLogActivity = false;
    public ?HabitLog $todayLog = null;
    public string $logNote = '';
    public bool $isScheduledToday = false;
    public bool $isEditingLog = false;
    public string $editLogNote = '';

    public function mount($habitId)
    {
        $this->habit = Habit::with(['creator', 'schedules', 'approvedParticipants.user', 'logs.user'])->findOrFail($habitId);

        $this->userIsParticipant = $this->habit->hasParticipant(Auth::id());
        $this->userIsCreator = $this->habit->user_id === Auth::id();
        $this->isScheduledToday = $this->habit->isScheduledToday();
        
        // User can log activity if they are either the creator OR a participant
        $this->canLogActivity = ($this->userIsCreator || $this->userIsParticipant) && $this->isScheduledToday;

        if (($this->userIsParticipant || $this->userIsCreator) && $this->isScheduledToday) {
            $this->todayLog = HabitLog::where([
                'habit_id' => $this->habit->id,
                'user_id' => Auth::id(),
                'log_date' => today(),
            ])->first();
        }
    }

    public function lockHabit()
    {
        if ($this->habit->user_id !== Auth::id()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $this->habit->lock();
        session()->flash('success', 'Habit berhasil dikunci.');
        $this->mount($this->habit->id); // Refresh data
    }

    public function unlockHabit()
    {
        if ($this->habit->user_id !== Auth::id()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $this->habit->unlock();
        session()->flash('success', 'Habit berhasil dibuka kuncinya.');
        $this->mount($this->habit->id); // Refresh data
    }

    public function logActivity()
    {
        if (!$this->userIsParticipant && !$this->userIsCreator) {
            session()->flash('error', 'Anda tidak memiliki akses untuk mencatat aktivitas habit ini.');
            return;
        }

        if (!$this->isScheduledToday) {
            session()->flash('error', 'Habit ini tidak dijadwalkan untuk hari ini.');
            return;
        }

        try {
            // Use firstOrCreate to prevent duplicate entries
            $log = HabitLog::firstOrCreate(
                [
                    'habit_id' => $this->habit->id,
                    'user_id' => Auth::id(),
                    'log_date' => today(),
                ],
                [
                    'log_time' => now(),
                    'logged_at' => now(),
                    'notes' => $this->logNote,
                    'status' => 'present', // Use proper status field
                ]
            );

            if ($log->wasRecentlyCreated) {
                session()->flash('success', 'Aktivitas berhasil dicatat!');
            } else {
                session()->flash('info', 'Anda sudah mencatat aktivitas hari ini.');
            }

            $this->logNote = '';
            $this->mount($this->habit->id); // Refresh data
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat mencatat aktivitas.');
        }
    }

    public function startEditLog()
    {
        if (!$this->todayLog || !$this->todayLog->canBeEdited()) {
            session()->flash('error', 'Log tidak dapat diedit lagi.');
            return;
        }

        if ($this->todayLog->user_id !== Auth::id()) {
            session()->flash('error', 'Anda hanya dapat mengedit log aktivitas Anda sendiri.');
            return;
        }

        $this->isEditingLog = true;
        $this->editLogNote = $this->todayLog->notes ?? '';
    }

    public function cancelEditLog()
    {
        $this->isEditingLog = false;
        $this->editLogNote = '';
    }

    public function updateLog()
    {
        if (!$this->todayLog || !$this->todayLog->canBeEdited()) {
            session()->flash('error', 'Log tidak dapat diedit lagi.');
            return;
        }

        if ($this->todayLog->user_id !== Auth::id()) {
            session()->flash('error', 'Anda hanya dapat mengedit log aktivitas Anda sendiri.');
            return;
        }

        try {
            $this->todayLog->update([
                'notes' => $this->editLogNote,
            ]);

            session()->flash('success', 'Log aktivitas berhasil diperbarui!');
            $this->isEditingLog = false;
            $this->editLogNote = '';
            $this->mount($this->habit->id); // Refresh data
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat memperbarui log.');
        }
    }

    public function title()
    {
        return $this->habit->name . ' - Daily Habit Tracker';
    }

    public function getNextScheduledDayProperty()
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $today = strtolower(now()->format('l'));
        $todayIndex = array_search($today, $days);
        
        $scheduledDays = $this->habit->schedules()
            ->where('is_active', true)
            ->pluck('day_of_week')
            ->toArray();
        
        if (empty($scheduledDays)) {
            return null;
        }
        
        // Find next scheduled day
        for ($i = 1; $i <= 7; $i++) {
            $nextDayIndex = ($todayIndex + $i) % 7;
            $nextDay = $days[$nextDayIndex];
            
            if (in_array($nextDay, $scheduledDays)) {
                $schedule = $this->habit->schedules()
                    ->where('day_of_week', $nextDay)
                    ->where('is_active', true)
                    ->first();
                
                return [
                    'day' => $schedule->day_name ?? $nextDay,
                    'time' => $schedule->formatted_time ?? '',
                    'days_away' => $i == 7 ? 0 : $i // If 7 days away, it means next week same day
                ];
            }
        }
        
        return null;
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
            <div class="flex items-center justify-between mb-4">
                <div class="flex space-x-3">
                    <a href="{{ route('satu-tapak.index') }}"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                        ← Kembali
                    </a>
                    @if ($habit->user_id === Auth::id() && !$habit->is_locked)
                        <a href="{{ route('satu-tapak.invite', $habit->id) }}"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                            Undang Teman
                        </a>
                        <button wire:click="lockHabit"
                            wire:confirm="Apakah Anda yakin ingin mengunci habit ini? Setelah dikunci, habit tidak dapat diubah lagi."
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                            🔒 Kunci Habit
                        </button>
                    @elseif ($habit->user_id === Auth::id() && $habit->is_locked)
                        <button wire:click="unlockHabit"
                            wire:confirm="Apakah Anda yakin ingin membuka kunci habit ini? Setelah dibuka, habit dapat diubah kembali."
                            class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                            🔓 Buka Kunci Habit
                        </button>
                    @endif
                    <a href="{{ route('satu-tapak.statistics', $habit->id) }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                        📊 Statistik
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Habit Info -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Informasi Habit</h2>
                        @if($userIsCreator && !$habit->is_locked)
                            <a href="{{ route('satu-tapak.edit', $habit->id) }}" 
                               class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 text-sm font-medium">
                                ✏️ Edit Habit
                            </a>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Creator</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $habit->creator->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                            <p class="text-gray-900 dark:text-gray-100">
                                @if ($habit->is_locked)
                                    <span
                                        class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full dark:bg-red-900/50 dark:text-red-300">
                                        🔒 Terkunci
                                    </span>
                                @else
                                    <span
                                        class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full dark:bg-green-900/50 dark:text-green-300">
                                        ✏️ Aktif
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Durasi</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $habit->duration_days }} hari</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Sisa Hari</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $habit->remainingDays() }} hari</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Mulai</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $habit->start_date->format('d M Y') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Berakhir</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $habit->end_date->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Schedule -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Jadwal</h2>

                    @if($habit->schedules->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach ($habit->schedules as $schedule)
                                @php
                                    $today = strtolower(now()->format('l'));
                                    $isToday = $schedule->day_of_week === $today;
                                @endphp
                                <div class="@if($isToday) bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 @else bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 @endif border rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <div class="@if($isToday) text-green-900 dark:text-green-100 @else text-blue-900 dark:text-blue-100 @endif font-medium">
                                            {{ $schedule->day_name }}
                                            @if($isToday)
                                                <span class="text-xs bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-300 px-2 py-1 rounded-full ml-2">Hari Ini</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="@if($isToday) text-green-700 dark:text-green-300 @else text-blue-700 dark:text-blue-300 @endif text-sm">
                                        {{ $schedule->formatted_time }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 text-4xl mb-2">📅</div>
                            <p class="text-gray-500 dark:text-gray-400">Belum ada jadwal yang ditetapkan</p>
                        </div>
                    @endif
                </div>

                <!-- Log Activity (for creator and participants) -->
                @if ($userIsParticipant || $userIsCreator)
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Catat Aktivitas Hari Ini</h2>
                            @if($userIsCreator && !$userIsParticipant)
                                <span class="text-xs bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 px-2 py-1 rounded-full">Pembuat</span>
                            @elseif($userIsParticipant && !$userIsCreator)
                                <span class="text-xs bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 px-2 py-1 rounded-full">Peserta</span>
                            @elseif($userIsCreator && $userIsParticipant)
                                <span class="text-xs bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 px-2 py-1 rounded-full">Pembuat & Peserta</span>
                            @endif
                        </div>

                        @if (!$isScheduledToday)
                            {{-- Not scheduled today --}}
                            <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                <div class="flex items-center space-x-3">
                                    <div class="text-gray-400 text-2xl">📅</div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-700 dark:text-gray-300">Tidak ada jadwal hari ini</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            Habit ini dijadwalkan untuk: 
                                            @foreach($habit->schedules as $schedule)
                                                {{ $schedule->day_name }}@if(!$loop->last), @endif
                                            @endforeach
                                        </p>
                                        @if($this->nextScheduledDay)
                                            <p class="text-sm text-blue-600 dark:text-blue-400 mt-2">
                                                📌 Jadwal berikutnya: {{ $this->nextScheduledDay['day'] }} 
                                                @if($this->nextScheduledDay['days_away'] == 1)
                                                    (besok)
                                                @elseif($this->nextScheduledDay['days_away'] > 1)
                                                    ({{ $this->nextScheduledDay['days_away'] }} hari lagi)
                                                @else
                                                    (minggu depan)
                                                @endif
                                                pukul {{ $this->nextScheduledDay['time'] }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @elseif ($todayLog)
                            @if (!$isEditingLog)
                                {{-- Display existing log --}}
                                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start">
                                            <span class="text-green-600 dark:text-green-400 text-2xl mr-3">✅</span>
                                            <div class="flex-1">
                                                <p class="font-medium text-green-900 dark:text-green-100">Sudah mencatat aktivitas hari ini!</p>
                                                @if ($todayLog->notes)
                                                    <p class="text-sm text-green-700 dark:text-green-300 mt-1">{{ $todayLog->notes }}</p>
                                                @endif
                                                <div class="flex items-center justify-between mt-2">
                                                    <p class="text-xs text-green-600 dark:text-green-400">
                                                        Dicatat pada: {{ $todayLog->created_at->format('H:i') }}
                                                    </p>
                                                    @if($todayLog->user_id === Auth::id())
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                                            @if($todayLog->canBeEdited())
                                                                <span class="text-blue-600 dark:text-blue-400">{{ $todayLog->formatted_remaining_edit_time }}</span>
                                                            @else
                                                                <span>Tidak dapat diedit lagi</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @if($todayLog->user_id === Auth::id() && $todayLog->canBeEdited())
                                            <button wire:click="startEditLog" 
                                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 text-sm font-medium ml-4">
                                                ✏️ Edit
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @else
                                {{-- Edit form --}}
                                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <span class="text-blue-600 dark:text-blue-400 text-2xl mr-3">✏️</span>
                                        <div>
                                            <p class="font-medium text-blue-900 dark:text-blue-100">Edit Log Aktivitas</p>
                                            <p class="text-xs text-blue-600 dark:text-blue-400">{{ $todayLog->formatted_remaining_edit_time }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <div>
                                            <label for="editLogNote" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Catatan (Opsional)
                                            </label>
                                            <textarea wire:model="editLogNote" id="editLogNote" rows="3"
                                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100"
                                                placeholder="Ceritakan bagaimana Anda menjalankan habit hari ini..."></textarea>
                                        </div>
                                        
                                        <div class="flex space-x-2">
                                            <button wire:click="updateLog" wire:loading.attr="disabled"
                                                    wire:loading.class="opacity-50 cursor-not-allowed"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                                                <span wire:loading.remove wire:target="updateLog">💾 Simpan</span>
                                                <span wire:loading wire:target="updateLog">Menyimpan...</span>
                                            </button>
                                            <button wire:click="cancelEditLog"
                                                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                                                ❌ Batal
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="space-y-4">
                                <div>
                                    <label for="logNote"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Catatan (Opsional)
                                    </label>
                                    <textarea wire:model="logNote" id="logNote" rows="3"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100"
                                        placeholder="Ceritakan bagaimana Anda menjalankan habit hari ini..."></textarea>
                                </div>

                                <button wire:click="logActivity" wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-not-allowed"
                                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                                    <span wire:loading.remove>✅ Catat Aktivitas</span>
                                    <span wire:loading>Mencatat...</span>
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Participants -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Peserta ({{ $habit->approvedParticipants->count() }})
                    </h2>

                    <div class="space-y-3">
                        @foreach ($habit->approvedParticipants as $participant)
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-8 h-8 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 dark:text-blue-300 font-semibold text-sm">
                                        {{ strtoupper(substr($participant->user->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $participant->user->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Bergabung
                                        {{ $participant->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Activity Logs -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Aktivitas Terbaru</h2>

                    @if ($habit->logs->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada aktivitas yang dicatat.</p>
                    @else
                        <div class="space-y-3 max-h-64 overflow-y-auto">
                            @foreach ($habit->logs->take(10) as $log)
                                <div class="border-l-2 border-green-500 pl-3 py-2">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-gray-100 text-sm">
                                                {{ $log->user->name }}</p>
                                            @if ($log->notes)
                                                <p class="text-gray-600 dark:text-gray-400 text-xs mt-1">
                                                    {{ Str::limit($log->notes, 50) }}</p>
                                            @endif
                                        </div>
                                        <span
                                            class="text-xs text-gray-500 dark:text-gray-400">{{ $log->log_date->format('d M') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
