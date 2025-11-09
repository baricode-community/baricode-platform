<?php

use Livewire\Volt\Component;
use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component
{
    public Habit $habit;
    public bool $userIsParticipant = false;
    public ?HabitLog $todayLog = null;
    public string $logNote = '';

    public function mount($habitId)
    {
        $this->habit = Habit::with(['creator', 'schedules', 'approvedParticipants.user', 'logs.user'])
                           ->findOrFail($habitId);
        
        $this->userIsParticipant = $this->habit->hasParticipant(Auth::id());
        
        if ($this->userIsParticipant) {
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

    public function logActivity()
    {
        if (!$this->userIsParticipant) {
            session()->flash('error', 'Anda bukan peserta habit ini.');
            return;
        }

        if ($this->todayLog) {
            session()->flash('error', 'Anda sudah mencatat aktivitas hari ini.');
            return;
        }

        try {
            HabitLog::create([
                'habit_id' => $this->habit->id,
                'user_id' => Auth::id(),
                'log_date' => today(),
                'notes' => $this->logNote,
                'completed' => true,
            ]);

            session()->flash('success', 'Aktivitas berhasil dicatat!');
            $this->logNote = '';
            $this->mount($this->habit->id); // Refresh data
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat mencatat aktivitas.');
        }
    }

    public function title()
    {
        return $this->habit->name . ' - Daily Habit Tracker';
    }
}; ?>

<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                <div class="flex items-center">
                    <a href="{{ route('satu-tapak.index') }}" 
                       class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                        ← Kembali
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $habit->name }}</h1>
                        @if($habit->description)
                            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $habit->description }}</p>
                        @endif
                    </div>
                </div>
                
                <div class="flex space-x-3">
                    @if($habit->user_id === Auth::id() && !$habit->is_locked)
                        <a href="{{ route('satu-tapak.invite', $habit->id) }}" 
                           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                            Undang Teman
                        </a>
                        <button wire:click="lockHabit"
                                wire:confirm="Apakah Anda yakin ingin mengunci habit ini? Setelah dikunci, habit tidak dapat diubah lagi."
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                            🔒 Kunci Habit
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
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informasi Habit</h2>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Creator</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $habit->creator->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                            <p class="text-gray-900 dark:text-gray-100">
                                @if($habit->is_locked)
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full dark:bg-red-900/50 dark:text-red-300">
                                        🔒 Terkunci
                                    </span>
                                @else
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full dark:bg-green-900/50 dark:text-green-300">
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
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($habit->schedules as $schedule)
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                                <div class="font-medium text-blue-900 dark:text-blue-100">{{ $schedule->day_name }}</div>
                                <div class="text-sm text-blue-700 dark:text-blue-300">{{ $schedule->formatted_time }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Log Activity (only for participants) -->
                @if($userIsParticipant)
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Catat Aktivitas Hari Ini</h2>
                        
                        @if($todayLog)
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                <div class="flex items-center">
                                    <span class="text-green-600 dark:text-green-400 text-2xl mr-3">✅</span>
                                    <div>
                                        <p class="font-medium text-green-900 dark:text-green-100">Sudah mencatat aktivitas hari ini!</p>
                                        @if($todayLog->notes)
                                            <p class="text-sm text-green-700 dark:text-green-300 mt-1">{{ $todayLog->notes }}</p>
                                        @endif
                                        <p class="text-xs text-green-600 dark:text-green-400 mt-1">Dicatat pada: {{ $todayLog->created_at->format('H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="space-y-4">
                                <div>
                                    <label for="logNote" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Catatan (Opsional)
                                    </label>
                                    <textarea wire:model="logNote"
                                              id="logNote"
                                              rows="3"
                                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100"
                                              placeholder="Ceritakan bagaimana Anda menjalankan habit hari ini..."></textarea>
                                </div>
                                
                                <button wire:click="logActivity"
                                        wire:loading.attr="disabled"
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
                        @foreach($habit->approvedParticipants as $participant)
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 dark:text-blue-300 font-semibold text-sm">
                                        {{ strtoupper(substr($participant->user->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $participant->user->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Bergabung {{ $participant->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Activity Logs -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Aktivitas Terbaru</h2>
                    
                    @if($habit->logs->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada aktivitas yang dicatat.</p>
                    @else
                        <div class="space-y-3 max-h-64 overflow-y-auto">
                            @foreach($habit->logs->take(10) as $log)
                                <div class="border-l-2 border-green-500 pl-3 py-2">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-gray-100 text-sm">{{ $log->user->name }}</p>
                                            @if($log->notes)
                                                <p class="text-gray-600 dark:text-gray-400 text-xs mt-1">{{ Str::limit($log->notes, 50) }}</p>
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $log->log_date->format('d M') }}</span>
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