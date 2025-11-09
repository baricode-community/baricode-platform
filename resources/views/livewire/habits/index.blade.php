<?php

use Livewire\Volt\Component;
use App\Models\Habit;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component
{
    public string $activeTab = 'my-habits';
    public $myHabits;
    public $participatingHabits;
    public int $pendingInvitations = 0;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $user = Auth::user();
        
        $this->myHabits = $user->habits()
            ->with(['schedules', 'participants'])
            ->orderBy('created_at', 'desc')
            ->get();

        $this->participatingHabits = $user->habitParticipations()
            ->with(['habit.schedules', 'habit.creator'])
            ->where('status', 'approved')
            ->get()
            ->pluck('habit');

        $this->pendingInvitations = $user->receivedHabitInvitations()
            ->where('status', 'pending')
            ->count();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function title()
    {
        return 'Daily Habit Tracker - Satu Tapak';
    }
}; ?>

<div>
    <div class="">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Daily Habit Tracker</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Kelola dan pantau habit harian Anda bersama komunitas</p>
            </div>
            <a href="{{ route('satu-tapak.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
                Buat Habit Baru
            </a>
        </div>

        <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
            <nav class="-mb-px flex space-x-8">
                <button wire:click="switchTab('my-habits')" 
                        class="py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200
                            {{ $activeTab === 'my-habits' 
                                ? 'border-blue-500 text-blue-600 dark:text-blue-400 dark:border-blue-400' 
                                : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                    Habit Saya
                </button>
                <button wire:click="switchTab('participating-habits')" 
                        class="py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200
                            {{ $activeTab === 'participating-habits' 
                                ? 'border-blue-500 text-blue-600 dark:text-blue-400 dark:border-blue-400' 
                                : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                    Ikut Serta
                </button>
                <a href="{{ route('satu-tapak.invitations.index') }}" 
                   class="py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm dark:text-gray-400 dark:hover:text-gray-200">
                    Undangan ({{ $pendingInvitations }})
                </a>
            </nav>
        </div>

        <!-- My Habits Tab -->
        @if($activeTab === 'my-habits')
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Habit yang Saya Buat</h2>
                
                @if($myHabits->isEmpty())
                    <div class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="text-gray-400 text-6xl mb-4">🎯</div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Belum Ada Habit</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Mulai perjalanan kebiasaan baik Anda dengan membuat habit pertama</p>
                        <a href="{{ route('satu-tapak.create') }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                            Buat Habit Sekarang
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($myHabits as $habit)
                            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg dark:hover:shadow-xl dark:hover:shadow-gray-700/50 transition duration-200">
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $habit->name }}</h3>
                                    @if($habit->is_locked)
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full dark:bg-red-900/50 dark:text-red-300">
                                            🔒 Terkunci
                                        </span>
                                    @else
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full dark:bg-green-900/50 dark:text-green-300">
                                            ✏️ Dapat Diubah
                                        </span>
                                    @endif
                                </div>
                                
                                @if($habit->description)
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ Str::limit($habit->description, 100) }}</p>
                                @endif

                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Durasi:</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $habit->duration_days }} hari</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Mulai:</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $habit->start_date->format('d M Y') }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Sisa:</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $habit->remainingDays() }} hari</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Peserta:</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $habit->participants->where('status', 'approved')->count() }} orang</span>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Jadwal:</p>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($habit->schedules as $schedule)
                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded dark:bg-blue-900/50 dark:text-blue-300">
                                                {{ $schedule->day_name }} {{ $schedule->formatted_time }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="flex space-x-2">
                                    <a href="{{ route('satu-tapak.show', $habit->id) }}" 
                                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg text-sm font-medium transition duration-200">
                                        Detail
                                    </a>
                                    @if(!$habit->is_locked)
                                        <a href="{{ route('satu-tapak.invite', $habit->id) }}" 
                                           class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition duration-200">
                                            Undang
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <!-- Participating Habits Tab -->
        @if($activeTab === 'participating-habits')
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Habit yang Saya Ikuti</h2>
                
                @if($participatingHabits->isEmpty())
                    <div class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="text-gray-400 text-6xl mb-4">👥</div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Belum Ikut Habit</h3>
                        <p class="text-gray-500 dark:text-gray-400">Anda belum mengikuti habit apapun. Tunggu undangan dari teman atau buat habit sendiri.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($participatingHabits as $habit)
                            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg dark:hover:shadow-xl dark:hover:shadow-gray-700/50 transition duration-200">
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $habit->name }}</h3>
                                    <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded-full dark:bg-purple-900/50 dark:text-purple-300">
                                        👤 Peserta
                                    </span>
                                </div>
                                
                                @if($habit->description)
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ Str::limit($habit->description, 100) }}</p>
                                @endif

                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Creator:</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $habit->creator->name }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Sisa:</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $habit->remainingDays() }} hari</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Peserta:</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $habit->participants->where('status', 'approved')->count() }} orang</span>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Jadwal:</p>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($habit->schedules as $schedule)
                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded dark:bg-blue-900/50 dark:text-blue-300">
                                                {{ $schedule->day_name }} {{ $schedule->formatted_time }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <a href="{{ route('satu-tapak.show', $habit->id) }}" 
                                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg text-sm font-medium transition duration-200">
                                    Detail & Log Aktivitas
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>